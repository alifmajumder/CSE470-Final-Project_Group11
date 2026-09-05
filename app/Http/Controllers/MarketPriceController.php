<?php

namespace App\Http\Controllers;

use App\Models\MarketPrice;
use Illuminate\Http\Request;

class MarketPriceController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'item_name' => 'required|string|max:100',
            'unit' => 'required|string|max:50',
            'price' => 'required|numeric|min:0'
        ]);

        MarketPrice::create($request->all());
        return redirect()->back()->with('success', 'Market price added successfully!');
    }

    public function destroy($id)
    {
        MarketPrice::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Market price removed!');
    }
}