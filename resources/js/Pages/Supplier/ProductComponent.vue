<template>
  <div class="product-container">
    <div class="product-header">
      <h4>Product Management</h4>
      <button @click="showAddModal = true" class="btn-primary">Add Product</button>
    </div>

    <div class="product-grid">
      <div class="product-card" v-for="product in products" :key="product.id">
        <h5>{{ product.name }}</h5>
        <p><strong>Category:</strong> {{ product.category.name }}</p>
        <p><strong>Price:</strong> ${{ product.price }}</p>
        <p><strong>Stock:</strong> {{ product.stock }} units</p>
        <div class="product-actions">
          <button @click="editProduct(product)" class="btn-edit">Edit</button>
          <button @click="deleteProduct(product.id)" class="btn-delete">Delete</button>
        </div>
      </div>
    </div>

    <!-- Add/Edit Modal -->
    <div v-if="showAddModal || showEditModal" class="modal-overlay">
      <div class="modal">
        <h4>{{ isEditing ? 'Edit Product' : 'Add Product' }}</h4>
        <form @submit.prevent="saveProduct">
          <div class="form-group">
            <label>Name</label>
            <input v-model="productForm.name" required />
          </div>
          <div class="form-group">
            <label>Description</label>
            <textarea v-model="productForm.description" required></textarea>
          </div>
          <div class="form-group">
            <label>Price</label>
            <input type="number" v-model="productForm.price" required />
          </div>
          <div class="form-group">
            <label>Stock</label>
            <input type="number" v-model="productForm.stock" required />
          </div>
          <div class="form-group">
            <label>Category</label>
            <select v-model="productForm.category_id" required>
              <option value="">Select Category</option>
              <option v-for="category in categories" :key="category.id" :value="category.id">
                {{ category.name }}
              </option>
            </select>
          </div>
          <div class="modal-actions">
            <button type="submit" class="btn-primary">Save</button>
            <button @click="closeModal" class="btn-secondary">Cancel</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import { defineComponent } from 'vue';
import { usePage } from '@inertiajs/vue3';

export default defineComponent({
  data() {
    return {
      products: [],
      categories: [],
      showAddModal: false,
      showEditModal: false,
      isEditing: false,
      productForm: {
        id: null,
        name: '',
        description: '',
        price: '',
        stock: '',
        category_id: '',
      },
    };
  },

  computed: {
    Auth() {
      return usePage().props.auth;
    },
  },

  mounted() {
    this.loadProducts();
    this.loadCategories();
    this.listenForUpdates();
  },

  methods: {
    async loadProducts() {
      try {
        const response = await axios.get(route('supplier.products.index'));
        this.products = response.data;
      } catch (error) {
        console.error('Error loading products:', error);
      }
    },

    async loadCategories() {
      try {
        const response = await axios.get(route('supplier.categories'));
        this.categories = response.data;
      } catch (error) {
        console.error('Error loading categories:', error);
      }
    },

    editProduct(product) {
      this.isEditing = true;
      this.productForm = { ...product };
      this.showEditModal = true;
    },

    async deleteProduct(id) {
      if (!confirm('Are you sure you want to delete this product?')) return;

      try {
        await axios.delete(route('supplier.products.destroy', id));
        this.loadProducts();
      } catch (error) {
        console.error('Error deleting product:', error);
      }
    },

    async saveProduct() {
      try {
        const routeName = this.isEditing ? 'supplier.products.update' : 'supplier.products.store';
        const response = await axios[this.isEditing ? 'put' : 'post'](
          route(routeName, this.isEditing ? this.productForm.id : null),
          this.productForm
        );

        this.closeModal();
        this.loadProducts();
      } catch (error) {
        console.error('Error saving product:', error);
      }
    },

    closeModal() {
      this.showAddModal = false;
      this.showEditModal = false;
      this.isEditing = false;
      this.productForm = {
        id: null,
        name: '',
        description: '',
        price: '',
        stock: '',
        category_id: '',
      };
    },

    listenForUpdates() {
      window.Echo.private(`product.${this.Auth.user.id}`)
        .listen('.product-update', (e) => {
          this.loadProducts();
        });
    },
  },
});
</script>

<style scoped>
.product-container {
  padding: 2rem;
  max-width: 1200px;
  margin: 0 auto;
}

.product-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
}

.product-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 2rem;
}

.product-card {
  background: #fff;
  border-radius: 0.5rem;
  padding: 1.5rem;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.product-actions {
  display: flex;
  gap: 1rem;
  margin-top: 1rem;
}

.form-group {
  margin-bottom: 1rem;
}

.form-group label {
  display: block;
  margin-bottom: 0.5rem;
}

.form-group input,
.form-group select,
.form-group textarea {
  width: 100%;
  padding: 0.5rem;
  border: 1px solid #ddd;
  border-radius: 4px;
}

.form-group textarea {
  height: 100px;
  resize: vertical;
}

.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
}

.modal {
  background: #fff;
  padding: 2rem;
  border-radius: 0.5rem;
  width: 90%;
  max-width: 500px;
}

.modal-actions {
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
  margin-top: 1rem;
}

.btn-primary {
  background: #2196f3;
  color: white;
  border: none;
  padding: 0.5rem 1rem;
  border-radius: 4px;
  cursor: pointer;
}

.btn-secondary {
  background: #ddd;
  color: #333;
  border: none;
  padding: 0.5rem 1rem;
  border-radius: 4px;
  cursor: pointer;
}

.btn-edit {
  background: #4caf50;
  color: white;
  border: none;
  padding: 0.25rem 0.5rem;
  border-radius: 4px;
  cursor: pointer;
}

.btn-delete {
  background: #f44336;
  color: white;
  border: none;
  padding: 0.25rem 0.5rem;
  border-radius: 4px;
  cursor: pointer;
}
</style>
