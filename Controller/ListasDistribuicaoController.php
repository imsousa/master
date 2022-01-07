<?php

class ListasDistribuicaoController extends AppController {
    
    var $name = 'ListasDistribuicao';
    var $uses = array('Gestao');
    var $layout = 'ajax';
    var $helpers = array('Html','Js');
    var $components = array('RequestHandler','Session');

	function index(){
		$array_para_view = array();
		
		$sql = "SELECT Newsletter.id, Newsletter.descricao, Newsletter.mandatory, Subs.option_id, Subs.id as subscription_id
					FROM t_newsletter as Newsletter
					LEFT JOIN t_newsletter_subscriptions AS Subs
						ON Newsletter.id = Subs.newsletter_id AND Subs.samaccountname='".$this->username."'";
		
		$newsletters = $this->Gestao->query($sql);
		for($i = 0; $i < count($newsletters); $i++){
			$array_para_view[$i] = $newsletters[$i];
			$sql = "SELECT * 
						FROM t_newsletter_available_options  
						JOIN t_newsletter_options
							ON t_newsletter_options.id = t_newsletter_available_options.option_id
						WHERE newsletter_id = ".$newsletters[$i]['Newsletter']['id'];
			$resultados_options = $this->Gestao->query($sql);
			$array_para_view[$i]['Options'] = $resultados_options;
		}
		
		$this->set("resultados", $array_para_view);
		
		//pr($newsletters);
		//pr($array_para_view);
	}
	
	function gravarAlteracoes(){
		$this->autoRender = false;
		$username = $this->username;
		foreach ($_POST["newsletter"] as $i => $newsletterId) {
			$newsletterId = (int) $newsletterId;
			$option = (int)$_POST["option"][$newsletterId];
			
			// Verificar se o estudante já esta subscrito a esta newsletter, 
			// caso esteja verificar se opção mudou e actualizar se necessario,
			// caso nao esteja adicionar subscription
			$sql = "SELECT * FROM t_newsletter_subscriptions 
							WHERE samaccountname='".$username."' 
							AND newsletter_id=$newsletterId";
							
			$result = $this->Gestao->query($sql);

			if (count($result) > 0) { // Ja está subscribed
				if ($result[0]['t_newsletter_subscriptions ']['option_id'] == $option) { 
					// Unchanged subscription
				} else {
					// Change subscription
					$sql = "UPDATE t_newsletter_subscriptions 
								SET option_id=$option 
								WHERE samaccountname='$username' 
								AND newsletter_id=$newsletterId";
					$this->Gestao->query($sql);
					}
			} else { 
				// Ainda nao esta subscribed, nova subscription
				// Add subscription
				$isStudent = ($this->isStudent) ? 1 : 0;
				$email = $this->fullUsername;
				$sql = "INSERT INTO t_newsletter_subscriptions(samaccountname, isStudent, newsletter_id, option_id, email)
							VALUES ('".$username."',$isStudent,$newsletterId,$option,'".stripslashes($email)."')";
				$this->Gestao->query($sql);
				}
		}
		// Find and remove newsletters the user unsubscribed
		$inNewsletters = "";
		if (count($_POST["newsletter"]) > 0) {
			$inNewsletters = "AND newsletter_id NOT IN (".implode(", ",$_POST["newsletter"]).")";
		}
		$sql = "DELETE FROM t_newsletter_subscriptions
						WHERE samaccountname='".$username."' 
						$inNewsletters";
		
		$this->Gestao->query($sql);
	
		echo 1;
	}
	
}

?>