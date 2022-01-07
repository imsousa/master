<?php

class SistemaImpressaoController extends AppController {
    
    var $name = 'SistemaImpressao';
    var $uses = array('SistemaImpressao');
    var $layout = 'ajax';
    var $helpers = array('Html','Js');
    var $components = array('RequestHandler','Session');

	
	function impressoes(){
		
		$this->layout = 'ajax';
		$username = $this->username;
		$impressoes = $this->SistemaImpressao->find('all', array('order' => 'SistemaImpressao.data DESC', 'conditions' => array('SistemaImpressao.username' => $username)));
		$this->set("impressoes", $impressoes);

	}
	
}

?>