<?php

namespace Application\Com;


/*==========================================================================*/
/*											    							*/
/*					API return JSON header creation 						*/
/*											    							*/
/*==========================================================================*/
/**
 *	API return JSON header creation utility
 *
 */
class JsonHeader
{
	/*==================================================================*/
	/*					Public staticＩＦ    							*/
	/*==================================================================*/
	/**
	 * Create data for response
	 *
	 * @param string $data Data before JSON conversion (below results section)
	 * @return array All the array data before JSON conversion
	 */
	public static function getArrayData($data)
	{
		$topdata = array('ret' => 0, 'message' => "success");
		return array_merge($topdata, $data);
	}
	
	/**
	 * Create error response data
	 *
	 * @param string $resval Value to set for parameter ret
	 * @param string $message Value to set for parameter message (message string)
	 * @return array All the array data before JSON conversion
	 */
	public static function getErrorArrayData($resval, $message)
	{
		$topdata = array('ret' => $resval, 'message' => ((string)$message));
		return $topdata;
	}
}
