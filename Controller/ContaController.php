<?php


App::import('Vendor', 'ora_conn', array('file' => 'Oraconn' . DS . 'ora_conn.php'));

App::uses('CakeEmail', 'Network/Email');
App::uses('Sanitize', 'Utility');

class ContaController extends AppController
{

	var $name = 'Conta';
	var $uses = array('ExtraDetails', 'Ficheiro', 'Gestao');
	var $layout = 'ajax';
	var $helpers = array('Html', 'Js');
	var $components = array('RequestHandler', 'Session');

	public function index()
	{

		$user_info = $this->fullDetailsAD;

		$utilizador = array();
		if ($this->isStudent) {
			$utilizador = $this->detalhesStudent($user_info);
		} else {
			$utilizador = $this->detalhesFunc($user_info);
		}


		$grupos = (isset($user_info[0]['memberof'])) ? $user_info[0]['memberof'] : NULL;

		$grupos_formatado = array();
		for ($i = 0; $i < count($grupos) - 1; $i++) {
			//Ver só Grupos dentro da OU=GRUPOS. - hamiranda

			//echo 'A analisar..'.$grupos[$i].'<br>';

			if (strstr($grupos[$i], ",OU=Grupos")) {
				$pieces = explode(",", $grupos[$i]);
				$final = str_replace("CN=", "", $pieces[0]);
				$res = $this->ldap->group_info($final);
				$description_of_group = (isset($res[0]['description'])) ? $res[0]['description'][0] : NULL;
				array_push($grupos_formatado, array(
					'nome' => $final,
					'descricao' => $description_of_group
				));
			}
		}

		$this->set("grupos", $grupos_formatado);

		$this->set("estudante", $this->isStudent);

		$this->set("utilizador", $utilizador);
	}




	function detalhesFunc($userinfo)
	{

		$utilizador['nome_completo'] = isset($userinfo[0]["cn"][0]) ? ($userinfo[0]["cn"][0]) : NULL;
		$utilizador['nome_a_mostrar'] = isset($userinfo[0]["displayname"][0]) ? ($userinfo[0]["displayname"][0]) : NULL;
		$utilizador['username'] = $userinfo[0]['samaccountname'][0];
		$utilizador['mail'] = $userinfo[0]['mail'][0];
		$utilizador['funcao'] = isset($userinfo[0]["title"][0]) ? ($userinfo[0]["title"][0]) : NULL;
		$utilizador['unidade'] = isset($userinfo[0]["department"][0]) ? ($userinfo[0]["department"][0]) : NULL;
		$utilizador['bi'] = isset($userinfo[0]["employeeid"][0]) /*&& is_numeric($userinfo[0]["employeeid"][0])*/ ? $userinfo[0]["employeeid"][0] : NULL;
		$utilizador['data_criacao_conta'] = isset($userinfo[0]["whencreated"][0]) ? $this->contaCriadaEm($userinfo[0]["whencreated"][0]) : NULL;
		$utilizador['data_expiracao_password'] = $this->passwordExpiraEm($userinfo[0]['samaccountname'][0]);
		$utilizador['contactos_sala'] = isset($userinfo[0]["physicaldeliveryofficename"][0]) ? utf8_decode($userinfo[0]["physicaldeliveryofficename"][0]) : NULL;
		$utilizador['contactos_telefone_directo'] = isset($userinfo[0]["telephonenumber"][0]) ? $userinfo[0]["telephonenumber"][0] : NULL;
		/* Se só tiver uma extensão fica só a 0 */
		if (!isset($userinfo[0]['othertelephone'][1]) && isset($userinfo[0]['othertelephone'][0])) {
			$utilizador['contactos_extensao_principal'] = $userinfo[0]['othertelephone'][0];
			$utilizador['contactos_extensao_alternativa'] = NULL;
		}
		/* Se tiver duas, fica a extensao principal a 1*/ else if (isset($userinfo[0]['othertelephone'][1]) && isset($userinfo[0]['othertelephone'][0])) {
			$utilizador['contactos_extensao_principal'] = $userinfo[0]['othertelephone'][1];
			$utilizador['contactos_extensao_alternativa'] = $userinfo[0]['othertelephone'][0];
		} else {
			$utilizador['contactos_extensao_principal'] = NULL;
			$utilizador['contactos_extensao_alternativa'] = NULL;
		}
		$utilizador['contactos_telemovel'] = isset($userinfo[0]["mobile"][0]) && $userinfo[0]['mobile'] != NULL ? $userinfo[0]["mobile"][0] : ' ';

		if (isset($userinfo[0]["homedrive"][0]) && isset($userinfo[0]["homedirectory"][0])) {
			$drive = $userinfo[0]["homedrive"][0] . $userinfo[0]["homedirectory"][0];
			$utilizador['armazenamento'] = $drive;
		} else
			$utilizador['armazenamento'] = ' ';

		$utilizador['mifare'] = isset($user[0]['extensionattribute14'][0]) ? $user[0]['extensionattribute14'][0] : NULL;

		$utilizador['extra'] = $this->extra_details($this->fullUsername);

		return $utilizador;
	}

