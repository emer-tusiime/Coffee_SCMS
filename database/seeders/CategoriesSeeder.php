<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategoriesSeeder extends Seeder
{
    public function run()
    {
        $categories = [
            ['name' => 'Arabica'],
            ['name' => 'Robusta'],
            ['name' => 'Instant Coffee'],
            ['name' => 'Espresso'],
            ['name' => 'Decaf'],
        ];
        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}