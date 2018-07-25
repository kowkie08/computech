<?php

namespace App\Http\Controllers;

use App\Cart;
use Session;
use Illuminate\Http\Request;
use App\Product;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\DB;
use Validator;

class ProductController extends Controller
{
    public function getProducts()
    {
//    	$products = Product::all()->where('Status' , 1);
        $products = DB::table('products')
            ->join('suppliers', 'products.supplierID', '=', 'suppliers.id')
            ->select('products.*', 'suppliers.name AS supplier')
            ->get();

        return view("product")->with('products', $products);
    }

    public function getUserProducts()
    {
//    	$products = Product::all()->where('Status' , 1);
        $products = DB::table('products')
            ->join('suppliers', 'products.supplierID', '=', 'suppliers.id')
            ->select('products.*', 'suppliers.name AS supplier')
            ->get();

        return view("user_product", ['products' => $products]);
    }

    public function getProductById($id)
    {
        $products = Product::all()->where('id', $id);

        return json_encode($products);
    }

    public function edit(Request $request)
    {

        $product = Product::all()->where('id', $request->id);
        $product->supplierID = $request->supplierID;
        $product->name = $request->name;
        $product->category = $request->category;
        $product->brand_name = $request->brand_name;
        $product->description = $request->description;
        $product->isHot = $request->isHot;
        $product->quantity = $request->quantity;
        $product->image = $request->image;
        $product->price = $request->price;

        if ($product->save()) {

        } else {

        }

    }

    public function archive(Request $request)
    {
        $product = Product::all()->where('id', $id);
        $product->Status = "0";
        if ($product->save()) {

        } else {

        }
    }

    public function add(Request $request)
    {

        $data = $request->all();
        $product = new Product($data);
        $product->Status = 1;
        $code1 = str_random(10);
        if ($request->hasFile('image')) {
            $request->file('image');

            $request->image->storeAs('public', $code1 . ".jpeg");

            $product->image = $code1;
        }

        if ($product->save()) {
            return Redirect::to('/product');

        } else {

        }
    }

    public function getAddToCart(Request $request, $id)
    {
        $product = Product::find($id);
        $oldCart = Session::has('cart') ? Session::get('cart') : null;
        $cart = new Cart($oldCart);
        $cart->add($product, $product->id);
        $request->session()->put('cart', $cart);
        return redirect()->route('product.index');
    }

    public function getCart()
    {
        if (!Session::has('cart')) {
            return view('cart');
        }
        $oldCart = Session::get('cart');
        $cart = new Cart($oldCart);
        return view('cart', ['products' => $cart->items, 'totalPrice' => $cart->totalPrice]);
    }

    public function getCheckout()
    {
        if (!Session::has('cart')) {
            return view('cart');
        }
        $oldCart = Session::get('cart');
        $cart = new Cart($oldCart);
        $total = $cart->totalPrice;
        return view('checkout', ['total' => $total]);
    }

    public function postCheckout(Request $request)
    {
        if (!Session::has('cart')) {
            return redirect()->route('shop.shoppingCart');
        }
        $oldCart = Session::get('cart');
        $cart = new Cart($oldCart);
        $order = new Order();
    }
}