	function detalhesStudent($userinfo)
	{
		$utilizador['nome_completo'] = isset($userinfo[0]["cn"][0]) ? ($userinfo[0]["cn"][0]) : NULL;
		$utilizador['nome_a_mostrar'] = isset($userinfo[0]["displayname"][0]) ? ($userinfo[0]["displayname"][0]) : NULL;
		$utilizador['username'] = $userinfo[0]['samaccountname'][0];
		$utilizador['mail'] = $userinfo[0]['mail'][0];
		$utilizador['funcao'] = isset($userinfo[0]["title"][0]) ? ($userinfo[0]["title"][0]) : NULL;
		$utilizador['unidade'] = isset($userinfo[0]["department"][0]) ? ($userinfo[0]["department"][0]) : NULL;
		$utilizador['cd_aluno'] = isset($userinfo[0]["employeenumber"][0]) && is_numeric($userinfo[0]["employeenumber"][0]) ? $userinfo[0]["employeenumber"][0] : NULL;
		$utilizador['bi'] = isset($userinfo[0]["employeeid"][0]) && is_numeric($userinfo[0]["employeeid"][0]) ? $userinfo[0]["employeeid"][0] : NULL;
		$utilizador['data_criacao_conta'] = isset($userinfo[0]["whencreated"][0]) ? $this->contaCriadaEm($userinfo[0]["whencreated"][0]) : NULL;
		$utilizador['data_expiracao_password'] = $this->passwordExpiraEm($userinfo[0]['samaccountname'][0]);
		if (isset($userinfo[0]["homedrive"][0]) && isset($userinfo[0]["homedirectory"][0])) {
			$drive = $userinfo[0]["homedrive"][0] . $userinfo[0]["homedirectory"][0];
			$utilizador['armazenamento'] = $drive;
		} else
			$utilizador['armazenamento'] = NULL;



		$utilizador['mifare'] = isset($userinfo[0]['extensionattribute14'][0]) ? $userinfo[0]['extensionattribute14'][0] : NULL;



		$utilizador['mecanismo_recuperacao_pergunta'] = isset($userinfo[0]["extensionattribute11"][0]) ? $userinfo[0]["extensionattribute11"][0] : NULL;
		$utilizador['mecanismo_recuperacao_resposta'] = isset($userinfo[0]["extensionattribute12"][0]) ? $userinfo[0]["extensionattribute12"][0] : NULL;
		$utilizador['mecanismo_recuperacao_email'] = isset($userinfo[0]["extensionattribute13"][0]) ? $userinfo[0]["extensionattribute13"][0] : NULL;

		return $utilizador;
	}

