<?php
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('Controller', 'Controller');

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package		app.Controller
 * @link		http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
 
App::import('Vendor', 'ldap', array('file' => 'ldap' . DS . 'adldap.php'));
App::import('Vendor', 'SharedSession', array('file' => 'SharedSession'.DS.'SharedSession.php'));
 

header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
header("Pragma: no-cache"); // HTTP 1.0.
header("Expires: 0"); // Proxies. 

class AppController extends Controller {
	
	public $username = NULL;
	
	public $domain = NULL;
	
    public $isLoggedIn = false;
	
    public $fullUsername =  NULL;
	
	public $fullDetailsAD = NULL;
	
	public $isTeacher = false;
	
	public $isStudent = false;
	
	public $displayName = '';
	
	public $cdTeacher = 0;
	
	public $nAluno = 0;
	
	public $ldap = NULL;
	
	function beforeFilter() {	
		$drupalSession = new SharedSession();
	
		$this->drupalUser = $drupalSession->user;	
		if (isset($this->drupalUser->roles[DRUPAL_ANONYMOUS_RID])) { // User is not logged in in drupal
			die('N&atilde;o est&aacute; <a href="/user">autenticado.</a>');
		} else{
			
			
			
			
			
			$this->fullUsername = strtolower($this->drupalUser->name);
			$pieces = explode("@", strtolower($this->drupalUser->name));
			$this->username = $pieces[0];
			$this->isLoggedIn = TRUE;
			$this->domain  = $pieces[1];
			$ldap = NULL;
			
			
			
			if($pieces[1]  == 'teclabs.pt') {
				die('<script type="text/javascript">$("#main_perfil_div").html("Contas teclabs não suportadas.")</script>');
			}
			
			if( $pieces[1] == 'alunos.fc.ul.pt' || $pieces[1] == 'alunos.ciencias.ulisboa.pt') {
				$ldap = new ADLdap(1);	
				$this->ldap = $ldap;
				$user_info = $ldap->user_info($pieces[0]);
				$this->fullDetailsAD = $user_info;
				$this->isStudent = TRUE;
				$this->displayName = $user_info[0]['cn'][0];
				$this->nAluno = $user_info[0]['employeenumber'][0];
			}
			else if( $pieces[1] == 'fc.ul.pt' || $pieces[1] == 'ciencias.ulisboa.pt' || $pieces[1] == 'teclabs.pt') {  //fc.ul.pt, teclabs, ciencias.ulisboa.pt, etc..

				$ldap = new ADLdap(0);
				$this->ldap = $ldap;
				$user_info = $ldap->user_info($pieces[0]);
				$this->fullDetailsAD = $user_info;
				$this->displayName = $user_info[0]['displayname'][0];
				if(!empty($user_info[0]['extensionattribute9'][0])) {
					$this->isTeacher = TRUE;
					$this->cdTeacher = $user_info[0]['extensionattribute9'][0];
				}
			}else{
				die('Erro: A sua conta não está preparada para aceder ao portal de Ciências.');
			}
		}
	}
}
