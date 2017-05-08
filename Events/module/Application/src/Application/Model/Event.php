<?php
/**
 * Event management model
 *
 */

namespace Application\Model;

class Event
{
	public $Id;
	public $EventFulId;
	public $EventName;
	public $Location;
	public $Lat;
	public $Lon;
	public $Description;
	public $OngoingFlg;
	public $VenueName;
	public $VenueAddr;
	public $CityName;
	public $RegionName;
	public $PostalCode;
	public $CountryName;
	public $AllDayFlg;
	public $StartTime;
	public $EndTime;
	public $Url;

	public $Time;
	public $DeleteFlg;
	

	public function exchangeArray($data)
	{
		$this->Id     	 = (isset($data['id'])) ? (int)$data['id'] : 0;
		$this->EventFulId  = (isset($data['eventful_id'])) ? $data['eventful_id'] : "";
		$this->EventName  = (isset($data['event_name'])) ? $data['event_name'] : "";
		$this->Location  = (isset($data['location'])) ? $data['location'] : 0;
		$this->Lat  = (isset($data['lat'])) ? $data['lat'] : 0;
		$this->Lon  = (isset($data['lon'])) ? $data['lon'] : 0;
		$this->Description   = (isset($data['description'])) ? $data['description'] : "";
		$this->OngoingFlg  = (isset($data['ongoing_flag'])) ? (int)$data['ongoing_flag'] : 0;
		$this->VenueName   = (isset($data['venue_name'])) ? $data['venue_name'] : "";
		$this->VenueAddr   = (isset($data['venue_address'])) ? $data['venue_address'] : "";
		$this->CityName   = (isset($data['city_name'])) ? $data['city_name'] : "";
		$this->RegionName   = (isset($data['region_name'])) ? $data['region_name'] : "";
		$this->PostalCode   = (isset($data['postal_code'])) ? (int)$data['postal_code'] : 0;
		$this->CountryName   = (isset($data['country_name'])) ? $data['country_name'] : "";
		$this->AllDayFlg   = (isset($data['all_day'])) ? (int)$data['all_day'] : 0;
		$this->StartTime   = (isset($data['start_time'])) ? $data['start_time'] : null;
		$this->EndTime   = (isset($data['end_time'])) ? $data['end_time'] : null;
		$this->Url   = (isset($data['url'])) ? $data['url'] : "";
		$this->Time   = (isset($data['time'])) ? $data['time'] : null;
		$this->DeleteFlg = (isset($data['delete_flg'])) ? $data['delete_flg'] : false;
	}
}

