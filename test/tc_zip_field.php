<?php
class TcZipField extends TcBase {

	function test(){
		$this->field = new ZipField([]);

		// Some valid codes
		$zip = $this->assertValid("111 50");
		$this->assertEquals("111 50",$zip);
	
		$zip = $this->assertValid("cw3 9ss");
		$this->assertValid("CW3 9SS",$zip);

		// Invalid codes
		$this->assertInvalid("#");
		$this->assertInvalid("?");

		// Re-validating code for the given country
		$zip = $this->assertValid("11150");
		$this->assertEquals("11150",$zip);
		$this->assertTrue($this->field->is_valid_for("CZ",$zip,$err));
		$this->assertEquals("111 50",$zip);
		$this->assertNull($err);

		// Re-validating bad code
		$zip = $this->assertValid("11150");
		$this->assertEquals("11150",$zip);
		$this->assertFalse($this->field->is_valid_for("IE",$zip,$err));
		$this->assertEquals("11150",$zip);
		$this->assertEquals("Please enter a valid zip code",$err);

		// Accepting zip code for one country only (IE)
		$this->field = new ZipField([
			"country" => "IE",
		]);
		$zip = $this->assertValid("V94T2XR");
		$this->assertEquals("V94 T2XR",$zip);
		//
		$err = $this->assertInvalid("123 33");
		$this->assertEquals(_("Please enter a valid zip code"),$err);

		// Accepting zip for one country only (CZ)
		$this->field = new ZipField([
			"country" => "CZ",
		]);
		$zip = $this->assertValid("123 45");
		$this->assertEquals("123 45",$zip);
		//
		$err = $this->assertInvalid("CW3 9SS");
		$this->assertEquals(_("Enter the ZIP code as NNN NN"),$err);
		//
		$zip = $this->assertValid("56789");
		$this->assertEquals("567 89",$zip);
	}

	function test_Austria(){
		$this->_testCountry("AT",array(
			"1010" => "1010"
		),array("123 45"));
	}

	function test_CzechRepublic(){
		$this->_testCountry("CZ",array(
				"123 45" => "123 45",
				"67890" => "678 90",
			),array("CW3 9SS")
		);
	}

	function test_GreatBritain(){
		$this->_testCountry("GB",array(
			"CW3 9SS" => "CW3 9SS",
			"se50eg" => "SE50EG",
		));
	}

	function test_Ireland(){
		$this->_testCountry("IE",array(
			"V94 T2XR" => "V94 T2XR",
			"d02af30" => "D02 AF30",
		),array("123 45"));
	}

	function test_Slovakia(){
		$this->_testCountry("CZ",array(
				"123 45" => "123 45",
				"67890" => "678 90",
			),array("CW3 9SS")
		);
	}

	function _testCountry($country,$valid_codes,$invalid_codes_in_the_country = array()){
		$this->field = new ZipField();

		foreach($valid_codes as $input => $expected){
			$zip = $this->assertValid($input);
			$this->assertTrue($this->field->is_valid_for($country,$zip,$err));
			$this->assertEquals($expected,$zip);

			$zip = $this->assertValid(" $input ");
			$this->assertTrue($this->field->is_valid_for($country,$zip,$err));
			$this->assertEquals($expected,$zip);
		}

		foreach($invalid_codes_in_the_country as $input){
			$zip = $this->assertValid($input);
			$this->assertFalse($this->field->is_valid_for($country,$zip,$err));
		}
	}
}
