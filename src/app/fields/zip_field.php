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
	static $Patterns;
	static $OutputFilters;

	function __construct($options = array()){
		$options += array(
			"country" => null, // e.g. "CZ"
			"null_empty_output" => true,
			"error_messages" => array(
				"invalid" => _("Please enter a valid zip code"),
			),
			"format_hints" => array(
				"CZ" => _("Enter the ZIP code as NNN NN"),
				"SK" => _("Enter the ZIP code as NNN NN"),
			),
		);

		$this->country = $options["country"];
		$this->format_hints = $options["format_hints"];

		$_patterns = array();
		foreach(self::$Patterns as $key => $pattern){
			$_patterns[] = "(?<$key>$pattern)";
		}
		parent::__construct("/^(".join("|",$_patterns).")$/",$options);
	}

	function clean($value){
		$value = trim($value);
		$value = strtoupper($value);
		$value = preg_replace('/\s+/',' ',$value);

		list($err,$value) = parent::clean($value);
		if(!$value || !is_null($err)){
			return array($err,$value);
		}
		if($this->country && !$this->is_valid_for($this->country,$value,$err)){
			$value = null;
		}
		$this->cleaned_value = $value;
		return array($err,$value);
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
		$format_hints = $this->format_hints;

		if(is_null($zip)){
			$zip = $this->cleaned_value;
		}

		$err_message = null;

		if(isset(self::$Patterns[$country])){
			$patern = self::$Patterns[$country];

			if(!preg_match("/^$patern$/",$zip)){
				// trying to check the code again but without white spaces
				$_zip = preg_replace('/\s*/','',$zip);
				if($_zip!==$zip && ($this->is_valid_for($country,$_zip,$err_message))){
					$zip = $_zip;
				}else{
					$err_message = isset($format_hints[$country]) ? $format_hints[$country] : $this->messages["invalid"];
					return false;
				}
			}
		}else{
			trigger_error("ZipField: matching pattern missing for $country");
		}

		if(isset(self::$OutputFilters[$country])){
			$pattern = '/' . self::$Patterns[$country] . '/';
			$replace = self::$OutputFilters[$country];
			if(is_callable($replace)) {
				preg_match($pattern, $zip, $matches);
				$zip = $replace($matches);
			} else {
				$zip = preg_replace($pattern,$replace,$zip);
			}
		}

		return true;
	}
}


#https://en.wikipedia.org/wiki/List_of_ISO_3166_country_codes
#https://publications.europa.eu/code/en/en-390105.htm
#Whether to include country prefix or not have been determined from other
#sources, as the link above provide inaccurate informations.

ZipField::$Patterns = array(
		"BE" => '\d{4}',
		"BG" => '\d{4}',
		"CZ" => '(\d{3}) ?(\d{2})',
		"DK" => '\d{4}',
		"DE" => '\d{5}',
		"EE" => '\d{5}',
		"IE" => '([A-Z\d]{3}) ?([A-Z\d]{4})',
		"GR" => '(\d{3}) ?(\d{2})',
		"ES" => '\d{5}',
		"FR" => '\d{5}',
		"HR" => '(HR-)?(\d{5})',
		"IT" => '\d{5}',
		"CY" => '\d{4}',
		"LV" => '(LV-)?(\d{4})',
		"LT" => '(LT-)?(\d{5})',
		"LU" => '(L-)?(\d{4})',
		"HU" => '\d{4}',
		"MT" => '([A-Z]{3}) ?(\d{4})',
		"NL" => '(\d{4}) ?([A-Z]{2})',
		"AT" => '\d{4}',
		"PL" => '(\d{2})-?(\d{3})',
		"PT" => '(\d{4})-?(\d{3})',
		"RO" => '\d{6}', // Four-digit postal codes were first introduced in Romania in 1974. Beginning with 1 May 2003, postal codes have six digits.
		"SI" => '(SI-)?(\d{4})',
		"SK" => '(\d{3}) ?(\d{2})',
		"FI" => '(FI-|AX-)?(\d{5})',
		"SE" => '(SE-)?(\d{3}) ?(\d{2})',
		"UK" => '([Gg][Ii][Rr] 0[Aa]{2})|((([A-Za-z][0-9]{1,2})|(([A-Za-z][A-Ha-hJ-Yj-y][0-9]{1,2})|(([A-Za-z][0-9][A-Za-z])|([A-Za-z][A-Ha-hJ-Yj-y][0-9][A-Za-z]?))))\s?[0-9][A-Za-z]{2})', // https://stackoverflow.com/questions/164979/regex-for-matching-uk-postcodes
);

ZipField::$OutputFilters = array(
		'CZ' => '\1 \2',
		'IE' => '\1 \2',
		'GR' => '\1 \2',
		'HR' => '\2',
		'LV' => 'LV-\2',
		'LT' => 'LT-\2',
		'LU' => 'L-\2',
		'MT' => '\1 \2',
		'NL' => '\1 \2',
		'PL' => '\1-\2',
		'PT' => '\1-\2',
		'SI' => '\2',
		'SK' => '\1 \2',
		'FI' => function($matches) { 
							return ($matches[1]?$matches[1]:'FI-') . $matches[2];
						},
		'SE' => 'SE-\2 \3',
		'UK' => function($matches) {
							$zip = str_replace(' ','', $matches[0]);
							return substr($zip,0,strlen($zip)-3) . ' ' . substr($zip,strlen($zip)-3);
						}
);


