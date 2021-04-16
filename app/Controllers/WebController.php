<?php

namespace App\Controllers;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \App\Models\{User , Charges, product , ProductCategories , Slider ,Options};
use \Carbon\Carbon;
use Illuminate\Database\Capsule\Manager as Capsule;
use \App\Helpers\Paginator;

defined('BASEPATH') OR exit('No direct script access allowed');

class WebController extends Controller {


    public function index($request,$response,$args){

        if(!isset($_SESSION['products_quantity'])){
            $_SESSION['products_quantity'] = 0;
        }
        if(!empty($_GET['q'])){
            $search = $_GET['q'];
            $products = Product::where('name','Like','%'.$search.'%')->orWhere('name','Like','%'.$search.'%')
            ->orderBy('created_at','DESC')->get()->toArray();
        }else {
           $products = Product::orderBy('created_at','DESC')->get()->toArray();
        }

        $sliders = Slider::all()->toArray();
        $ProductCategories = ProductCategories::where('active','1')->get()->toArray();
        $pinnedproducts = Product::where('show_home','on')->first();
        $products_quantity = $_SESSION['products_quantity'] ?? 0;
        $view = 'front/index.twig';
        $laterDate = (new \DateTime('tomorrow + 2 days'))->format('Y-m-d');

        return $this->view->render($response,$view,compact('products','sliders','ProductCategories','pinnedproducts','laterDate', 'products_quantity'));
    }

    public function collections_all($request,$response,$args){
        $model          = new Product();
        $count          = $model->count();         
        $page           = ($request->getParam('page', 0) > 0) ? $request->getParam('page') : 1;
        $limit          = 20; 
        $lastpage       = (ceil($count / $limit) == 0 ? 1 : ceil($count / $limit));   
        $skip           = ($page - 1) * $limit;
        $products         = $model->skip($skip)->take($limit)->orderBy('created_at', 'desc')->get();
        $urlPattern     = "?page=(:num)";
        $p = new Paginator($count, $limit, $page, $urlPattern);        

        //$products = Product::orderBy('created_at','DESC')->get()->toArray();
        $view = 'front/collections_all.twig';

        return $this->view->render($response,$view,compact('products','p'));
    }

    public function search($request,$response,$args){
        $search = $_GET['q'];
        if(!empty($search)){
            $products = Product::where('name','Like','%'.$search.'%')->orWhere('name','Like','%'.$search.'%')
            ->orderBy('created_at','DESC')->get()->toArray();
        }

        $view = 'front/search.twig';
        return $this->view->render($response,$view,compact('products','search'));
    }
    
    
    public function categories($request,$response,$args){
        
        $category = ProductCategories::where('slug',$args['slug'])->first();
        
        $current_categorie = $category->name;
        
        
        if($category) {
            $model          = new Product();
            $count          = $model->where('categoryID',$category->id)->count();         
            $page           = ($request->getParam('page', 0) > 0) ? $request->getParam('page') : 1;
            $limit          = 20; 
            $lastpage       = (ceil($count / $limit) == 0 ? 1 : ceil($count / $limit));   
            $skip           = ($page - 1) * $limit;
            $products         = $model->where('categoryID',$category->id)->skip($skip)->take($limit)->orderBy('created_at', 'desc')->get()->toArray();
            $urlPattern     = "?page=(:num)";
            $p = new Paginator($count, $limit, $page, $urlPattern);  

        }else {
            $model          = new Product();
            $count          = $model->count();         
            $page           = ($request->getParam('page', 0) > 0) ? $request->getParam('page') : 1;
            $limit          = 20; 
            $lastpage       = (ceil($count / $limit) == 0 ? 1 : ceil($count / $limit));   
            $skip           = ($page - 1) * $limit;
            $products         = $model->skip($skip)->take($limit)->orderBy('created_at', 'desc')->get()->toArray();
            $urlPattern     = "?page=(:num)";
            $p = new Paginator($count, $limit, $page, $urlPattern);        
        }
    
        $view = 'front/categories.twig';
        return $this->view->render($response,$view,compact('products','current_categorie','p'));
        
        
    }
    
    

