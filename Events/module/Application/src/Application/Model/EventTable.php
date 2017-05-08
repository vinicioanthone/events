<?php
/**
 * SpotTable DB access
 *
 */
namespace Application\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Expression;
use Application\Com\DebugLog;

class EventTable
{
	protected $mTableGateway;
	
	/*==========================================================================*/
	/*											    							*/
	/*					constructor 			    							*/
	/*											    							*/
	/*==========================================================================*/
	/**
	 * Constructor
	 *
	 */
	public function __construct(TableGateway $tableGateway)
	{
		$this->mTableGateway = $tableGateway;
	}


	/*==========================================================================*/
	/*											    							*/
	/*					Event public IF		    								*/
	/*											    							*/
	/*==========================================================================*/
	/*==================================================================*/
	/*					entry／update／delete							*/
	/*==================================================================*/
	/**
	 * Event entry
	 *
	 */
	 public function entryEvent(Event $event)
	 {

	 	$lat = $event->Lat;
	 	$lon = $event->Lon;

	 	$data = array(
			'id'     	 => $event->Id,
			'eventful_id' => $event->EventFulId,
			'event_name' => $event->EventName,
			'location' 	 => new Expression("GeomFromText( 'POINT(".$lon." ".$lat.")')"),
			'description' => $event->Description,
			'ongoing_flag' => $event->OngoingFlg,
			'venue_name' => $event->VenueName,
			'venue_address' => $event->VenueAddr,
			'city_name' => $event->CityName,
			'region_name' => $event->RegionName,
			'postal_code' => $event->PostalCode,
			'country_name' => $event->CountryName,
			'all_day' => $event->AllDayFlg,
			'start_time' => $event->StartTime,
			'end_time' => $event->EndTime,
			'url' => $event->Url,

			
			'delete_flg' => $event->DeleteFlg,
		);

		// var_dump($data);

		if( $event->Id != 0 ) {
			// DebugLog::warningLog(__CLASS__, __FUNCTION__, __LINE__, "entry id ERR:id=".$spot->Id);
			return false;
		}
		if($this->getEventByEventFulId($event->EventFulId)){

			return false;
		}
		
		$this->mTableGateway->insert($data);
		
		return true;
	 }


	 /**
	 * event update
	 *
	 */
	public function updateEvent(Event $event)
	{
		// ＩＤが無ければエラー
		if( ! $this->getSpotById($event->Id) ) {
			// DebugLog::warningLog(__CLASS__, __FUNCTION__, __LINE__, "update id ERR:id=".$spot->Id);
			return false;
		}

	 	$lat = $event->Lat;
	 	$lon = $event->Lon;

	 	$data = array(
			'id'     	 => $event->Id,
			'eventful_id' => $event->EventFulId,
			'event_name' => $event->EventName,
			'location' 	 => new Expression("GeomFromText( 'POINT(".$lon." ".$lat.")')"),
			'description' => $event->Description,
			'ongoing_flag' => $event->OngoingFlg,
			'venue_name' => $event->VenueName,
			'venue_address' => $event->VenueAddr,
			'city_name' => $event->CityName,
			'region_name' => $event->RegionName,
			'postal_code' => $event->PostalCode,
			'country_name' => $event->CountryName,
			'all_day' => $event->AllDayFlg,
			'start_time' => $event->StartTime,
			'end_time' => $event->EndTime,
			'url' => $event->Url,

			
			'delete_flg' => $event->DeleteFlg,
		);

		$this->mTableGateway->update($data, array('id' => $event->Id));

		return true;
	}


	/**
	 * delete event
	 *
	 */
	public function deleteEvent(Event $event)
	{
		// error if no ID
		if( ! $this->getSpotById($event->Id) ) {
			DebugLog::warningLog(__CLASS__, __FUNCTION__, __LINE__, "delete id ERR:id=".$event->Id);
			return false;
		}

		$this->mTableGateway->delete(array('id' => $event->Id));
		return true;
	}
	


	 /*==================================================================*/
	/*					GET					   							*/
	/*==================================================================*/
	/**
	 * Number of cases
	 *
	 */
	public function getRowCount()
	{
		$resultSet = $this->fetchAll();
		$rowCount = count($resultSet);

		return $rowCount;
	}


	/**
	 * Get all
	 *
	 */
	public function fetchAll()
	{
		$resultSet = $this->mTableGateway->select(function ($select) {
			$select->columns(array('id',
								'eventful_id', 
								'event_name',
								'location' => new Expression('ST_asText(location)'),
								'lat' => new Expression('ST_Y(location)'),
								'lon' => new Expression('ST_X(location)'),
								'description',
								'ongoing_flag',
								'venue_name',
								'venue_address',
								'city_name',
								'region_name',
								'postal_code',
								'country_name',
								'all_day',
								'start_time',
								'end_time',
								'url',
								'time' => new Expression('CONVERT_TZ(time, "UTC", "+9:00")')));
			$select->order('time DESC');
		});
		return $resultSet;
	}



