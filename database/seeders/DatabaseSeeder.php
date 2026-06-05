<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Menu;
use App\Models\Table;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin QuickDine',
            'email' => 'admin@quickdine.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Staf Operasional',
            'email' => 'staf@quickdine.com',
            'password' => Hash::make('password123'),
            'role' => 'staff',
        ]);

        Table::create([
            'table_number' => 1,
            'status' => 'available',
            'qr_code_token' => uniqid('qr_'),
        ]);

        Table::create([
            'table_number' => 2,
            'status' => 'available',
            'qr_code_token' => uniqid('qr_'),
        ]);

        $kopi = Category::create(['name' => 'Kopi']);
        $nonKopi = Category::create(['name' => 'Non-Kopi']);
        $makanan = Category::create(['name' => 'Makanan']);
        $cemilan = Category::create(['name' => 'Cemilan']);

        Menu::create([
            'category_id' => $kopi->id,
            'name' => 'Kopi Susu Gula Aren',
            'description' => 'Espresso, susu segar, dan gula aren asli.',
            'price' => 22000,
            'stock' => 50,
            'image_url' => 'https://images.unsplash.com/photo-1593443320739-77f74939d0da?w=200&q=80'
        ]);

        Menu::create([
            'category_id' => $kopi->id,
            'name' => 'Americano Dingin',
            'description' => 'Double shot espresso dengan air dingin.',
            'price' => 18000,
            'stock' => 100,
            'image_url' => 'https://images.unsplash.com/photo-1517701550927-30cfcb64c5ed?w=200&q=80'
        ]);

        Menu::create([
            'category_id' => $nonKopi->id,
            'name' => 'Matcha Latte',
            'description' => 'Teh hijau matcha premium dengan susu segar.',
            'price' => 25000,
            'stock' => 40,
            'image_url' => 'https://images.unsplash.com/photo-1536256263959-770b48d82b0a?w=200&q=80'
        ]);

        Menu::create([
            'category_id' => $makanan->id,
            'name' => 'Nasi Goreng Spesial',
            'description' => 'Nasi goreng dengan bumbu rahasia, ayam suwir, dan telur mata sapi.',
            'price' => 35000,
            'stock' => 30,
            'image_url' => 'https://images.unsplash.com/photo-1603133872878-684f208fb84b?w=200&q=80'
        ]);

        Menu::create([
            'category_id' => $cemilan->id,
            'name' => 'Kentang Goreng',
            'description' => 'French fries renyah dengan taburan garam dan parsley.',
            'price' => 20000,
            'stock' => 60,
            'image_url' => 'https://images.unsplash.com/photo-1576107232684-1279f390859f?w=200&q=80'
        ]);
    }
}
