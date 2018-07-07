<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use Auth;
use App\Product, App\Category;

use Log;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $userId = $user->id;
        $products = Product::get();
        $categories = Category::get();
        return view('products.index', ['nav' => 'product', 'products' => $products, 'categories' => $categories, 'uid' => $userId ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        $user = Auth::user();
        $userId = $user->id;

        // 搜索商品，根据商品名称
        $query = $request->input('query');
        $products = Product::where('name', 'like', '%' . $query . '%')->get();
        foreach($products as $product) {
            $product->category->name;
        }
        return response()->json(['products' => $products]);
    }
    
    /**
     * 保存新创建的商品
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $product = new Product;
        $product->name = $request->name;
        $product->save();
        
        return redirect()->route('products.index');
    }
}
