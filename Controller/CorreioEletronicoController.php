<?php
App::import('Vendor', 'ldap', array('file' => 'ldap' . DS . 'adldap.php'));


class CorreioEletronicoController extends AppController {
    
    var $name = 'CorreioEletronico';
    var $uses = array('EmailAliasRequest','AccountStatus','UserLog','Gestao');
    var $layout = 'ajax';
    var $helpers = array('Html','Js');
    var $components = array('RequestHandler','Session');
	
    public function index(){
		$ldap = NULL;
		$role = ($this->isStudent) ? 1 : 0;
		$ldap = new ADLdap($role);
		$userinfo = $ldap->user_info($this->username);
		$utilizador = array(); 
		$this->set('role', $role);
		$utilizador['dominio'] = ($role==0) ? 'fc.ul.pt' : 'alunos.fc.ul.pt';
		$utilizador['email'] = $userinfo[0]['mail'][0];	
		$utilizador['email_fwd'] = NULL;

		//Verificar se ha pedidos de reencaminhamento
		$status_pedido_fwd = $this->getDefinedEmailForwardStatus();
		$status_pedido_fwd_ds = NULL;
		switch($status_pedido_fwd){
			case "-1":$utilizador['email_fwd'] = NULL; 
					  break;
			default : $utilizador['email_fwd'] = $this->getDefinedEmailForward();; 
					  break;
		}

		//Pré site gestao (sem registo na BD
		$utilizador['email_fwd_na_ad'] = isset($userinfo[0]['altrecipient'][0]) ? $userinfo[0]['altrecipient'][0] : NULL;
		
		if($utilizador['email_fwd_na_ad'] != $utilizador['email_fwd'] && $utilizador['email_fwd_na_ad']!=NULL)  {
			$sql = "INSERT INTO t_email_forward_requests (idstatus, comments, date_validation, date_request, samaccountname, localcopy, email_forward, student) VALUE (1, 'Pre-gestao. Adicionado automaticamente.', '".date('Y-m-d H:i:s')."', '".date('Y-m-d H:i:s')."', '".$this->username."',1, '".$utilizador['email_fwd_na_ad']."', '".(($this->isStudent) ? 1 : 0)."')";
			$this->Gestao->query($sql);
			$status_pedido_fwd = 1;
			$utilizador['email_fwd']  = $utilizador['email_fwd_na_ad'];
		}
		$this->set("status_pedido_fwd", $status_pedido_fwd);
		
		
		/*Aliases*/
		$aliases = $this->returnAliases($userinfo[0]['proxyaddresses']);
		$utilizador['aliases'] =  $aliases;
		$aliases_pendentes = $this->getPedidosAlias();
		$this->set("aliases_pendentes", $aliases_pendentes);
		
		$this->set("utilizador", $utilizador);
		
		
		/*ACL para arquivo mail*/
		
		$acesso_ao_arquivo = false;
		if($ldap->user_ingroup($userinfo[0]['samaccountname'][0], 'CI_Tecnicos')){
			$acesso_ao_arquivo = true;
			
		}
		
		$SQL_CHK = "SELECT * FROM t_arquivo_mail_2016 WHERE username = '".$this->username."'";
		$results_q = $this->Gestao->query($SQL_CHK);
		$this->set("arquivo_ativo", (count($results_q)>0));		
		$this->set("acesso_ao_aquivo", $acesso_ao_arquivo);
		
	}
	
	function ativarArquivoEmail() {
		$this->autoRender = false;
		
		$SQL_CHK = "SELECT * FROM t_arquivo_mail_2016 WHERE username = '".$this->username."'";
		$results_q = $this->Gestao->query($SQL_CHK);
		if(count($results_q)>0) {
			echo 'Já tem o arquivo de email ativo.';
		}else{
			$SQL_CHK = "INSERT INTO t_arquivo_mail_2016 (username, data) VALUES ('".$this->username."', '".date('Y-m-d H:i:s')."')";
			$results_q = $this->Gestao->query($SQL_CHK);
			echo 1;
		}
		
		
	}

