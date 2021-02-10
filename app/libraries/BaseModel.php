<?php
  abstract class BaseModel implements iModel {
    
    public function construct($data = null){
        
      if($data)
      {
        foreach($data as $key => $value) {
          $this->{$key} = $value;
        }
      }
    }
    
  }