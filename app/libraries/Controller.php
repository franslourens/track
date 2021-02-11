<?php
  class Controller {
    public function construct() {
        header('Content-Type: application/json');
    }

    public function model($model){

      require_once FRAMEWORK . 'models/' . $model . '.php';

      return new $model();
    }

    public function view($url, $data = []){

      if(file_exists(FRAMEWORK . 'views/' . $url . '.php')){

        require_once FRAMEWORK . 'views/' . $url . '.php';
      } else {

        die('View does not exist');
      }
    }

    public static $headers = array("json" => "application/json", "txt" => "aplication/text", "xml" => "text/xml", "html" => "text/html", "csv" => "application/csv");

    public function getallheaders()
    {
       $headers = [];
       
       foreach ($_SERVER as $name => $value)
       {
           if (substr($name, 0, 5) == 'HTTP_')
           {
               $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
           }
       }
       
       return $headers;
    }
  
    public function format()
    {
        $headers = $this->getallheaders();

        $formats = self::$headers;
        
        if ($headers['Content-Type'] && empty($_REQUEST["format"])) {
            foreach ($formats as $key => $type) {
                $content_type = preg_match("/(\w+\/[\w]+)/", $headers['Content-Type'], $data);
                if ($type == $data[1]) {
                    $_REQUEST["format"] = $key;
                }
            }
        }

        return $_REQUEST["format"];
    }
    
    public function respond_with($data)
    {
        echo $data;
        exit;
    }    
    
    public function respond_to($format, $data = array())
    {
      
        if ($this->format() == $format) {
            header("Content-type: " . self::$headers[$format]);
            
            if ($data["with"]) {
                $this->respond_with($data["with"]);
            }
        }
    }    

    public function payload($json = true)
    {
        $data = $this->getallheaders();
        $payload = file_get_contents('php://input');

        if (@preg_match("/x-www-form-urlencoded/", $data["Content-Type"])) {
            switch (strtolower($_SERVER['REQUEST_METHOD'])) {
                case "post":
                    return $_POST;
                    break;
                case "get":
                    return $_GET;
                    break;
                default:
                    return $_REQUEST;
                    break;
            }
        }
        $payload = file_get_contents('php://input');
        if ($json) {
            $payload = json_decode($payload, true);
        }

        return $this->payload = $payload;
    }

   /**
     * Assert
     *
     * Check to see if an object exists
     *
     * @param $data object to be assertained
     * @param $message to be displayed
     * @code $message to be displayed
     **/
    public function assert($data, $message = null, $code)
    {
        if (!empty($data)) {
            return true;
        }
        
        switch ($code) {
            case "204":
                header($_SERVER["SERVER_PROTOCOL"]." 204  No Content");
                header("Status: 204 ");
                break;

            case "402":
                header($_SERVER["SERVER_PROTOCOL"]." 402 Payment Required");
                header("Status: 402");
                break;
            case "401":
                header($_SERVER["SERVER_PROTOCOL"]." 401 Not authorized");
                header("Status: 401");
                break;
            case "406":
                header($_SERVER["SERVER_PROTOCOL"]." 406 Not Acceptable");
                header("Status: 406");
                break;

            case "403":
                header($_SERVER["SERVER_PROTOCOL"]." 403 Forbidden");
                header("Status: 403");
                break;
            case "400":
                header($_SERVER["SERVER_PROTOCOL"]." 400 Bad Request");
                header("Status: 400");
                break;
            case "500":
                header($_SERVER["SERVER_PROTOCOL"]." 500 Internal Server Error");
                header("Status: 500");
                break;
            case "415":
                header($_SERVER["SERVER_PROTOCOL"]." 415  Unsupported Media Type");
                header("Status: 415 ");
                break;
            case "410":
                header($_SERVER["SERVER_PROTOCOL"]." 410  Gone");
                header("Status: 410 ");
                break;

            case "501 ":
                header($_SERVER["SERVER_PROTOCOL"]." 501  Not Implemented");
                header("Status: 501 ");
                break;
            case "200":
                header($_SERVER["SERVER_PROTOCOL"]." 200  Created");
                header("Status: 200");
                break;
            case "201":
                header($_SERVER["SERVER_PROTOCOL"]." 201  Created");
                header("Status: 201");
                break;

            case "202":
                header($_SERVER["SERVER_PROTOCOL"]." 202  Accepted");
                header("Status: 202");
                break;

            case "206 ":
                header($_SERVER["SERVER_PROTOCOL"]." 206  Partial Content");
                header("Status: 206 ");
                break;
            default:
                header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
                header("Status: 404");
        }
        
        if ($message) {
            echo $message;
        }
        
        exit;
    }
    
    public function accepts()
    {
      $data = func_get_args();

      foreach ($data as $i => $m) {
          $data[$i] = strtolower($m);
      }
    
      if (!in_array(strtolower($_SERVER["REQUEST_METHOD"]), $data)) {
            $this->assert(false, null, 400);
      }
    }
  }