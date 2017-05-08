<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\Json\Json;
use Zend\Json\Server\Request;
use Application\Model\Event;
use Application\Model\Category;

use Application\Com\TableGetter;
use Application\Com\JsonHeader;
use Application\Com\DebugLog;
use Application\Com\AuthManager;
use Zend\View\Model\JsonModel;




/*==================================================================*/
/*					Error messages									*/
/*==================================================================*/
define("ERR_PRM_ID",  1);
define("ERR_PRM_MSG", "Parameter error");
define("ERR_ADMIN_ID", 2);
define("ERR_ADMIN_MSG", "Not registered");
define("ERR_GENRE_ID", 3);
define("ERR_GENRE_MSG", "Unregistered category");
define("ERR_SPOT_ID", 4);
define("ERR_SPOT_MSG", "Unregistered event");
define("ERR_AUTH_ID", 5);
define("ERR_AUTH_MSG", "Error with authentication. Please verify the API key");
define("ERR_REG_ID", 6);
define("ERR_REG_MSG", "Already registered");
define("ERR_SRV_ID", 7);
define("ERR_SRV_MSG", "server error");


define('SUCCESS', "pass");


/*==========================================================================*/
/*											    							*/
/*					Event controller		  								*/
/*											    							*/
/*==========================================================================*/
/**
 *	Event controller
 *
 */
class EventApiController extends AbstractActionController
{
	protected $mTableGetter;


	/*==========================================================================*/
	/*											    							*/
	/*					Event API				    							*/
	/*											    							*/
	/*==========================================================================*/

	

	/*==================================================================*/
	/*					entry／delete		    						*/
	/*==================================================================*/



	/**
	 * entry event
	 *
	 *
	 */
	public function entryEventAction()
	{
		// Disable automatic rendering mode
		$view = new ViewModel();
		$view->setTerminal(true);

		// Error information
		$err = false;

		// Parameter acquisition / check
		if( ! $this->getParameter("eventfulId", $eventfulId) ) {
			$err = true;
		}

		if( ! $this->getParameter("eventName", $eventName) ) {
			$err = true;
		}

		if( ! $this->getParameter("latitude", $lat) ) {
			$err = true;
		}

		if( ! $this->getParameter("longitude", $lon) ) {
			$err = true;
		}

		if( ! $this->getParameter("description", $description) ) {
			$description = "";
		}

		if( ! $this->getParameter("ongoingFlag", $ongoingFlag) ) {
			$err = true;
		}

		if( ! $this->getParameter("categories", $categories) ) {
			$err = true;
		}

		if( ! $this->getParameter("venueName", $venueName) ) {
			$err = true;
		}

		if( ! $this->getParameter("venueAddress", $venueAddr) ) {
			$err = true;
		}

		if( ! $this->getParameter("cityName", $cityName) ) {
			$err = true;
		}

		if( ! $this->getParameter("regionName", $regionName) ) {
			$err = true;
		}

		if( ! $this->getParameter("postalCode", $postalCode) ) {
			$err = true;
		}

		if( ! $this->getParameter("countryName", $countryName) ) {
			$err = true;
		}

		if( ! $this->getParameter("allDayFlag", $allDayFlag) ) {
			$err= true;
		}

		if( ! $this->getParameter("startTime", $startTime) ) {
			$err = true;
		}
		if( ! $this->getParameter("endTime", $endTime) ) {
			$err = true;
		}

		if( ! $this->getParameter("url", $url) ) {
			$err = true;
		}


		// check if is numeric 
		if (!is_numeric($lat) || !is_numeric($lon)){

			$err = true;

		}



		// in case of error true
		if( $err ) {
			// Generate return data
			$result = JsonHeader::getErrorArrayData(ERR_PRM_ID, ERR_PRM_MSG);

			// Json output
			return new JsonModel($result);
		}


		// cut comment and spotname
		if (mb_strlen($description, "UTF-8") > 256 ){

 			$description = mb_substr($description, 0, 256, "UTF-8");
		}
		if (mb_strlen($eventName, "UTF-8") > 64 ){

 			$eventName = mb_substr($eventName, 0, 64, "UTF-8");
		}



		// Get table
		$tableGetter = $this->getTableGetter();
		$eventTable  = $tableGetter->getEventTable($this);


		// Confirm API authentication
		if( AuthManager:: authenticate($this, $tableGetter) != SUCCESS )
		{

			// Generate return data
			$result = JsonHeader::getErrorArrayData(ERR_AUTH_ID, ERR_AUTH_MSG);

			// Json output
			return new JsonModel($result);
		}




		// data count
		$dataCount = 1;

			// Get new event 
			$event = new Event();
			// $spot->Id 	= 0;
			// 
			// 
			
			$event->EventFulId  = $eventfulId;
			$event->EventName  = $eventName;
			$event->Lat  = $lat;
			$event->Lon  = $lon;
			$event->Description   = $description;
			$event->OngoingFlg  = $ongoingFlag;
			$event->VenueName   = $venueName;
			$event->VenueAddr   = $venueAddr;
			$event->CityName   = $cityName;
			$event->RegionName   = $regionName;
			$event->PostalCode   = $postalCode;
			$event->CountryName   = $countryName;
			$event->AllDayFlg   = $allDayFlag;
			$event->StartTime   = $startTime;
			$event->EndTime   = $endTime;
			$event->Url   = $url;
			
			$event->DeleteFlg = false;



			// event registration in table
			if($eventTable->entryEvent($event)){

				// Return the number for reference
				$resultData = array('count' => $dataCount);

				// Generate return data
				$result = JsonHeader::getArrayData($resultData);

			}

			else {
				// Generate return data
				$result = JsonHeader::getErrorArrayData(ERR_SRV_ID, ERR_SRV_MSG);
			}
		

		// Json output
		return new JsonModel($result);
	}


	


