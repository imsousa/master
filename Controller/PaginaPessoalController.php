<?php

App::import('Vendor', 'ldap', array('file' => 'ldap' . DS . 'adldap.php'));
App::import('Vendor', 'ad', array('file' => 'ldap' . DS . 'ad.php'));
App::import('Vendor', 'cas', array('file' => 'CAS-1.2.0'.DS.'CAS.php'));
App::import('Vendor', 'ora_conn', array('file' => 'Oraconn' . DS . 'ora_conn.php'));
App::import('Vendor', 'SharedSession', array('file' => 'SharedSession' . DS . 'SharedSession.php'));
App::uses('CakeEmail', 'Network/Email');
App::uses('Sanitize', 'Utility');

class PaginaPessoalController extends AppController {
    
    var $name = 'PaginaPessoal';
    var $uses = array('Tomadas', 
						'Pedidos', 
						'Courses', 
						'ExtraDetails', 
						'Ficheiro', 
						'Funcionarios', 
						'Keywords', 
						'SistemaInvestigacao', 
						'PerfilUnico', 
						'VinculosFuncionario', 
						'Carreiras', 
						'Categorias',
						'Gestao');
    var $layout = 'ajax';
    var $helpers = array('Html','Js');
    var $components = array('RequestHandler','Session');
	
	//ENVIA COMO PARAMETRO O USERNAME DA PESSOA A CUJA PÁGINA SE ESTÁ A ACEDER (OU ENVIA NULL CASO SE ESTEJA A EDITAR OS NOSSOS DADOS)
	public function beforeFilter() {
		
		// nao tem parametro
		//if(!(isset($this->request->params['pass']) && count($this->request->params['pass'])))
		
		if($this->request->params['action'] != 'showFile' && $this->request->params['action'] != 'meta' && $this->request->params['action'] != 'foto') {
			if(!(isset($this->request->params['pass']) && count($this->request->params['pass'])))
				parent::beforeFilter();
		}
			
	}
	
	
	public function meta() {
		$this->autoRender = false;
		$requested_uri = (isset($_REQUEST['requested_uri']) && trim($_REQUEST['requested_uri'])!=NULL) ? $_REQUEST['requested_uri'] : die();
		$pieces = explode("/",str_replace(array("/en/", "/pt/"),"/",$requested_uri));
		$username = strip_tags($pieces[2]); 
		$email_user = $pieces[2]."@fc.ul.pt"; 
		$resultado = $this->ExtraDetails->query("SELECT * FROM detalhes d WHERE d.bi = '".$email_user."'");
		$ldap = new ADLdap(0);
		$user_info = $ldap->user_info(($username));
		$nome_a_mostrar = (isset($user_info[0]["displayname"][0]) ? ($user_info[0]["displayname"][0]) : NULL);
		$cv_resumido = (isset($resultado[0]['d']['cv_resumido']) ? html_entity_decode($resultado[0]['d']['cv_resumido'],ENT_COMPAT,'UTF-8') : NULL);
		if(count($resultado)>0) {
			echo json_encode(array(
							  'foto' => '/servicos/perfil/PaginaPessoal/publicFoto/'.$email_user.'?action=foto', 
							  'url' => '/perfil/'.$username ,
							  'cv_resumido' => (($cv_resumido==NULL) ? 'Página Pessoal' : $cv_resumido),
					 		  'nome' =>  $nome_a_mostrar ));
		}
		//$extra = $this->extra_details($email_user);

	}
	
	public function publicFoto($username) {
		$this->autoRender = false;
		if($username != NULL) {
			$extra = $this->extra_details($username);
			if($extra != NULL && isset($extra['foto']) && is_array($extra['foto'])){
				$filename = basename($extra['foto']['name']);
				$file_extension = strtolower(substr(strrchr($filename,"."),1));

				switch( $file_extension ) {
					case "gif": $ctype="image/gif"; break;
					case "png": $ctype="image/png"; break;
					case "jpeg":
					case "jpg": $ctype="image/jpeg"; break;
					default:
				}
				header('Content-type: '. $ctype); 
				//header('Content-type:'.$extra['foto']['type']);
				print($extra['foto']['content']);
			}
			else{
				/*
				header('Content-Type: image/jpeg');
				$src = $_SERVER['DOCUMENT_ROOT'].'servicos/perfil/webroot/img/no-user.gif';
				readfile($src);
				exit();
				*/
				$file = $_SERVER['DOCUMENT_ROOT'].'/servicos/perfil/webroot/img/no-user.gif';
				ob_clean();
    			flush();
				echo readfile($file);
    			exit;
			}
		}
		else{
			$file = $_SERVER['DOCUMENT_ROOT'].'/servicos/perfil/webroot/img/no-user.gif';
				ob_clean();
    			flush();
				echo readfile($file);
    			exit;
		}
	}
	
	//ELIMINA FOTO/CV
	
