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
        User::firstOrCreate(
            ['email' => 'admin@quickdine.com'],
            [
                'name' => 'Admin QuickDine',
                'password' => Hash::make('password123'),
                'role' => 'admin',
            ]
        );

        User::firstOrCreate(
            ['email' => 'staf@quickdine.com'],
            [
                'name' => 'Staf Operasional',
                'password' => Hash::make('password123'),
                'role' => 'staff',
            ]
        );

        Table::firstOrCreate(
            ['table_number' => 1],
            ['status' => 'available', 'qr_code_token' => uniqid('qr_')]
        );

        Table::firstOrCreate(
            ['table_number' => 2],
            ['status' => 'available', 'qr_code_token' => uniqid('qr_')]
        );

        $kopi = Category::firstOrCreate(['name' => 'Kopi']);
        $nonKopi = Category::firstOrCreate(['name' => 'Non-Kopi']);
        $makanan = Category::firstOrCreate(['name' => 'Makanan']);
        $cemilan = Category::firstOrCreate(['name' => 'Cemilan']);

        Menu::firstOrCreate(
            ['name' => 'Kopi Susu Gula Aren'],
            [
                'category_id' => $kopi->id,
                'description' => 'Espresso, susu segar, dan gula aren asli.',
                'price' => 22000,
                'stock' => 50,
                'image_url' => 'https://images.unsplash.com/photo-1593443320739-77f74939d0da?w=200&q=80'
            ]
        );

        Menu::firstOrCreate(
            ['name' => 'Matcha Latte'],
            [
                'category_id' => $nonKopi->id,
                'description' => 'Teh hijau matcha premium dengan susu segar.',
                'price' => 25000,
                'stock' => 40,
                'image_url' => 'https://images.unsplash.com/photo-1536256263959-770b48d82b0a?w=200&q=80'
            ]
        );

        Menu::firstOrCreate(
            ['name' => 'Nasi Goreng Spesial'],
            [
                'category_id' => $makanan->id,
                'description' => 'Nasi goreng dengan bumbu rahasia, ayam suwir, dan telur mata sapi.',
                'price' => 35000,
                'stock' => 30,
                'image_url' => 'https://images.unsplash.com/photo-1603133872878-684f208fb84b?w=200&q=80'
            ]
        );

        Menu::firstOrCreate(
            ['name' => 'Kentang Goreng'],
            [
                'category_id' => $cemilan->id,
                'description' => 'French fries renyah dengan taburan garam dan parsley.',
                'price' => 20000,
                'stock' => 60,
                'image_url' => 'https://images.unsplash.com/photo-1576107232684-1279f390859f?w=200&q=80'
            ]
        );
    }
}