	/*==================================================================*/
	/*							obtain data    							*/
	/*==================================================================*/



	/**
	 * get event by coordinates
	 *
	 */
	public function getEventByCoordinatesAction()
	{
		// Disable automatic rendering mode
		$view = new ViewModel();
		$view->setTerminal(true);

		// // Error information
		$err = false;
		$useCategory = false;
		$useDate = false;
		$useDistance = false;
		$useOngoing = false;

		
		// Parameter acquisition / check
		if( ! $this->getParameter("latitude", $lat) ) {
			$err = true;
		}

		if( ! $this->getParameter("longitude", $lon) ) {
			$err = true;
		}

		if( ! $this->getParameter("count", $max) ) {
			// $max = null;
			$max = 100;

		}
		if(  $this->getParameter("categories", $categoryListString) ) {
			$useCategory = true;

		}
		else{
			$category = NULL;
		}

		if(  $this->getParameter("distance", $distance) ) {
			$useDistance = true;

		}

		if( ! $this->getParameter("ongoing", $ongoing) ) {
			$ongoing = NULL;

		}



		if ($useDistance){

			if(is_numeric($distance)){

				$dist = (int)$distance; //TODO cast for float
			}
			else {

				// // Generate return data
				$result = JsonHeader::getErrorArrayData(ERR_PRM_ID, ERR_PRM_MSG);

				// Json output
				return new JsonModel($result);

			}
		}
		else{
			$dist = 5000; //default distance in m

		}


		// check if numeric
		if (!is_numeric($lat) || !is_numeric($lon)){

			$err = true;

		}



		// Check parameters
		if( $err ) {
			// Generate return data
			$result = JsonHeader::getErrorArrayData(ERR_PRM_ID, ERR_PRM_MSG);

			// Json output
			return new JsonModel($result);
		}

		// // Get table
		$tableGetter  = $this->getTableGetter();
		$eventTable   = $tableGetter->getEventTable($this);
		$categoryTable  = $tableGetter->getCategoryTable($this);

		// Confirm API authentication
		if( AuthManager:: authenticate($this, $tableGetter) != SUCCESS )
		{

			// Generate return data
			$result = JsonHeader::getErrorArrayData(ERR_AUTH_ID, ERR_AUTH_MSG);

			// Json output
			return new JsonModel($result);
		}


		// get event table
		$resultSet = $eventTable->getEventByGeometry($lat, $lon, $max, $dist, $ongoing);

		// var_dump($resultSet->getDataSource());
		$rowCount = count($resultSet);

		// Set the result to Array
		$eventData = array();


		foreach($resultSet as $row) {


			$categoriesDbArray = [];
			$mCategory = "";			
			$categories = $categoryTable->getCategoriesByEventId($row->Id);

			foreach ($categories as $category) {

				// data from db
				$categoriesDbArray[] = $category->Category;
			}


			// only if use category
			if ($useCategory && isset($categoryListString)){


				$categoriesReqArray = [];

				// remove spaces from request string
				$categoryListString = str_replace(' ', '', $categoryListString);

				// separate by comma
				if(strrpos($categoryListString, ",")){

					$categoriesReqArray = explode(",", $categoryListString);
				}
				else{

					$categoriesReqArray[0] = $categoryListString;

				}

			
			
				
				// compare data request vs DB
				$search = array_intersect($categoriesReqArray, $categoriesDbArray);

				
				if(empty($search)){

					// echo "empty";

					// scape iteration thus skipping element
					continue;

				}
				else{
					// echo "not empty";
					// var_dump($search);

					$mCategory = $categoriesDbArray;
					
				}


			}
			else {

				$mCategory = $categoriesDbArray;
			}




			// array for response
			$eventArray =
				array(
					array(
						'id'        => $row->Id,
						'latitude'  => $row->Lat,
						'longitude' => $row->Lon,
						'eventName'  => $row->EventName,
						'description' => $row->Description,
						'categories' 	=> $mCategory,
						'ongoingFlag' => $row->OngoingFlg,
						'venueName' 	=> $row->VenueName,
						'venueAddress' => $row->VenueAddr,
						'cityName' => $row->CityName,
						'regionName' => $row->RegionName,
						'postalCode' => $row->PostalCode,
						'countryName' => $row->CountryName,
						'cityName' => $row->CityName,
						'allDayFlag' => $row->AllDayFlg,
						'startTime' => $row->StartTime,
						'endTime' => $row->EndTime,
						'url' => $row->Url,
						'createdAt' => $row->Time

					)
				);
			$eventData	= array_merge($eventData, $eventArray);
		}



		$topData = array('count' => count($eventData));


		$eventResult = array( 'event' => $eventData );

		$resultData = array_merge($topData, $eventResult);

		// Generate return data
		$result = JsonHeader::getArrayData($resultData);
		// var_dump($result);

		// Json output
		return new JsonModel($result);
	}