	function deleteAnexo(){
		$this->autoRender = false;
		$tipo = $_REQUEST['type'];
		$id = $_REQUEST['val'];
		$campos_a_alterar =NULL;
		switch($tipo){
			case "foto": $campos_a_alterar = 'foto_file_id'; break;
			case "file": $campos_a_alterar = 'cv_file_id'; break;
		}
		$sql_find = "SELECT ".$campos_a_alterar."
					 FROM detalhes
					 WHERE ".$campos_a_alterar." = " .$id;
		$results =	$this->ExtraDetails->query($sql_find);
		if(count($results)>0){
			$sql2 = "UPDATE detalhes SET ".$campos_a_alterar." = NULL WHERE ".$campos_a_alterar." = " .$id;	
			$results2 =	$this->ExtraDetails->query($sql2);		
			$sql3 = "DELETE FROM ficheiros WHERE id = ".$results[0]['detalhes'][$campos_a_alterar];
			$results3 =	$this->ExtraDetails->query($sql3);
			return 1;
		}else{
			return "Erro a obter ficheiro para remover";
		}
	}
	
	
	/*
	//RETORNA A INFORMAÇÃO COM BASE NO USERNAME DO UTILIZADOR
	//
	*/
	public function index($uid = NULL) {
		//RECEBE USERNAME SE ESTIVER A SER VISUALISADA UMA PÁGINA PESSOAL POR UM VISITANTE AO SITE
		$utilizador = array();
		if($uid == NULL){//SE NÂO RECEBER O PARAMETRO DO USERNAME VAI VERIFICAR SE USER TEM LOGIN FEITO
			if($this->isLoggedIn){			
				$user_info = $this->fullDetailsAD;				
				if($this->isStudent) {
					die("Não tem permissões para visualização desta página");
				}
				// edita a pagina
				else{
					$visita = 0;
					$utilizador = $this->detalhesFunc($user_info);
					$this->set("utilizador", $utilizador);
					$this->set("visita", $visita);
					//var_dump($utilizador);
					//die();
				}		
			}
			else { 
				die("Não tem permissões para visualização desta página");
			}
		}
		else{ //VISITA-SE UMA PÁGINA
		
			//Verifica se a string enviada como parametro contem apenas letras
			if(Sanitize::clean($uid)) {
				$ldap = new ADLdap(0);
				$user_info = $ldap->user_info(strip_tags($uid));
				//Caso o id de Funcionário inserido não esteja correto dá mensagem de erro
				if($user_info['count'] == 0){
					echo '<div class="alert alert-danger" style=" margin-top:10px; margin-bottom:10px">
							<h4 class="alert-heading">Atenção!</h4>
							<ul><li>O funcionário da Faculdade de Ciências da Universidade de Lisboa que pretende consultar não existe, 
							pedimos que tente novamente, o nosso obrigado.</li></ul></div>';
					die();
				}
				$this->fullDetailsAD = $user_info;
				$visita = 1;
				$utilizador = $this->detalhesFunc($user_info);
				$this->set("utilizador", $utilizador);
				$this->set("visita", $visita);
			} else { //Existem caracteres especiais no parametro enviado, logo o username não está correto e pode ser uma tentativa de SQL injection
				echo '<div class="alert alert-danger" style=" margin-top:10px; margin-bottom:10px">
							<h4 class="alert-heading">Atenção!</h4>
							<ul><li>Nõo foi introduzido um username de funcionário válido, pedimos que tente novamente, o nosso obrigado.</li></ul></div>';
				die();
			}
		}
	}
	/*
	//
	*/
 	//
	
	//ACEDE E EDITA A PRÓPRIA PÁGINA

