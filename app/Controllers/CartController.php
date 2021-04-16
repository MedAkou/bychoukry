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
        $quantity = $_SESSION['quantity'];
        $sub_total = $_SESSION['sub_total'];
        $products_quantity = $_SESSION['products_quantity'] ?? 0;
        $total_price = 0;    

    
        if(empty($products)){
            return $this->view->render($response,'front/cart.twig');
        }

        $product_names = Product::select()->whereIn('id', $products)->get();
        foreach($product_names as $key => $product_name){
            $prod_names[$product_name['id']]['id'] =  $product_name['id'];
            $prod_names[$product_name['id']]['name'] =  $product_name['name'];
            $prod_names[$product_name['id']]['thumbnail'] =  $product_name['thumbnail'];
        }

        //dd($_SESSION['product']);

        $total_price = array_sum($sub_total);
    
        $data = [
            'products' => implode(',', $products),
            'colors' => implode(',', $colors),
            'prices' => implode(',', $prices),
        ];

    
        return $this->view->render($response,'front/cart.twig', compact('total_price','sub_total','quantity','products_quantity', 'prod_names', 'products', 'colors', 'sizes', 'prices', 'data'));
    }

    public function add($request, $response, $args){
        $request = $request->getParams();        

        if(!isset($_SESSION['product'][$request['product']])){
            $_SESSION['products_quantity']++;            
        }
        if(empty($request['products_quantity'])){
            $_SESSION['quantity'][$request['product']] = $_SESSION['quantity'][$request['product']] + 1;
        }else{
            $_SESSION['quantity'][$request['product']] = $_SESSION['quantity'][$request['product']] + $request['products_quantity'];
        }
        

        $_SESSION['product'][$request['product']] = $request['product'];
        $_SESSION['color'][$request['product']] = $request['color'];
        $_SESSION['size'][$request['product']] = $request['size'];

        if($request['box_product'] == "Avec Boite"){
            $_SESSION['box_product'][$request['product']] = 50;
        }else{
            $_SESSION['box_product'][$request['product']] = 0;
        }
        $_SESSION['price'][$request['product']] = $request['price'] + $_SESSION['box_product'][$request['product']];    
        $_SESSION['sub_total'][$request['product']] = $_SESSION['quantity'][$request['product']] * ($request['price'] + $_SESSION['box_product'][$request['product']]);

        
    
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

    public function delete($request, $response, $args){
        $id = $args['id'];
        unset($_SESSION["product"][$id]);
        unset($_SESSION['color'][$id]);
        unset($_SESSION['size'][$id]);
        unset($_SESSION['price'][$id]);
        unset($_SESSION['quantity'][$id]);
        if(!isset($_SESSION["product"][$id])){
            $_SESSION['products_quantity']--;
        }

        return $response->withRedirect($this->router->pathFor('cart'));
    }
}