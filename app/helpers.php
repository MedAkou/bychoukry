<?php




function Deliver(){
    return  $_SESSION['auth-deliver'] ?? false;
}

function Employee(){
   return  $_SESSION['auth-employee'] ?? false; 
}







function auth(){
    return $_SESSION['auth-logged'];
}






  /*
  *    Debug
  */
  function st($string){
      if($string){
          echo '<pre>';
          print_r($string);
          echo '</pre>';
      }
      return ' ';
  }
  



  function getColor($number){
    if($number < 20 ) {
      return 'danger';
    }
    if($number < 50 and $number > 20 ){
      return 'primary';
    }
    if($number < 70 and $number > 50 ){
      return 'info';
    }
    if($number > 70 ){
      return 'success';
    }
  }



  /*
  *    Debug
  */
  function sv($string){
      st($string);
      exit;
  }

  function dd($string){
    return sv($string);
  }




  /*
  * Clean POST data
  */
  function clean($post) {
    $clean = [];
    if(is_array($post)){
      
      foreach ($post as $key => $value):
        $clean[$key] = clean($value);
      endforeach;
      return $clean;
    }else{
      return safe($post);
    }
  }

   
    
    
    
    /*
    *    Clean the Inputs
    */
    function safe($data) {
        // Strip HTML Tags
        $clear = strip_tags($data);
        // Clean up things like &amp;
        $clear = html_entity_decode($clear);
        // Strip out any url-encoded stuff
        $clear = urldecode($clear);
        // Replace Multiple spaces with single space
        $clear = preg_replace('/ +/', ' ', $clear);
        // Trim the string of leading/trailing space
        $clear = trim($clear);
        return $clear;
    }
    

  