	/*==================================================================*/
	/*				local functions										*/
	/*==================================================================*/
	/**
	 * DB table management acquisition
	 *
	 */
	public function getTableGetter()
	{
		if( ! $this->mTableGetter ) {
			$this->mTableGetter = new TableGetter();
		}

		return $this->mTableGetter;
	}


	/**
	 * Input parameter check
	 * @in  --- key   Parameter key
	 *　@out --- value Parameter
	 *　@ret --- false or true
	 */
	private function getParameter($key, &$value)
	{


		$request = $this->getRequest();
		// var_dump($request);

		if ($request->isGet()){

			$body = $request->getQuery();
			// var_dump($body->$key);
			if ( !array_key_exists($key, $body) ){

				return false;
			}

			$value = $body->$key;
			// var_dump($value);



		}
		else if($request->isPost()){

			$contentType = $request->getHeaders('Content-Type')->toString();
			// var_dump($contentType);
			$body = $request->getContent();
			// var_dump($body);
			if (!$body) {
				return false;
			}

			if (!$this->isJson($body)){

				return false;
			}

			if(strstr($contentType, 'application/json')){

				$json = Json::decode($body);
				// var_dump($json);
				//
				if ( !array_key_exists($key, $json)){

					return false;
				}

				$value = $json->$key;


			}
			else{
				return false;
			}

		}
		return true;
	}


	// check if is Json
	public function isJson($string,$return_data = false) {
	  $data = json_decode($string);
	 return (json_last_error() == JSON_ERROR_NONE) ? ($return_data ? $data : TRUE) : FALSE;
	}




}
