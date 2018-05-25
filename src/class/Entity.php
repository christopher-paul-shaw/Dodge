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
    protected $privateFields = [];

    public $readOnly = false;
    public $ipLocked = false;

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
        $this->path = Path::get(Path::DATA)."/{$this->type}/";
        $this->currentDirectory =  $this->path.$this->identifier;   

        $this->data = $this->db->fetch("{$this->type}/getById", ["id_{$this->type}" => $this->identifier]);
        var_dump($this->data);
    }

    public function create ($payload) {  
        if (file_exists($this->currentDirectory)) {
           	throw new Exception("Entity Already Exists");
        }        
        mkdir($this->currentDirectory, 0777, true);        
        foreach ($payload as $field => $value) {
            $this->setValue($field,$value);
        }
    }
    
    public function update ($payload) {
        foreach ($payload as $field => $value) {
            $this->setValue($field,$value);
        }
    }
    
    public function delete () {
        $this->removeDirectory($this->currentDirectory);
    } 
    
    public function search ($filters=false) { 
        $items = [];

        $dir = new DirectoryIterator($this->path);

		foreach ($dir as $fileinfo) {
		    if (!$fileinfo->isDir() || $fileinfo->isDot()) continue;
	        $identifier = $fileinfo->getFilename();
            $items[$identifier]['id'] = $identifier;
            $this->currentDirectory = $this->path.$identifier.'/';
            $valuesDirectory = new DirectoryIterator($this->currentDirectory);
            foreach ($valuesDirectory as $valueinfo) {
                if ($valueinfo->isDot()) continue;
                $field = explode('.',$valueinfo->getFilename())[0];
                if (in_array($field, $this->privateFields)) continue;
                $items[$identifier][$field] = $this->getValue($field);
            }
         
        }

        return $items;  
    }

    public function protectField ($field) {
        if (strstr($field,'./')) {
            throw new Exception("Invalid Field");
        }
    }

    public function getValue ($field) {
        return $this->data->$field ?? false;
    }

    public function setValue ($field,$value=false) {
 
        if (in_array($field,$this->blockFields) || $this->readOnly) return;

       $this->db->update("{$this->type}/update", [
            "field" => $field,
            "value" => $value,
            "id_{$this->type}" => $this->data->id_{$this->type}
       ]);
    }

    public function blankValue ($field) {
        $this->protectField($field);
        $path = "{$this->currentDirectory}/{$field}.dat";
        return file_put_contents($path, '');
    }

    public function removeDirectory($path) {
        if ($readOnly) return; 
        $files = glob($path . '/*');
		foreach ($files as $file) {
            is_dir($file) ? $this->removeDirectory($file) : unlink($file);
		}
        rmdir($path);
    }
}