	function edit(){	
		$this->autoRender = false;
		
		if(isset($_POST['type'])){
			$tipo = trim($_POST['type']);
			switch($tipo){
				case "fwd": if($this->isValidEmail($_POST['mail']))
								echo $this->defineEmailForward($_POST['mail'], $_POST['checkbox']); 
								//die('Vou alterar para '.$_POST['mail'].' check: '.($_POST['checkbox']));
							else
								echo 'Email não é válido';
							break;
				case "remove_fwd":
								echo $this->deleteEmailForward($_POST['mail']); 
								break;
								
				case "removerAlias": $resposta_del_alias = $this->removeAlias($_POST['alias']);
									 if(!$resposta_del_alias) 
									 	echo 'Não foi possível remover o alias'; 
									 else
										echo 1;
									 break;
				case "novoAlias":
				
				
				 $alias_a_adicionar = trim($_POST['alias']);
				 
				 if( empty( $alias_a_adicionar) ) {
					  die ('O alias pretendido não poder ser vazio.'); 
				  }
				  
				  if(preg_match('/^[a-z0-9.\-]+$/i', $alias_a_adicionar)) {
					  
				  }else
				 	 die ('O alias pretendido só pode ter números, letras e pontos.'); 
					 
				
				 //if (!ctype_alnum($alias_a_adicionar) ) {
				//	die ('O alias pretendido só pode ter números e letras'); 
				 //}
				 
				 echo $this->pedidoAlias($alias_a_adicionar); 
				 
				 break;
			}
		}
	}
	
	private function changeEmailAliasRequestState($requestId, $newStatus) {
        $newStatus = (int) $newStatus;
        //$whiteList = array(2, 4, 5);

		/*
        if (!$this->admin) {
            echo json_encode(array("success" => false, "msg" => "Acesso não autorizado."));
            die();
        }

        if (!in_array($newStatus, $whiteList)) {
            echo json_encode(array("success" => false, "msg" => "Novo estado desconhecido.")); 
            die();
        }
		*/
		
        $request = $this->EmailAliasRequest->find("first", array("conditions" => array("idrequest" => $requestId)));
		
        $request["EmailAliasRequest"]["idstatus"] = $newStatus;
        $request["EmailAliasRequest"]["date_validation"] = date("Y-m-d H:i:s");

        $alias = $request["EmailAliasRequest"]["email_alias_number"] == 1 ?
                $request["EmailAliasRequest"]["email_alias1"] :
                $request["EmailAliasRequest"]["email_alias2"];

        if ($newStatus == 2) {

            $ldap = new ADldap($request["EmailAliasRequest"]["student"]);

            $domain_1 = ($request["EmailAliasRequest"]["student"]) ? '@alunos.fc.ul.pt' : '@fc.ul.pt';

            $valAlias = explode("@", $alias);

            if ($request["EmailAliasRequest"]["student"] == NULL) {
                //echo json_encode(array("success" => false, "msg" => "Dominio do alias incorrecto."));
				//die();
				return array("success" => false, "msg" => "Dominio do alias incorrecto.");
            
            }

            if ($ldap->exchange_add_address($request["EmailAliasRequest"]["samaccountname"], $alias . $domain_1)) {

                //Adicionar também novos aliases ULISBOA
                $domain_ulisboa = ($request["EmailAliasRequest"]["student"]) ? '@alunos.ciencias.ulisboa.pt' : '@ciencias.ulisboa.pt';

                if ($ldap->exchange_add_address($request["EmailAliasRequest"]["samaccountname"], $alias . $domain_ulisboa)) {
                    $request["EmailAliasRequest"]["comments"] = "Aceite";
                } else {
                    //echo json_encode(array("success" => false, "msg" => "Erro alterar o alias (ULisboa)."));
                    //die();
					return array("success" => false, "msg" => "Erro alterar o alias (ULisboa).");
                }
            } else {
                //echo json_encode(array("success" => false, "msg" => "Erro alterar o alias."));
                //die();
				return array("success" => false, "msg" => "Erro alterar o alias.");
            }
        } else if ($newStatus == 5) {
            $request["EmailAliasRequest"]["comments"] = "Recusado";
        }

        if ($this->EmailAliasRequest->save($request["EmailAliasRequest"])) {
            $action = "Pedido de Email Alias " . $request["EmailAliasRequest"]["comments"] . " - :" . $request["EmailAliasRequest"]["samaccountname"];
            $action .= " email_alias[" . $request["EmailAliasRequest"]["email_alias_number"] . "]=" . $alias;
            $log["username"] = $this->username;
            $log["action"] = $action;
            $log["date"] = date("Y-m-d H:i:s");
            $this->UserLog->save($log);
			return array("success" => true);
            //echo json_encode(array("success" => true));
            //die();
        } else {
            //echo json_encode(array("success" => false, "msg" => "Erro ao actualizar o estado do pedido."));
            //die();
			return array("success" => false, "msg" => "Erro ao actualizar o estado do pedido.");
        }
    }
	
	
	
	
	
