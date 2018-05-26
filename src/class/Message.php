<?php
namespace App;
use Gt\Core\Path;
use Exception;
use Gt\Core\Config;


class Message extends Entity {
	
	public $type = 'message';
	
	public function __construct($identifier=false) {
		parent::__construct($identifier);
	}
	
	
}