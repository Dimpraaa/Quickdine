<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Menu;
use App\Models\Category;
use App\Events\WaiterCalled;

class MenuController extends Controller
{
    public function index($table_number)
    {
        // Validasi Meja
        if ($table_number !== 'TA') {
            $table = \App\Models\Table::where('table_number', $table_number)->first();
            if (!$table) {
                abort(404, 'Meja tidak ditemukan atau QR Code tidak valid.');
            }
        }

        session(['current_table' => $table_number]);

        $menus = Menu::with('category')->get();
        $categories = Category::all();

        return view('menu.index', compact('menus', 'categories', 'table_number'));
    }

    public function callWaiter(Request $request)
    {
        $request->validate([
            'table_number' => 'required'
        ]);

        $table = \App\Models\Table::where('table_number', $request->table_number)->first();
        if ($table) {
            $table->update(['needs_waiter' => true]);
        }

        broadcast(new \App\Events\KitchenUpdated());

        return response()->json(['success' => true]);
    }
}