   function detalhesFunc($userinfo) {
	
	  //pr($userinfo);
	  //die();
	   
			$utilizador['nome_completo'] = isset($userinfo[0]["cn"][0]) ? utf8_encode($userinfo[0]["cn"][0]) : NULL;
			$utilizador['nome_a_mostrar'] = isset($userinfo[0]["displayname"][0]) ? utf8_encode($userinfo[0]["displayname"][0]) : NULL;
			$utilizador['username'] = $userinfo[0]['samaccountname'][0];
			$utilizador['mail'] = $userinfo[0]['mail'][0];
			//$utilizador['funcao'] = isset($userinfo[0]["title"][0]) ? utf8_encode($userinfo[0]["title"][0]) : NULL;
			$utilizador['unidade'] = isset($userinfo[0]["department"][0]) ? utf8_encode($userinfo[0]["department"][0]) : NULL;
			$utilizador['bi'] = isset($userinfo[0]["employeeid"][0]) /*&& is_numeric($userinfo[0]["employeeid"][0])*/ ? $userinfo[0]["employeeid"][0]:NULL;
			//$utilizador['data_criacao_conta'] = isset($userinfo[0]["whencreated"][0]) ? $this->contaCriadaEm($userinfo[0]["whencreated"][0]) : NULL;
			//$utilizador['data_expiracao_password'] = $this->passwordExpiraEm($userinfo[0]['samaccountname'][0]);
			$utilizador['contactos_sala'] = isset($userinfo[0]["physicaldeliveryofficename"][0]) ? utf8_decode($userinfo[0]["physicaldeliveryofficename"][0]):NULL;
			$utilizador['contactos_telefone_directo'] = isset($userinfo[0]["telephonenumber"][0]) ? $userinfo[0]["telephonenumber"][0] : NULL;
			/* Se só tiver uma extensão fica só a 0 */
			if(!isset($userinfo[0]['othertelephone'][1]) && isset($userinfo[0]['othertelephone'][0])){
				$utilizador['contactos_extensao_principal'] = $userinfo[0]['othertelephone'][0];
				$utilizador['contactos_extensao_alternativa'] = NULL;																
			}
			/* Se tiver duas, fica a extensao principal a 1*/
			else if(isset($userinfo[0]['othertelephone'][1]) && isset($userinfo[0]['othertelephone'][0])){
				$utilizador['contactos_extensao_principal'] = $userinfo[0]['othertelephone'][1];
				$utilizador['contactos_extensao_alternativa'] = $userinfo[0]['othertelephone'][0];																			
			}else{
				$utilizador['contactos_extensao_principal'] = NULL;
				$utilizador['contactos_extensao_alternativa'] = NULL;	
			} 
			//$utilizador['contactos_telemovel'] = isset($userinfo[0]["mobile"][0]) && $userinfo[0]['mobile'][0] !=NULL ? $userinfo[0]["mobile"][0] : ' ';
			$utilizador['contactos_telemovel'] = isset($userinfo[0]["mobile"][0]) ? $userinfo[0]["mobile"][0] : NULL;
			
			if(isset($userinfo[0]["homedrive"][0]) && isset($userinfo[0]["homedirectory"][0])){
				$drive = $userinfo[0]["homedrive"][0].$userinfo[0]["homedirectory"][0];
				$utilizador['armazenamento'] = $drive;
			}else
				$utilizador['armazenamento'] = ' ';
			
			$utilizador['mifare'] = isset($user[0]['extensionattribute14'][0]) ? $user[0]['extensionattribute14'][0] : NULL;
			//ALTERAÇÃO RUI BATISTA---------------------------
			//Vou buscar os dados que guardo na tabela de detalhes
			
			
			//ALTERAÇÃO RUI BATISTA----Full username dá erro quando as contas são ciencias.ulisboa.pt---- (outubro 2017)
			$user = explode("@",$utilizador['mail']);
			if($user[1] == "fc.ul.pt"){
				$newEmail = $utilizador['mail'];
			} else {
				$newEmail = $user[0]."@fc.ul.pt";
			}
			
			$resultado = $this->ExtraDetails->query("SELECT * FROM detalhes d WHERE d.bi = '".$newEmail."'");
			//ALTERAÇÃO RUI BATISTA---------------------------------------------------------------------- (outubro 2017)
			//$resultado = $this->ExtraDetails->query("SELECT * FROM detalhes d WHERE d.bi = '".$utilizador['mail']."'");
			
			$utilizador['pagina_pessoal'] = isset($resultado[0]['d']['pagina_pessoal']) ? utf8_encode($resultado[0]['d']['pagina_pessoal']) : NULL;
			$utilizador['cv_resumido'] = isset($resultado[0]['d']['cv_resumido']) ? utf8_encode($resultado[0]['d']['cv_resumido']) : NULL;
			$utilizador['interesses_cientificos'] = isset($resultado[0]['d']['interesses_cientificos']) ? utf8_encode($resultado[0]['d']['interesses_cientificos']) : NULL;
			$utilizador['scientific_interests'] = isset($resultado[0]['d']['scientific_interests']) ? utf8_encode($resultado[0]['d']['scientific_interests']) : NULL;
			$utilizador['pub1'] = isset($resultado[0]['d']['pub1']) ? utf8_encode($resultado[0]['d']['pub1']) : NULL;
			$utilizador['pub2'] = isset($resultado[0]['d']['pub2']) ? utf8_encode($resultado[0]['d']['pub2']) : NULL;
			$utilizador['pub3'] = isset($resultado[0]['d']['pub3']) ? utf8_encode($resultado[0]['d']['pub3']) : NULL;
			$utilizador['pub4'] = isset($resultado[0]['d']['pub4']) ? utf8_encode($resultado[0]['d']['pub4']) : NULL;
			$utilizador['pub5'] = isset($resultado[0]['d']['pub5']) ? utf8_encode($resultado[0]['d']['pub5']) : NULL;
			$utilizador['email_publico'] = isset($resultado[0]['d']['email_publico']) ? utf8_encode($resultado[0]['d']['email_publico']) : NULL;
			
			//EMAIL ALIAS------------------------
	   		$aliases = null;
	   		if(isset($userinfo[0]['proxyaddresses'])) {
				$aliases = $this->returnAliases($userinfo[0]['proxyaddresses']);
			}
			$utilizador['aliases'] = $aliases;
			
			/* $sql = "SELECT idstatus,email_alias_number,email_alias1,email_alias2,student  FROM t_email_alias_requests WHERE samaccountname = '".$utilizador['username']."' AND idstatus = 2"; 
		$results_q = $this->Gestao->query($sql);
		$utilizador['aliases'] = $results_q;*/
		
			//EMAIL ALIAS------------------------
			//pr($utilizador); die();
			//Vai buscar a informação do funcionário presente no CENSUS
			$user_name=$utilizador['username'];
			$idFuncionario = $this->PerfilUnico->query("SELECT MAX(f.funcionario_id)  AS id FROM t_perfil_unico_producao f WHERE f.username = '".$user_name."'");
			if (!isset($idFuncionario[0][0]['id'])){
				
				//Verificamos se é o próprio que está a aceder à sua página
				$drupalSession = new SharedSession();
        		$this->drupalUser = $drupalSession->user;
				if (!isset($_SESSION['ServicosCake']['isLoggedIn']) || !$_SESSION['ServicosCake']['isLoggedIn']) {
					//utilizador está logado daqui para baixo
		
					$fullUsername = $this->drupalUser->name;
		
					$_SESSION['ServicosCake']['fullUsername'] = $fullUsername;
					$_SESSION['ServicosCake']['username'] = $username;
				}
				$mail = explode("@", $this->drupalUser->name);
				$username = $mail[0];
				if (isset($this->drupalUser->roles[DRUPAL_ANONYMOUS_RID])) { // User is not logged in in drupal
					$username = "";
				}
				//echo "User Visitado: " . $user_name .  "<br>User a Visitar: " . $username;
				if(!strcmp($user_name,$username))	{		
					echo '<div class="alert alert-danger" style=" margin-top:10px; margin-bottom:10px">
							<h4 class="alert-heading">Atenção!</h4>
							<ul><li>A página pretendida não pode ser apresentada. Pode requerer a sua implementação através do seguinte email: <a href="mailto:projectos@ciencias.ulisboa.pt">projectos@ciencias.ulisboa.pt</a>, indicando o nome do utilizador que pretende visualizar</li></ul></div>';
					die();
				} else {
					echo '<div class="alert alert-danger" style=" margin-top:10px; margin-bottom:10px">
							<h4 class="alert-heading">Atenção!</h4>
							<ul><li>A página a que está a tentar aceder está temporariamente indisponível</li></ul></div>';
					die();
				}
			}
			
			//Verifica se user que existe no Perfil Único também existe nos Funcionários do Census
			$userExisteNoCensus = $this->Funcionarios->query("SELECT f.id FROM t_funcionarios f WHERE f.id =". $idFuncionario[0][0]['id']);
			//Vai buscar as Keywordse as palavras chave ao LOGOS
			if(count($idFuncionario) > 0 && isset($userExisteNoCensus[0]['f']['id'])) {
				$resultado = $this->Funcionarios->query("SELECT f.keywords, f.palavraschave FROM t_funcionarios f WHERE f.id =". $idFuncionario[0][0]['id']);
				//$resultado = $this->Funcionarios->query("SELECT f.keywords, f.palavraschave FROM t_funcionarios f WHERE f.id = 6544"); //Fernando Lopes
				if($resultado[0]['f']['keywords'] != ''){
					$utilizador['keywords']=$resultado[0]['f']['keywords'];
				} else {
					$utilizador['keywords']='';
				}
				if($resultado[0]['f']['palavraschave'] != ''){
					$utilizador['palavraschave']=$resultado[0]['f']['palavraschave'];
				} else {
					$utilizador['palavraschave']='';
				}
				//Vai buscar os Sistemas de Investigação
				$resultado = $this->Funcionarios->query("SELECT f.researcher_sys1_type_id, f.researcher_sys2_type_id, f.researcher_sys3_type_id,  f.researcher_sys4_type_id,f.researcher_sys1_id, f.researcher_sys2_id, f.researcher_sys3_id, f.researcher_sys4_id FROM t_funcionarios f WHERE f.id =". $idFuncionario[0][0]['id']);
				
				 
				 
				//ORCID
				if(($resultado[0]['f']['researcher_sys1_type_id'] != '') && ($resultado[0]['f']['researcher_sys1_type_id'] != 0) && ($resultado[0]['f']['researcher_sys1_type_id'] != NULL)){
					$utilizador['researcher_sys1_type_id']=$resultado[0]['f']['researcher_sys1_type_id'];
					//Designação
					$designacao = $this->SistemaInvestigacao->query("SELECT s.designacao FROM t_sistema_investigacao s WHERE s.id =". $utilizador['researcher_sys1_type_id']);
					$utilizador['researcher_sys1_designacao'] = $designacao[0]['s']['designacao'];
					//ORCID ID
					if(($resultado[0]['f']['researcher_sys1_id'] != '') && ($resultado[0]['f']['researcher_sys1_id'] != NULL)){
						$utilizador['researcher_sys1_id']=$resultado[0]['f']['researcher_sys1_id'];
					} else {
						$utilizador['researcher_sys1_id']='';
					}
				} else {
					$utilizador['researcher_sys1_type_id']='';
				}
				
				//RESEARCHER
				if(($resultado[0]['f']['researcher_sys2_type_id'] != '') && ($resultado[0]['f']['researcher_sys2_type_id'] != 0) && ($resultado[0]['f']['researcher_sys2_type_id'] != NULL)){
					$utilizador['researcher_sys2_type_id']=$resultado[0]['f']['researcher_sys2_type_id'];
					//Designação
					$designacao = $this->SistemaInvestigacao->query("SELECT s.designacao FROM t_sistema_investigacao s WHERE s.id =". $utilizador['researcher_sys2_type_id']);
					$utilizador['researcher_sys2_designacao'] = $designacao[0]['s']['designacao'];
					//RESEARCHER ID
					if(($resultado[0]['f']['researcher_sys2_id'] != '') && ($resultado[0]['f']['researcher_sys2_id'] != NULL)){
						$utilizador['researcher_sys2_id']=$resultado[0]['f']['researcher_sys2_id'];
					} else {
						$utilizador['researcher_sys2_id']='';
					}
				} else {
					$utilizador['researcher_sys2_type_id']='';
				}
				
				//SCOPUS
				if(($resultado[0]['f']['researcher_sys3_type_id'] != '') && ($resultado[0]['f']['researcher_sys3_type_id'] != 0) && ($resultado[0]['f']['researcher_sys3_type_id'] != NULL)){
					$utilizador['researcher_sys3_type_id']=$resultado[0]['f']['researcher_sys3_type_id'];
					//Designação
					$designacao = $this->SistemaInvestigacao->query("SELECT s.designacao FROM t_sistema_investigacao s WHERE s.id =". $utilizador['researcher_sys3_type_id']);
					$utilizador['researcher_sys3_designacao'] = $designacao[0]['s']['designacao'];
					//SCOPUSID
					if(($resultado[0]['f']['researcher_sys3_id'] != '') && ($resultado[0]['f']['researcher_sys3_id'] != NULL)){
						$utilizador['researcher_sys3_id']=$resultado[0]['f']['researcher_sys3_id'];
					} else {
						$utilizador['researcher_sys3_id']='';
					}
				} else {
					$utilizador['researcher_sys3_type_id']='';
				}
				
				//GOOGLE SCHOLAR
				if(($resultado[0]['f']['researcher_sys4_type_id'] != '') && ($resultado[0]['f']['researcher_sys4_type_id'] != 0) && ($resultado[0]['f']['researcher_sys4_type_id'] != NULL)){
					$utilizador['researcher_sys4_type_id']=$resultado[0]['f']['researcher_sys4_type_id'];
					//Designação
					$designacao = $this->SistemaInvestigacao->query("SELECT s.designacao FROM t_sistema_investigacao s WHERE s.id =". $utilizador['researcher_sys4_type_id']);
					$utilizador['researcher_sys4_designacao'] = $designacao[0]['s']['designacao'];
					//GOOGLE SCHOLAR
					if(($resultado[0]['f']['researcher_sys4_id'] != '') && ($resultado[0]['f']['researcher_sys4_id'] != NULL)){
						$utilizador['researcher_sys4_id']=$resultado[0]['f']['researcher_sys4_id'];
					} else {
						$utilizador['researcher_sys4_id']='';
					}
				} else {
					$utilizador['researcher_sys4_type_id']='';
				}
				
				//VAI BUSCAR A CARREIRA E A CATEGORIA
				
				$res = $this->VinculosFuncionario->query("SELECT carrera_id, categoria_id FROM t_vinculos_funcionario WHERE funcionario_id =". $idFuncionario[0][0]['id']." AND actual=1");
				if(count($res)> 0 && $res[0]['t_vinculos_funcionario']['carrera_id'] != NULL) {
					$carreira = $this->Carreiras->query("SELECT carreira FROM t_carreiras WHERE id =". $res[0]['t_vinculos_funcionario']['carrera_id']);
					if(count($carreira)>0) 
						$utilizador['carreira']= $carreira[0]['t_carreiras']['carreira'];
					else
						$utilizador['carreira'] = '';
				} else {
					$utilizador['carreira'] = '';
				}
				if(count($res)> 0 && $res[0]['t_vinculos_funcionario']['categoria_id'] != NULL) {
					$categoria = $this->Categorias->query("SELECT categoria FROM t_categorias_profissionais WHERE id =". $res[0]['t_vinculos_funcionario']['categoria_id']);
					if(count($categoria)>0) 
						$utilizador['categoria']= $categoria[0]['t_categorias_profissionais']['categoria'];
					else
						$utilizador['categoria'] = '';
				} else {
					$utilizador['categoria'] = '';
				}
				/*
				$utilizador['carreira']= $carreira[0]['t_carreiras']['carreira'];
				$utilizador['categoria']= $categoria[0]['t_categorias_profissionais']['categoria'];
				*/
				//pr($utilizador);
				//die();
				
			} else {
				//$mensagem = "Os dados do utilizador não estão atualizados. Contacte <a href='mailto:falopes@ciencias.ulisboa.pt'>falopes@ciencias.ulisboa.pt</a> e indique o seu username: <u>".$utilizador['username']."</u>";
				//die($mensagem);
				//$utilizador['mensagem']=$mensagem;
				$utilizador['keywords']='';
				$utilizador['palavraschave']='';
				$utilizador['researcher_sys1_type_id']='';
				$utilizador['researcher_sys1_id']='';
				$utilizador['researcher_sys2_type_id']='';
				$utilizador['researcher_sys2_id']='';
				$utilizador['researcher_sys3_type_id']='';
				$utilizador['researcher_sys3_id']='';
				$utilizador['researcher_sys4_type_id']='';
				$utilizador['researcher_sys4_id']='';
				$utilizador['carreira'] = '';
				$utilizador['categoria'] = '';
			}
			
			$utilizador['extra'] = $this->extra_details($utilizador['mail']);
			//pr($utilizador);
			//die();
			return $utilizador;
		}
	
