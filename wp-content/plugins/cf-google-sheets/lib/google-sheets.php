<?php
require_once plugin_dir_path(__FILE__).'php-google-oauth/Google_Client.php';
include_once ( plugin_dir_path(__FILE__) . 'autoload.php' );
use Google\Spreadsheet\DefaultServiceRequest;
use Google\Spreadsheet\ServiceRequestFactory;


class cfgooglesheet {
	private $token;
	private $spreadsheet;
	private $worksheet;
	const clientId = '333801235716-8n98am0peiuknmov5bu65ajfvckcolh2.apps.googleusercontent.com';
	const clientSecret = 'EOY7yqljaljARuXYpwqXWkYC';
	const redirect = 'urn:ietf:wg:oauth:2.0:oob';

	public function __construct() {
	}

	//constructed on call
	public static function preauth($access_code){		
		$client = new CFGS_Google_Client();
		$client->setClientId(cfgooglesheet::clientId);
		$client->setClientSecret(cfgooglesheet::clientSecret);
		$client->setRedirectUri(cfgooglesheet::redirect);
		$client->setScopes(array('https://spreadsheets.google.com/feeds'));
		
		$results = $client->authenticate($access_code);
		
		$tokenData = json_decode($client->getAccessToken(), true);
		cfgooglesheet::updateToken($tokenData);
	}
	
	public static function updateToken($tokenData){
		$tokenData['expire'] = time() + intval($tokenData['expires_in']);
		try{
			$tokenJson = json_encode($tokenData);
			update_option('gs_token', $tokenJson);
		} catch (Exception $e) {
			Cfgs_Connector_Utility::debug_log("Token write fail! - ".$e->getMessage());
		}
	}
	
	public function auth(){
		$tokenData = json_decode(get_option('gs_token'), true);
		
		if(time() > $tokenData['expire']){
			$client = new CFGS_Google_Client();
			$client->setClientId(cfgooglesheet::clientId);
			$client->setClientSecret(cfgooglesheet::clientSecret);
			$client->refreshToken($tokenData['refresh_token']);
			$tokenData = array_merge($tokenData, json_decode($client->getAccessToken(), true));
			cfgooglesheet::updateToken($tokenData);
		}
		
		/* this is needed */
		$serviceRequest = new DefaultServiceRequest($tokenData['access_token']);
		ServiceRequestFactory::setInstance($serviceRequest);
	}

	//preg_match is a key of error handle in this case
	public function settitleSpreadsheet($title) {
		$this -> spreadsheet = $title;
	}

	//finished setting the title
	public function settitleWorksheet($title) {
		$this -> worksheet = $title;
	}

	//choosing the worksheet
	public function add_row($data, $auto = true) {
		$spreadsheetService = new Google\Spreadsheet\SpreadsheetService();
		$spreadsheetFeed = $spreadsheetService->getSpreadsheets();
		if ( empty($spreadsheetFeed) )
			throw new Exception("Can't connect to Google Sheets feed"); 
		$spreadsheet = $spreadsheetFeed->getByTitle($this->spreadsheet);
		if ( empty($spreadsheet) )
			throw new Exception("Can't find Google Sheet named '" . $this->spreadsheet . "'"); 
		$worksheetFeed = $spreadsheet->getWorksheets();
		if ( empty($worksheetFeed) )
			throw new Exception("Can't connect to tabs feed in Google Sheet named '" . $this->spreadsheet . "'"); 
		$worksheet = $worksheetFeed->getByTitle($this->worksheet);
		if ( empty($worksheet) )
			throw new Exception("Can't find tab named '" . $this->worksheet . "' in Google Sheet named '" . $this->spreadsheet . "'"); 

		if ($auto) {
			$cellFeed = $worksheet->getCellFeed();
			if ( empty($cellFeed) )
				throw new Exception("Can't connect to tab named '" . $this->worksheet . "' in Google Sheet named '" . $this->spreadsheet . "'"); 
			// get all current header names
			$names = array(); 
			$i = 1;
			while ( 1 ) {
				$cell = $cellFeed->getCell(1, $i);
				if ( empty($cell) )
					break;
				$value = $cell->getContent();
				if ( empty($value) )
					break;
				array_push($names, $value);
				++$i;
			}
			// insert all missing header names
			foreach ($data as $name => $value) {
				if ( !in_array($name, $names) ) {
					$cellFeed->editCell(1, $i, $name);
					++$i;
				}
			}
		}

		$listFeed = $worksheet->getListFeed();
		if ( empty($listFeed) )
			throw new Exception("Can't connect to tab named '" . $this->worksheet . "' in Google Sheet named '" . $this->spreadsheet . "'"); 
		$listFeed->insert($data);
	}

}
?>
