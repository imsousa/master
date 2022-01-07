<?php

class ControloAcessoController extends AppController {
    
    var $name = 'ControloAcesso';
    var $uses = array('ControloAcesso');
    var $layout = 'ajax';
    var $helpers = array('Html','Js');
    var $components = array('RequestHandler','Session');

	function index(){
		

		$user = $this->fullDetailsAD;
		$user_mifare = $user[0]['extensionattribute14'][0];
		
		if(strlen( $user[0]['extensionattribute14'][0]) == 0) 
			die('<h1>Controlo de Acesso</h1> Não tem cartão associado à sua conta');
		

		$url = "http://sqlsrvws.fc.ul.pt/verex.php?function=listaControloAcessoUser&number=".$user_mifare;
		$resp = $this->curlPost($url);
		if($resp==-1)
			die('<h1>Controlo de Acesso</h1> Sem resultados');
		else
			$this->set("resultados",json_decode($resp)); 

	}
	
	private function curlPost($url, $params = NULL) {  
		$login = "TTGuijkoH";
		$password = "p923h9h##sfuhh1";
		if($params != NULL) $url .= '?' . http_build_query($params);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($ch, CURLOPT_USERPWD, "$login:$password");
		$data = curl_exec($ch);
		$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
		$header = substr($data, 0, $header_size);
		curl_close($ch);                              
		return $data;
	}
}

?>