	function extra_details($bi)
	{

		$resultado = $this->ExtraDetails->query("SELECT * FROM detalhes d WHERE d.bi = '" . $bi . "'");

		$array_formatado = array();
		if (count($resultado) > 0) {
			$id_foto = $resultado[0]['d']['foto_file_id'];
			if ($id_foto != NULL) {
				$resultado_foto = $this->ExtraDetails->query("SELECT * FROM ficheiros f WHERE f.id = " . $id_foto);
				if (count($resultado_foto) > 0) {
					$array_formatado['foto'] = $resultado_foto[0]['f'];
				} else {
					$array_formatado['foto'] = NULL;
				}
			} else {
				$array_formatado['foto'] = NULL;
			}
			$id_cv = $resultado[0]['d']['cv_file_id'];

			if ($id_cv != NULL) {
				$resultado_cv = $this->ExtraDetails->query("SELECT * FROM ficheiros f WHERE f.id = " . $id_cv);
				if (count($resultado_cv) > 0) {
					$array_formatado['cv'] = $resultado_cv[0]['f'];
				} else {
					$array_formatado['cv'] = NULL;
				}
			} else {
				$array_formatado['cv'] = NULL;
			}
			return $array_formatado;
		} else
			return NULL;
	}

	function removerFicheiro($tipo, $username)
	{
		$campos_a_alterar = NULL;
		switch ($tipo) {
			case "foto":
				$campos_a_alterar = 'foto_file_id';
				break;
			case "cv":
				$campos_a_alterar = 'cv_file_id';
				break;
		}
		$sql_find = "SELECT " . $campos_a_alterar . "
					 FROM detalhes
					 WHERE bi = '" . $username . "' AND " . $campos_a_alterar . " is not NULL";
		$results =	$this->ExtraDetails->query($sql_find);
		if (count($results) > 0) {
			$sql2 = "UPDATE detalhes SET " . $campos_a_alterar . " = NULL WHERE bi = '" . $username . "'";
			$results2 =	$this->ExtraDetails->query($sql2);
			$sql3 = "DELETE FROM ficheiros WHERE id = " . $results[0]['detalhes'][$campos_a_alterar];
			$results3 =	$this->ExtraDetails->query($sql3);
			return 1;
		} else {
			return "Erro a obter foto para remover";
		}
	}

	function deleteAnexo()
	{
		$this->autoRender = false;
		$tipo = $_REQUEST['type'];
		$id = $_REQUEST['val'];
		$campos_a_alterar = NULL;
		switch ($tipo) {
			case "foto":
				$campos_a_alterar = 'foto_file_id';
				break;
			case "file":
				$campos_a_alterar = 'cv_file_id';
				break;
		}
		$sql_find = "SELECT " . $campos_a_alterar . "
					 FROM detalhes
					 WHERE " . $campos_a_alterar . " = " . $id;
		$results =	$this->ExtraDetails->query($sql_find);
		if (count($results) > 0) {
			$sql2 = "UPDATE detalhes SET " . $campos_a_alterar . " = NULL WHERE " . $campos_a_alterar . " = " . $id;
			$results2 =	$this->ExtraDetails->query($sql2);
			$sql3 = "DELETE FROM ficheiros WHERE id = " . $results[0]['detalhes'][$campos_a_alterar];
			$results3 =	$this->ExtraDetails->query($sql3);
			return 1;
		} else {
			return "Erro a obter ficheiro para remover";
		}
	}

