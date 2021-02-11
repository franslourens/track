<?php
  class Main extends Controller{
    
    public function __construct() {
      
        parent::construct();
    }
    
    public function index()
    {
        $person = array("/persons" => array(array("/show/:person_id"),
                                            array("/save"),
                                            array("/save/:person_id"),
                                            array("/destroy/:person_id"),
                                            array("/cars/:car_id"),
                                            array("/symptoms/:symptom_id"),
                                           )
                       );
        
       $car = array("/cars" => array(array("/show/:car_id"),
                                     array("/save"),
                                     array("/save/:car_id"),
                                     array("/destroy/:car_id")
                                    )
                    );
       
       $symptoms = array("/symptoms" => array(array("/show/:symptom_id"),
                                     array("/save"),
                                     array("/save/:symptom_id"),
                                     array("/destroy/:symptom_id")
                                    )
                    );
       
        $results = array(array("/results/index"));
        
        echo json_encode(array_merge($person, $car, $symptoms, $results));
        exit;
    }
    
}