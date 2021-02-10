<?php
  class Symptoms extends Controller {
    
    public function __construct() {
       parent::construct();
       $this->symptomModel = $this->model('Symptom');
    }


    public function index() {
      $this->accepts("get");
      
      $symptoms = $this->symptomModel::collection();
        
      echo json_encode($symptoms);
      exit;
    }
    
    public function show($id) {
      $this->accepts("get");
      
      $data = $this->symptomModel::retrieveByPk($id);
      $symptom = new Symptom($data);
    
      echo json_encode($symptom->serialize());
      exit;
    }

    public function save($id = null) {
        $this->accepts("post", "put");
        
        $payload = $this->payload();
        $model = $this->symptomModel;
        
        if(!$model->validate($payload))
        {
          echo json_encode(array("status" => "failed", "message" => "Please provide all parameters"));
          exit;
        }
 
        if($id) {
          $symptom = Symptom::retrieveByPk($id);
          $symptom->update($payload);
        }
        else
        {
          $symptom = new Symptom($payload);
          $symptom->save();
        }
        
        $this->respond_to("json", array("with" => json_encode($symptom->serialize())));
    }
    
    public function destroy($id) {
        $this->accepts("delete");
        
        $symptom = Symptom::retrieveByPk($id);
        
        if(!$symptom->id) {
          echo json_encode(array("status" => "failed", "message" => "Could not find symptom"));
          exit;            
        }
        
        if($symptom->delete()) {
          echo json_encode(array("status" => "ok"));
          exit;       
        }
        
        echo json_encode(array("status" => "failed", "message" => "Could not delete symptom"));
        exit;               
    }

  }