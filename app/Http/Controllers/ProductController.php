<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Product;

class ProductController extends Controller
{
    public function index(Request $request) 
    {
        $products = Product::with(['offers.city'])->paginate(20);
        return view('products', ['products' => $products]);
    }
}