	/*ALIAS*/
	public function pedidoAlias($new_mail_alias){
		date_default_timezone_set('Europe/London');
		$date_request = date("Y-m-d H:i:s");
		$role = ($this->isStudent) ? 1 : 0;
		$dominio = 	($role==0) ? 'fc.ul.pt' : 'alunos.fc.ul.pt';
		if(!$this->aliasExiste($new_mail_alias."@".$dominio)){
			//Verificar se ja existe pedido!
			$SQL_CHK = "SELECT * FROM t_email_alias_requests WHERE samaccountname = '".$this->username."' AND idstatus in (1) AND (email_alias1 = '".$new_mail_alias."' OR email_alias2 = '".$new_mail_alias."')";
			$results_q = $this->Gestao->query($SQL_CHK);
			if(count($results_q)==0){
				$alias_number = -1;
				switch(count($this->returnAliases($this->username))){
					case 0: $alias1 = $new_mail_alias;
					        $alias2 = NULL; 
							$alias_number = 1; 
							if(count($this->getPedidosAlias())>1)
								return 'Não pode fazer mais pedidos de aliases.';
							break;
					case 1: $alias1 = NULL; 
							$alias2 = $new_mail_alias; 
							$alias_number = 2; 
							if(count($this->getPedidosAlias())>0)
								return 'Não pode fazer mais pedidos de aliases.';
							break;
					default: return 'Ja tem 2 aliases registados.'; break;
				}
				$student = $this->isStudent?1:0;
				
				$SQL_INSRT_RQST = "INSERT INTO t_email_alias_requests (idstatus, date_request, samaccountname, email_alias1, email_alias2, email_alias_number, student) VALUES (1, '".$date_request."', '".$this->username."', '".$alias1."', '".$alias2."', ".$alias_number.", ".$student.")";
				
				$result = $this->Gestao->query($SQL_INSRT_RQST);
				/* 	8 Mar 2018 - autovalidacao para contas FC */
				if(!$this->isStudent){
					/* validar automaticamente */
					$newStatus = 2;
					//get the inserted id
					
					$SQL_LAST_ID = "SELECT MAX(idrequest) as id FROM t_email_alias_requests WHERE idstatus = 1 AND samaccountname= '".$this->username."'";
					$result_id = $this->Gestao->query($SQL_LAST_ID);
					
					$requestId = is_array($result_id)?array_shift($result_id):NULL;
					$requestId = is_array($requestId)?$requestId[0]['id']:NULL;
					if($requestId != NULL){
						$this->changeEmailAliasRequestState($requestId, $newStatus);
					}
				}
				/* 	8 Mar 2018 - autovalidacao para contas FC - end */
			    return 1;
			}else{
				return 'Pedido j&aacute; existe.';
			}
		}else{
			return 'Alias n&atilde;o disponivel';
		}
	}
	
	public function removeAlias($alias){
		$pieces = explode("@", $alias);
		$username = $pieces[0];
		$domain_real = $pieces[1];
		
		$domain_ulisboa = ($domain_real=='fc.ul.pt') ? 'ciencias.ulisboa.pt' : 'alunos.ciencias.ulisboa.pt';
		
		try { 
			$role = ($this->isStudent) ? 1 : 0;
			$adldap = new ADLdap($role); 
		} 
		catch (adLDAPException $e) { echo $e; exit();  }
			
		try {			
			$result = $adldap->exchange_del_address($this->username, $alias);
			$result = $adldap->exchange_del_address($this->username, $username.'@'.$domain_ulisboa); 
			return $result;
		}
		catch (adLDAPException $e) {
			echo $e; exit();   
		}
		return FALSE;	
	}

	public function aliasExiste($new_mail_alias){
		$role = ($this->isStudent) ? 1 : 0;
		$ldap  = new ADLdap($role);	
		$found = $ldap->email_exists($new_mail_alias);
		return $found;
	}

	public function getPedidosAlias(){
		$sql = "SELECT idstatus,email_alias_number,email_alias1,email_alias2,student  FROM t_email_alias_requests WHERE samaccountname = '".$this->username."' AND idstatus in (1)"; 
		$results_q = $this->Gestao->query($sql);
		$aliases = array();
		for($i = 0; $i<count($results_q); $i++){
			$nr_alias = $results_q[$i]['t_email_alias_requests']['email_alias_number'];
			$aliases[$i] = array();
			$aliases[$i]['alias'] = $results_q[$i]['t_email_alias_requests']['email_alias'.$nr_alias].( ($results_q[$i]['t_email_alias_requests']['student']==1) ? '@alunos.fc.ul.pt' : '@fc.ul.pt');
			$aliases[$i]['estado'] = $results_q[$i]['t_email_alias_requests']['idstatus'];
		}
		return  $aliases;
	}
	
