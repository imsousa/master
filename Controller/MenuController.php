<?php



class MenuController extends AppController {
	
	var $uses = array('MenuItem','MenuAcl');
    var $layout = 'ajax';
    var $helpers = array('Html','Js');
    var $components = array('RequestHandler','Session');
	
	
	/**
	 * Vai buscar o menu e apresenta
	 */
	public function getMenu() {
		
		$this->set("username", $this->username);
		
		$grupos = $this->getGroups($this->username);
        
		
		if ($this->isStudent) { // Aluno
			array_push($grupos,"ALUNO");
		}
		else if($this->isTeacher){ // Docente
			array_push($grupos,"DOCENTE");
			
		} else { // Funcionario
			array_push($grupos,"FUNCIONARIO");
		} 
		
		$options["joins"] = array(
			array(
				'table' => 'menu_acl',
				'alias' => 'MenuAcl',
				'type' => 'LEFT',
				'conditions' => array(
					'MenuItem.id = MenuAcl.menu_item_id',
				)
			)
		);
		
		$options["conditions"] = array(
			"OR" => array(
				array(
					"MenuItem.public" => 0,
					"MenuAcl.entity" => $this->username,
					"MenuAcl.is_group" => 0,	
				),
				array(
					"MenuItem.public" => 0,
					"MenuAcl.entity" => $grupos,
					"MenuAcl.is_group" => 1,	
				),
				array(
					"MenuItem.public" => 1
				)
			
			),
			// visivel
			"MenuItem.visible" => 1,
			"MenuItem.parent_id" => 0	
		);
		
		$options["fields"] = array('DISTINCT MenuItem.url', 'MenuItem.label', 'MenuItem.other_window', 'MenuItem.parent_id', 
									'MenuItem.id', 'MenuItem.ordem', 'MenuItem.full_url','MenuItem.icon','MenuItem.plano_b');
				
		$options["order"] = array("MenuItem.ordem ASC");
		
		
		
		// Get parents only
		$menuItems = $this->MenuItem->find("all", $options);
		
		
		
		// Get children of parents
		foreach ($menuItems as &$item) {
			$options["conditions"]["MenuItem.parent_id"] = $item["MenuItem"]["id"];
                        $item["children"] = $this->MenuItem->find("all", $options);
                      
		}
		//pr($menuItems);
		$this->request->data = $menuItems;
		$this->set("menu",$menuItems);
	}
	
	public function newMenuItem($parent) {
		if(empty($this->request->data)){
			$options["order"] = array("MenuItem.ordem ASC");
			$this->set("menu",$this->MenuItem->find("threaded", $options));
			
			if(isset($parent))
				$this->request->data["MenuItem"]["parent_id"] = (int) $parent;
		} else {
			$this->autoRender = false;
			 
			$lastSibling = $this->MenuItem->find("first",  
				array("conditions" => array("parent_id" => $this->request->data["MenuItem"]["parent_id"]), 
					  "order" => "MenuItem.ordem DESC"));
			
			$this->request->data["MenuItem"]["ordem"] = $lastSibling["MenuItem"]["ordem"] + 1;
			
			if($this->MenuItem->save($this->request->data)){
				echo json_encode(array("success"=>true));
			} else {
				foreach($this->MenuItem->validationErrors as $field => $errors){
					$msg[$field] = implode(" \n ",$errors);
				}
				echo json_encode(array("success"=>false, "msg"=>implode(" \n ",$msg)));
				//pr($this->MenuItem->validationErrors);
			}
		}
	}
	
	public function editMenu(){
		if(!empty($_POST)){
			$items = array();
			foreach($_POST["data"] as $item){
				$items[] = $item["MenuItem"];
				
				foreach($item["children"] as $child){
					$items[] = $child["MenuItem"];
				}					
			}
			
			if($this->MenuItem->saveAll($items)){
				$this->Session->setFlash("Alterações Gravadas",'default', array('class' => 'alert alert-success'));
			} else {
				$this->Session->setFlash("Erro ao gravar Alterações",'default', array('class' => 'alert alert-error'));
			}
		} 
	
		$options["order"] = array("MenuItem.ordem ASC");
		
		$menuItems = $this->MenuItem->find("threaded", $options);
		$log = $this->MenuItem->getDataSource()->getLog(false, false);
		
		//pr($log);
		$this->request->data = $menuItems;
		//pr($menuItems);
		$this->set("menu",$menuItems);
	}
	
