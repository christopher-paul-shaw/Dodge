<?php
namespace App;
use Gt\Core\Path;
use Exception;
use DirectoryIterator;
use Gt\Core\Config;


class User extends Entity {
	
	public $blockFields = [
		'current_password',
		'new_password',
		'confirm_password',    
	];   

	public $privateFields = [
		'password',    
	];     
	
	public $type = 'user';
	public function __construct ($identifier=false) {
    	parent::__construct($identifier);
        $this->ipLocked = !empty($this->config['user']->ipLocked); 
    	$this->multiLogin = !empty($this->config['user']->multiLogin); 
	}

	public static function getUserByEmail($email) {
		$query = new Self();
		$result = $query->db->fetch("user/getUserByEmail",['email' => $email]);
	
		return $result->id_user ?? null;
	}

	public function changePassword (
	$current=false, 
	$new=false, 
	$confirm=false) {

		$realPassword = $this->getValue('password');
		if (!$current) {
			throw new Exception("Current Password Can Not Be Blank");
		}

		if (!password_verify($current,$realPassword)) {
			throw new Exception("Current Password Incorrect");
		}

		if (empty($new)) {
			throw new Exception("New Password can not be blank");
		}

		if ($new != $confirm) {
			throw new Exception("New Passwords Do Not Match");
		}

		$this->setValue('password',$new);
	}

	public function search ($payload=[]) {
		$defaults = [
			'name' => '',
		];
		$payload = array_replace($defaults, $payload);
		return parent::search($payload);
	}



	public function setValue ($field, $value=false) {
		if (is_array($field)) {
			foreach ($field as $k => $v) {
				if (strstr($k,'password')) {
					$field[$k] = $this->password_hash($v);
				}
			}
		}
		else if (strstr($field, 'password')) {
			$value = $this->password_hash($value);
		
		}
		parent::setValue($field, $value);
	}

	private function password_hash ($password) {
		return password_hash($password, PASSWORD_BCRYPT);
	}

	public static function isAdmin () {
		$user = new self($_SESSION['user_id']);
		$level = $user->getValue('permission');
		return strtolower($level) == 'admin';
	}

	public static function isLoggedIn ($ip_locked = true) {

		if (empty($_SESSION['user_id'])) {
			return false;
		}

		$user = new self($_SESSION['user_id']);

		if (empty($user->multiLogin) && (empty($_SESSION['token']) || $_SESSION['token'] != $user->getValue('token'))) {
			$user->logOut();
			return false;
		}

		if ($user->ipLocked && ($_SERVER['REMOTE_ADDR'] != $user->getValue('ip'))) {
			return false;
		}

		return true;

	}

	public static function logIn ($email, $password) {

		$user_id = self::getUserByEmail($email);
		$user = new self($user_id);

		$realPassword = $user->getValue('password');
	
		if (!password_verify($password, $realPassword)) {
			throw new Exception("Failed to Login");
		}

		$token = rand(0,9000);
		$_SESSION['user_id'] = $user_id;
		$_SESSION['email'] = $email;
		$_SESSION['token'] = $token;

		$payload = [
			'ip' => $_SERVER['REMOTE_ADDR'],
			'token' => $token,
		];
		$user->setValue($payload);
	}

	public static function logOut () {
		session_destroy();
	}
	
}