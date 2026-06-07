<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // --- Users ---
        User::factory()->create([
            'name'     => 'Admin',
            'email'    => 'admin@farm.local',
            'password' => bcrypt('password'),
            'role'     => 'admin',
        ]);

        User::factory()->create([
            'name'     => 'Customer',
            'email'    => 'customer@farm.local',
            'password' => bcrypt('password'),
            'role'     => 'customer',
        ]);

        User::factory()->create([
            'name'     => 'Shop Buyer',
            'email'    => 'shop@farm.local',
            'password' => bcrypt('password'),
            'role'     => 'shop',
        ]);

        User::factory()->create([
            'name'     => 'Staff Member',
            'email'    => 'staff@farm.local',
            'password' => bcrypt('password'),
            'role'     => 'staff',
        ]);

        // --- Categories ---
        $vegetables = Category::firstOrCreate(['slug' => 'vegetables'], ['name' => 'Vegetables']);
        $fruits     = Category::firstOrCreate(['slug' => 'fruits'],     ['name' => 'Fruits']);
        $dairy      = Category::firstOrCreate(['slug' => 'dairy'],      ['name' => 'Dairy']);
        $poultry    = Category::firstOrCreate(['slug' => 'poultry'],    ['name' => 'Poultry']);
        $other      = Category::firstOrCreate(['slug' => 'other'],      ['name' => 'Other']);

        // --- Products ---
        Product::create([
            'name'        => 'Tomatoes',
            'description' => 'Fresh vine tomatoes picked daily.',
            'price'       => 40.00,
            'bulk_price'  => 32.00,
            'unit'        => 'kg',
            'stock'       => 50,
            'is_active'   => true,
            'category_id' => $vegetables->id,
        ]);

        Product::create([
            'name'        => 'Spinach',
            'description' => 'Tender baby spinach, washed and ready.',
            'price'       => 25.00,
            'bulk_price'  => 20.00,
            'unit'        => 'bunch',
            'stock'       => 30,
            'is_active'   => true,
            'category_id' => $vegetables->id,
        ]);

        Product::create([
            'name'        => 'Carrots',
            'description' => 'Sweet garden carrots.',
            'price'       => 35.00,
            'bulk_price'  => 28.00,
            'unit'        => 'kg',
            'stock'       => 40,
            'is_active'   => true,
            'category_id' => $vegetables->id,
        ]);

        Product::create([
            'name'        => 'Coconut',
            'description' => 'Fresh Kerala coconut.',
            'price'       => 30.00,
            'bulk_price'  => 24.00,
            'unit'        => 'piece',
            'stock'       => 60,
            'is_active'   => true,
            'category_id' => $fruits->id,
        ]);

        Product::create([
            'name'        => 'Fresh Milk',
            'description' => 'Farm fresh cow milk.',
            'price'       => 60.00,
            'bulk_price'  => 50.00,
            'unit'        => 'litre',
            'stock'       => 20,
            'is_active'   => true,
            'category_id' => $dairy->id,
        ]);

        // --- Sample order for customer ---
        $customer = User::where('email', 'customer@farm.local')->first();
        $products = Product::take(3)->get();

        $order = Order::create([
            'user_id'          => $customer->id,
            'status'           => 'pending',
            'total'            => 0,
            'delivery_address' => '12 Mango Lane, Thrissur, Kerala 680001',
            'notes'            => 'Please leave at the gate.',
        ]);

        $total = 0;
        foreach ($products as $product) {
            $qty = rand(1, 4);
            $order->items()->create([
                'product_id' => $product->id,
                'quantity'   => $qty,
                'unit_price' => $product->price,
            ]);
            $total += $qty * $product->price;
        }

        $order->update(['total' => $total]);
    }
}