<?php



class PaginasController extends AppController {
    
    var $name = 'Paginas';
    var $uses = array();
    var $layout = 'ajax';
    var $helpers = array('Html','Js');
    var $components = array('RequestHandler','Session');
 


	public function dreamspark() { 
	
	}
	
	public function colibri1() { 
	
	}
	
	public function colibri2() { 
	
	}
	
	public function filesender() { 
	
	}

	public function office365() {
		$this->set("username",$this->fullUsername);
	}
}
?>