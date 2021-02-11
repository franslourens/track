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
    
    protected $id;
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

      $results = $db->resultset();

      return $results;
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
    
    public function serialize()
    {
      return array("id" => $this->id ? $this->id : $this->db->lastInsertId(),
                   "make" => $this->make,
                   "model" => $this->model,
                   "color" => $this->color,
                   "licence" => $this->licence
                   );
    }
  }