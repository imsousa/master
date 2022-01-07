<?php



class MenuFakeController extends AppController {
	
	public $uses = array('ExtraDetails');
	
	function index() {
		$this->set("fullusername", $this->fullUsername);
		$this->set("username", $this->username);
		$this->layout = 'ajax';
		$this->set("data_foto", $this->showFoto());	
	}
	

	public function showFoto(){
		$this->layout = 'ajax';
		if($this->isStudent){
			$sql = "SELECT FOTOGRAFIA FROM fotografias f, alunos a
					WHERE a.cd_aluno = ".$this->nAluno."
					and a.id_individuo = f.id";
			$result = oracle_execQuery_Rows($sql);       
			//$this->set('dados_foto', $result->data_rows['FOTOGRAFIA'][0]);
			return $result->data_rows['FOTOGRAFIA'][0];
		}		
		else {
			
			$userinfo = $this->ADUser;
			//if(isset($userinfo[0]["employeeid"][0]) && is_numeric($userinfo[0]["employeeid"][0])){
				//$bi = $userinfo[0]["employeeid"][0];
				$resultado = $this->ExtraDetails->query("SELECT * FROM detalhes d WHERE d.bi = '".$this->fullUsername."'");
				if(count($resultado)>0){
					$id_foto = $resultado[0]['d']['foto_file_id'];
					if($id_foto!=NULL){
						$resultado_foto = $this->ExtraDetails->query("SELECT * FROM ficheiros f WHERE f.id = ".$id_foto);
						if(count($resultado_foto)>0){
							//$this->set('dados_foto', $resultado_foto[0]['f']['content']);
							return $resultado_foto[0]['f']['content'];
						}
					}
				
				}
			}
		return NULL;		
	}
	
	
	
	
	
	
}
?>