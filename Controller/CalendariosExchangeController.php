<?php
App::uses('Sanitize', 'Utility');
App::import('Vendor', 'ldap', array('file' => 'ldap' . DS . 'adldap.php'));
App::import('Vendor', 'SharedSession', array('file' => 'SharedSession'.DS.'SharedSession.php'));

date_default_timezone_set("Europe/Lisbon");	

App::import('Vendor', 'NTLMSoapClient', array('file' => 'ExchangeWS' . DS . 'NTLMSoapClient.php'));
App::import('Vendor', 'NTLMSoapClient_Exchange', array('file' => 'ExchangeWS' . DS . 'NTLMSoapClient' . DS . 'Exchange.php'));
App::import('Vendor', 'ExchangeWebServices', array('file' => 'ExchangeWS' . DS . 'ExchangeWebServices.php'));
App::import('Vendor', 'EWSType', array('file' => 'ExchangeWS' . DS . 'EWSType.php'));

/*User*/
App::import('Vendor', 'EWSType_ExchangeImpersonationType', array('file' => 'ExchangeWS' . DS . 'EWSType' . DS . 'ExchangeImpersonationType.php'));
App::import('Vendor', 'EWSType_ConnectingSIDType', array('file' => 'ExchangeWS' . DS . 'EWSType' . DS . 'ConnectingSIDType.php'));
App::import('Vendor', 'browsercheck', array('file' => 'browsercheck' . DS . 'BrowserChecker.php'));

/*Items e Calendario*/
App::import('Vendor', 'EWSType_FindItemType', array('file' => 'ExchangeWS' . DS . 'EWSType' . DS . 'FindItemType.php'));
App::import('Vendor', 'EWSType_ItemQueryTraversalType', array('file' => 'ExchangeWS' . DS . 'EWSType' . DS . 'ItemQueryTraversalType.php'));
App::import('Vendor', 'EWSType_ItemResponseShapeType', array('file' => 'ExchangeWS' . DS . 'EWSType' . DS . 'ItemResponseShapeType.php'));
App::import('Vendor', 'EWSType_CalendarViewType', array('file' => 'ExchangeWS' . DS . 'EWSType' . DS . 'CalendarViewType.php'));
//App::import('Vendor', 'EWSType_CalendarViewType', array('file' => 'ExchangeWS' . DS . 'EWSType' . DS . 'EWSType_CalendarViewType.php'));

/*Folder*/
App::import('Vendor', 'EWSType_FindFolderType', array('file' => 'ExchangeWS' . DS . 'EWSType' . DS . 'FindFolderType.php'));
App::import('Vendor', 'EWSType_FolderQueryTraversalType', array('file' => 'ExchangeWS' . DS . 'EWSType' . DS . 'FolderQueryTraversalType.php'));
App::import('Vendor', 'EWSType_FolderResponseShapeType', array('file' => 'ExchangeWS' . DS . 'EWSType' . DS . 'FolderResponseShapeType.php'));
App::import('Vendor', 'EWSType_FolderResponseShapeType', array('file' => 'ExchangeWS' . DS . 'EWSType' . DS . 'FolderResponseShapeType.php'));
App::import('Vendor', 'EWSType_DefaultShapeNamesType', array('file' => 'ExchangeWS' . DS . 'EWSType' . DS . 'DefaultShapeNamesType.php'));
App::import('Vendor', 'EWSType_IndexedPageViewType', array('file' => 'ExchangeWS' . DS . 'EWSType' . DS . 'IndexedPageViewType.php'));
App::import('Vendor', 'EWSType_NonEmptyArrayOfBaseFolderIdsType', array('file' => 'ExchangeWS' . DS . 'EWSType' . DS . 'NonEmptyArrayOfBaseFolderIdsType.php'));
App::import('Vendor', 'EWSType_DistinguishedFolderIdType', array('file' => 'ExchangeWS' . DS . 'EWSType' . DS . 'DistinguishedFolderIdType.php'));
App::import('Vendor', 'EWSType_DistinguishedFolderIdNameType', array('file' => 'ExchangeWS' . DS . 'EWSType' . DS . 'DistinguishedFolderIdNameType.php'));


/*NOVOS IMPORTS SO PARA O CALENDAR*/
App::import('Vendor', 'EWSType_EmailAddressType', array('file' => 'ExchangeWS' . DS . 'EWSType' . DS . 'EmailAddressType.php'));
App::import('Vendor', 'EWSType_CreateItemType', array('file' => 'ExchangeWS' . DS . 'EWSType' . DS . 'CreateItemType.php'));
App::import('Vendor', 'EWSType_DeleteItemType', array('file' => 'ExchangeWS' . DS . 'EWSType' . DS . 'DeleteItemType.php'));
App::import('Vendor', 'EWSType_CalendarEventDetails', array('file' => 'ExchangeWS' . DS . 'EWSType' . DS . 'CalendarEventDetails.php'));
App::import('Vendor', 'EWSType_CalendarEvent', array('file' => 'ExchangeWS' . DS . 'EWSType' . DS . 'CalendarEvent.php'));

App::import('Vendor', 'EWSType_DisposalType', array('file' => 'ExchangeWS' . DS . 'EWSType' . DS . 'DisposalType.php'));
App::import('Vendor', 'EWSType_ItemIdType', array('file' => 'ExchangeWS' . DS . 'EWSType' . DS . 'ItemIdType.php'));
App::import('Vendor', 'EWSType_NonEmptyArrayOfBaseItemIdsType', array('file' => 'ExchangeWS' . DS . 'EWSType' . DS . 'NonEmptyArrayOfBaseItemIdsType.php'));

