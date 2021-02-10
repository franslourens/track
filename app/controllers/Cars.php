<?php
  class Cars extends Controller{
    
    public function __construct() {
      
        parent::construct();
        $this->carModel = $this->model('Car');
    }


    public function index(){
        $this->accepts("get");
        
        $persons = $this->carModel::collection();
        echo json_encode($persons);
        exit;
    }
    
    public function show($id){
      $this->accepts("get");
      
      $data = $this->carModel::retrieveByPk($id);
      echo json_encode($car->serialize());
      exit;
      
    }

    public function save($id = null) {
        $this->accepts("post", "put");
        
        $payload = $this->payload();
        $model = $this->carModel;
        
        if(!$model->validate($payload))
        {
          echo json_encode(array("status" => "failed", "message" => "Please provide all parameters"));
          exit;
        }
 
        $car = new Car($payload);
        $car->save();
        
        $this->respond_to("json", array("with" => json_encode($car->serialize())));
    }
    
     public function destroy($id) {
        $this->accepts("delete");
      
        $car = Car::retrieveByPk($id);
        
        if(!$car->id) {
          echo json_encode(array("status" => "failed", "message" => "Could not find car"));
          exit;            
        }
        
        if($car->delete()) {
          echo json_encode(array("status" => "ok"));
          exit;       
        }
        
        echo json_encode(array("status" => "failed", "message" => "Could not delete car"));
        exit;               
    }   
  }