	function guardarFicheiroEAssociarAoPerfil()
	{

		$tipo = $_REQUEST['tipo_ficheiro'];

		$this->autoRender = false;
		$this->removerFicheiro($tipo, $this->fullUsername);
		$campo_a_alterar = NULL;
		switch ($tipo) {
			case "foto":
				$campo_a_alerar = 'foto_file_id';
				break;
			case "cv":
				$campo_a_alerar = 'cv_file_id';
				break;
		}

		if (isset($_REQUEST['qqfile'])) {
			$input = fopen("php://input", "r");
			$f_name = $_REQUEST['qqfile'];
			$f_type = $_SERVER['CONTENT_TYPE'];
			$f_content = stream_get_contents($input);
			$f_size = $_SERVER['CONTENT_LENGTH'];
		}
		if (isset($_FILES['qqfile'])) {
			//var_dump($_FILES);
			$input = fopen($_FILES['qqfile']['tmp_name'], "r");
			$f_name = $_FILES['qqfile']['name'];
			$f_type = $_FILES['qqfile']['type'];
			$f_content = stream_get_contents($input);
			$f_size = $_FILES['qqfile']['size'];
		}

		fclose($input);
		if ($f_size < 2097152/* 1023222*/) {
			$bi = $this->fullUsername;
			if ($bi != NULL) {
				//Upload do ficheiro
				$ficheiro = array();
				$ficheiro['name'] = $f_name;
				$ficheiro['type'] = $f_type;
				$ficheiro['size'] = $f_size;
				$ficheiro['content'] = $f_content;

				$res_fich = $this->Ficheiro->save($ficheiro);
				//ir buscar o ID do ficheiro que foi feito upload
				//$id_ficheiro = $this->getIdFromFile($f_name, $f_size, $f_type);

				$id_ficheiro = $res_fich['Ficheiro']['id'];

				if ($id_ficheiro != NULL) {
					//Associar ao perfil_ver se ja existe na tabela
					$result_perfil_details = $this->ExtraDetails->query("SELECT * FROM detalhes WHERE bi = '" . $bi . "'");
					if (count($result_perfil_details) > 0) {
						$sql = "UPDATE detalhes SET " . $campo_a_alerar . " = " . $id_ficheiro . " WHERE bi='" . $bi . "'";
					} else {
						$sql = "INSERT INTO detalhes (bi, " . $campo_a_alerar . ") VALUES ('" . $bi . "', '" . $id_ficheiro . "')";
					}
					$this->ExtraDetails->query($sql);

					echo json_encode(array(1 => 'SUCC', 'success' => true));

					$this->enviarMail("default", "Alteração de Perfil", $this->fullUsername, array(), 'Caro(a) ' . $this->displayName . ',<br><br>O seu perfil foi atualizado com sucesso.');
				} else {
					die(json_encode(array(1 => 'FAIL')));
				}
			} else
				die(json_encode(array(1 => 'FAIL')));
		} else {
			die(json_encode(array(1 => 'FAIL')));
		}
	}



	function mecanismosRecuperacao()
	{
		$ldap = NULL;
		$role = ($this->isStudent) ? 1 : 0;
		$ldap = new ADLdap($role);
		$userinfo = $ldap->user_info($this->username);
		$utilizador = array();
		$utilizador['mecanismo_recuperacao_bi'] = isset($userinfo[0]["employeeid"][0]) && is_numeric($userinfo[0]["employeeid"][0]) ? $userinfo[0]["employeeid"][0] : NULL;
		$utilizador['mecanismo_recuperacao_pin'] = isset($userinfo[0]["extensionattribute10"][0]) ? 'Definido' : NULL;
		$utilizador['mecanismo_recuperacao_pergunta'] = isset($userinfo[0]["extensionattribute11"][0]) ? $userinfo[0]["extensionattribute11"][0] : NULL;
		$utilizador['mecanismo_recuperacao_resposta'] = isset($userinfo[0]["extensionattribute12"][0]) ? '**********' : NULL;
		$utilizador['mecanismo_recuperacao_email'] = isset($userinfo[0]["extensionattribute13"][0]) ? $userinfo[0]["extensionattribute13"][0] : NULL;
		$this->set("utilizador", $utilizador);
	}


	function mudarResposta($val)
	{

		$ldap = NULL;
		$role = ($this->isStudent) ? 1 : 0;
		$ldap = new ADLdap($role);
		$attr_to_change = "extensionattribute12";

		$val = html_entity_decode($val, ENT_COMPAT, 'UTF-8');
		$val =  hash("sha256", strtolower(trim($val)));
		$attrs[$attr_to_change] = $val;

		//$attrs[$attr_to_change] = $val;

		$response = $ldap->user_modify($this->username, $attrs);
		if ($response) {
			$this->enviarMail("default", "Alteração de Perfil", $this->fullUsername, array(), 'Caro(a) ' . $this->displayName . ',<br><br>O seu perfil foi atualizado com sucesso.');
			die('Perfil atualizado com sucesso.');
		} else
			die('Ocorreu um erro ao alterar Resposta');
	}





