<?php

App::import('Vendor', 'ADCampusSiges', array('file' => 'adldapcampus' . DS . 'ADCampusSiges.php'));
App::import('Vendor', 'ADCampus', array('file' => 'adldapcampus' . DS . 'ADCampus.php'));
App::uses('CakeEmail', 'Network/Email');
App::uses('Sanitize', 'Utility');
class CampusController extends AppController {
    
    var $name = 'Campus';
    var $uses = array();
    var $layout = 'ajax';
    var $helpers = array('Html','Js');
    var $components = array('RequestHandler','Session');
 

	public function index() { 
		//$this->autoRender = false;
		$this->set("tem_conta_campus", FALSE);
		if(isset($_REQUEST['cd_aluno'])) {
			//$cd_aluno =	 50001; //EXISTE
			$cd_aluno = Sanitize::clean($_REQUEST['cd_aluno']);
		}
		
		
		/*Tentativa de ligação*/
		try {
			$ad = new ADCampusSiges();
		}catch(Exception $e) {
			die( '<span style="color:red">Falhou a ligação Campus@UL</span><br>' );
			//$e->getMessage()
		}
		
		/*Procurar o aluno*/
		try {
			$res = $ad->user_info_cod_aluno($cd_aluno);
			//Tem conta CAMPUS
			if($res['count']>0) {
				$this->set("tem_conta_campus", TRUE);
				$mail = $res[0]['mail'][0];
				$this->set("mail", $mail);
				$samaccountname = $res[0]['samaccountname'][0];
				$ad_campus = new ADCampus();
				$res_campus = 	$ad_campus->user_info($samaccountname);
				$pwdlastset = $res_campus[0]['pwdlastset'][0];
				$maxpwdage = $ad_campus->getPasswordAge();
				$account_control = $res_campus[0]['useraccountcontrol'][0] ;
				$password_expira = $this->passwordAExpirar($pwdlastset,$account_control,$maxpwdage);
				$this->set("password_expira", $password_expira);
				$this->set("nao_existe", FALSE);
			}
			else
				$this->set("nao_existe", TRUE);
		}catch(Exception $e) {
			echo $e;
		}	
		
	}
	
	
	public function passwordAExpirar($pwdlastset, $account_control, $maxpwdage) {
		if (!function_exists('bcmod')) {
			 return ("Missing function support [bcmod] http://www.php.net/manual/en/book.bc.php");
		};
		
		if ($account_control == '66048') { 
			return "Does not expire"; 
		} 
		if ($pwdlastset === '0') { 
			return "Password expirada."; 
		}
				
		if (bcmod($maxpwdage, 4294967296) === '0') { 
			return "Domain does not expire passwords"; 
		}
		
		$pwdexpire = bcsub($pwdlastset, $maxpwdage);
		 // Convert MS's time to Unix time 
		 $expiryts = bcsub(bcdiv($pwdexpire, '10000000'), '11644473600');
		 $expiryformat = date('Y-m-d H:i:s', bcsub(bcdiv($pwdexpire, '10000000'), '11644473600'));
		 return $expiryformat;
	}
	
	public function help () {
		$this->layout = 'ajax';
	}
	
	public function comoObter () {
		$this->layout = 'ajax';
	}
	
}
?>