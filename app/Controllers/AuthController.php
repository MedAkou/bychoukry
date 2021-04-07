<?php

namespace App\Controllers;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \App\Models\User;

defined('BASEPATH') OR exit('No direct script access allowed');

class AuthController extends Controller {
    
    public function getLogin($request,$response) {
        if(isset($_SESSION['auth-user'])){
            return $response->withRedirect($this->container->router->pathFor('website.home'));
        }
        return $this->container->view->render($response,'admin/admin/login.twig');
    } 
    
     
    public function attempt($email,$password) {

        $user = User::where('username',$email)->orwhere('email',$email)->orwhere('phone',$email)->first();
     
        if($user->password  === $password){
             $type = $user->role;
            if($type == 'admin'){
                $_SESSION['auth-admin'] = $user->id;
                $_SESSION['auth-user'] = $user->id;
                $_SESSION['auth-logged']  = $user;
                return $user;
            }
        }
       
         return false; 
    }
    


    public function login($request,$response) {
        
        $post = $request->getParams();
        
        // get the login credentials
        $user = $post['user_login'];
        $pass = $post['pass_login'];
          
        // admin login
        $auth = $this->attempt($user,$pass);
        
        if($auth) {
            $type = $auth->role;
            if($type == 'admin'){
                return $response->withRedirect($this->container->router->pathFor('admin.index'));
            }
        }else {
            $this->flasherror('المعلومات غير صحيحة');
            return $response->withRedirect($this->container->router->pathFor('admin.index'));
        }
    }
    
    public function logout($request,$response) {

        session_start();

     
        unset($_SESSION['auth-admin']);
       
        
        //clear session from globals
        $_SESSION = [];
        
        //clear session from disk
        session_destroy();
            
        return $response->withRedirect($this->container->router->pathFor('admin.index'));
    }
 
 
    
    
  
   
  
    

    
}