	function edit()
	{
		$this->autoRender = false;
		if (isset($_REQUEST['type'])) {

			$tipo = Sanitize::clean($_REQUEST['type']);
			$val = NULL;

			if (isset($_REQUEST['val']))
				$val = Sanitize::clean(trim($_REQUEST['val']));

			$ldap = NULL;
			$role = ($this->isStudent) ? 1 : 0;
			$ldap = new ADLdap($role);
			$attr_to_change = '';

			switch ($tipo) {
				case "mail":
					if (filter_var($val, FILTER_VALIDATE_EMAIL) || $val == '') {
						$attr_to_change = "extensionattribute13";
					} else {
						die('Email inválido');
					}

					break;
					//case "bi": $attr_to_change = "employeeid";  break;
				case "telemovel":
					$attr_to_change = "mobile";
					break;
				case "sala":
					$attr_to_change = "physicaldeliveryofficename";
					break;
				case "tlf_direto":
					$attr_to_change = "telephonenumber";
					break;
				case "ext_principal":
					$this->definirExtensoes("principal", $val);
					return false;
				case "ext_alt":
					$this->definirExtensoes("alt", $val);
					return false;
				case "pergunta_resposta":
					$attr_to_change = "extensionattribute11";
					break;
				case "pergunta_resposta_2": //$attr_to_change = "extensionattribute12"; break;
					$this->mudarResposta($val);
					return false;
				case "pin":
					$attr_to_change = "extensionattribute10";
					$val = $this->generateNewPin();
					break;
				case "password":
					$oldpassword = $_REQUEST['val'];
					$msg = $this->mudarPasswordControl($oldpassword, $_POST['new_pass']);
					echo $msg;
					return false;
				case "foto_remove":
					echo $this->removerFicheiro("foto", $this->fullUsername);
					return false;
					break;
				case "cv_remove":
					echo $this->removerFicheiro("cv", $this->fullUsername);
					return false;
					break;
			}
			if ($attr_to_change != NULL) {
				if (trim($val) == '' || trim($val) == NULL) {
					$att[$attr_to_change] = '  ';
					$ldap->user_modify($this->username, $att);
					echo 1;
				} else {
					$attrs[$attr_to_change] = html_entity_decode($val, ENT_COMPAT, 'UTF-8');
					$response = $ldap->user_modify($this->username, $attrs);
					if ($response == TRUE) {
						if ($tipo == 'pin') {
							echo $val;
							$mail = isset($this->ADUser[0]["extensionattribute13"]) ? $this->ADUser[0]["extensionattribute13"][0] : (isset($this->ADUser[0]["altrecipient"]) ? $this->ADUser[0]["altrecipient"][0] : NULL);

							$this->enviarMail("default", "Alteração de Perfil", $this->fullUsername, array(), 'Caro(a) ' . $this->displayName . ',<br><br>O seu perfil foi atualizado com sucesso. <br><br>Novo PIN de recuperação: <strong>' . $val . '</strong>');
						} else {
							echo 1;
							$this->enviarMail("default", "Alteração de Perfil", $this->fullUsername, array(), 'Caro(a) ' . $this->displayName . ',<br><br>O seu perfil foi atualizado com sucesso.');
						}
						//$this->enviarMail('changed_profile', 'Alteração de Perfil', $this->fullUsername);
					} else {
						echo -1;
					}
				}
			}
		}
	}

	function generateNewPin()
	{
		$better_token = md5(uniqid(mt_rand(), true));
		$pin = substr($better_token, 0, 8);
		$sql = "INSERT INTO t_letters (username, pin, locked, tolerance) VALUES ('" . $this->username . "', '" . $pin . "', 0,0)";
		$this->Gestao->query($sql);
		return $pin;
	}

