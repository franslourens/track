<?php
use PHPUnit\Framework\TestCase;

define("FRAMEWORK", "/var/www/track/public/app/");
require_once(FRAMEWORK . "config/testing.php");

final class CarTest extends TestCase
{
     public function _popular() {
        return array(array(1, 1, false), //not popular
                     array(1, 3, true), //popular
                     array(1, 5, true), //popular
                     array(1, 0, false) //not popular
                    );
    }   
 
    /**
     * @test
     * @group cartrack
     * @dataProvider _popular
     **/
    public function popular($carId, $owners, $result) {
    	$car = $this->getMockBuilder("Car")
					->setConstructorArgs(array())
					->setMethods(null)
					->getMock();
                     
        $popular = $car::popular($carId, $owners);
        
        $this->assertEquals($popular,$result);
    }
    
    public function _safe() {
        return array(array(1, false), //moving virus
                     array(3, false), //moving virus
                     array(5, false), //moving virus
                     array(0, true) //no virus
                    );
    }   
 
    /**
     * @test
     * @group cartrack
     * @dataProvider _safe
     **/
    public function is_safe($count, $result) {     
    	$car = $this->getMockBuilder("Car")
					->setConstructorArgs(array())
					->setMethods(null)
					->getMock();
                       
        $is_safe = $car::is_safe($count);
        
        $this->assertEquals($is_safe, $result);
    }    
}