	public function returnAliases($proxy_add){
		$smtp_aliases_index = 0;
		$smtp_aliases = NULL;
			if(isset($proxy_add)){
				$emails = $proxy_add;
				$got_alias = FALSE;
				$principal_email = NULL;
				
				for ($i = 0; $i < $emails["count"]; $i++) {
					$pos   = strpos($emails[$i], ':');
					$smtp  = substr($emails[$i], 0, $pos);
					$email = substr($emails[$i], $pos + 1, strlen($emails[$i]));
					if (strcmp ($smtp, 'SMTP') == 0)
						$principal_email = $email;
					else {
						$pattern = NULL;
						
					    if($this->isStudent)
							$pattern =  '@alunos.fc.ul.pt';
						else 
							$pattern = '@fc.ul.pt';
						
						
						//php7 fix! 
						if(!function_exists('eregi'))          
						{ 
							function eregi($pattern, $subject, &$matches = []) { 
								return preg_match('/'.$pattern.'/i', $subject, $matches); 
							} 
						}

						
						if (strcmp ($smtp, 'smtp') == 0 && (eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*".$pattern."$", $email) == TRUE)) {
							$got_alias = TRUE;
							$smtp_aliases[$smtp_aliases_index++] = $email;
						}
					}
				}
			}
			return $smtp_aliases;
	}
	
	/*FWD*/
	public function getDefinedEmailForwardStatus($fwdemail = NULL){
		date_default_timezone_set('Europe/London');
		$date_request = date("Y-m-d H:i:s");
		$sql = "SELECT idstatus FROM t_email_forward_requests WHERE samaccountname = '".$this->username."' AND idstatus in (0,1,2)"; 
		$results_q = $this->Gestao->query($sql);
		if(count($results_q)>0)
			return $results_q[0]['t_email_forward_requests']['idstatus'];
		else 	
			return -1;
	}
	
	public function getDefinedEmailForward(){
		date_default_timezone_set('Europe/London');
		$date_request = date("Y-m-d H:i:s");
		$alias = '';			
		$sql = "SELECT email_forward FROM t_email_forward_requests WHERE samaccountname = '".$this->username."' AND idstatus in(0,1,2)";
		$results_q = $this->Gestao->query($sql);
		if (count($results_q)> 0) {
			 return $results_q[0]['t_email_forward_requests']['email_forward'];	
		}
		return NULL;			
	}			

	public function defineEmailForward($forwardEmail, $localcopy = 1){
		date_default_timezone_set('Europe/London');
		$date_request = date("Y-m-d H:i:s");
		$alias = '';	
		$student = 0;
		if($this->isStudent)
			$student = 1;
		$sql = "INSERT INTO t_email_forward_requests(samaccountname, date_request, email_forward, localcopy, student) ".
			   "VALUES('".$this->username."', '".$date_request."', '".$forwardEmail."', '".$localcopy."', ".$student.")";
		$results_q = $this->Gestao->query($sql); 
		return 1;			
	}

	public function deleteEmailForward($forwardEmail){
		date_default_timezone_set('Europe/London');
		$date_request = date("Y-m-d H:i:s");
		
		$sql = "SELECT email_forward FROM t_email_forward_requests WHERE samaccountname = '".$this->username."' AND email_forward = '".$forwardEmail."' AND idstatus = 1";
		$results_q = $this->Gestao->query($sql);

		if (count($results_q) > 0){
			$sql = "UPDATE t_email_forward_requests SET idstatus = 2 WHERE samaccountname = '".$this->username."' AND email_forward = '".$forwardEmail."' AND idstatus = 1";		
		}
		//insert new request to delete alias
		else{
			$student = 0;
			
			if($this->isStudent)
				$student = 1;
				
			$sql = "INSERT INTO t_email_forward_requests(samaccountname, date_request, email_forward, idstatus, student) ".
		   "VALUES('".$this->username."', '".$date_request."', '".$forwardEmail."', 2, ".$student.")";
		}	
		$results_q2 = $this->Gestao->query($sql);
		return 1;		
	}
	
	/*Helper*/
	public function isValidEmail( $email ){
   	 return filter_var( $email, FILTER_VALIDATE_EMAIL );
	}


	
	
	
}

?>