	function definirExtensoes($tipo, $val)
	{

		$role = ($this->isStudent) ? 1 : 0;
		$ldap = new ADLdap($role);
		$userinfo = $ldap->user_info($this->username);
		$attrs = NULL;

		if ($tipo == 'principal') {
			//Se não houver posicao 1, a extensao principal está na prosicao 0
			if (!isset($userinfo[0]['othertelephone'][1]) && trim($userinfo[0]['othertelephone'][1]) == NULL) {
				if (trim($val) == '' || trim($val) == NULL)
					$val = "  ";
				$attrs['othertelephone'][0] = $val;
			}
			//Ja existe ext alternativa e principal, é só mudar a posicao 1;,
			else if (trim($userinfo[0]['othertelephone'][0]) != NULL && isset($userinfo[0]['othertelephone'][1]) && trim($userinfo[0]['othertelephone'][1]) != NULL) {
				$attrs['othertelephone'][0] = $val;
			} else {
				//apagar extensao principal
				$attrs['othertelephone'][0] = " ";
			}
		} else if ($tipo == 'alt') {
			if (isset($userinfo[0]['othertelephone'][1]) && trim($userinfo[0]['othertelephone'][1]) != NULL) {
				//Ja existe extensao principal e Alternativa; Alterar valor da posicao 0;
				if (trim($val) == '' || trim($val) == NULL) {
					$attrs['othertelephone'][0] = $userinfo[0]['othertelephone'][1];
					$attrs['othertelephone'][1] = " ";
				} else {
					$attrs['othertelephone'][0] = $val;
					$attrs['othertelephone'][1] = $userinfo[0]['othertelephone'][1];
				}
			} else if (isset($userinfo[0]['othertelephone'][0]) && !isset($userinfo[0]['othertelephone'][1]) && trim($userinfo[0]['othertelephone'][1]) == NULL) {
				//Ja existe extensao principal; Passar extensao principal para a posicao 1 e escrever a ext alternativa a na posicao 1; 
				if (trim($val) != '' && trim($val) != NULL) {
					$attrs['othertelephone'][1] = $userinfo[0]['othertelephone'][0];
					$attrs['othertelephone'][0] = $val;
				}
			}
		}

		if ($attrs != NULL) {
			$response = $ldap->user_modify($this->username, $attrs);
			if ($response == TRUE) {
				echo 1;
				$this->enviarMail("default", "Alteração de Perfil", $this->fullUsername, array(), 'Caro(a) ' . $this->displayName . ',<br><br>O seu perfil foi atualizado com sucesso.');
			}
		}
	}


	function mudarPassword()
	{
		//form de mudanca de password
		$ldap = NULL;
		$ldap = new ADLdap($this->role);
		$userinfo = $ldap->user_info($this->username);

		$nome 		= $userinfo[0]['cn'][0];
		$username 	= $userinfo[0]['samaccountname'][0];
	}


	/*
	
	MUDAR PASSWORD RULES
	
	*/

