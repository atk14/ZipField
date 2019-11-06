<?php
class TcZipField extends TcBase {

	function test(){
		$this->field = new ZipField([]);

		// Austria
		$this->_test("AT",array(
			"1010" => "1010"
		),array("123 45"));

		// Czech Republic
		$this->_test("CZ",array(
				"123 45" => "123 45",
				"67890" => "678 90",
			),array("CW3 9SS")
		);

		// Slovakia
		$this->_test("CZ",array(
				"123 45" => "123 45",
				"67890" => "678 90",
			),array("CW3 9SS")
		);

		// Great Britain
		$this->_test("GB",array(
			"CW3 9SS" => "CW3 9SS",
			"se50eg" => "SE50EG",
		));

		// Ireland
		$this->_test("IE",array(
			"V94 T2XR" => "V94 T2XR",
			"d02af30" => "D02 AF30",
		),array("123 45"));

		// Overall invalid codes
		$this->assertInvalid("#");
		$this->assertInvalid("1");

		// Accepting zip only for one country only (IE)
		$this->field = new ZipField([
			"country" => "IE",
		]);
		$zip = $this->assertValid("V94T2XR");
		$this->assertEquals("V94 T2XR",$zip);
		//
		$err = $this->assertInvalid("123 33");
		$this->assertEquals(_("Please enter a valid zip code"),$err);

		// Accepting zip only for one country only (CZ)
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

	function _test($country,$valid_codes,$invalid_codes = array()){
		foreach($valid_codes as $input => $expected){
			$zip = $this->assertValid($input);
			$this->assertTrue($this->field->is_valid_for($country,$zip,$err));
			$this->assertEquals($expected,$zip);

			$zip = $this->assertValid(" $input ");
			$this->assertTrue($this->field->is_valid_for($country,$zip,$err));
			$this->assertEquals($expected,$zip);
		}

		foreach($invalid_codes as $input){
			$zip = $this->assertValid($input);
			$this->assertFalse($this->field->is_valid_for($country,$zip,$err));
		}
	}

}
