<?php
/**
 * User User account (1 account per OS） model
 *
 */

namespace Application\Model;

class AccessKey
{
	
	public $AccessUser   = NULL;
	public $AccessKey  = NULL;
	// public $Expire = NULL;

	
	public function exchangeArray($data)
	{
		$this->AccessUser  	= (isset($data['access_user'])) ? $data['access_user'] : "";
		$this->AccessKey 	= (isset($data['access_key'])) ? $data['access_key'] : "";
		// $this->Expire = (isset($data['expire'])) ? $data['expire'] : 0;
	}
}