<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use App\Models\Menu;
use App\Models\Category;
use App\Models\Table;

class OrderFeatureTest extends TestCase
{
    use DatabaseTransactions;

    protected $category;
    protected $table;
    protected $menu;

    protected function setUp(): void
    {
        parent::setUp();
        \Illuminate\Database\Eloquent\Model::unguard();

        $this->category = Category::firstOrCreate(
            ['name' => 'Makanan Utama Test']
        );

        $this->table = Table::firstOrCreate(
            ['table_number' => 999],
            ['qr_code_token' => 'qr_test_999', 'status' => 'available']
        );

        $this->menu = Menu::forceCreate([
            'category_id' => $this->category->id,
            'name' => 'Nasi Goreng Spesial Test',
            'price' => 25000,
            'stock' => 10,
            'description' => 'Nasi goreng enak'
        ]);
    }

    public function test_guest_can_create_dine_in_order_and_stock_is_reduced(): void
    {
        $payload = [
            'order_type' => 'dine_in',
            'table_number' => $this->table->table_number,
            'payment_method' => 'cash',
            'cart' => [
                [
                    'id' => $this->menu->id,
                    'qty' => 2,
                    'price' => 25000,
                    'notes' => 'Pedas'
                ]
            ]
        ];

        $response = $this->postJson(route('checkout.store'), $payload);

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);

        // Pastikan order masuk ke database
        $this->assertDatabaseHas('orders', [
            'table_id' => $this->table->id,
            'order_type' => 'dine_in',
            'payment_method' => 'cash',
            'status' => 'pending'
        ]);

        // Pastikan stok berkurang (10 - 2 = 8)
        $this->assertDatabaseHas('menus', [
            'id' => $this->menu->id,
            'stock' => 8
        ]);
    }

    public function test_order_fails_if_stock_is_insufficient(): void
    {
        $payload = [
            'order_type' => 'dine_in',
            'table_number' => $this->table->table_number,
            'payment_method' => 'cash',
            'cart' => [
                [
                    'id' => $this->menu->id,
                    'qty' => 15, // Melebihi stok (10)
                    'price' => 25000,
                    'notes' => ''
                ]
            ]
        ];

        $response = $this->postJson(route('checkout.store'), $payload);

        // Akan merespons status 500 karena Exception dilempar di OrderService
        $response->assertStatus(500)
                 ->assertJson(['success' => false]);

        // Stok tidak boleh berkurang
        $this->assertDatabaseHas('menus', [
            'id' => $this->menu->id,
            'stock' => 10
        ]);

        // Order tidak boleh tercipta karena Rollback Transaction
        $this->assertDatabaseMissing('orders', [
            'table_id' => $this->table->id,
            'order_type' => 'dine_in'
        ]);
    }
}
