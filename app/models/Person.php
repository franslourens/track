<?php
  /**
   * Person Model
   *
   * @author Frans Lourens <franslourens86@gmail.com>
   * @version 1.0.0
   * @package Models
   */

  require_once(FRAMEWORK . "models/Car.php");
  require_once(FRAMEWORK . "models/Symptom.php");
  
  class Person extends BaseModel {
    private $db;
    
    public $id;
    
    protected $name;
    protected $surname;
    protected $phone;

    public function __construct($data = null){
      
      parent::construct($data);
      
      $this->db = new Database;
    }
    
    public function validate($data) {
      
      if(!$data["name"] || !$data["surname"] || !$data["phone"])
      {
        return false;
      }
      
      return true;
    }
    
    public static function collection(){
      $db = new Database;
      $db->query("SELECT * FROM person;");

      $results = $db->resultset();

      return $results;
    }
    
    public function symptoms() {
      $db = new Database;
      
      $db->query("SELECT * FROM person_symptom WHERE person_id = :person_id");
      $db->bind(':person_id', $this->id);
      
      $rows = $db->resultset();

      $data = array();
      
      foreach($rows as $row)
      {
        $symptom = Symptom::retrieveByPk($row->symptom_id);
        $data[] = $symptom->serialize();
      }
      
      return $data; 
    }
    
    public function cars() {
      $db = new Database;
      
      $db->query("SELECT * FROM person_car WHERE person_id = :person_id");
      $db->bind(':person_id', $this->id);

      $rows = $db->resultset();

      $data = array();
      
      foreach($rows as $row)
      {
        $car = Car::retrieveByPk($row->car_id);
        $data[] = $car->serialize();
      }
      
      return $data; 
    }
    
    public static function results()
    {
      $db = new Database;
      
      $db->query("SELECT * FROM person_symptom JOIN person ON person_symptom.person_id = person.id JOIN person_car ON person.id = person_car.person_id;");

      $results = $db->resultset();
      
      $data = array();
      
      foreach($results as $result) {
        $person = Person::retrieveByPk($result->person_id);
        $data[] = $person->serialize();
      }

      return $data;
    }
  
    public static function retrieveByPk($id) {
      $db = new Database;
      
      $db->query("SELECT * FROM person WHERE id = :id");

      $db->bind(':id', $id);
      
      $row = $db->single();

      return new Person($row);
    }
    
   /**
    * canBook
    * 
    * validates the booking date that its not in the past or current day
    * 
    * @param booking_date String eg '2021-02-10'
    * @return bool
    **/    
    public function canBook($booking_date) {
      $date = new DateTime($booking_date);
      $now = new DateTime();
      
      if($date == $now) {
        return false;
      }
 
      if($date < $now) {
        return false;
      }
      
      if($now->format('d') == $date->format('d')) {
          return false; 
      }
      
      return true;
    }
    
    public function addCar($id, $booking_date)
    {
      if(!$this->canBook($booking_date)) {
        throw new InvalidBookingDateException("Booking Date must be in the future");
      }
      
      $this->db->query('INSERT INTO person_car (person_id,car_id,booking_date) VALUES (:person_id, :car_id, :booking_date)');
  
      $this->db->bind(':person_id', $this->id);
      $this->db->bind(':car_id', $id);
      $this->db->bind(':booking_date', $booking_date);
      
      if($this->db->execute()){
        return true;
      } else {
        return false;
      }         
    }
    
    public function removeCar($carId)
    {   
      $this->db->query('DELETE FROM person_car WHERE person_id = :person_id AND car_id = :car_id');

      $this->db->bind(':person_id', $this->id);
      $this->db->bind(':car_id', $carId);

      if($this->db->execute()){
        return true;
      } else {
        return false;
      }
    }
    
    public function removeSymptom($symptomId)
    {
      $this->db->query('DELETE FROM person_symptom WHERE person_id = :person_id AND symptom_id = :symptom_id');

      $this->db->bind(':person_id', $this->id);
      $this->db->bind(':symptom_id', $symptomId);

      if($this->db->execute()){
        return true;
      } else {
        return false;
      }      
    }

  /**
    * verifySymptom
    * 
    * verifies if the symptom added to user has the same severity group
    * 
    * @param $severity String eg 'most_common'
    * @return bool
    **/    
    public function verifySymptom($severity) {
        $symptoms = $this->symptoms();

        foreach ($symptoms as $symptom)
        {
          if($symptom["severity"] != $severity) {
            return false;
          }
        }
        
        return true;
    }
    
    public function addSymptom($symptomId)
    {
      $symptom = Symptom::retrieveByPk($symptomId);

      if(!$symptom->severity) {
        throw new BadMethodCallException("No symtom found");
      }
      
      if(!$this->verifySymptom($symptom->severity)) {
        throw new InvalidSymptomException("Cannot add a symptom with different severity");
      }
      
      $this->db->query('INSERT INTO person_symptom (person_id,symptom_id,showing) VALUES (:person_id, :symptom_id, :showing)');
  
      $this->db->bind(':person_id', $this->id);
      $this->db->bind(':symptom_id', $symptom->id);
      $this->db->bind(':showing', true);
      
      if($this->db->execute()){
        return true;
      } else {
        return false;
      }      
    }
    
    public function update($data){
      $this->db->query('UPDATE person SET phone = :phone  WHERE id = :id');

      $this->db->bind(':phone', $data['phone']);
      $this->db->bind(':id', $this->id);
      
      if($this->db->execute()){
        $this->phone = $data['phone'];
        return true;
      } else {
        return false;
      }
    }    

    public function save($data = null){
      
      $this->db->query('INSERT INTO person (name,surname,phone,created_at) 
      VALUES (:name, :surname, :phone, :created_at)');

      $this->db->bind(':name', $data['name'] ? $data['name'] : $this->name);
      $this->db->bind(':surname', $data['surname'] ? $data['surname'] : $this->surname);
      $this->db->bind(':phone', $data['phone'] ? $data['phone'] : $this->phone);
      $this->db->bind(':created_at', date("Y-m-d H:i:s"));
      
      if($this->db->execute()){
        return true;
      } else {
        return false;
      }
    }
    
    public function delete() {
      $this->db->query('DELETE FROM person WHERE id = :id');

      $this->db->bind(':id', $this->id);
      
      if($this->db->execute()){
        return true;
      } else {
        return false;
      }
    }
    
    public function serialize()
    {
      return array("id" =>  $this->id ? $this->id : $this->db->lastInsertId(),
                   "name" => $this->name,
                   "surname" => $this->surname,
                   "phone" => $this->phone,
                   "symptoms" => $this->symptoms(),
                   "car" => $this->cars()
                   );
    }
  }
  
class InvalidBookingDateException extends Exception{}
class InvalidSymptomException extends Exception{}