    public function bache($request,$response,$args){
        $view = 'front/bache.twig';
        return $this->view->render($response,$view);
    }
    
    
    
    public function product($request,$response,$args){
        $id = rtrim($args['id'], '/');
        $product = Product::find($id);
        $view = 'front/product.twig';
        $laterDate = (new \DateTime('tomorrow + 2 days'))->format('Y-m-d');
       
        $current_categorie = $product->category->name ;
        $products_quantity = $_SESSION['products_quantity'] ?? 0;

        return $this->view->render($response,$view,compact('product','laterDate','current_categorie', 'products_quantity'));
    }
    
    public function thankyou($request,$response,$args){
        $request->getParams();
        $current_categorie = $request->getParam('products');
        
        $view = 'front/thankyou.twig';
        return $this->view->render($response,$view,compact('current_categorie'));
    }
    
    public function collections($request,$response,$args){
        $ProductCategories = ProductCategories::where('active','1')->get()->toArray();

        $view = 'front/collections.twig';
        return $this->view->render($response,$view,compact('ProductCategories'));
    }
    
    
    public function cuisine($request,$response,$args){
        $view = 'landing/cuisine.twig';
        return $this->view->render($response,$view);
    }
    
    public function cosmetic($request,$response,$args){
        $view = 'landing/cosmetic.twig';
        return $this->view->render($response,$view);
    }
    
    public function sport($request,$response,$args){
        $view = 'landing/sport.twig';
        return $this->view->render($response,$view);
    }
    
    public function voiture($request,$response,$args){
        $view = 'landing/voiture.twig';
        return $this->view->render($response,$view);
    }
    
    public function accessoires($request,$response,$args){
        $view = 'landing/accessoires.twig';
        return $this->view->render($response,$view);
    }
    
    public function clouthing($request,$response,$args){
        $view = 'landing/clouthing.twig';
        return $this->view->render($response,$view);
    }

    
    public function pixels($request,$response,$args){
        $options =  \App\Models\Options::all('name','value')->toArray();
        $la = [];
        foreach($options as $option){
            $la[$option['name']] = $option['value'];
        }
        $options = $la;
       //dd($options);
        $view = 'landing/pixels.twig';
        return $this->view->render($response,$view,compact('options'));
    }
    
        
        
        
    public function pixels_save($request,$response,$args){
        
       $option = new \App\Models\Options();
        
       $post = $request->getParams();

       foreach($post as $key => $value ){
            $option->update_option($key,$value);
       }
       
       
      return  $response->withRedirect($this->router->pathFor('pixels'));
    }





    public function upload($request, $response){        

        $path =  SCRIPTDIR.'public/uploads/';
        if(!isset($_FILES['excel'])){
            return 'المرجوا اضافة الملف';
        }

        //dd($path);
        
        $uploader = new \App\Helpers\Uploader2("upload");
        $file = $_FILES['excel'];        
        $uploader->file = $file;
        $uploader->path = $path;
        $name = $uploader->save();
        $ext = strtolower(last(explode('.', $file['name'])));
        $csv = $uploader->path.$name;
        
        $xlsx = \App\Helpers\SimpleXLSX::parse($csv);
        $orders = $xlsx->rows();
        unset($orders[0]);
        
        
        $alldata = [];
        foreach ($orders as $order) {
            if(array_filter($order) and $order[0] != ""){
                $thumbnail = str_replace('https://cdn.shopify.com/s/files/1/0287/0666/8637/products/','',@$order[30]);
                $thumbnail = reset(explode('?v=',$thumbnail));
            $item = [
                'name'=>@$order[1],
                'price'=>@$order[9],
                'description'=> @$order[2],
                'thumbnail'=>$thumbnail,
                'size'=> @$order[9] == 'Sans Boite' ? 'with_box' : 'no_box',
                'categoryID'=>11,
            ];
            
            $alldata[] = $item;
                }
            }        

        Capsule::table('products')->insert($alldata);
        
        dd("10 vitess");
        }




      
      
}