	function extra_details($bi){
		
		//ALTERAÇÃO RUI BATISTA----Full username dá erro quando as contas são ciencias.ulisboa.pt---- (outubro 2017)
			$user = explode("@",$bi);
			if($user[1] == "fc.ul.pt"){
				$newBi = $bi;
			} else {
				$newBi = $user[0]."@fc.ul.pt";
			}
			
			$resultado = $this->ExtraDetails->query("SELECT * FROM detalhes d WHERE d.bi = '".$newBi."'");
			//$resultado = $this->ExtraDetails->query("SELECT * FROM detalhes d WHERE d.bi = '".$bi."'");
			//ALTERAÇÃO RUI BATISTA---------------------------------------------------------------------- (outubro 2017)

		$array_formatado = array();
		
		if(count($resultado)>0){
			$id_foto = $resultado[0]['d']['foto_file_id'];
			if($id_foto!=NULL){
				$resultado_foto = $this->ExtraDetails->query("SELECT * FROM ficheiros f WHERE f.id = ".$id_foto);
				if(count($resultado_foto)>0){
					$array_formatado['foto'] = $resultado_foto[0]['f'];
				}else{
					$array_formatado['foto'] = NULL;
				}	
			}
			else{
				$array_formatado['foto'] = NULL;
			}
			
			$id_cv = $resultado[0]['d']['cv_file_id'];
			
			if($id_cv!=NULL){
				$resultado_cv = $this->ExtraDetails->query("SELECT * FROM ficheiros f WHERE f.id = ".$id_cv);
				if(count($resultado_cv)>0){
					$array_formatado['cv'] = $resultado_cv[0]['f'];
				}else{
					$array_formatado['cv'] = NULL;
				}	
			}else{
				$array_formatado['cv'] = NULL;
			}
			
			return $array_formatado;
		}
		else return NULL;
	}
	
