<?php

namespace App\Http\Controllers\Factory;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductVariant;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:factory']);
    }

    public function index(Request $request)
    {
        $factory = Auth::user()->factory;
        $query = Product::where('factory_id', $factory->id)
            ->where('type', 'processed')
            ->with(['category']);
        if ($request->has('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status === 'active');
        }
        $products = $query->latest()->paginate(12);
        $inventorySummary = [
            'total_products' => $products->total(),
            'low_stock' => Product::where('factory_id', $factory->id)
                ->where('type', 'processed')
                ->where('stock', '<=', 10)
                ->count(),
            'out_of_stock' => Product::where('factory_id', $factory->id)
                ->where('type', 'processed')
                ->where('stock', '<=', 0)
                ->count(),
        ];
        $factoryExists = $factory !== null;
        return view('factory.products.index', [
            'products' => $products,
            'categories' => [], // No categories needed
            'inventorySummary' => $inventorySummary,
            'filters' => $request->all(),
            'factoryExists' => $factoryExists,
        ]);
    }

    public function create()
    {
        $categories = Category::where('type', 'product')->get();
        return view('factory.products.create', [
            'categories' => $categories,
            'product' => new Product()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'status' => 'required|boolean',
        ]);
        $validated['factory_id'] = Auth::user()->factory->id;
        $validated['type'] = 'processed';
        $validated['supplier_id'] = null;
        $validated['category_id'] = null;
        $validated['sku'] = null;
        $product = Product::create($validated);
        return redirect()->route('factory.products.index')
            ->with('success', 'Product created successfully.');
    }

    public function edit(Product $product)
    {
        // Verify the product belongs to this factory
        if ($product->factory_id !== Auth::user()->factory->id) {
            return redirect()->route('factory.products.index')
                ->with('error', 'You do not have permission to edit this product.');
        }
        
        $categories = Category::where('type', 'product')->get();
        $product->load(['category']);
        
        return view('factory.products.edit', [
            'product' => $product,
            'categories' => $categories
        ]);
    }

    public function update(Request $request, Product $product)
    {
        // Verify the product belongs to this factory
        if ($product->factory_id !== Auth::user()->factory->id) {
            return redirect()->route('factory.products.index')
                ->with('error', 'You do not have permission to update this product.');
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'cost' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'min_stock_level' => 'nullable|integer|min:0',
            'weight' => 'nullable|numeric|min:0',
            'dimensions' => 'nullable|string',
            'status' => 'boolean',
            'is_featured' => 'boolean',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'removed_images' => 'nullable|array',
            'removed_images.*' => 'exists:product_images,id',
        ]);
        
        // Generate slug if name changed
        if ($product->name !== $validated['name']) {
            $validated['slug'] = Str::slug($validated['name']);
        }
        
        $validated['status'] = $request->has('status');
        $validated['is_featured'] = $request->has('is_featured');
        
        // Start transaction
        DB::beginTransaction();
        
        try {
            // Calculate stock difference
            $stockDifference = $validated['stock'] - $product->stock;
            $validated['stock'] = $validated['stock']; // Update to new stock value
            
            // Update the product
            $product->update($validated);
            
            // Handle removed images
            if ($request->has('removed_images')) {
                $imagesToDelete = $product->images()->whereIn('id', $request->removed_images)->get();
                foreach ($imagesToDelete as $image) {
                    Storage::delete($image->image_path);
                    $image->delete();
                }
            }
            
            // Handle new images
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $path = $this->storeImage($image);
                    $product->images()->create(['image_path' => $path]);
                }
            }
            
            // Update inventory if stock changed
            if ($stockDifference != 0) {
                $product->inventory()->create([
                    'quantity' => $stockDifference,
                    'cost' => $validated['cost'],
                    'reason' => $stockDifference > 0 ? 'Stock adjustment (added)' : 'Stock adjustment (removed)',
                    'reference_type' => 'system',
                    'reference_id' => 0,
                    'created_by' => Auth::id(),
                ]);
            }
            
            DB::commit();
            
            return redirect()->route('factory.products.index')
                ->with('success', 'Product updated successfully.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error updating product: ' . $e->getMessage());
            return back()->withInput()
                ->with('error', 'Failed to update product. Please try again.');
        }
    }

    public function destroy(Product $product)
    {
        // Verify the product belongs to this factory
        if ($product->factory_id !== Auth::user()->factory->id) {
            return redirect()->route('factory.products.index')
                ->with('error', 'You do not have permission to delete this product.');
        }
        
        // Check if product has any orders
        if ($product->orderItems()->exists()) {
            return redirect()->route('factory.products.index')
                ->with('error', 'Cannot delete product with existing orders. Please archive it instead.');
        }
        
        // Start transaction
        DB::beginTransaction();
        
        try {
            // Delete product images from storage
            foreach ($product->images as $image) {
                Storage::delete($image->image_path);
                $image->delete();
            }
            
            // Delete variants and their images
            foreach ($product->variants as $variant) {
                foreach ($variant->images as $image) {
                    Storage::delete($image->image_path);
                    $image->delete();
                }
                $variant->delete();
            }
            
            // Delete inventory records
            $product->inventory()->delete();
            
            // Finally, delete the product
            $product->delete();
            
            DB::commit();
            
            return redirect()->route('factory.products.index')
                ->with('success', 'Product deleted successfully.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error deleting product: ' . $e->getMessage());
            return redirect()->route('factory.products.index')
                ->with('error', 'Failed to delete product. Please try again.');
        }
    }
    /**
     * Store uploaded image and return path
     */
    private function storeImage($file)
    {
        // Create directory if not exists
        $directory = 'products/' . now()->format('Y/m');
        if (!Storage::exists($directory)) {
            Storage::makeDirectory($directory);
        }
        
        // Generate filename
        $filename = Str::random(20) . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs($directory, $filename);
        
        // Create thumbnails if needed
        $this->createThumbnails($file, $directory, $filename);
        
        return $path;
    }
    
    /**
     * Create thumbnails for the uploaded image
     */
    private function createThumbnails($file, $directory, $filename)
    {
        $sizes = [
            'thumb' => [200, 200],
            'medium' => [500, 500],
            'large' => [800, 800],
        ];
        
        foreach ($sizes as $type => $dimensions) {
            $image = Image::make($file);
            $image->resize($dimensions[0], $dimensions[1], function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
            
            $thumbPath = $directory . '/' . $type . '_' . $filename;
            Storage::put($thumbPath, (string) $image->encode());
        }
    }
    
    /**
     * Toggle product status
     */
    public function toggleStatus(Product $product)
    {
        if ($product->factory_id !== Auth::user()->factory->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized action.'
            ], 403);
        }
        
        $product->status = !$product->status;
        $product->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Product status updated successfully.',
            'status' => $product->status
        ]);
    }
} 