	public function deleteMenuItem($id){
		$id = (int) $id;
		$this->autoRender = false;
		
		/*Verificar se fica orfao ao apagar*/
		$r = $this->MenuItem->find('all', array('conditions' => array('MenuItem.parent_id' => $id)));
		if(count($r)==0) {
			$this->MenuItem->delete($id);
			echo json_encode(array("success" => true));
		}else{
			echo json_encode(array("error" => 'Item não apagado (existem filhos deste menu activos)'));
		}
	}
	
	function addIcon($id) {
		$id = (int) $id;
		if($id >= 0) {
			$found = $this->MenuItem->find('first',array('conditions' => array('MenuItem.id' => $id)));
			$this->set("dados_item", $found);
			
			
		}else{
			die('Menu not found');
		}
	}
	
	function changeIcon() {
		$this->autoRender = false;
		$id = (!empty($_REQUEST['menu_id'])) ? $_REQUEST['menu_id'] : die('Menu inválido');
		$icon = (!empty($_REQUEST['new_icon']) && strlen($_REQUEST['new_icon'])>2) ? $_REQUEST['new_icon'] : die('Icon inválido');
		$label = (!empty($_REQUEST['label']) && strlen($_REQUEST['label'])>0) ? $_REQUEST['label'] : die('Label inválida');
		try {
			$r = 	$this->MenuItem->save(array('id' => $id, 'icon' => $icon, 'label' => $label));
				die('Icon alterado com sucesso.');
		}catch(Exception $e) {
			die('Ocorreu um erro ao alterar o icon');
		}
	}
	
	public function editarAcl($id){
		$id = (int) $id;
		
		if(!empty($this->request->data)){
			if(!$this->MenuAcl->saveMany($this->request->data,array("atomic"=>false))){
				pr($this->MenuAcl->validationErrors);
			} 			
		}
		
		$this->request->data = $this->MenuAcl->find("all",array("conditions"=>array("menu_item_id" => $id)));
		$this->set("menu_item_id",$id);
		
		$options["order"] = array("MenuItem.ordem ASC");
		$this->set("menu",$this->MenuItem->find("threaded", $options));
	}
	
	public function deleteAclEntry($id) {
		$id = (int) $id;
		$this->autoRender = false;
		
		$this->MenuAcl->delete($id);
	}
	
	public function copyAcl(){
		$this->autoRender = false;
		$to = (int)$this->request->data["to"];
		$from = (int)$this->request->data["from"];
		
		if ($to === 0 || $from === 0) {
			die();
		}
		
		
		$originalAcl = $this->MenuAcl->find("all",array("conditions"=>array("menu_item_id" => $to)));
		$aclToCopy = $this->MenuAcl->find("all",array("conditions"=>array("menu_item_id" => $from)));
		
		$resultAcl = array();
		foreach($aclToCopy as &$fromEntry){
			$resultAcl[$fromEntry["MenuAcl"]["entity"]."-".$fromEntry["MenuAcl"]["is_group"]]["menu_item_id"] = $to;
			$resultAcl[$fromEntry["MenuAcl"]["entity"]."-".$fromEntry["MenuAcl"]["is_group"]]["entity"] = $fromEntry["MenuAcl"]["entity"];
			$resultAcl[$fromEntry["MenuAcl"]["entity"]."-".$fromEntry["MenuAcl"]["is_group"]]["is_group"] = $fromEntry["MenuAcl"]["is_group"];
		}
		foreach($originalAcl as &$toEntry){
			$resultAcl[$toEntry["MenuAcl"]["entity"]."-".$toEntry["MenuAcl"]["is_group"]]["menu_item_id"] = $to;
			$resultAcl[$toEntry["MenuAcl"]["entity"]."-".$toEntry["MenuAcl"]["is_group"]]["entity"] = $toEntry["MenuAcl"]["entity"];
			$resultAcl[$toEntry["MenuAcl"]["entity"]."-".$toEntry["MenuAcl"]["is_group"]]["is_group"] = $toEntry["MenuAcl"]["is_group"];
		}
		
		$this->MenuAcl->deleteAll(array("menu_item_id" => $to));
		foreach($resultAcl as $newEntry){
			$this->MenuAcl->create();
			$this->MenuAcl->save($newEntry);	
		}		
	}
	
	
		public function help() {}
	
