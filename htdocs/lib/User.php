<?php

class User
{
	/**
	 * Returns true if the user has a valid email, false if not.
	 *
	 * @return boolean
	 */
	public function hasValidEmail()
	{
		return (preg_match(
		'/^[-!#$%&\'*+\\.\/0-9=?A-Z^_`{|}~]+'.   // the user name
		'@'.                                     // the ubiquitous at-sign
		'([-0-9A-Z]+\.)+' .                      // host, sub-, and domain names
		'([0-9A-Z]){2,4}$/i',                    // top-level domain (TLD)
		trim($this->email)));
	}

	/**
	 * Save the user as a new user.
	 *
	 * @return boolean
	 */
	public function save()
	{
		$query = kl_str_sql('INSERT INTO users(
		name,
		email,
		login,
		password,
		is_admin,
		is_allowed
		) VALUES (!s,!s,!s,!s,!i,!i)',
		$this->name,
		$this->email,
		$this->login,
		$this->password,
		$this->is_admin,
		$this->is_allowed
		);

		if (!$res = DBH::$db->query($query)) {
			return false;
		} else {
			$this->user_id = DBH::$db->insertID();
			return $this->user_id;
		}
	}

	public function update()
	{
		$query = kl_str_sql('
		UPDATE users SET 
		name=!s,
		email=!s,
		login=!s,
		password=!s,
		is_admin=!i,
		is_allowed=!i
		WHERE user_id=!i',
		$this->name,
		$this->email,
		$this->login,
		$this->password,
		$this->is_admin,
		$this->is_allowed,
		$this->user_id
		);
//echo $query;
		if (!DBH::$db->query($query)) {
			return false;
		} else {
			return true;
		}
	}

	/**
	 * Get user by id.
	 *
	 * @param integer $id
	 * @return User
	 */
	public static function get($id)
	{
		if (is_null($id)) {
			return new User();
		}
		
		$query = kl_str_sql("SELECT * FROM users WHERE user_id=!i",$id);

		if(!$res = DBH::$db->query($query) OR !$row = DBH::$db->fetchRow($res)) {
			return false;
		} else {
			$user = new User();
			DBH::$db->loadFromDbRow($user, $res, $row);
			return $user;
		}
	}

	public function isAnonymous()
	{
		return !$this->user_id;
	}
	
	public function isAdmin()
	{
		return $this->is_admin;
	}

	public function isAllowed()
	{
		return $this->is_allowed;
	}
	/**
	 * Get a user by username.
	 *
	 * @param string $username
	 * @return User
	 */
	public static function getByLogin($username)
	{
		$query = kl_str_sql('SELECT * FROM users WHERE login=!s', $username);

		if(!$res = DBH::$db->query($query) OR !$row = DBH::$db->fetchRow($res)) {
			return false;
		} else {
			$user = new User();
			DBH::$db->loadFromDbRow($user, $res, $row);
			return $user;
		}
	}

	/**
	 * Get an user by email.
	 *
	 * @param string $email
	 * @return User
	 */
	public static function getByEmail($email)
	{
		$query = kl_str_sql('SELECT * FROM users WHERE email=!s', $email);
		if (!$res = DBH::$db->query($query) OR !$row = DBH::$db->fetchRow($res)) {
			return false;
		} else {
			$user = new User();
			DBH::$db->loadFromDbRow($user, $res, $row);
			return $user;
		}
	}

	/**
	 * Get all users.
	 *
	 * @return array Array of User
	 */
	public static function getAll()
	{
		$query = kl_str_sql('SELECT * FROM users ORDER BY is_admin DESC, is_allowed DESC, user_id ASC');
		if(!$res = DBH::$db->query($query)) {
			return false;
		} else {
			$retval=array();
			while($row = DBH::$db->fetchRow($res)) {
				$tmp = new User();
				DBH::$db->loadFromDbRow($tmp, $res, $row);
				$retval[] = $tmp;
			}
			return $retval;
		}
	}

	function delete(){
		$query=kl_str_sql("DELETE FROM users WHERE user_id=!i",$this->user_id);
		//echo $query;
		if(!$res=DBH::$db->query($query)){
			return false;
		}
		else{
			return true;
		}
	}

	public function setImportArray($array)
	{
		$this->name 		= $array['name'];
		$this->email		= $array['email'];
		$this->login		= $array['login'];
		$this->password		= $array['password'];
		$this->is_admin		= $array['is_admin'];
		$this->is_allowed	= $array['is_allowed'];
	}
}
?>
