<?php 

namespace App;
use \Psr\Http\Message\ServerRequestInterface as Request;
use Carbon\Carbon;
use PHPtricks\Orm\Database;
use Noodlehaus\Config;
use Illuminate\Database\Capsule\Manager as Capsule;
use Slim\Flash\Messages as Flash;
use \App\Auth;


use \App\Models\{Options , User , SentLists , Lists , Product , Post  };
use \App\Helpers\{ Cash , Listing , Revenue , Search , Stats , Helper , Api };


defined('BASEPATH') OR exit('No direct script access allowed');

class Starter { 
    

    public function __construct($container){

                // set the session 
                $this->startSession();

                // connect to database
                $this->config($container);

                // set Local lang for time and set the time zone
                $this->setLocal();
                $this->SetTimeZone();

                // show errors
                $this->activateDebugMode();

                // Memory Limit
                $this->memorySettings();

                // connect to database
                $this->connectDB($container);
                
                // Register Twig View
                $this->registerTwigView($container);
                
                // Register Flash Messages
                $this->registerFlashMessages($container);

                // connect to database
                $this->setLang($container);

                // set 404 error page
                $this->SetNotFound($container);

                // Load the controllers 
                $this->loadControllers($container);
                
                // add all type of guard to container
                $this->safeGuard($container);

                // add assets to container
           
             $this->addAssetsToContainer($container);


             ini_set("display_errors",0);
             ini_set('log_errors', 0);
             error_reporting(0);
             @ini_set('display_errors',0);
             //error_reporting(E_ALL); 
             ini_set('display_errors', '0');


                
    }



    protected $capsule;

    public function addAssetsToContainer ($container){
        if (session_status() == PHP_SESSION_NONE) {
            return session_start();
          }

          $cart_count = @$_SESSION['products_quantity'] > 0 ? ' ('. $_SESSION['products_quantity'].') ' : '';
          
        $container['view']->getEnvironment()->addGlobal('assets', $container['conf']['url.assets']);
        $container['view']->getEnvironment()->addGlobal('config', $container['conf']['app']); 
        $container['view']->getEnvironment()->addGlobal('url', $container['conf']['url']); 
        $container['view']->getEnvironment()->addGlobal('dir', $container['conf']['dir']); 
        $container['view']->getEnvironment()->addGlobal('ALLPRODUCTS', \App\Models\Product::all()); 
        $container['view']->getEnvironment()->addGlobal('ALLCATEGORIES', \App\Models\ProductCategories::all('id','name' ,'slug')); 
        $options = (new \App\Controllers\SettingsController($container))->getOptions();
        $container['view']->getEnvironment()->addGlobal('options', $options); 
        $container['view']->getEnvironment()->addGlobal('count_cart', $cart_count ); 

    }


    public function options(){
      
    }



    public function safeGuard ($container){

            if(isset($_SESSION['auth-logged'])) {   
                $container['view']->getEnvironment()->addGlobal('auth',$_SESSION['auth-logged']);
            }


            if(isset($_SESSION['auth-admin'])) {   
                $container['view']->getEnvironment()->addGlobal('admin',$this->capsule->table('users')->find($_SESSION['auth-admin']) );
            }
            


    }

    public function memorySettings(){
        ini_set('memory_limit', '1024M');
    }



    public function SetNotFound($container){
            $container['notFoundHandler'] = function ($container) {
                return function ($request, $response) use ($container) {
                    global $container;
                    echo " 404";
                    exit;
                    return $response->withHeader('Location', '/');
                };
            };
    }


	public function connectDB($container){
		
           // Connect To DataBase
            $capsule = new Capsule;
              
            $capsule->addConnection([
                'driver'    => $container['conf']['db_live.driver'],
                'host'      => $container['conf']['db_live.host'],
                'database'  => $container['conf']['db_live.name'],
                'username'  => $container['conf']['db_live.username'],
                'password'  => $container['conf']['db_live.password'],
                'charset'   => $container['conf']['db_live.charset'],
                'collation' => $container['conf']['db_live.collation'],
                'prefix'    => '',
                'strict' => false
            ]);
             
                  
                   // Make this Capsule instance available globally via static methods... (optional)
                    $capsule->setAsGlobal();

                    // Setup the Eloquent ORM... (optional; unless you've used setEventDispatcher())
                    $capsule->bootEloquent();

                    $this->capsule = $capsule;

                try {
                    Capsule::connection()->getPdo();
                } catch (\Exception $e) {
                    die("Could not connect to the database.  Please check your configuration. "  );
                }


	}

    public function setLang($container){
        $file = BASEPATH.'/app/lang/admin/ar.php';
        $container['view']->getEnvironment()->addGlobal('l', Config::load($file));
        $_SESSION['l'] = include ($file);
    }


    public function config($container){
                // Get All the settings Frpm Config File
       return $container['conf'] = function () {
            return Config::load(INC_ROOT.'/app/config.php');
        };

    }


    public function activateDebugMode(){
       return Helper::setDevelepment();
    }

	public function startSession(){
        if (session_status() == PHP_SESSION_NONE) {
          return session_start();
        }
	}

	public function setLocal(){
          return  Carbon::setLocale('ar');
	}

	public function SetTimeZone(){
           return  date_default_timezone_set('Africa/Casablanca');
	}

    public function loadControllers($container){
       return  Helper::setController($container);
    }



    // Register Twig View helper
    public function registerFlashMessages($container){
        // Register Flash Messages
        $container['flash'] = function ($container) {
            return new \Slim\Flash\Messages();
        };
    }
    // Register Twig View helper
    public function registerTwigView($container){

            // Register Twig View helper
            $container['view'] = function ($c) {
                $view = new \Slim\Views\Twig('../app/Views', [
                   // 'cache' => false,
                ]);
                
                // Instantiate and add Slim specific extension
                $router = $c->get('router');
                $uri = \Slim\Http\Uri::createFromEnvironment(new \Slim\Http\Environment($_SERVER));
                $view->addExtension(new \Slim\Views\TwigExtension($router, $uri));
                    
                
                $view->addExtension(new \Knlv\Slim\Views\TwigMessages(
                new \Slim\Flash\Messages()
                ));
                $view->getEnvironment()->addglobal('flash',$c->flash);
                
            



                
                $filter = new \Twig_SimpleFilter('video_framer', function ($vide_link) {
                  //  return $vide_link;
                        return  (new \App\Helpers\Video($vide_link))->render_embed();
                });
                $view->getEnvironment()->addFilter($filter);
                   



                
                $filter = new \Twig_SimpleFilter('dateOnly', function ($username) {
                    $date = date('Y-m-d', strtotime($username));
                    return $date;
                });
                $view->getEnvironment()->addFilter($filter);
                   
		   
                                

                $filter = new \Twig_SimpleFilter('navAvatar', function ($gender) {
                });
                $view->getEnvironment()->addFilter($filter);

                $filter = new \Twig_SimpleFilter('st', function ($username) {
                    return st($username);
                });
                $view->getEnvironment()->addFilter($filter);
                
                return $view;
            };

    }
       


  

}