	//Regra 1 - nao poder ter 3 ou mais caracteres da password igual ao username ou nome completo		
	private function checkPasswordRuleIsPassInUserOrDisplayName($password, $username, $displayname)
	{

		return NULL;
		//mega martelada por causa do paulo verissimo

		$DEFAULT_SEARCH_SIZE = 3;
		$token 	= "";
		$err 	= NULL;
		$pw_len = strlen($password);

		for ($i = 0; $i < $pw_len - $DEFAULT_SEARCH_SIZE; $i++) {
			$token = substr($password, $i, $DEFAULT_SEARCH_SIZE);

			if (preg_match('/' . $token . '/', $username)) {
				$err .= 'Password tem no minimo ' . $DEFAULT_SEARCH_SIZE . ' caracters consecutivos do nome de utilizador.';
				return $err;
			}
		}
		for ($i = 0; $i < $pw_len - $DEFAULT_SEARCH_SIZE; $i++) {
			$token = substr($password, $i, $DEFAULT_SEARCH_SIZE);
			if (preg_match('/' . $token . '/', $displayname)) {
				$err .= 'Password tem no minimo ' . $DEFAULT_SEARCH_SIZE . ' caracters consecutivos do nome completo.';
				return $err;
			}
		}
		return $err;
	}
	//Regra 2 - mais de 6 chars
	private function checkPasswordRuleMinLength($password)
	{
		$DEFAULT_PASSWORD_SIZE = 6;

		return (strlen($password) >= $DEFAULT_PASSWORD_SIZE);
	}
	//Regra 3 - 1 char Maisculo
	private function checkPasswordRuleMinOneCapChar($string)
	{
		$containsLetter  = preg_match('/[A-Z]/',    $string);
		return ($containsLetter == 1);
	}
	//Regra 4 - 1 char minusculo
	private function checkPasswordRuleMinOneNonCapChar($string)
	{
		$containsLetter  = preg_match('/[a-z]/',    $string);
		return ($containsLetter == 1);
	}
	//Regra 5 - 1 char  number
	private function checkPasswordRuleMinOneNumber($string)
	{
		$containsDigit   = preg_match('/\d/',          $string);
		return ($containsDigit == 1);
	}
	//Regra 6 - 1 special char
	private function checkPasswordRuleMinOneSpecialChar($string)
	{
		$containsSpecial = preg_match('/[^a-zA-Z\d]/', $string);
		return ($containsSpecial == 1);
	}
	/* @return NULL if OK! */
	private function checkPasswordRules($password, $username, $displayname)
	{
		$MINIMAL_RULES 				= 3;
		$result 					= NULL;
		$count_misc_minim_rules 	= 4;
		$mandatory_ok = true;
		// mandatory rules
		/*if(($e = $this->checkPasswordRuleIsPassInUserOrDisplayName($password,$username, $displayname)) !== NULL){
			$result .= '<li>'.$e.'</li>';
			$mandatory_ok = false;
		}*/
		//mega martelada por causa do paulo verissimo
		// mandatory rules
		if (!$this->checkPasswordRuleMinLength($password)) {
			$result .= '<li>Tem de ter pelo menos 6 caracteres;</li>';
			$mandatory_ok = false;
		}

		// 3 out of 4 optional rules
		if (!$this->checkPasswordRuleMinOneCapChar($password)) {
			$result .= '<li>Tem de ter pelo menos caracteres com letras maiúsculas (A..Z);</li>';
			$count_misc_minim_rules--;
		}

		if (!$this->checkPasswordRuleMinOneNonCapChar($password)) {
			$result .= '<li>Tem de ter pelo menos caracteres com letras minúsculas (a..z);</li>';
			$count_misc_minim_rules--;
		}
		if (!$this->checkPasswordRuleMinOneNumber($password)) {
			$result .= '<li>Tem de ter pelo menos um algarismo (0..9);</li>';
			$count_misc_minim_rules--;
		}
		if (!$this->checkPasswordRuleMinOneSpecialChar($password)) {
			$result .= '<li>Tem de ter pelo menos um caracter não alfabético (!,$,#,%,&)</li>';
			$count_misc_minim_rules--;
		}

		// acertou todas
		if ($count_misc_minim_rules >= $MINIMAL_RULES && $mandatory_ok) return NULL;
		else {
			// erro
			$result = (($result == NULL) ?  $result : ('<ul>' . $result . '<li><b>Atenção</b>: Não é possível reutilizar qualquer uma das três palavras passe anteriores.</li></ul>'));

			return $result;
		}
	}

	/*
	* PASSWORD RULES - end
	*/
	function mudarPasswordControl($oldpassword, $newpassword)
	{
		$ldap = NULL;
		$role = ($this->isStudent) ? 1 : 0;
		$ldap = new ADLdap($role);
		$username = $this->username;
		$result = NULL;
		// verifica se existe e tem passowrd

		if (trim($oldpassword) == trim($newpassword)) {
			$result .= "<li>Password nova não pode ser igual à antiga.</li>";
		}
		$user_exists = $ldap->user_exists($username, $oldpassword);
		//Não foi possivel encontrar o utilizador [ ".$username." ] ou p
		if (!$user_exists) {
			$result .= "<li>Palavra passe actual introduzida é inválida.</li>";
		}
		$fields = array('displayname', 'cn', 'department', 'physicaldeliveryofficename', 'distinguishedname', 'samaccountname');
		$userinfo = $ldap->user_info($username, $fields);
		$displayname = $userinfo[0]['cn'][0];

		if ($result == NULL) {
			$checkpasswd_result = $this->checkPasswordRules($newpassword, $username, $displayname);
			if ($checkpasswd_result == NULL) {
				// proceed



				$succ = $ldap->user_password($username, $newpassword);
				//$succ = 1;

				/*
				*/

				if ($succ) {
					$this->enviarMail("default", "Alteração de Perfil", $this->fullUsername, array(), 'Caro(a) ' . $this->displayName . ',<br><br>A sua palavra passe foi atualizada com sucesso.');
					return true;
				} else $result .= "<li>Ocorreu um erro a alterar a palavra passe.</li>";
			} else $result .= $checkpasswd_result;
		}
		$result = '<ul>' . $result . '</ul>';
		return $result;
	}


