<?php

namespace App\Controllers;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \App\Models\{User , Charges , Options};
use \Carbon\Carbon;
use Illuminate\Database\Capsule\Manager as Capsule;

defined('BASEPATH') OR exit('No direct script access allowed');

class SettingsController extends Controller {



    
    public function getOptions(){
        
      $whereData = ['contact_icon','messenger_link','contact_icon','phone_number','phone','email','tagline','name','pixel','stats','logo','color','banner_footer','whatsapp','whatsapp_number','footer_img','fb_link','twitter_link','youtube_link','instagram_link','show_home_video','home_video_h1','home_video_h3','home_video_link'];
      
      $options = [];
      
      foreach($whereData as $item ){
        $result = Options::where('name',$item)->select('value')->first();    
        if($result) {
            $options[$item] =  $result->value;
        }
      }
      
      return $options;
    }
    
    
    
    public function index($request,$response,$args){
        
        $id = $_SESSION['auth-logged']->id;
        $email = User::find($id)->email;   
        
        $options = $this->getOptions();
        return $this->view->render($response, 'admin/admin/settings.twig', compact('options','email')); 
    }


    public function profile($request,$response,$args){
        
        $post = $request->getParams();

        $id = $_SESSION['auth-logged']->id;
        $user = User::find($id);    
            
        if(isset($post['email']) and !empty($post['email'])){
            $user->email =  $post['email'];
        }
                  
        if(isset($post['password']) and !empty($post['password'])){
            $user->password =  $post['password'];
        }
         
        $user->save();
        
        return $response->withStatus(302)->withHeader('Location', $this->router->pathFor('settings.index'));
    }
    
    
    public function update($request,$response,$args){
        
        $params = $request->getParams();
        
        $settings  = new Options();
        
        if(!isset($params['whatsapp'])){
           $params['whatsapp'] = "off";
        }
        
        if(isset($params['settings_form'])) {
            if(!isset($params['footer_img'])){
               $params['footer_img'] = "off";
            }
        }

        if(isset($params['video_form'])) {
            if(!isset($params['show_home_video'])){
               $params['show_home_video'] = "off";
            }
        }




     

        if(isset($params['contact_form'])) {
            if(!isset($params['contact_icon'])){
               $params['contact_icon'] = "off";
            }     
        }   
         
        
        
        if ($_FILES['logo']['size'] != 0 && $_FILES['logo']['error'] == 0) {
             $uploader = new \App\Helpers\Uploader("upload");
             $file = $_FILES['logo'];
             $uploader->file = $file;
             $uploader->path = $this->dir('media');
             $name = $uploader->save();
             $params['logo'] = $name;
        }
        

        if ($_FILES['banner_footer']['size'] != 0 && $_FILES['banner_footer']['error'] == 0) {
             $uploader = new \App\Helpers\Uploader("upload");
             $file = $_FILES['banner_footer'];
             $uploader->file = $file;
             $uploader->path = $this->dir('media');
             $name = $uploader->save();
             $params['banner_footer'] = $name;
        }

        foreach($params as $key => $value ){
            $settings->update_option($key,$value);
        }
        return $response->withStatus(302)->withHeader('Location', $this->router->pathFor('settings.index'));
    }
      

    



}