	function edit(){
		if(isset($_REQUEST['type'])){
			$tipo = Sanitize::clean($_REQUEST['type']);
			$val = NULL;
			if($_REQUEST['type'] == 'displayname') { //APRESENTA PROBLEMAS NOS EMAILS ENVIADOS
				if(isset($_REQUEST['val']))
					$val = $_REQUEST['val'];
			} else {
				if(isset($_REQUEST['val']))
					$val = Sanitize::clean(trim($_REQUEST['val']));
			}
			$ldap = NULL;
			$ldap = new ADLdap($this->role);
			$attr_to_change = '';
			
			switch($tipo){
			 case "mail": $attr_to_change = "extensionattribute13";  break;
			 //case "bi": $attr_to_change = "employeeid";  break;
			 case "telemovel": $attr_to_change = "mobile"; break;
			 case "sala": $attr_to_change = "physicaldeliveryofficename"; break;
			 case "tlf_direto": $attr_to_change = "telephonenumber"; break;
			 case "ext_principal": $this->definirExtensoes("principal", $val); return false;
			 case "ext_alt": $this->definirExtensoes("alt", $val); return false;
			 case "pergunta_resposta":$attr_to_change = "extensionattribute11"; break;
			 case "pergunta_resposta_2": $attr_to_change = "extensionattribute12"; break;
			 case "pin":  $attr_to_change = "extensionattribute10"; $val = $this->generateNewPin(); break;
			 case "password": $oldpassword = $_REQUEST['val']; $msg = $this->mudarPasswordControl($oldpassword, $_POST['new_pass']); echo $msg; return false;
			 case "foto_remove": echo $this->removerFicheiro("foto", $this->fullUsername);  return false; break;
			 case "cv_remove": echo $this->removerFicheiro("cv", $this->fullUsername); return false; break;
			 case "foto": 	$this->guardarFicheiroEAssociarAoPerfil("foto");
							//$this->enviarMail('changed_profile', 'Alteração de Perfil', $this->fullUsername);
							return false;
			//ALTERAÇÃO RUI BATISTA---------------------------
			case "pagina_pessoal": echo $this->editaDetalhes("pagina_pessoal", $this->fullUsername, $val); return false;
			case "cv_resumido": echo $this->editaDetalhes("cv_resumido", $this->fullUsername, $val); return false;
			case "interesses_cientificos": echo $this->editaDetalhes("interesses_cientificos", $this->fullUsername, $val); return false;
			case "scientific_interests": echo $this->editaDetalhes("scientific_interests", $this->fullUsername, $val); return false;
			case "pub1": echo $this->editaDetalhes("pub1", $this->fullUsername, $val); return false;
			case "pub2": echo $this->editaDetalhes("pub2", $this->fullUsername, $val); return false;
			case "pub3": echo $this->editaDetalhes("pub3", $this->fullUsername, $val); return false;
			case "pub4": echo $this->editaDetalhes("pub4", $this->fullUsername, $val); return false;
			case "pub5": echo $this->editaDetalhes("pub5", $this->fullUsername, $val); return false;
			case "email_publico": echo $this->editaDetalhes("email_publico", $this->fullUsername, $val); return false;
			case "displayname": echo $this->alterarNome("displayname", $this->fullUsername, $val); return false;
			//ALTERAÇÃO RUI BATISTA---------------------------			
			 case "cv":     $this->guardarFicheiroEAssociarAoPerfil("cv"); 
							//$this->enviarMail('changed_profile', 'Alteração de Perfil', $this->fullUsername);
							return false;
							
						
			}
			if($attr_to_change!=NULL){
				if(trim($val)=='' || trim($val)==NULL){
					$att[$attr_to_change] = '  ';
					$ldap->user_modify($this->username, $att);
					echo 1;
				}else{
					$attrs[$attr_to_change] = $val; 
					$response = $ldap->user_modify($this->username, $attrs);
					if ($response == TRUE) {
						if($tipo == 'pin'){
							echo $val;
							$mail = isset($this->ADUser[0]["extensionattribute13"]) ? $this->ADUser[0]["extensionattribute13"][0] : 
								(isset($this->ADUser[0]["altrecipient"]) ? $this->ADUser[0]["altrecipient"][0] : NULL);
							//$this->enviarMail('changed_profile_pin', 'Alteração de Perfil - Novo PIN', array($this->fullUsername,$mail), array("pin" => $val));
						}else{
							echo 1;
							//$this->enviarMail('changed_profile', 'Alteração de Perfil', $this->fullUsername);
						}
						//$this->enviarMail('changed_profile', 'Alteração de Perfil', $this->fullUsername);
					}else {
						echo -1;
					}
				}
			}
		}
			
	}
	/*
	function enviarMail($template, $subject, $to, $viewVars = array()){
		$email = new CakeEmail('default');
		$email->template($template)
			  ->viewVars($viewVars)
			  ->emailFormat('html')
			  ->subject($subject)
			  ->to($to)
			 ->send();
	}
	*/
	function definirExtensoes($tipo, $val){
		$ldap = new ADLdap($this->role);
		$userinfo = $ldap->user_info($this->username);
		$attrs = NULL;
		
		if($tipo == 'principal'){
			//Se não houver posicao 1, a extensao principal está na prosicao 0
			if(!isset($userinfo[0]['othertelephone'][1]) && trim($userinfo[0]['othertelephone'][1])==NULL){
				if(trim($val) == '' || trim($val) == NULL)
					$val = "  ";
				$attrs['othertelephone'][0] = $val; 
			}
			//Ja existe ext alternativa e principal, é só mudar a posicao 1;,
			else if(trim($userinfo[0]['othertelephone'][0])!=NULL && isset($userinfo[0]['othertelephone'][1]) && trim($userinfo[0]['othertelephone'][1])!=NULL){
				/*if(trim($val) == '' || trim($val) == NULL){
					$attrs['othertelephone'][0] = $userinfo[0]['othertelephone'][0];
					$attrs['othertelephone'][1] = "  ";
				}else{*/
					//echo 'VAL'.$val.'<br>';
					$attrs['othertelephone'][0] = $val;
						//$attrs['othertelephone'][1] = $userinfo[0]['othertelephone'][0];
					//pr($attrs['othertelephone']);
				
				/*}*/
			}else{
				//apagar extensao principal
				$attrs['othertelephone'][0] = " ";
			}
		}
		else if ($tipo == 'alt'){
			if(isset($userinfo[0]['othertelephone'][1]) && trim($userinfo[0]['othertelephone'][1])!=NULL){
				//Ja existe extensao principal e Alternativa; Alterar valor da posicao 0;
				if(trim($val) == '' || trim($val) == NULL){
					$attrs['othertelephone'][0] = $userinfo[0]['othertelephone'][1];
					$attrs['othertelephone'][1] = " ";
				}else{
					$attrs['othertelephone'][0] = $val; 
					$attrs['othertelephone'][1] = $userinfo[0]['othertelephone'][1];
				}			
			} else if(isset($userinfo[0]['othertelephone'][0]) && !isset($userinfo[0]['othertelephone'][1]) && trim($userinfo[0]['othertelephone'][1])==NULL){
				//Ja existe extensao principal; Passar extensao principal para a posicao 1 e escrever a ext alternativa a na posicao 1; 
				if(trim($val) != '' && trim($val) !=NULL) {
					$attrs['othertelephone'][1] = $userinfo[0]['othertelephone'][0]; 
					$attrs['othertelephone'][0] = $val; 
				}
			}	
		}	
		
		if($attrs != NULL){		
			$response = $ldap->user_modify($this->username, $attrs);
			if ($response == TRUE) {
				echo 1;
				//$this->enviarMail('changed_profile', 'Alteração de Perfil', $this->fullUsername);
			}
		}
		
		
		
		
	}
	//ALTERAÇÃO RUI BATISTA-----------------------------------
	function editaDetalhes($tipo, $username, $val){
		$campos_a_alterar =NULL;
		switch($tipo){
			case "pagina_pessoal": $campos_a_alterar = 'pagina_pessoal'; break;
			case "cv_resumido": $campos_a_alterar = 'cv_resumido'; break;
			case "interesses_cientificos": $campos_a_alterar = 'interesses_cientificos'; break;
			case "scientific_interests": $campos_a_alterar = 'scientific_interests'; break;
			case "pub1": $campos_a_alterar = 'pub1'; break;
			case "pub2": $campos_a_alterar = 'pub2'; break;
			case "pub3": $campos_a_alterar = 'pub3'; break;
			case "pub4": $campos_a_alterar = 'pub4'; break;
			case "pub5": $campos_a_alterar = 'pub5'; break;
			case "email_publico": $campos_a_alterar = 'email_publico'; break;
		}
		
		//ALTERAÇÃO RUI BATISTA----Full username dá erro quando as contas são ciencias.ulisboa.pt---- (outubro 2017)
		$user = explode("@",$username);
			if($user[1] == "fc.ul.pt"){
				$newUsername = $username;
			} else {
				$newUsername = $user[0]."@fc.ul.pt";
			}
			
		$sql_find = "SELECT ".$campos_a_alterar."
					 FROM detalhes
					 WHERE bi = '".$newUsername."'";
		$results =	$this->ExtraDetails->query($sql_find);
		
		if(count($results)>0){ //SE JÁ EXISTE ENTRADA PARA ESTE USER
			$sql2 = "UPDATE detalhes SET ".$campos_a_alterar." = '".$val."' WHERE bi = '".$newUsername."'";	
			$results2 =	$this->ExtraDetails->query($sql2);
			return 1;
		} else { //SE NÃO EXISTIR ENTRADA PARA ESTE USER CRIA-SE NOVA ENTRADA
			$sql3 = "INSERT INTO detalhes (bi, ".$campos_a_alterar.") VALUES ('".$newUsername."', '".$val."')";	
			$results3 = $this->ExtraDetails->query($sql3);
			return 1;
		}
		
		
		//ALTERAÇÃO RUI BATISTA---------------------------------------------------------------------- (outubro 2017)
		
		/*
		$sql_find = "SELECT ".$campos_a_alterar."
					 FROM detalhes
					 WHERE bi = '".$username."'";
		$results =	$this->ExtraDetails->query($sql_find);
		
		if(count($results)>0){ //SE JÁ EXISTE ENTRADA PARA ESTE USER
			$sql2 = "UPDATE detalhes SET ".$campos_a_alterar." = '".$val."' WHERE bi = '".$username."'";	
			$results2 =	$this->ExtraDetails->query($sql2);
			return 1;
		/*}else{
			return "Erro a apagar o campo " . $campos_a_alterar;*/ /*
		} else { //SE NÃO EXISTIR ENTRADA PARA ESTE USER CRIA-SE NOVA ENTRADA
			$sql3 = "INSERT INTO detalhes (bi, ".$campos_a_alterar.") VALUES ('".$username."', '".$val."')";	
			$results3 = $this->ExtraDetails->query($sql3);
			return 1;
		}
		*/
	}
	
