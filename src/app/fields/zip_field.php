<?php
/**
 *
 * Usage
 *
 *	$field = new ZipField([]);
 *	// after form cleaning, a complementary method is_valid_for() can be called for a specific country...
 *	$field->is_valid_for("SK"); // or
 *	$field->is_valid_for("SK",$zip); // or
 *	$field->is_valid_for("SK",$zip,$err);
 *
 *	$field = new ZipField(["country" => "CZ"]); // accepts zip of the one country only
 */
class ZipField extends RegexField {

	static $Patterns = [
		"CZ" => '\d{3} ?\d{2}',
		"SK" => '\d{3} ?\d{2}',
		"DE" => '\d{5}',
		"IT" => '\d{5}',
		"IE" => '[A-Z\d]{3} ?[A-Z\d]{4}',
		"UK" => '([Gg][Ii][Rr] 0[Aa]{2})|((([A-Za-z][0-9]{1,2})|(([A-Za-z][A-Ha-hJ-Yj-y][0-9]{1,2})|(([A-Za-z][0-9][A-Za-z])|([A-Za-z][A-Ha-hJ-Yj-y][0-9][A-Za-z]?))))\s?[0-9][A-Za-z]{2})', // https://stackoverflow.com/questions/164979/regex-for-matching-uk-postcodes
		"AT" => '\d{4}',
		"HU" => '\d{4}',
	];

	static $OutputFilters = [
		'CZ' => ['/^(\d{3})(\d{2})$/','\1 \2'],
		'SK' => ['/^(\d{3})(\d{2})$/','\1 \2'],
		'IE' => ['/^(.{3})(.{4})$/','\1 \2'],
	];

	function __construct($options = array()){
		$options += array(
			"country" => null, // e.g. "CZ"
			"null_empty_output" => true,
		);

		$this->country = $options["country"];

		$_patterns = [];
		foreach(self::$Patterns as $key => $pattern){
			$_patterns[] = "(?<$key>$pattern)";
		}
		parent::__construct("/^(".join("|",$_patterns).")$/",$options);

		$this->update_messages(array("invalid" => _("Please enter a valid zip code")));
	}

	function clean($value){
		$value = trim($value);
		$value = strtoupper($value);

		list($err,$value) = parent::clean($value);
		if(isset($value) && $this->country){
			$this->is_valid_for($this->country,$value,$err);
		}
		$this->cleaned_value = $value;
		return [$err,$value];
	}

	/**
	 *
	 *	$fied->is_valid_for("CZ"); // true or false
	 *
	 *	// automatic value filtering is considered
	 *	$zip = "12345";
	 *	$fied->is_valid_for("CZ",$zip); // true
	 *	echo $zip; // "123 45"
	 *
	 * Typical usage in a controller
	 *
	 *	if($this->request->post() && ($d = $this->form->validate($this->params))){
	 *		if(!$this->form->fields["zip"]->is_valid_for($d["country"],$d["zip"],$err_msg)){
	 *			$this->set_error("zip",$err_msg);
	 *			return;
	 *		}
	 *		$user = User::CreateNewRecord($d);
	 *		// ...
	 *	}
	 */
	function is_valid_for($country,&$zip = null,&$err_message = null){
		static $format_hints;
		if(!$format_hints){
			$format_hints = [
				"CZ" => _("Enter the ZIP code as NNN NN"),
				"SK" => _("Enter the ZIP code as NNN NN"),
			];
		}

		if(is_null($zip)){
			$zip = $this->cleaned_value;
		}

		if(isset(self::$Patterns[$country])){
			$patern = self::$Patterns[$country];

			if(!preg_match("/^$patern$/",$zip)){
				$err_message = isset($format_hints[$country]) ? $format_hints[$country] : $this->messages["invalid"];
				$zip = null;
				return false;
			}
		}else{
			trigger_error("ZipField: matching pattern missing for $country");
		}

		if(isset(self::$OutputFilters[$country])){
			$pattern = self::$OutputFilters[$country][0];
			$replace = self::$OutputFilters[$country][1];
			$zip = preg_replace($pattern,$replace,$zip);
		}

		return true;
	}
}
