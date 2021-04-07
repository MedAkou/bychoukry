<?php

namespace App\Controllers;

use App\Models\Product;
defined('BASEPATH') OR exit('No direct script access allowed');
class CartController extends Controller{

    public function index($request, $response, $args){
        $products = $_SESSION['product'];
        $colors = $_SESSION['color'];
        $sizes = $_SESSION['size'];
        $prices = $_SESSION['price'];
        $products_quantity = $_SESSION['products_quantity'] ?? 0;
    
        $product_names = Product::select('id', 'name')->whereIn('id', $products)->get();
        foreach($product_names as $product_name){
            $prod_names[$product_name['id']] =  $product_name['name'];
        }
    
        $data = [
            'products' => implode(',', $products),
            'colors' => implode(',', $colors),
            'prices' => implode(',', $prices),
        ];
    
        return $this->view->render($response,'front/cart.twig', compact('products_quantity', 'prod_names', 'products', 'colors', 'sizes', 'prices', 'data'));
    }

    public function add($request, $response, $args){
        $request = $request->getParams();
        if(!isset($_SESSION['product'][$request['product']])){
            $_SESSION['products_quantity']++;
        }
        $_SESSION['product'][$request['product']] = $request['product'];
        $_SESSION['color'][$request['product']] = $request['color'];
        $_SESSION['size'][$request['product']] = $request['size'];
        $_SESSION['price'][$request['product']] = $request['price'];
    
    
        return json_encode($_SESSION);
    }

    public function remove($request, $response, $args){
        $request = $request->getParams();
        unset($_SESSION["product"][$request['product']]);
        unset($_SESSION['color'][$request['product']]);
        unset($_SESSION['size'][$request['product']]);
        unset($_SESSION['price'][$request['product']]);
        if(!isset($_SESSION["product"][$request['product']])){
            $_SESSION['products_quantity']--;
        }
    
        return json_encode($_SESSION['products_quantity']);
    }
}