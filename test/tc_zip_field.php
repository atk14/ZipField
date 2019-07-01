<?php
class TcZipField extends TcBase {

	function test(){
		$this->field = new ZipField([]);

		// cz
		$zip = $this->assertValid("123 45");
		$this->assertEquals("123 45",$zip);

		$zip = $this->assertValid(" 67890 ");
		$this->assertEquals("67890",$zip);

		$this->assertTrue($this->field->is_valid_for("CZ",$zip,$err));
		$this->assertEquals("678 90",$zip); // filtering in progress: "67890" -> "678 90"
		$this->assertEquals(null,$err);

		$this->assertFalse($this->field->is_valid_for("UK",$zip,$err));
		$this->assertEquals(null,$zip);
		$this->assertEquals(_("Please enter a valid zip code"),$err);

		// gb
		$zip = $this->assertValid("CW3 9SS");
		$this->assertEquals("CW3 9SS",$zip);

		$zip = $this->assertValid("se50eg");
		$this->assertEquals("se50eg",$zip);

		// at
		$zip = $this->assertValid("1010");
		$this->assertEquals("1010",$zip);

		// invalid
		$this->assertInvalid("#");
		$this->assertInvalid("1");

		// accepting zip only for the one country only
		$this->field = new ZipField([
			"country" => "CZ",
		]);
		$zip = $this->assertValid("123 45");
		$this->assertEquals("123 45",$zip);

		$err = $this->assertInvalid("CW3 9SS");
		$this->assertEquals(_("Enter the ZIP code as NNN NN"),$err);

		$zip = $this->assertValid("56789");
		$this->assertEquals("567 89",$zip);
	}

	function test_isValidFor(){
		$field = new ZipField([]);
	}
}
