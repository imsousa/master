<?php

App::import('Vendor', 'NTLMSoapClient', array('file' => 'ExchangeWS' . DS . 'NTLMSoapClient.php'));
App::import('Vendor', 'NTLMSoapClient_Exchange', array('file' => 'ExchangeWS' . DS . 'NTLMSoapClient' . DS . 'Exchange.php'));
App::import('Vendor', 'ExchangeWebServices', array('file' => 'ExchangeWS' . DS . 'ExchangeWebServices.php'));
App::import('Vendor', 'EWSType', array('file' => 'ExchangeWS' . DS . 'EWSType.php'));

/*User*/
App::import('Vendor', 'EWSType_ExchangeImpersonationType', array('file' => 'ExchangeWS' . DS . 'EWSType' . DS . 'ExchangeImpersonationType.php'));
App::import('Vendor', 'EWSType_ConnectingSIDType', array('file' => 'ExchangeWS' . DS . 'EWSType' . DS . 'ConnectingSIDType.php'));


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

/*NEW*/

App::import('Vendor', 'EWSType_NonEmptyArrayOfFieldOrdersType', array('file' => 'ExchangeWS' . DS . 'EWSType' . DS . 'NonEmptyArrayOfFieldOrdersType.php'));

App::import('Vendor', 'EWSType_FieldOrderType', array('file' => 'ExchangeWS' . DS . 'EWSType' . DS . 'FieldOrderType.php'));

class BlocoNotificacoesController extends AppController {
    
    var $name = 'BlocoNotificacoes';
    var $uses = array('MoodleProd');
    var $layout = 'ajax';
    var $helpers = array('Html','Js');
    var $components = array('RequestHandler','Session');

    public function index(){
		/*
		$nr_msgs_moodle = $this->getNrMsgsMoodle();
	
		$this->set("moodle_nr_msgs_por_ler", $nr_msgs_moodle);		
			
		$nr_msgs_exchange = $this->getNrMsgsExchange();

		$this->set("exchange_nr_msgs_por_ler", $nr_msgs_exchange);
		*/
	}
	
	function getNrMsgsMoodle(){
		$results = array();
		try {
			$sql = "SELECT m.*
					FROM mdl_user u, mdl_message m
					WHERE u.id = m.useridto
					and u.email = '".$this->fullUsername."' ";
			$results = $this->MoodleProd->query($sql);
		
		}catch(Exception $e) {
			return 0;
		}
		return count($results);
	}
	
	function getNrMsgsExchange(){
		$ews = new ExchangeWebServices("webmail.ciencias.ulisboa.pt", "ewsimp@fc.ul.pt", "45rftg7KKKl", "Exchange2010_SP2");
		//Impersonate
		$ei = new EWSType_ExchangeImpersonationType();
		$sid = new EWSType_ConnectingSIDType();
		$sid->PrimarySmtpAddress = $this->fullUsername;
		$ei->ConnectingSID = $sid;
		$ews->setImpersonation($ei);

		//Pedido de procurar basta
		$request = new EWSType_FindFolderType();
		$request->Traversal = EWSType_FolderQueryTraversalType::DEEP;
		$request->FolderShape = new EWSType_FolderResponseShapeType();
		$request->FolderShape->BaseShape = EWSType_DefaultShapeNamesType::ALL_PROPERTIES;
		
		//Configurar a View
		$request->IndexedPageFolderView = new EWSType_IndexedPageViewType();
		$request->IndexedPageFolderView->BasePoint = 'Beginning';
		$request->IndexedPageFolderView->Offset = 0;
		
		//Começar na ROOT (tmb aceita INBOX)
		$request->ParentFolderIds = new EWSType_NonEmptyArrayOfBaseFolderIdsType();
		$request->ParentFolderIds->DistinguishedFolderId = new EWSType_DistinguishedFolderIdType();
		$request->ParentFolderIds->DistinguishedFolderId->Id = EWSType_DistinguishedFolderIdNameType::ROOT;

		//O Pedido
		$response = $ews->FindFolder($request);
		//echo '<pre>'.print_r($response, true).'</pre>';
		foreach($response->ResponseMessages->FindFolderResponseMessage->RootFolder->Folders->Folder as $folder){
			//echo $folder->DisplayName.'<br>';
			
			$nome_da_caixa_a_analisar = strtolower($folder->DisplayName);
			
			if($nome_da_caixa_a_analisar == 'a receber' || $nome_da_caixa_a_analisar == 'inbox'){
				
				//pr($folder);
				
				return $folder->UnreadCount;
			}
		}

		return 0;
		
	
	}
	
	function getEmails() {
		die();
		$this->autoRender = false;
		
		$ews = new ExchangeWebServices("webmail.ciencias.ulisboa.pt", "ewsimp@fc.ul.pt", "45rftg7KKKl", "Exchange2010_SP2");
		//Impersonate
		$ei = new EWSType_ExchangeImpersonationType();
		$sid = new EWSType_ConnectingSIDType();
		$sid->PrimarySmtpAddress = $this->fullUsername;
		$ei->ConnectingSID = $sid;
		$ews->setImpersonation($ei);
		
		$request = new EWSType_FindItemType();
		$request->ItemShape = new EWSType_ItemResponseShapeType();
		$request->ItemShape->BaseShape = EWSType_DefaultShapeNamesType::DEFAULT_PROPERTIES;
		$request->Traversal = EWSType_ItemQueryTraversalType::SHALLOW;
		$request->ParentFolderIds = new EWSType_NonEmptyArrayOfBaseFolderIdsType();
		$request->ParentFolderIds->DistinguishedFolderId = new EWSType_DistinguishedFolderIdType();
		$request->ParentFolderIds->DistinguishedFolderId->Id = EWSType_DistinguishedFolderIdNameType::INBOX;
		
		// sort order
		$request->SortOrder = new EWSType_NonEmptyArrayOfFieldOrdersType();
		$request->SortOrder->FieldOrder = array();
		$order = new EWSType_FieldOrderType();
		
		// sorts mails so that oldest appear first
		// more field uri definitions can be found from types.xsd (look for UnindexedFieldURIType)
		$order->FieldURI->FieldURI = 'item:DateTimeReceived'; 
		$order->Order = 'Descending'; 
		$request->SortOrder->FieldOrder[] = $order;
		
		$response = $ews->FindItem($request);
		echo '<pre>'.print_r($response, true).'</pre>';
		
		
		
	}
}
?>