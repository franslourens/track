<?php
  /**
   * Car Model
   *
   * @author Frans Lourens <franslourens86@gmail.com>
   * @version 1.0.0
   * @package Models
   */
  
  class Car extends BaseModel {
    private $db;
    
    public $id;
    protected $make;
    protected $model;
    protected $color;
    protected $licence;

    public function __construct($data = null){
        
      parent::construct($data);
      $this->db = new Database;
    }
    
    public function validate($data) {
      
      if(!$data["make"] || !$data["model"] || !$data["color"] || !$data["licence"]) {
        return false;
      }
      
      return true;
    }
    
    public static function collection(){
      $db = new Database;
      $db->query("SELECT * FROM car;");

      $data = array();

      $results = $db->resultset();
      
      foreach($results as $result) {
        
        $symptoms = Car::symptoms($result->id);
        
        $result->popular = Car::popular($result->id);
        $result->cases = $symptoms;
        $result->safe = Car::is_safe($symptoms);
        
        $data[] = $result;
      }

      return $data;
    }   
  
    public static function retrieveByPk($id) {
      $db = new Database;
      
      $db->query("SELECT * FROM car WHERE id = :id");

      $db->bind(':id', $id);
      
      $row = $db->single();

      return new Car($row);
    }

    public function save($data = null) {
        
      $this->db->query('INSERT INTO car (make,model,color,licence) 
      VALUES (:make, :model, :color, :licence)');

      $this->db->bind(':make', $data['make'] ? $data['make'] : $this->make);
      $this->db->bind(':model', $data['model'] ? $data['model'] : $this->model);
      $this->db->bind(':color', $data['color'] ? $data['color'] : $this->color);
      $this->db->bind(':licence', $data['licence'] ? $data['licence'] : $this->licence);
      
      if($this->db->execute()){
        return true;
      } else {
        return false;
      }
    }
    
    public function delete(){
      $this->db->query('DELETE FROM car WHERE id = :id');

      $this->db->bind(':id', $this->id);
      
      if($this->db->execute()){
        return true;
      } else {
        return false;
      }
    }
    
    public static function owners($id) {
      $db = new Database;
      
      $db->query("SELECT * FROM person_car WHERE car_id = :car_id");

      $db->bind(':car_id', $id);
      
      return count($db->resultset()); 
    }
    
    public static function popular($id) {
      $owners = Car::owners($id);

      if($owners > 1) {
        return true;
      }
      
      return false;
    }
    
    public static function ownersPerMonth($id) {
      $db = new Database;
      
      $start_date = date("Y-m-01");
      $end_date = date("Y-m-t");
      
      $db->query("SELECT * FROM person_car WHERE car_id = :car_id and booking_date BETWEEN '{$start_date}' AND '{$end_date}' ");

      $db->bind(':car_id', $id);
      
      return $db->resultset();       
    }
    
    public static function person($id) {
      return Person::retrieveByPk($id);
    }
    
    public static function symptoms($id) {
      $ownersPerMonth = Car::ownersPerMonth($id);
      
      $data = array();
      
      foreach($ownersPerMonth as $owner) {
        $person = Car::person($owner->person_id);
        $data[$id] = count($person->symptoms());
      }
      
      return count(array_unique($data));      
    }
    
    public static function is_safe($symptoms) {
      
      if($symptoms >= 1) {
        return false;
      }
      
      return true;
    }
    
    public function serialize()
    {
      $symptoms = Car::symptoms($this->id);
      
      return array("id" => $this->id ? $this->id : $this->db->lastInsertId(),
                   "make" => $this->make,
                   "model" => $this->model,
                   "color" => $this->color,
                   "licence" => $this->licence,
                   "popular" => $this->popular($this->id),
                   "cases" => $symptoms,
                   "safe" => $this->is_safe($symptoms)
                   );
    }
  }