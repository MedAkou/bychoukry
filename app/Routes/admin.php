<?php

// make namespace short
use \App\Controllers\AuthController as auth;
use \App\Middleware\flashMiddleware as flash;
use \App\Middleware\OldInputMidddleware as old;
use \App\Middleware\logoutMiddleware as logout;
use \App\Controllers\ApiController as Api;
use \App\Controllers\SettingsController as settings;
use \App\Controllers\CartController as cart;
use \App\Controllers\SliderController as slider;
use \App\Controllers\ProductsCategoriesController as productscats;
use Illuminate\Database\Capsule\Manager as Capsule;

// security , disable direct access
defined('BASEPATH') or exit('No direct script access allowed');






$app->get('[/]', Web::class.':index')->setName('website.index');
$app->get('/product/{id}', Web::class.':product')->setName('website.product');
$app->get('/thank-you', Web::class.':thankyou')->setName('website.thankyou');
$app->get('/categories/{slug}', Web::class.':categories')->setName('website.categories');
$app->get('/collections[/]', Web::class.':collections')->setName('website.collections');
$app->get('/collections/all[/]', Web::class.':collections_all')->setName('website.collections_all');
$app->get('/search', Web::class.':search')->setName('website.search');
$app->post('/upload', Web::class.':upload')->setName('website.upload');




$app->post('/login[/]', auth::class .':login')->setName('login');
$app->get('/logout[/]', auth::class .':logout')->setName('logout')->add( new logout($container) );





$app->group('/admin', function ($container) use($app) {

    // Dashboard index
    $this->get('[/]','Data:index')->setName('admin.index')->add( new App\Middleware\adminMiddleware($container));
   
   
    $this->get('/change/statue','Data:index')->setName('admin.index')->add( new App\Middleware\adminMiddleware($container));
    $this->post('/load/list','Data:load')->setName('admin.load');
    $this->post('/list/update','Data:update')->setName('admin.update');
    $this->post('/list/create','Data:create')->setName('admin.create');
    $this->post('/export/excel','ExcelExporter:exportData')->setName('admin.update');


    $this->post('/remove/item',function($request){
        $id = $_POST['id'];
        $list = \App\Models\NewOrders::find($id);
        $list->delete();
    });

    $this->post('/change/statue',function(){
        $id = $_POST['id'];
        $statue = trim($_POST['statue']);
        $list = \App\Models\NewOrders::find($id);
        $list->statue = $statue;
        $list->save();
    });

  
    // Slider System
    $this->group('/slider', function (){
       $this->any('[/]', slider::class .':index')->setName('slider');
       $this->any('/create', slider::class .':create')->setName('slider.create');
       $this->any('/edit/{id}[/]', slider::class .':edit')->setName('slider.edit');
       $this->get('/delete/{id}[/]', slider::class .':delete')->setName('slider.delete');
       $this->any('/beside-slider[/]', settings::class .':slider')->setName('beside-slider');
    });
 
    // products cateogies
    $this->group('/categories', function (){
        $this->any('[/]', productscats::class .':index')->setName('products.categories');
        $this->any('/edit/{id}[/]', productscats::class .':edit')->setName('products.categories.edit');
        $this->get('/delete/{id}[/]', productscats::class .':delete')->setName('products.categories.delete');
    });


    // new orders system
    $this->get('/data', 'Data:index')->setName('data');
    $this->get('/settings', settings::class.':index')->setName('settings.index');
    $this->post('/settings', settings::class.':update')->setName('settings.update');
    $this->post('/profile', settings::class.':profile')->setName('settings.profile');


    // Products system
    $this->group('/products', function (){
        $this->get('[/]', 'Products:index')->setName('products');
        $this->any('/create[/]', 'Products:create')->setName('products.create');
        $this->any('/edit/{id}[/]', 'Products:edit')->setName('products.edit');
        $this->get('/delete/{id}[/]', 'Products:delete')->setName('products.delete');
        $this->get('/duplicate/{id}[/]', 'Products:duplicate')->setName('products.duplicate');
        $this->get('/blukdelete[/]', 'Products:blukdelete')->setName('products.blukdelete');
    });

 
    
})->add( new App\Middleware\authMiddleware($container) );


$app->post('/storeApi[/]', function ($request, $response, $args) { 


    $source =  $_POST['casa'];
    if(isset($_POST['db'])){
        $source = $_POST['casa_ola_bra_casa'];
    }

    $NewOrders = [
        'name'  =>  $_POST['fullname'] ,
        'tel'  =>  $_POST['phone'] ,
        'adress'  =>  $_POST['address'] ,
        'city'  =>  $_POST['city'] ,
        'price' =>  '',
        'source' => $source,
        'color' => $_POST['color'],
        'size' =>  $_POST['size'],
    ];

    $order = \App\Models\NewOrders::create($NewOrders);



    $source =  $_POST['casa'];
    if(!isset($_POST['db'])){
     //   dd('ldldld')
        $products = $_SESSION['product'];
        //$products = array_keys($products);
        foreach($products as $key => $product){
            $OrderDetails[] = [
                'order_id'  =>  $order['id'] ,
                'product_id'  =>  $product ,
                'quantity' => $_SESSION['quantity'][$key],
                'price' => $_SESSION['sub_total'][$key],
                'box' => $_SESSION['box_product'][$key] == 50 ? "Avec Boite" : "Sans Boite",
            ];
        }
    }else {
        $OrderDetails[] = [
            'order_id'  =>  $order['id'] ,
            'product_id'  => $_POST['product_id'] ,
            'quantity' => $_POST['product_quantity'] ,
            'price' => $_POST['price'],
            'box' => "Sans Boite",
        ];
    }
    
    //dd($OrderDetails);

    
    Capsule::table('orderdetails')->insert($OrderDetails);
    //\App\Models\OrderDetails::create($OrderDetails);

    unset($_SESSION["product"]);
    unset($_SESSION["color"]);
    unset($_SESSION["size"]);
    unset($_SESSION["price"]);
    unset($_SESSION['products_quantity']);
    unset($_SESSION['quantity']);
    unset($_SESSION['sub_total']);
    
});

$app->get('/cart[/]', cart::class.':index')->setName('cart');
$app->post('/addToCart[/]', cart::class.':add');
$app->post('/removeFromCart[/]', cart::class.':remove');
$app->get('/cart/delete/{id}', cart::class.':delete')->setName('cart.delete');

//   Middlewares
$app->add( new flash($container) );
$app->add( new old($container) );



