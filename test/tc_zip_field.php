<?php
class TcZipField extends TcBase {

	function test(){
		$this->field = new ZipField(array());

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
		$this->field = new ZipField(array(
			"country" => "IE",
		));
		$zip = $this->assertValid("V94T2XR");
		$this->assertEquals("V94 T2XR",$zip);
		//
		$err = $this->assertInvalid("123 33");
		$this->assertEquals(_("Please enter a valid zip code"),$err);

		// Accepting zip for one country only (CZ)
		$this->field = new ZipField(array(
			"country" => "CZ",
		));
		$zip = $this->assertValid("123 45");
		$this->assertEquals("123 45",$zip);
		//
		$err = $this->assertInvalid("CW3 9SS");
		$this->assertEquals(_("Enter the ZIP code as NNN NN"),$err);
		//
		$zip = $this->assertValid("56789");
		$this->assertEquals("567 89",$zip);

		// Using options null_empty_output
		$this->field = new ZipField(array("required" => false)); // null_empty_output is true by default
		$val = $this->assertValid(" ");
		$this->assertTrue($val === null);
		//
		$this->field = new ZipField(array("required" => false, "null_empty_output" => false));
		$val = $this->assertValid(" ");
		$this->assertTrue($val === "");
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

	function test_France(){
		$this->_testCountry("FR",array(
				"12345" => "12345",
				"111 22" => "11122",
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

	function test_Romania(){
		$this->_testCountry("RO",array(
			"123456" => "123456",
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
			$err = "err";
			$this->assertTrue($this->field->is_valid_for($country,$zip,$err));
			$this->assertEquals($expected,$zip);
			$this->assertNull(null,$err);

			$zip = $this->assertValid(" $input ");
			$err = "err";
			$this->assertTrue($this->field->is_valid_for($country,$zip,$err));
			$this->assertEquals($expected,$zip);
			$this->assertNull(null,$err);
		}

		foreach($invalid_codes_in_the_country as $input){
			$zip = $this->assertValid($input);
			$err = "";
			$this->assertFalse($this->field->is_valid_for($country,$zip,$err));
			$this->assertTrue(strlen($err)>0);
		}
	}

	function provider_zips() {
		$uk_options = array(
			'length' => array(5,9),
			'mixed'  => true,
		);
		return array(
			array('BE', '1234'),
			array('BG', '1234'),
			array('CZ', '123 40'),
			array('CZ', '12340', '123 40'),
			array('DK', '1234'),
			array('DE', '12345'),
			array('EE', '12345'),
			array('IE', 'AB1 CD23', array('mixed' => true)),
			array('IE', 'AB1CD23','AB1 CD23', array('mixed' => true)),
			array('IE', '12A 34BC', array('mixed' => true)),
			array('IE', '12A34BC','12A 34BC', array('mixed' => true)),
			array('GR', '123 45'),
			array('GR', '12345', '123 45'),
			array('ES', '12345'),
			array('FR', '12345'),
			array('HR', '12345'),
			array('HR', 'HR-12345','12345'),
			array('IT', '12345'),
			array('CY', '1234'),
			array('LV', '1234', 'LV-1234'),
			array('LV', 'LV-1234', 'LV-1234'),
			array('LT', '12345', 'LT-12345'),
			array('LT', 'LT-12345', 'LT-12345'),
			array('LU', '1234', 'L-1234'),
			array('LU', 'L-1234', 'L-1234'),
			array('HU', '1234'),
			array('MT', 'ABC 1234'),
			array('MT', 'ABC1234', 'ABC 1234'),
			array('NL', '1234 AB'),
			array('NL', '1234AB', '1234 AB'),
			array('AT', '1234'),
			array('PL', '12-345'),
			array('PL', '12345', '12-345'),
			array('PT', '1234-567'),
			array('PT', '1234567', '1234-567'),
			array('RO', '123456'),
			array('SI', '1234'),
			array('SI', 'SI-1234', '1234'),
			array('SK', '123 45'),
			array('SK', '12345', '123 45'),
			array('FI', 'FI-12345'),
			array('FI', '12345', 'FI-12345'),
			array('FI', 'AX-12345', 'AX-12345'),
			array('SE', 'SE-123 45'),
			array('SE', '12345', 'SE-123 45'),
			array('SE', '123 45', 'SE-123 45'),
			array('SE', 'SE-12345', 'SE-123 45'),
			array('UK', 'SW1W 0NY', $uk_options),
			array('UK', 'SW1W0NY', 'SW1W 0NY', $uk_options),
			array('UK', 'PO16 7GZ', $uk_options),
			array('UK', 'PO167GZ', 'PO16 7GZ', $uk_options),
			array('UK', 'L1 8JQ', $uk_options),
			array('UK', 'L18JQ', 'L1 8JQ', $uk_options),
		);
	}
	/**
	 * @dataProvider provider_zips
	 **/
	function test_zips($country, $zip, $cannonical = null, $options=array()) {
		$field = new ZipField(array());
		if(is_array($cannonical)) {
			$options=$cannonical;
			$cannonical = null;
		}
		if($cannonical === null) {
			$cannonical = $zip;
		}
		$zzip = $zip;
		$this->assertTrue($field->is_valid_for($country,$zzip,$err));
		$this->assertEquals($zzip, $cannonical);
		$options+= array(
			'mixed' => false,
			'length' => null
		);
		if(!$options['mixed']) {
			for($i=0;$i<strlen($zip);$i++) {
				$zzip = $zip;
				if(is_numeric($zip[$i])) {
					$zzip[$i]='A';
				} else {
					$zzip[$i]='1';
				}
				$this->assertFalse($field->is_valid_for($country,$zzip,$err));
		  }
		}
		if(!$options['length'] || strlen($zip) == $options['length'][0]) {
				$zzip = substr($zip,1);
				$this->assertFalse($field->is_valid_for($country,$zzip,$err));
				$zzip = substr($zip,strlen($zzip)-1);
				$this->assertFalse($field->is_valid_for($country,$zzip,$err));
		}
		if(!$options['length'] || strlen($zip) == $options['length'][1]) {
				foreach(array('a', 'A', '0', '#') as $add) {
					$zzip = $zip . $add;
					$this->assertFalse($field->is_valid_for($country,$zzip,$err));
					$zzip = $add . $zip;
					$this->assertFalse($field->is_valid_for($country,$zzip,$err));
				}
		}
	}

	function test_errors_with_custom_message() {

		# first test with specified country code
		$this->field = new ZipField(array(
			"country" => "CZ",
			"error_messages" => array(
				"invalid" => "This zip is invalid",
			),
			"format_hints" => array(
				"CZ" => "Format NNN NN is expected",
				"SK" => "Format NNN NN must be used",
				# hint for SE is not included in default option in constructor
				"SE" => "Unknown format for Sweden",
			),
		));

		$this->assertValid("12345");
		$err = $this->assertInvalid("1234");
		$this->assertEquals("Format NNN NN is expected", $err);
		# test is_valid_for() using different country code
		$this->assertFalse($this->field->is_valid_for("SE",$zip,$err));
		$this->assertEquals("Unknown format for Sweden", $err);
		$this->assertFalse($this->field->is_valid_for("UK",$zip,$err));
		$this->assertEquals("This zip is invalid", $err);

		# other test without country code
		$this->field = new ZipField(array(
			"error_messages" => array(
				"invalid" => "This zip is invalid",
			),
			"format_hints" => array(
				"CZ" => "Format NNN NN is expected",
				"SK" => "Format NNN NN must be used",
				# hint for SE is not included in default option in constructor
				"SE" => "Unknown format for Sweden",
			),
		));
		$err = $this->assertInvalid("123 0");

		$this->assertEquals("This zip is invalid", $err);

		$this->assertValid("1230");
		$this->assertFalse($this->field->is_valid_for("CZ",$zip,$err));
		$this->assertEquals("Format NNN NN is expected", $err);
		$this->assertFalse($this->field->is_valid_for("SK",$zip,$err));
		$this->assertEquals("Format NNN NN must be used", $err);
		# For UK there is no format hint defined. Not even in default options in constructor.
		# So error message defined for 'invalid' is returned
		$this->assertFalse($this->field->is_valid_for("UK",$zip,$err));
		$this->assertEquals("This zip is invalid", $err);
		$this->assertFalse($this->field->is_valid_for("SE",$zip,$err));
		$this->assertEquals("Unknown format for Sweden", $err);
	}
}