class CalendariosExchangeController extends AppController {
    
    var $name = 'CalendariosExchange';
    var $uses = array('');
    var $helpers = array('Html','Js');
    var $components = array('RequestHandler','Session');
	var	$localTZ = 'Portugal/Lisbon';
	var $cal_to_impersonate = NULL;
	var $user_name = NULL;
	
	 public function beforeFilter(){ 
        //Auth/Aut - ACL
		$drupalSession = new SharedSession();
		$this->drupalUser = $drupalSession->user;
		$username = $this->drupalUser->name;	
		$user = explode("@", $username);
		$this->user_name = $user[0];
    } 
	
	
	
	public function index() {
		
		$this->layout='ajax';
		$ldap    = new ADLdap(0);	
		$calendarios = array('suporte');
		$username = $this->user_name;
		$this->set("calendarios", $calendarios);	
			
	}

	function details() {
		if(isset($_REQUEST['unique_id'])){
			$dados = $this->data($_REQUEST['unique_id'],$_REQUEST['data']);
			$this->set("dados" ,$dados);
		}else
			die('Ocorreu um erro');
		
	}

    public function data($unique_id_filter, $data){
		
		$sala = (isset($_REQUEST['sala'])) ? $_REQUEST['sala'] : die('Tem de escolher uma sala');
	
		if($unique_id_filter==NULL)
			$this->autoRender = false;
		
		//Data de inicio
		$dt_inicio_ts =	(isset($_REQUEST['start'])) ? $_REQUEST['start'] : strtotime(date($data.' 00:00:00'));;
		$dt_fim_ts = (isset($_REQUEST['end'])) ? $_REQUEST['end'] : strtotime(date($data.' 23:59:59'));;
		
		/*START CALENDAR*/
		$today = date("Y-m-d");
		$localTZ = 'Portugal/Lisbon';
		$daysahead = '1'; // Today Only
		 
		//$ews = new ExchangeWebServices ($this->host, $this->username, $this->password);
		$ews = new ExchangeWebServices("webmail.ciencias.ulisboa.pt", "ewsimp@fc.ul.pt", "45rftg7KKKl", "Exchange2010_SP2");
		//$ews = new ExchangeWebServices("mail.fc.ul.pt", "ewsimp@fc.ul.pt", "45rftg7KKKl");
		
		//Impersonate
		$ei = new EWSType_ExchangeImpersonationType();
		$sid = new EWSType_ConnectingSIDType();
		$sid->PrimarySmtpAddress = $sala.'@fc.ul.pt';
		$ei->ConnectingSID = $sid;
		$ews->setImpersonation($ei);
		
		$request = new EWSType_FindItemType();
		$request->Traversal = EWSType_ItemQueryTraversalType::SHALLOW;
		$request->ItemShape = new EWSType_ItemResponseShapeType();
		$request->ItemShape->BaseShape = EWSType_DefaultShapeNamesType::DEFAULT_PROPERTIES;
		$request->CalendarView = new EWSType_CalendarViewType();
		$request->CalendarView->StartDate = date ('c',$dt_inicio_ts);
		$request->CalendarView->EndDate = date ('c',$dt_fim_ts);
		$request->ParentFolderIds = new EWSType_NonEmptyArrayOfBaseFolderIdsType();
		$request->ParentFolderIds->DistinguishedFolderId = new EWSType_DistinguishedFolderIdType();
		$request->ParentFolderIds->DistinguishedFolderId->Id = EWSType_DistinguishedFolderIdNameType::CALENDAR;
		$mailBox = new EWSType_EmailAddressType();
		$mailBox->EmailAddress = $sala.'@fc.ul.pt';;
		$request->ParentFolderIds->DistinguishedFolderId->Mailbox = $mailBox;
		$response = $ews->FindItem($request);
		$TotalItemCount = $response->ResponseMessages->FindItemResponseMessage->RootFolder->TotalItemsInView;
	
		if($TotalItemCount == 1)
			$array_to_analyze = $response->ResponseMessages->FindItemResponseMessage->RootFolder->Items;
		else
			$array_to_analyze = $response->ResponseMessages->FindItemResponseMessage->RootFolder->Items->CalendarItem;
		
		$items_formatados = array();
		$count = 0;
		foreach($array_to_analyze as $res) { 
			$unique_id = md5($res->ItemId->Id);
			$ms_id = $res->ItemId->Id;
			$subject = (strlen($res->Subject)>1) ? $res->Subject : 'Sem assunto';
			$localizacao = (strlen($res->Location)>1) ? $res->Location : 'Sem localização';
			$hora_inicio = $res->Start;
			$hora_fim = $res->End;
			$tem_attch = $res->HasAttachments;
			$organizadores = $res->Organizer;
			$organizadores_string = NULL;
			foreach($organizadores as $org) {
				$organizado_por =  $org->Name;
				$organizadores_string = $organizadores_string.$organizado_por."; ";
			}
			
			if(($unique_id_filter!=NULL && $unique_id == $unique_id_filter) || $unique_id_filter==NULL){
				
				array_push($items_formatados, array('unique_id' => ($unique_id), 'id' => $count, 'title' => $subject, 'localizacao' => ($localizacao), 'start' => strtotime($hora_inicio),  'end' => strtotime($hora_fim), 'backgroundColor' => "#444444", 'allDay' => FALSE, 'organizadores' => $organizadores_string, 'ms_id' => $ms_id) );
				
			}
			//if($count==1)	break;
			$count ++;
		}
		if($unique_id_filter!=NULL)
			return $items_formatados;
		else
			echo json_encode($items_formatados);		
    }
}

?>