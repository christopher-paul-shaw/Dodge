<?php
namespace App;
use Gt\Core\Path;
use Exception;
use Gt\Core\Config;


class Room extends Entity {
	
	public $type = 'message';
	public function __construct($identifier=false) {
		parent::__construct($identifier);
	}


	public function messageSearch ($payload=[]) {
		$payload["id_room"] = $this->identifier;
		return $this->db->fetchAll("message/search",$payload);
    }
	
}