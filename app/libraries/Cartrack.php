<?php
  class Cartrack {
    protected $currentController = 'Main';
    protected $currentMethod = 'index';
    protected $params = [];

    public function __construct(){
      $url = $this->getUrl();

      if(file_exists(FRAMEWORK . 'controllers/'.ucwords($url[0]).'.php')){
        $this->currentController = ucwords($url[0]);
        unset($url[0]);
      } else {
        echo json_encode(array("status" => "failed", "message" => "No endpoint found"));
        exit;     
      }

      require_once(FRAMEWORK . 'controllers/' . $this->currentController . '.php');

      $this->currentController = new $this->currentController;
      
      $url[1] = @explode('.', $url[1])[0];

  
      if(isset($url[1])){
        
        if(method_exists($this->currentController, $url[1])){
          $this->currentMethod = $url[1];
          unset($url[1]);
        }
        
        if(!is_callable(array($this->currentController, $this->currentMethod))){
          echo json_encode(array("status" => "failed", "message" => "No method found"));
          exit;     
        }
      }

      $this->params = $url ? array_values($url) : [];
      
      call_user_func_array([$this->currentController, $this->currentMethod], $this->params);
    }

    public function getUrl(){
        if(isset($_GET['url'])) {
          
          $url = rtrim($_GET['url'], '/');
          $url = filter_var($url, FILTER_SANITIZE_URL);
          $url = explode('/', $url);
          
          return $url;
        }
    }
  }