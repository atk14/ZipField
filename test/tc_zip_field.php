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
		$uk_options = [
			'length' => [5,9],
			'mixed'  => true,
		];
		return [
			['BE', '1234'],
			['BG', '1234'],
			['CZ', '123 40'],
			['CZ', '12340', '123 40'],
			['DK', '1234'],
			['DE', '12345'],
			['EE', '12345'],
			['IE', 'AB1 CD23', ['mixed' => true]],
			['IE', 'AB1CD23','AB1 CD23', ['mixed' => true]],
			['IE', '12A 34BC', ['mixed' => true]],
			['IE', '12A34BC','12A 34BC', ['mixed' => true]],
			['GR', '123 45'],
			['GR', '12345', '123 45'],
			['ES', '12345'],
			['FR', '12345'],
			['HR', '12345'],
			['HR', 'HR-12345','12345'],
			['IT', '12345'],
			['CY', '1234'],
			['LV', '1234', 'LV-1234'],
			['LV', 'LV-1234', 'LV-1234'],
			['LT', '12345', 'LT-12345'],
			['LT', 'LT-12345', 'LT-12345'],
			['LU', '1234', 'L-1234'],
			['LU', 'L-1234', 'L-1234'],
			['HU', '1234'],
			['MT', 'ABC 1234'],
			['MT', 'ABC1234', 'ABC 1234'],
			['NL', '1234 AB'],
			['NL', '1234AB', '1234 AB'],
			['AT', '1234'],
			['PL', '12-345'],
			['PL', '12345', '12-345'],
			['PT', '1234-567'],
			['PT', '1234567', '1234-567'],
			['RO', '123456'],
			['SI', '1234'],
			['SI', 'SI-1234', '1234'],
			['SK', '123 45'],
			['SK', '12345', '123 45'],
			['FI', 'FI-12345'],
			['FI', '12345', 'FI-12345'],
			['FI', 'AX-12345', 'AX-12345'],
			['SE', 'SE-123 45'],
			['SE', '12345', 'SE-123 45'],
			['SE', '123 45', 'SE-123 45'],
			['SE', 'SE-12345', 'SE-123 45'],
			['UK', 'SW1W 0NY', $uk_options],
			['UK', 'SW1W0NY', 'SW1W 0NY', $uk_options],
			['UK', 'PO16 7GZ', $uk_options],
			['UK', 'PO167GZ', 'PO16 7GZ', $uk_options],
			['UK', 'L1 8JQ', $uk_options],
			['UK', 'L18JQ', 'L1 8JQ', $uk_options],
		];
	}
	/**
	 * @dataProvider provider_zips
	 **/
	function test_zips($country, $zip, $cannonical = null, $options=[]) {
		$field = new ZipField([]);
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
		$options+= [
			'mixed' => false,
			'length' => null
		];
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
				foreach(['a', 'A', '0', '#'] as $add) {
					$zzip = $zip . $add;
					$this->assertFalse($field->is_valid_for($country,$zzip,$err));
					$zzip = $add . $zip;
					$this->assertFalse($field->is_valid_for($country,$zzip,$err));
				}
		}
	}
}
