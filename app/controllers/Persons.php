<?php
  class Persons extends Controller {
    
    public function __construct() {
      
        parent::construct();
        $this->personModel = $this->model('Person');
    }

    public function index(){
        $this->accepts("get");
        
        $persons = $this->personModel::collection();
        
        echo json_encode($persons);
        exit;
    }
    
    public function show($id){
      $this->accepts("get");
      
      $person = $this->personModel::retrieveByPk($id);
      
      echo json_encode($person->serialize());
      exit;
      
    }
 
    /**
    * cars
    * persons/cars/:car_id with payload {person_id: person_id}
    *
    *
    * this route should be in the format of persons/:person_id/cars
    * and with the car_id in the payload
    *
    * for time being a factor it will have to be refactored later to the proper url stucture
    *  
    * @param $car_id  int 
    * @return json
    **/       
    public function cars($car_id) {
        $this->accepts("post");
        
        $payload = $this->payload();
      
        try {
          $model = $this->personModel;
          $person = $this->personModel::retrieveByPk($payload["person_id"]);
          $person->addCar($car_id, date("Y-m-d H:i:s", strtotime($payload["booking_date"])));
        } catch (Exception $e) {
          echo json_encode(array("status" => "failed", "message" => $e->getMessage()));
          exit;         
        }
        
        echo json_encode($person->serialize());
        exit;
    }

    /**
    * symptoms
    * persons/symptoms/:symptom_id with payload {person_id: person_id}
    *
    *
    * this route should be in the format of persons/:person_id/symptoms
    * and with the symptom_id in the payload
    *
    * for time being a factor it will have to be refactored later to the proper url stucture
    *  
    * @param $symptom_id  int 
    * @return json
    **/       
    public function symptoms($symptom_id) {
        $this->accepts("put", "delete");
        
        $payload = $this->payload();

        if(strtolower($_SERVER["REQUEST_METHOD"]) == "delete") {
          return $this->removeSymptom($payload["person_id"], $symptom_id);
        }
        
        try {
          $model = $this->personModel;
          $person = $this->personModel::retrieveByPk($payload["person_id"]);
          $person->addSymptom($symptom_id);
        } catch (Exception $e) {
          echo json_encode(array("status" => "failed", "message" => $e->getMessage()));
          exit;         
        }
        
        echo json_encode($person->serialize());
        exit;
    }
    
    private function removeSymptom($person_id, $symptom_id) {
      try {
        $model = $this->personModel;
        $person = $this->personModel::retrieveByPk($person_id);
        $person->removeSymptom($symptom_id);
      } catch (Exception $e) {
        echo json_encode(array("status" => "failed", "message" => $e->getMessage()));
        exit;         
      }
      
      echo json_encode($person->serialize());
      exit;      
    }

    public function save($id = null) {
        $this->accepts("post", "put");
        
        $payload = $this->payload();
        $model = $this->personModel;
        
        if(!$model->validate($payload))
        {
          echo json_encode(array("status" => "failed", "message" => "Please provide all parameters"));
          exit;
        }
        
        if($id)
        {
            try{
              $person = $this->personModel::retrieveByPk($id);
              $result = $person->update($payload);
            } catch (Exception $e) {
              echo json_encode(array("status" => "failed", "message" => $e->getMessage()));
              exit;             
            }
        }
        else
        {
            $person = new Person($payload);
            
            try{
              $result = $person->save();
            } catch (Exception $e) {
              echo json_encode(array("status" => "failed", "message" => $e->getMessage()));
              exit;             
            }
        }
        
        if($result) {
          $this->respond_to("json", array("with" => json_encode($person->serialize())));
        }
    }
    
    public function destroy($id) {
        $this->accepts("delete");
      
        $person = $this->personModel::retrieveByPk($id);

        if(!$person->id) {
          echo json_encode(array("status" => "failed", "message" => "Could not find person"));
          exit;            
        }
        
        if($person->delete()) {
          echo json_encode(array("status" => "ok"));
          exit;       
        }
        
        echo json_encode(array("status" => "failed", "message" => "Could not delete person"));
        exit;               
    }

  }