	//ALTERAÇÃO RUI BATISTA-----------------------------------
	function alterarNome($tipo, $username, $val){
		$user = explode("@",$username);
		
		$ldap = new ADLdap(0);
		$res_modify = $ldap->user_modify($user[0], array($tipo => $val));	
		
		if($res_modify) //correu bem
			echo 1;
		else //correu mal
			echo 'Ocorreu um erro.';
	}
	
	//ALTERAÇÃO RUI BATISTA------------------------------------
	
	function removerFicheiro($tipo, $username){
		$campos_a_alterar =NULL;
		switch($tipo){
			case "foto": $campos_a_alterar = 'foto_file_id'; break;
			case "cv": $campos_a_alterar = 'cv_file_id'; break;
		}
		//ALTERAÇÃO RUI BATISTA----Full username dá erro quando as contas são ciencias.ulisboa.pt---- (outubro 2017)
		$user = explode("@",$username);
			if($user[1] == "fc.ul.pt"){
				$newUsername = $username;
			} else {
				$newUsername = $user[0]."@fc.ul.pt";
			}
		$sql_find = "SELECT ".$campos_a_alterar."
					 FROM detalhes
					 WHERE bi = '".$newUsername."' AND ".$campos_a_alterar." is not NULL";
		/*
		$sql_find = "SELECT ".$campos_a_alterar."
					 FROM detalhes
					 WHERE bi = '".$username."' AND ".$campos_a_alterar." is not NULL";*/
		$results =	$this->ExtraDetails->query($sql_find);
		if(count($results)>0){
			$sql2 = "UPDATE detalhes SET ".$campos_a_alterar." = NULL WHERE bi = '".$newUsername."'";
			//$sql2 = "UPDATE detalhes SET ".$campos_a_alterar." = NULL WHERE bi = '".$username."'";	
		//ALTERAÇÃO RUI BATISTA---------------------------------------------------------------------- (outubro 2017)
			$results2 =	$this->ExtraDetails->query($sql2);		
			$sql3 = "DELETE FROM ficheiros WHERE id = ".$results[0]['detalhes'][$campos_a_alterar];
			$results3 =	$this->ExtraDetails->query($sql3);
			return 1;
		}else{
			return "Erro a obter foto para remover";
		}
	}
	