	/*
	
	EMAIL HELPER
	
	*/
	function enviarMail($template, $subject, $to, $viewVars = array(), $msg)
	{
		$email = new CakeEmail('default');
		$email->template($template)
			->viewVars($viewVars)
			->emailFormat('html')
			->subject($subject)
			->to($to)
			->send($msg . '<br><br>Qualquer dúvida não hesite em nos contactar.<br>
										<br>
										Obrigado,<br>
										Suporte Informático - suporte@ciencias.ulisboa.pt<br>
										Direção de Serviços Informáticos - https://ciencias.ulisboa.pt/dsi <br>
										Ciências ULisboa - https://ciencias.ulisboa.pt <br>');
	}

	function showFile()
	{
		$this->autoRender = false;
		if (isset($_GET['id']) && isset($_GET['tipo'])) {
			if (!$this->isStudent) {
				$campo = NULL;
				switch (Sanitize::clean($_GET['tipo'])) {
					case "foto":
						$campo = 'd.foto_file_id';
						break;
					case "cv":
						$campo = 'd.cv_file_id';
						break;
				}
				$bi = $this->fullUsername;
				if ($bi != NULL && $campo != NULL) {
					$id = Sanitize::clean($_GET['id']);
					//ver se esta autorizado a ver o ficheiro
					$sql = "SELECT * FROM detalhes d WHERE d.bi = '" . $bi . "' AND  " . $campo . " = " . $id;
					$resultado = $this->ExtraDetails->query($sql);
					if (count($resultado) > 0) {
						$resultado_ficheiro = $this->ExtraDetails->query("SELECT * FROM ficheiros f WHERE f.id = " . $id);
						if (count($resultado_ficheiro) > 0) {
							$name = $resultado_ficheiro[0]['f']['name'];
							$type = $resultado_ficheiro[0]['f']['type'];
							$size = $resultado_ficheiro[0]['f']['size'];
							$content = $resultado_ficheiro[0]['f']['content'];

							if ($size != NULL && $type != NULL && $name != NULL) {
								header("Content-length: " . $size);
								header("Content-type: " . $type);
								header('Content-Disposition: attachment; filename="' . $name . '"');
								echo $content;
								exit;
							} else {
								die('Erro ao obter ficheiro');
							}
						}
					}
				}
			}
		}
	}

	/*
	
	AD HELPERS
	
	*/
	function contaCriadaEm($whencreated)
	{
		$year    = substr($whencreated, 0, 4);
		$month   = substr($whencreated, 4, 2);
		$day     = substr($whencreated, 6, 2);
		$hour    = substr($whencreated, 8, 2);
		$minutes = substr($whencreated, 10, 2);
		$second  = substr($whencreated, 12, 2);
		return $year . '-' . $month . '-' . $day . ' ' . $hour . ':' . $minutes . ':' . $second;
	}

	function contaExpiraEm($accountexpires)
	{
		if ($accountexpires == 9223372036854775807 || $accountexpires == 0) {
			return 'A conta nunca expira';
		} else {
			$dateLargeInt      = $accountexpires;
			$secsAfterADEpoch  = $dateLargeInt / (10000000);
			$ADToUnixConvertor = ((1970 - 1601) * 365.242190) * 86400;
			$unixTsLastLogon   = intval($secsAfterADEpoch - $ADToUnixConvertor);
			$lastlogon         = date("Y-m-d H:i:s", $unixTsLastLogon);
			return $lastlogon;
		}
	}

	function passwordExpiraEm($username)
	{
		$role = ($this->isStudent) ? 1 : 0;
		$ldap = new ADLdap($role);
		$array_expiracao = $ldap->user_password_expiry($username);
		$data_expiracao = $array_expiracao["expiryformat"];
		if ($data_expiracao == 'D')
			$data_expiracao = 'Nunca expira';
		if ($data_expiracao == 'P')
			$data_expiracao = 'Tem que alterar a password quando iniciar sessão';
		if ($data_expiracao == 'M')
			$data_expiracao = 'Não disponível';
		return $data_expiracao;
	}
}
