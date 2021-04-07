<?php

namespace App\Controllers;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

// Models
use \App\Models\{ Options , Lists , Product , DailyStock , Cities , StockGeneral , NewOrders  };
use \App\Models\{ User , Stock , MultiSale, Sources , StockEntree  , StockSortie  , StockSortieList , HistoryEntree };


// Classes And Libraries
use \App\Classes\{files , SystemLog};
use \App\Classes\Noanswser as ff;


defined('BASEPATH') OR exit('No direct script access allowed');

class DataController extends Controller {
    
   
    public function index($request,$response) {

        $type = $request->getParam('type') ?? 'waiting';
        
        $types = [
            'waiting' => NULL,
            'canceled' => 'ملغاة' ,
            'recall' => 'اعادة الإتصال' ,
            'livred'   => 'تم توزيعها' ,
            'unanswred'   => 'لا يجيب' ,
            'accepted'   => 'تم تأكيدها' ,
            'sent'   => 'تم ارسالها' ,
        ];

        $unanswred  = NewOrders::where('statue','تم توزيعها')->count();
        $canceled   = NewOrders::where('statue','ملغاة')->count();
        $recall  = NewOrders::where('statue','تم تأكيدها')->count();
        $new  = NewOrders::whereNull('statue')->count();
    
        $lists  = NewOrders::where('statue',$types[$type])->orderBy('id','DESC')->get();
       
        if(is_null($type)) {
            $type = 'waiting';
        }
        
       $twig = 'admin/admin/data.twig';
       return $this->view->render($response, $twig,  compact('view','lists','type','unanswred','canceled','recall','new') );  
    }
    


    public function load($request,$response,$args) {
       $id    = $_POST['id'];
       $list  = NewOrders::find($id)->toArray();
       $twig = 'admin/elements/table.twig';
       return $this->view->render($response, $twig,  compact('list') ); 
    }    
        
    
    public function create($request,$response,$args) {
        $post = $request->getParams();
        $list  = new NewOrders();
        $list->name = $post['name'];
        $list->tel = $post['tel'];	
        $list->adress = $post['adress'];
        $list->city = $post['city'];
        $list->quantity = $post['quantity'];
        $list->productID = $post['product'];
        $list->color = $post['color'];
        $list->size = $post['size'];
        $list->price = $post['price'];
        $list->save();
        return $response->withRedirect($this->router->pathFor('admin.index'));   
     }   
    
    

    public function update($request,$response,$args) {
       $post = $request->getParams();
       $list  = NewOrders::find($post['id']);
       $list->name = $post['name'];
       $list->tel = $post['tel'];	
       $list->adress = $post['adress'];
       $list->city = $post['city'];
       $list->quantity = $post['quantity'];
       $list->productID = $post['product'];
       $list->color = $post['color'];
       $list->size = $post['size'];
       $list->price = $post['price'];
       $list->cityID = $post['cityID'];
       $list->save();
       return $response->withRedirect($this->router->pathFor('admin.index'));   
    }   
    

  

    
    

}
