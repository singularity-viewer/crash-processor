<?php
/**
* Session.php
* This class handles sessions
*
* Description
* @package Singularity Crash Processor
* @author Latif Khalifa <latifer@streamgrid.net>
* @copyright Copyright &copy; 2012, Latif Khalifa
* 
* Permission is hereby granted, free of charge, to any person obtaining
* a copy of this software and associated documentation files
* (the "Software"), to deal in the Software without restriction, including
* without limitation the rights to use, copy, modify, merge, publish,
* distribute, sublicense, and/or sell copies of the Software, and to permit
* persons to whom the Software is furnished to do so, subject to the
* following conditions:
*
* - The above copyright notice and this permission notice shall be included
* in all copies or substantial portions of the Software.
*
* THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
* EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
* MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
* IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM,
* DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR
* OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE
* OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
* 
*/
class Session
{
	private $cookie;
	public $timeout;
	public $authenticated;
	public $validsession;

	/**
	 * User object containing info about the user.
	 *
	 * @var User
	 */
	public $user;
	
	function __construct()
	{
		$this->timeout = 604800; // 7 days
		$this->authenticated = false;
		$this->validsession = false;
		$this->cookie = "singularity_sid";
		$this->user = new User;
		register_shutdown_function(array(&$this, 'shutdown'));
	}

	function shutdown()
	{
		if ($this->persist || $this->ser_persist) {
			$ser=serialize($this->persist);
			if ($ser !== $this->ser_persist) {
				$this->ser_persist = $ser == 'N;' ? '' : $ser;
				$this->expires=time()+$this->timeout;
				$this->update();
			}
		}
	}

	function add()
	{
		$this->sid = md5(uniqid(rand()));
		$this->expires=time() + $this->timeout;

		DBH::$db->query(kl_str_sql("DELETE from session where sid=!s", $this->sid));
		$q = kl_str_sql("INSERT into session (sid, user_id, authenticated, expires, persist) ".
		"values (!s, !i, !i, !t, !s)",
		$this->sid,
		$this->user->user_id,
		$this->authenticated,
		$this->expires,
		$this->ser_persist
		);

		if (DBH::$db->query($q)) {
			setcookie($this->cookie, $this->sid, NULL, '/' . REL_DIR);
			return true;
		} else {
			return false;
		}
	}

	function update()
	{
		$q = kl_str_sql('UPDATE session SET user_id=!i, authenticated=!i, expires=!t, persist=!s WHERE sid=!s',
		$this->user->user_id, $this->authenticated, $this->expires, $this->ser_persist, $this->sid);

		if (($res = DBH::$db->query($q)) && DBH::$db->affectedRows()) {
			return true;
		} else {
			return $this->add();
		}
	}

	function remove($sid = false)
	{
		if (!$sid) {
			$sid = $this->sid;
		}
		$this->sid = NULL;
		$this->validsession = false;
		$this->authenticated = 0;
		$this->user = new User;
		$this->persist = NULL;
		$this->ser_persist = NULL;
		DBH::$db->query(kl_str_sql("DELETE from session where sid=!s", $sid));
		setcookie($this->cookie, '', 0, '/' . REL_DIR);
	}

	function check()
	{
		if (isset($_GET[$this->cookie])) {
			$this->sid = $_GET[$this->cookie];
		} else {
			$this->sid = $_COOKIE[$this->cookie];
		}
		
		$error = true;

		if (!$this->sid) {
			// No session id. Is anonymous access allowed?
			if (Option::get('allow_anon_access') == '1') {
				$this->user = new User();
				$this->user_id = $this->user->user_id;
				$this->add();
			} else {
				$this->user_id = NULL;
				$this->add();
			}
			$error = false;
		} else {
			$res = DBH::$db->query(kl_str_sql('SELECT * from session where sid=!s', $this->sid));
			if ($res AND $row = DBH::$db->fetchRow($res)) {
				$this->authenticated = (int)$row['authenticated'];
				$this->expires = strtotime($row['expires']);
				$this->ser_persist = $row['persist'];
				$this->user_id = (int)$row['user_id'];

				if (!$this->user_id) {
					$this->user_id = NULL;
				}

				if ($this->ser_persist) {
					$this->persist=@unserialize($this->ser_persist);
				}

				if ($this->expires >= time()) {
					$error = false;
					$this->validsession=true;

					if (!$this->user = User::get($this->user_id)) {
						$error = true;
					}
				}

				if ($this->expires-time() < $this->timeout/2) {
					$this->expires=time() + $this->timeout;
					setcookie($this->cookie, $this->sid, NULL, '/' . REL_DIR);
					if (!$this->update()) {
						$error = true;
					}
				}
			}

			if ($error) {
				if ($this->sid) {
					$this->remove($this->sid);
				}

				$this->add();
			}
		}
	} // End function check

	function authenticate($username, $password)
	{
		$user = User::getByUsername($username);
		if ($user AND $user->password === $password) {
			$this->user = $user;
			$this->user_id = $user->user_id;
			$this->authenticated = true;
			$this->persist->active_order = null;	
			$this->update();
			return true;
		}
		return false;
	}

	function loginRedirect()
	{
		if ($l = strlen(REL_DIR)) {
			$eatchars = $l + 2;
		} else {
			$eatchars = $l + 1;
		}
		$caller='/' . substr($_SERVER['PHP_SELF'], $eatchars);
		$callerarg = array();

		if ($nc = count($_GET)) {
			foreach($_GET as $key => $value) {
				$callerarg[] = urlencode($key) . '=' . urlencode($value);
			}
			$callerarg = implode('&', $callerarg);
		}
		$redirect = '/login.php?caller=' . urlencode($caller);

		if ($callerarg) {
			$redirect .= "&callerarg=" . urlencode($callerarg);
		}

		http::redirect($redirect);
	}

	public function requireUser()
	{
		if ((Option::get('allow_anon_access') == '0' && $this->isAnonymous())
			|| $this->user->isAnonymous()) {
			$this->loginRedirect();
		} else if (!$this->user->isAllowed()) {
			$this->remove($this->sid);
			$this->loginRedirect();
		}
	}

	/**
	 * Returns true if the user is anonymous.
	 *
	 * @return boolean
	 */
	public function isAnonymous()
	{
		if ($this->authenticated == 0) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Redirects the user to a login screen if he's not logged in as admin.
	 */
	public function requireAdmin()
	{
		if ($this->isAdmin() != true) {
			$this->remove($this->sid);
			$this->loginRedirect();
		}
	}

	/**
	 * Returns true if user is admin. False if not.
	 *
	 * @return boolean
	 */
	public function isAdmin()
	{
		return ($this->authenticated && $this->user->isAdmin());
	}
}

/*
* Local variables:
* tab-width: 4
* c-basic-offset: 4
* End:
* vim600: sw=4 ts=4 fdm=marker
* vim<600: sw=4 ts=4
*/
?>