	protected function getGroups($username) {
		$role = ($this->isStudent) ? 1 : 0;
		if ($role == 0) {
			$ldap = new ADLdap();
			
			if (!$ldap->user_exists($username)) {
				// Não existe
				return array();
			}
			//recursivo por causa dos subgrupos
			return $ldap->user_groups($username,TRUE);			
		} else if ($role == 1) {
			$ldap = new ADLdap(1);
			
			if (!$ldap->user_exists($username)) {
				// Não existe
				return array();
			}
			
			return $ldap->user_groups($username);
		} else {
			return array();
		}
	}
	
	protected function getGroupMembers($grupo, $tipoLdap=0){
		$ldap = new ADLdap($tipoLdap);
		$members = $ldap->group_members($grupo);
		
		$result = array();
		foreach($members as $member){
			$result[] = $member["samaccountname"];
		}

		return $result;
	}
	
	
	/**
	 * Scripts para ajudar a optimizar o ACL
	
	public function aclTester(){
		$this->autoRender = false;
		die("closed");
		$usersAlreadySolved = array("brseixas","fwestanqueiro","rmnunes","rjbatista","cmbangueses");
		
		$users = array("acrmartins","mmgodinho","epcravo","mmcruz","mdlouro","mereal","atsantos","mlteigas","ramarcal","acgoncalves","mdmatias",
					   "japica","aimarques","cmbernardino","mdfeteira","opinto","mdbispo","apfaria","ifandrade","lmcarrico","ambranco","pmveiga","jpneto",
					   "apclaudio","alrespicio","adbarbosa","spcrespo","vmvasconcelos","anbessani","accosta","nfneves","aeferreira","cborges",
					   "matoslopes","mmrocha","mhflorencio","mdferreira");
		
		$cd_docentes = array(40619,40716,40322,40038,40539,40142,41164,40477,40790,40386,40433,40566,41192,40116,41183,40590,40216,40232,40033,40376,40192);
		
		// Get funcionarios por departamentos ALL DONE
		$ldap = new ADLdap(0);
		$departamentos = $ldap->folder_parents();
		
		// Departamentos nao devem ter mais do que 1000 pessoas /me prays
		for ($i = 0; $i < $departamentos["count"]; $i++) {
			$dep = $departamentos[$i]["dn"];
			$depUsers = $ldap->return_users($dep); 
			for ($j = 0; $j < $depUsers["count"]; $j++) {
				if(isset($depUsers[$j]['extensionattribute9']) 
					&& in_array($depUsers[$j]['extensionattribute9'][0],$cd_docentes)
					&& !in_array($depUsers[$j]['samaccountname'][0],$users)){
					
					if (!in_array($depUsers[$j]['samaccountname'][0],$usersAlreadySolved))
						$users[] = $depUsers[$j]['samaccountname'][0];
				}
			}
			unset($depUsers);
		}	
		
		foreach($users as $user){
			$exists = $this->MenuAcl->find("first",array(
				"conditions"=>array("menu_item_id"=>69, "entity"=>$user, "is_group"=>0)));
			if(empty($exists)){
				$this->MenuAcl->create();
				$acl = array();
				$acl["menu_item_id"] = 69;
				$acl["entity"] = $user;
				$acl["is_group"] = 0;
				
				if($this->MenuAcl->save($acl)){
					echo "Saved $user <br/>";
				}
			}	
		}
		
		pr($users);
		die();
		
		$pessoas = $users;
		
		$candidate_groups = array();
		foreach($pessoas as $pidx => $pessoa){
			$pessoa_grupos = $this->getGroups($pessoa);
			
			// Check if pessoa shares a grupo with other pessoa
			for ($i = $pidx+1; $i < count($pessoas); $i++){
				$other_pessoa_grupos = $this->getGroups($pessoas[$i]);
				$shared_grupos = array_intersect($pessoa_grupos, $other_pessoa_grupos);
				
				if (count($shared_grupos) > 0) {
					foreach($shared_grupos as $shared) {
						if (!isset($candidate_groups[$shared])){
							$candidate_groups[$shared] = array();
							$candidate_groups[$shared][] = $pessoa;
						}
							
						if(!in_array($pessoas[$i],$candidate_groups[$shared]))
							$candidate_groups[$shared][] = $pessoas[$i];
					}
				}
			}
			if (count($result) > 0)
				$results[$pessoa] = $result;
		}
		
		$margem_funcs = 4;
			
		$result = array();
		foreach($candidate_groups as $cand_grupo => $shared_members) {
			$members = $this->getGroupMembers($cand_grupo);
			if(count($shared_members) + $margem_funcs >= count($members)){
				$result[] = "Encontrei um grupo candidato [ ".$cand_grupo." ], este grupo contem [ ".count($members)." ] membros e é 
				partilhado pelos utilizadores [ ".implode(", ",$shared_members)." ] (".count($shared_members)."). Os membros do grupo sao [ ".implode(", ",$members)." ].<br/>";	
			}
		}
		if (count($result) > 0)
			$results["GRUPOS CANDIDATOS"] = $result;
		
		pr($results); die();
		
				   
	}
	
	public function optimizeAcl(){
		if(!empty($this->request->data)){
			$this->autoRender = false;
			
			$results = array();
			
			$grupos = array();
			$pessoas = array();
			foreach($this->request->data["optimize"]["entity"] as $index => $entity){
				if($entity == "")
					continue;
				$entity = explode(",", $entity);
				
				$is_group = $this->request->data["optimize"]["is_group"][$index];
				if (!$is_group){
					$pessoas = array_merge($pessoas, $entity);
				} else {
					$grupos = array_merge($grupos, $entity);
				}					
			}
			
			
			$candidate_groups = array();
			foreach ($pessoas as $pidx => $pessoa){
				$result = array();
				
				// Check if pessoa is already a member of any of the grupos
				$pessoa_grupos = $this->getGroups($pessoa);
				$shared_grupos = array_intersect($pessoa_grupos, $grupos);
				if (count($shared_grupos) > 0) {
					$result[] = "O(s) grupo(s) [ ".implode(", ",$shared_grupos)." ] contem o utilizador [ ".$pessoa." ] e já existe na lista do ACL.<br/>";
				}	
				
				// Check if pessoa shares a grupo with other pessoa
				for ($i = $pidx+1; $i < count($pessoas); $i++){
					$other_pessoa_grupos = $this->getGroups($pessoas[$i]);
					$shared_grupos = array_intersect($pessoa_grupos, $other_pessoa_grupos);
					
					if (count($shared_grupos) > 0) {
						foreach($shared_grupos as $shared) {
							if (!isset($candidate_groups[$shared])){
								$candidate_groups[$shared] = array();
								$candidate_groups[$shared][] = $pessoa;
							}
								
							if(!in_array($pessoas[$i],$candidate_groups[$shared]))
								$candidate_groups[$shared][] = $pessoas[$i];
						}
					}
				}
				if (count($result) > 0)
					$results[$pessoa] = $result;
			}
			
			$margem_funcs = 4;
			
			$result = array();
			foreach($candidate_groups as $cand_grupo => $shared_members) {
				$members = $this->getGroupMembers($cand_grupo);
				
				$result[] = "Encontrei um grupo candidato [ ".$cand_grupo." ], este grupo contem [ ".count($members)." ] membros e é 
				partilhado pelos utilizadores [ ".implode(", ",$shared_members)." ] (".count($shared_members).").".
					(count($shared_members) + 4 >= count($members) ? "<br/> Os seus membros sao [ ".implode(", ",$members)." ]." : "")
				."<br/>";

			}
			if (count($result) > 0)
				$results["GRUPOS CANDIDATOS"] = $result;
			
			pr($results);
		} 
	}*/
	


	
}