	function guardarFicheiroEAssociarAoPerfil($tipo){
		$this->removerFicheiro($tipo, $this->fullUsername);
		$campo_a_alterar =NULL;
		switch($tipo){
			case "foto": $campo_a_alerar = 'foto_file_id'; break;
			case "cv": $campo_a_alerar = 'cv_file_id'; break;
		}
		
		if (isset($_REQUEST['qqfile'])){
			$input = fopen("php://input", "r");
			$f_name = $_REQUEST['qqfile'];
			$f_type = $_SERVER['CONTENT_TYPE'];
			$f_content = stream_get_contents($input);
			$f_size = $_SERVER['CONTENT_LENGTH'];
		}
		if (isset($_FILES['qqfile']))	{
			//var_dump($_FILES);
			$input = fopen($_FILES['qqfile']['tmp_name'], "r");
			$f_name = $_FILES['qqfile']['name'];
			$f_type = $_FILES['qqfile']['type'];
			$f_content = stream_get_contents($input);
			$f_size = $_FILES['qqfile']['size'];
		}

		fclose($input);
		if($f_size < 2097152/* 1023222*/){
			$bi = $this->fullUsername;
			if($bi != NULL){
				//Upload do ficheiro
				$ficheiro = array();
				$ficheiro['name'] = $f_name;
				$ficheiro['type'] = $f_type;
				$ficheiro['size'] = $f_size;
				$ficheiro['content'] = $f_content;
				
				//ALTERAÇÃO RUI BATISTA----Full username dá erro quando as contas são ciencias.ulisboa.pt---- (outubro 2017)
				$user = explode("@",$bi);
				if($user[1] == "fc.ul.pt"){
					$newBi = $bi;
				} else {
					$newBi = $user[0]."@fc.ul.pt";
				}
				
					$this->Ficheiro->save($ficheiro);
				//ir buscar o ID do ficheiro que foi feito upload
				$id_ficheiro = $this->getIdFromFile($f_name, $f_size, $f_type);
				if($id_ficheiro!=NULL){
					//Associar ao perfil_ver se ja existe na tabela
					//$result_perfil_details = $this->ExtraDetails->query("SELECT * FROM detalhes WHERE bi = '".$bi."'");
					$result_perfil_details = $this->ExtraDetails->query("SELECT * FROM detalhes WHERE bi = '".$newBi."'");
					if(count($result_perfil_details)>0){
						$sql = "UPDATE detalhes SET ".$campo_a_alerar." = ".$id_ficheiro." WHERE bi='".$newBi."'";
						//$sql = "UPDATE detalhes SET ".$campo_a_alerar." = ".$id_ficheiro." WHERE bi='".$bi."'";	
														
					}else{
						$sql = "INSERT INTO detalhes (bi, ".$campo_a_alerar.") VALUES ('".$newBi."', '".$id_ficheiro."')";	
						//$sql = "INSERT INTO detalhes (bi, ".$campo_a_alerar.") VALUES ('".$bi."', '".$id_ficheiro."')";	
					}
					$this->ExtraDetails->query($sql);
					
					echo json_encode(array(1 =>'SUCC', 'success' =>true));
					//$this->enviarMail('changed_profile', 'Alteração de Perfil', $this->fullUsername);
				}else{
					die(json_encode(array(1 =>'FAIL')));
				}
			}else
				die(json_encode(array(1 =>'FAIL')));
		}else{
			die(json_encode(array(1 =>'FAIL')));
		}
		//ALTERAÇÃO RUI BATISTA----------------------------------------------------------------------------- (outubro 2017)				
	}
	
