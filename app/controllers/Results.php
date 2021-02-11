<?php
  require_once(FRAMEWORK . "models/Person.php");
  
  class Results extends Controller{
    public function __construct(){
      parent::construct();
    }


    public function index(){
        $this->accepts("get");
        
        $results = Person::results();
        echo json_encode($results);
        exit;
    }

  }