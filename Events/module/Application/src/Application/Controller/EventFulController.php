<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

require_once '/opt/vinicio/vhosts/myhostname.com/module/events/module/Application/src/Application/vendor/autoload.php';

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;
use Application\Com\TableGetter;
use Application\Model\Event;
use Application\Model\Category;
use Zend\View\Model\JsonModel;
use Zend\Filter\StringTrim;
use Zend\Json\Server\Request;
use Zend\Json\Json;

use Guzzle\Http\EntityBody;
use GuzzleHttp\Client;

class EventFulController extends AbstractActionController
{

    protected $mTableGetter;
    
    public function EventFulAction()
    {

    	$viewmodel = new ViewModel();
        $viewmodel->setTerminal(true);

        // var_dump(realpath(__DIR__));
        // 
        
        if( ! $this->getParameter("latitude", $lat) ) {
			$lat = "33.8450598"; //MNA latitude
		}

		if( ! $this->getParameter("longitude", $lon) ) {
			$lon = "-118.3556668"; //MNA longitude
		}


        // Guzzle client
		$client = new Client();
		$tableGetter = $this->getTableGetter();
		$eventTable  = $tableGetter->getEventTable($this);
		$categoryTable  = $tableGetter->getCategoryTable($this);

        $url = 'http://api.eventful.com/json/events/search';
   	 	$appKey ='3PjhNf5nbQSWtXs8';
   	 	$location = $lat.','.$lon;
   	 	// $location = 'Torrance';
   	 	$within = 50;
   	 	$units = 'km';
   	 	// var_dump($location);
   	 	$date ='today';
   	 	$pageSize = 500;
   	 	// $category = '';
   	 	$sort = 'relevance';


	    // query
	   	$query = ['app_key' => $appKey,
	   			  'location' => $location,
	   			  'within' => $within,
	   			  'units' => $units,
	   			  'include' => 'categories',
	   			  'date' => $date,
	   			  'page_size' => 500,
	   			  'sort_order' => $sort ];
	   	// options for request
	   	$options = array(
						 'query' => $query,
						 'exceptions' => false);

	   	// var_dump($options);

	   	// create request
	   	$request = $client->createRequest('GET', $url, $options);


	   	// get response
	   	
	   	$response = $client->send($request);
	   	$body = $response->getBody();

		// echo $body;



		// get json data 
		$responseArrayDB = $response->json();


		// var_dump($responseArrayDB["events"]["event"]);
		
		if (isset($responseArrayDB["events"])) {

			$eventCount = 0;

			foreach ($responseArrayDB["events"]["event"] as $key => $item) {
			
			
				

				// check if date of event is today
			
				if(isset($item["start_time"])){

					$nowDate = date('Y-m-d H:i:s');
				    $nowDate=date('Y-m-d H:i:s', strtotime($nowDate));;
				    //echo $nowDate; // echos today! 
				    $startDateBegin = date('Y-m-d H:i:s', strtotime($item["start_time"]));
				    $endDateEnd = date('Y-m-d H:i:s', strtotime($item["stop_time"]));

				    if (($nowDate > $startDateBegin) && ($nowDate < $endDateEnd))
				    {
				      $ongoingFlag = 1;
				    }
				    else
				    {
				      $ongoingFlag = 0;
				    }

				}

				if(isset($item['description'])){

					// cut comment and spotname
					if (mb_strlen($item['description'], "UTF-8") > 512 ){

		 				$item['description'] = mb_substr($item['description'], 0, 512, "UTF-8");
					}

				}


				if(isset($item['title'])){

					if (mb_strlen($item['title'], "UTF-8") > 64 ){

		 				$item['title'] = mb_substr($item['title'], 0, 64, "UTF-8");
					}


				}

				if(isset($item['venue_name'])){

					if (mb_strlen($item['venue_name'], "UTF-8") > 64 ){

		 				$item['venue_name'] = mb_substr($item['venue_name'], 0, 64, "UTF-8");
					}


				}
				

			

			
				$event = new Event();

				$event->EventFulId  = (isset($item['id'])) ? $item['id'] : null;
				$event->EventName  = (isset($item['title'])) ? $item['title'] : null;
				$event->Lat  = (isset($item['latitude'])) ? $item['latitude'] : null;
				$event->Lon  = (isset($item['longitude'])) ? $item['longitude'] : null;
				$event->Description   = (isset($item['description'])) ? $item['description'] : "";
				$event->OngoingFlg  = (isset($ongoingFlag)) ? $ongoingFlag : null;
				$event->VenueName   = (isset($item['venue_name'])) ? $item['venue_name'] : null;
				$event->VenueAddr   = (isset($item['venue_address'])) ? $item['venue_address'] : null;
				$event->CityName   = (isset($item['city_name'])) ? $item['city_name'] : null;
				$event->RegionName   = (isset($item['region_name'])) ? $item['region_name'] : null;
				$event->PostalCode   = (isset($item['postal_code'])) ? (int)$item['postal_code'] : null;
				$event->CountryName   = (isset($item['country_name'])) ? $item['country_name'] : null;
				$event->AllDayFlg   = (isset($item['all_day'])) ? (int)$item['all_day'] : null;
				$event->StartTime   = (isset($item['start_time'])) ? $item['start_time'] : null;
				$event->EndTime   = (isset($item['stop_time'])) ? $item['stop_time'] : null;
				$event->Url   = (isset($item['url'])) ? $item['url'] : null;
				
				$event->DeleteFlg = false;


				// var_dump($event);

				if($eventTable->entryEvent($event)){

					// // ＤＢに格納した数を返却
					// $resultData = array('status' => "success");

					// // 返却データ生成
					// $result = JsonHeader::getArrayData($resultData);
					
					$eventCount++;

					if(isset($item["categories"]["category"])){

						// fill categories table
						// 
						foreach ($item["categories"]["category"] as $value) {


							$row = $eventTable->getEventByEventFulId($item['id']);

							// var_dump($row);

							if($row){

								$value['name'] = str_replace(' &amp; ', '&', $value['name']);

								$category = new Category();

								$category->EventId = $row->Id;
								$category->Category = $value['name'];

								// var_dump($category);

								if($categoryTable->entryCategory($category)){

									// echo "pass";
								}
								else{

									// echo "error";
								}

							}		

						}

					}

				}
				else{

					
					continue;

				}	

			}

			if ($eventCount == 0){

				$result["error"] = false;
                $result["message"] = "success but ".$eventCount." entries";
                return new JsonModel(array(
                	'eventCount' => $eventCount,
                    'status' => $result,
                    ));


			}
			else{

				$result["error"] = false;
                $result["message"] = "success. ".$eventCount." entries";
                return new JsonModel(array(
                	'eventCount' => $eventCount,
                    'status' => $result,
                    ));


			}

			
			
		}

		else{

			$result["error"] = true;
                $result["message"] = "failed. something happened";
                return new JsonModel(array(
                	'count' => 0,
                    'status' => $result,
                    ));
		}
    

        
    }

	
	/*==================================================================*/
	/*				Local functions										*/
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