	function getIdFromFile($name, $size, $type){
		$sql_find = "SELECT * FROM ficheiros WHERE name='".$name."' and size=".$size." and type='".$type."'";
		$results =	$this->ExtraDetails->query($sql_find);
		if(count($results)>0){
			return $results[0]['ficheiros']['id'];
		}else{
			return NULL;
		}
	}
	
	function showFile(){
		$this->layout = 'ajax';
		if(isset($_GET['id']) && isset($_GET['tipo'])){
			$campo = NULL;
			switch(Sanitize::clean($_GET['tipo'])){
				case "foto": $campo = 'd.foto_file_id'; break;
				case "cv": $campo = 'd.cv_file_id'; break;
			}
			$bi = $_GET['user'];
			if($bi != NULL && $campo !=NULL){
				$id = Sanitize::clean($_GET['id']);
				//ALTERAÇÃO RUI BATISTA----Full username dá erro quando as contas são ciencias.ulisboa.pt---- (outubro 2017)
				$user = explode("@",$bi);
				if($user[1] == "fc.ul.pt"){
					$newBi = $bi;
				} else {
					$newBi = $user[0]."@fc.ul.pt";
				}
				$sql = "SELECT * FROM detalhes d WHERE d.bi = '".$newBi."' AND  ".$campo." = ".$id;
				//$sql = "SELECT * FROM detalhes d WHERE d.bi = '".$bi."' AND  ".$campo." = ".$id;
				//ALTERAÇÃO RUI BATISTA----------------------------------------------------------------------------- (outubro 2017)	
				$resultado = $this->ExtraDetails->query($sql );						 										
				if(count($resultado)>0){
					$resultado_ficheiro = $this->ExtraDetails->query("SELECT * FROM ficheiros f WHERE f.id = ".$id);
					if(count($resultado_ficheiro)>0){
						$this->set('name', $resultado_ficheiro[0]['f']['name']);
						$this->set('type', $resultado_ficheiro[0]['f']['type']);
						$this->set('size', $resultado_ficheiro[0]['f']['size']);
						$this->set('content', $resultado_ficheiro[0]['f']['content']);
					}
				}
			}
		}
	}
	
	
	//ALIAS
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
	
}
 

?>