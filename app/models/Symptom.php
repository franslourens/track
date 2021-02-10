<?php
  /**
   * Symptom Model
   *
   * @author Frans Lourens <franslourens86@gmail.com>
   * @version 1.0.0
   * @package Models
   */

  class Symptom extends BaseModel {
    private $db;
    
    public $id;
    protected $description;
    public $severity;
    
    public static $symptoms = array("most_common" => array("fever","dry cough","tiredness"),
                                    "less_common" => array("aches and pains", "sore throat", "diarrhoea", "conjunctivitis", "headache", "loss of taste or smell", "a rash on skin, or discolouration of fingers or toes"),
                                    "serious" => array("difficulty breathing or shortness of breath", "chest pain or pressure", "loss of speech or movement")
                                   );
    
    const TABLE_NAME = "symptom";

    public function __construct($data = null) {
      
      parent::construct($data);
      $this->db = new Database;
      
    }
    
    public function validate($data) {
      if(!$data["description"] || !$data["severity"])
      {
        return false;
      }
      
      return true;
    }
    
    public static function collection(){
      $db = new Database;
      $db->query("SELECT * FROM symptom;");

      $results = $db->resultset();

      return $results;
    }   
  
    public static function retrieveByPk($id) {
      $db = new Database;
      
      $db->query("SELECT * FROM symptom WHERE id = :id");

      $db->bind(':id', $id);
      
      $row = $db->single();

      return new Symptom($row);
    }
    

    public function update($data){
      $this->db->query('UPDATE symptom SET severity = :severity');

      $this->db->bind(':severity', $data['severity']);

      if($this->db->execute()){
        return true;
      } else {
        return false;
      }
    }
    
    public static function populate()
    {   
        foreach(self::$symptoms as $severity => $description)
        {
            foreach($description as $value)
            {          
                $symptom = new Symptom(array("severity" => $severity, "description" => $value));
                $symptom->save();  
            }
 
        }

    }

    public function save($data = null){
        
      $this->db->query('INSERT INTO symptom (description,severity) 
      VALUES (:description, :severity)');

      $this->db->bind(':description', $data['description'] ? $data['description'] : $this->description);
      $this->db->bind(':severity', $data['severity'] ? $data['severity'] : $this->severity);
      
      if($this->db->execute()){
        return true;
      } else {
        return false;
      }
    }
    
    public function delete(){
      $this->db->query('DELETE FROM symptom WHERE id = :id');

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
                   "description" => $this->description,
                   "severity" => $this->severity
                   );
    }
  }