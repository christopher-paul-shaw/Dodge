<?php
namespace App;
use Gt\Core\Path;
use Exception;
use DirectoryIterator;
use Gt\Core\Config;
use Gt\Database\Connection\Settings;
use Gt\Database\Database;

class Entity {

    public $type = 'default'; 
    public $blockFields = [];
    public $readOnly = false;

    public function __construct ($identifier=false) {

    	$this->config = new Config();
    	$dbConfig = $this->config["database"];

    	$settings = new Settings(
    		Path::get(Path::SRC) . "/query",
    		$dbConfig->dsn,
    		$dbConfig->schema,
    		$dbConfig->host,
    		$dbConfig->port,
    		$dbConfig->username,
    		$dbConfig->password
    	);
    	$this->db = new Database($settings);
        $this->identifier = strtolower($identifier);
       
        if (!$identifier) return; 
        $data = $this->db->fetch("{$this->type}/getById", ["id_{$this->type}" => $this->identifier]);

        foreach ($data as $field => $value) {
            $this->data[$field] = $value;
        }
    }

    public function create ($payload) {  
        foreach($payload as $k => $v) {
            $this->data[$k] = $v;
        }
        $this->db->insert("{$this->type}/create", $this->data);
    }
    
    public function update ($payload) {
        $this->setValue($payload);
    }
    
    public function delete () {
        $this->db->update("{$this->type}/update", $this->data);
    } 
    
    public function search ($filters=false) { 

        return $this->db->fetchAll("{$this->type}/search", $filters);
    }

    public function protectField ($field) {
        if (strstr($field,'./')) {
            throw new Exception("Invalid Field");
        }
    }

    public function getValue ($field) {
        return $this->data[$field] ?? false;
    }

    public function setValue ($field,$value=false) {
 
        if (!is_array($field) && in_array($field,$this->blockFields) || $this->readOnly) return;

        if (is_array($field)) {
            foreach ($field as $k => $v) {
                if(in_array($k,$this->blockFields)) continue;
                $this->data[$k] = $v;
            }
        }
        else {
            $this->data[$field] = $value;
        }

        $this->db->update("{$this->type}/update", $this->data);
    }

    public function blankValue ($field) {
       return false;
    }

}
