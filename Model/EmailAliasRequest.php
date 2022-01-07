<?php
class EmailAliasRequest extends AppModel{
    public $name = 'EmailAliasRequest';
    public $useTable = 't_email_alias_requests';
	public $primaryKey = 'idrequest';
	public $displayField = 'samaacountname';
	public $useDbConfig = 'dbgestao';
	
	public $belongsTo = array(
		'AccountStatus' => array(
			'className' => 'AccountStatus',
			'foreignKey' => 'idstatus'
		)
	);			
	
	public function afterFind ($data) {
		$fcLdap = new ADldap();
		$alunosLdap = new ADldap(1);
		
		foreach ($data as &$entry) {
			if ($entry["EmailAliasRequest"]["student"]){
				$info = $alunosLdap->user_info($entry["EmailAliasRequest"]["samaccountname"],array("displayname"));
			} else {
				$info = $fcLdap->user_info($entry["EmailAliasRequest"]["samaccountname"],array("displayname"));
			}
			$entry["EmailAliasRequest"]["nome"] = $info[0]["displayname"][0];
		}
		
		return $data;
	}	

}

