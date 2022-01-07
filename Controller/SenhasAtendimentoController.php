<?php


class SenhasAtendimentoController extends AppController {
    
    var $name = 'SenhasAtendimento';
    var $uses = array('');
    var $layout = 'ajax';
    var $helpers = array('Html','Js');
    var $components = array('RequestHandler','Session');

	public function index(){
		$this->layout = 'ajax';
		$this->set("norefresh", (isset($_REQUEST['norefresh'])) ? TRUE : FALSE);
	}
	
	public function beforeFilter() {
		//TO-DO
	}
	
    public function data(){
		$url = "http://sqlsrvws.fc.ul.pt/senhas.php?function=listagem";
		$json_response = $this->curlPost($url);
		$this->set("senhas", json_decode($json_response));
		//return json_decode($json_response);
    }
	
		private function curlPost($url, $params = NULL) {  
		$login = "AAjk8e9K6";
		$password = "g54#Y00Qad21";
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