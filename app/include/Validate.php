<?php

/*
** This class will validate basic form input
*/

class Validate
{

	/*
	** Check that the input is a valid email address
	*/

	public function email($input)
	{
		return preg_match(
			'/^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/', 
			$input
		) === 1 ? true : false;
	}

	/*
	** Check that the input is a valid password
	*/

	public function password($input)
	{
		return strlen($input) >= 8 ? true : false;
	}

	/*
	** Check that the input is a valid username
	*/

	public function username($input)
	{
		$pattern = '/^[a-zA-Z0-9_]+$/';
		$length = strlen($input);

		if ($length < 2 || $length > 16) {
			return false;
		}

		return preg_match(
			$pattern, 
			$input
		) === 1 ? true : false;
	}

	/*
	** Run three basic input validate functions and return true
	** or false
	*/

	public function holy_trinity($input)
	{
		if (is_array($input)) {
			$validate_email = $this->email($input['email']);
			$validate_passw = $this->password($input['password']);
			$Validate_usern = $this->username($input['username']);

			return ($validate_passw && $Validate_usern && $validate_email);
		}
		return false;
	}

}