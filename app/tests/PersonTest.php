<?php
use PHPUnit\Framework\TestCase;

define("FRAMEWORK", "/vagrant/public/app/");
require_once(FRAMEWORK . "config/testing.php");

final class PersonTest extends TestCase
{
    public function _booking_dates() {
        return array(array(date("Y-01-01 H:i:s"), false), //past
                     array(date("Y-02-01 H:i:s"), false), //past
                     array(date("Y-m-d H:i:s"), false) , //today
                     array(date("Y-m-d H:i:s", strtotime('+1 hour')), true), //future
                     array(date("Y-m-d H:i:s", strtotime('+1 days')), true), //future
                     array(date("Y-m-d H:i:s", strtotime('+30 days')), true) //future
                    );
    }   
 
    /**
     * @test
     * @group cartrack
     * @dataProvider _booking_dates
     **/
    public function canBook($booking_date, $result) {
        
    	$person = $this->getMockBuilder("Person")
					   ->setConstructorArgs(array(
												   array("name" => "Chuck",
														 "surname" => "Norris",
														 "phone" => "0761017946",
														)
												   )
											 )
					   ->setMethods(null)
					   ->getMock();
                       
        $canBook = $person->canBook($booking_date);
        
        $this->assertEquals($result, $canBook);
    }
    
    public function _verify_symptom() {
        return array(array("most_common", array(array("id" => 1, "description" => "fever", "severity" => "most_common")), true), //same severity for symptom bieng added
                     array("less_common", array(array("id" => 1, "description" => "fever", "severity" => "most_common")), false) //different severity for symptom being added
                     );
    }
    
    /**
     * @test
     * @group cartrack
     * @dataProvider _verify_symptom
     **/
    public function verifySymptom($severity, $symptoms, $result) {
        
    	$person = $this->getMockBuilder("Person")
					   ->setConstructorArgs(array(
												   array("name" => "Chuck",
														 "surname" => "Norris",
														 "phone" => "0761017946",
														)
												   )
											 )
					   ->setMethods(["symptoms"])
					   ->getMock();
                       
        $person->expects($this->any())->method('symptoms')->will($this->returnValue($symptoms));
                       
        $verify = $person->verifySymptom($severity);
        
        $this->assertEquals($result, $verify);
    }    
}