	/**
	 * get by ID
	 *
	 */
	public function getEventByEventFulId($id)
	{
	 	
		// $rowset = $this->mTableGateway->select(array('id' => $id));
		// 
		$rowset = $this->mTableGateway->select(function ($select) use ($id) {
			$select->columns(array('id',
								'eventful_id', 
								'event_name',
								'location' => new Expression('ST_asText(location)'),
								'lat' => new Expression('ST_Y(location)'),
								'lon' => new Expression('ST_X(location)'),
								'description',
								'ongoing_flag',
								'venue_name',
								'venue_address',
								'city_name',
								'region_name',
								'postal_code',
								'country_name',
								'all_day',
								'start_time',
								'end_time',
								'url',
								'time' => new Expression('CONVERT_TZ(time, "UTC", "+9:00")')));
			$select->where(array('eventful_id' => $id,
								 'delete_flg' => false ));
			
		});
		$row = $rowset->current();
		// var_dump($row);

		
		return $row;
	}





	/**
	 * Get by geometry
	 *
	 */
	public function getEventByGeometry($lat, $lon, $max, $dist, $ongoing)
	{


		if ($max){
			$useCount = true;
			$max = (int)$max;

		}
		else{
			$useCount = false;
		}

		if (isset($ongoing)){

			// convert to boolen
			$ongoing = $ongoing === 'true'? true: false;

		}
		

		if (isset($ongoing) && $ongoing == true){

			$useOngoingFlag = true;
		}
		else{

			$useOngoingFlag = false;
		}

		// $dist = $dist/1000; //m to km


		// calculation for square
		$rlon1 = $lon - $dist / abs(cos(deg2rad($lat))*110575); 
		$rlon2 = $lon + $dist / abs(cos(deg2rad($lat))*110575); 
		$rlat1 = $lat - ($dist/110575);
		$rlat2 = $lat + ($dist/110575);
	 	
	 	

		$rowset = $this->mTableGateway->select(function ($select) use ($lat, $lon, $max, $rlon1, $rlon2, $rlat1, $rlat2, $useCount, $useOngoingFlag) {
			$select->columns(array('id',
								'eventful_id', 
								'event_name',
								'location' => new Expression('ST_asText(location)'),
								'lat' => new Expression('ST_Y(location)'),
								'lon' => new Expression('ST_X(location)'),
								'description',
								'ongoing_flag',
								'venue_name',
								'venue_address',
								'city_name',
								'region_name',
								'postal_code',
								'country_name',
								'all_day',
								'start_time',
								'end_time',
								'url',
								'time' => new Expression('CONVERT_TZ(time, "UTC", "+9:00")')));

			$select->where(array('ST_Within(location, envelope(linestring(point('.$rlon1.', '.$rlat1.'), point('.$rlon2.', '.$rlat2.')))) ',
								 'delete_flg' => false ));

			if($useOngoingFlag){
				$select->where(array('ongoing_flag' => 1));
			}

			$select->order(new Expression("ST_Distance( GeomFromText( 'POINT(".$lon." ".$lat.")' ), location)"));

			if ($useCount){

				$select->limit($max);

			}

			// var_dump($select->where);
			
			
		});

		// var_dump($rowset);
		
		return $rowset;
	}



	/**
	 * get by period
	 *
	 */
	public function getSpotByPeriod($start, $end)
	{
	 	
	 	$startDate = new \DateTime($start);
	 	$endDate   = new \DateTime($end);

	 	$startDate->setTimezone(new \DateTimeZone('UTC'));
	 	$endDate->setTimezone(new \DateTimeZone('UTC'));

	 	$startUTC = $startDate->format('Y-m-d H:i:s');
	 	$endUTC = $endDate->format('Y-m-d H:i:s');

	 	// var_dump($startUTC, $endUTC);

		$rowset = $this->mTableGateway->select(function ($select) use ($startUTC, $endUTC) {
			$select->columns(array('id',
								'eventful_id', 
								'event_name',
								'location' => new Expression('ST_asText(location)'),
								'lat' => new Expression('ST_Y(location)'),
								'lon' => new Expression('ST_X(location)'),
								'description',
								'ongoing_flag',
								'venue_name',
								'venue_address',
								'city_name',
								'region_name',
								'postal_code',
								'country_name',
								'all_day',
								'start_time',
								'end_time',
								'url',
								'time' => new Expression('CONVERT_TZ(time, "UTC", "+9:00")')));

			$select->where(array('delete_flg' => false     ));
			$select->where->greaterThanOrEqualTo('time', $startUTC);
			$select->where->lessThanOrEqualTo('time', $endUTC);
			$select->order('time DESC');
		});

		return $rowset;
	}



	
	
}

