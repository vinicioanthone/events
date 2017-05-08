<?php

namespace Application\Com;

use Zend\Db\TableGateway\TableGateway;
use Application\Model\AccessKey;
use Application\Com\TableGetter;
use Zend\Http\Request;


/*==========================================================================*/
/*											    							*/
/*					Authorization/Authentication    						*/
/*											    							*/
/*==========================================================================*/
/**
 *	Authorization utility
 *
 */
class AuthManager
{


  

	/*==================================================================*/
	/*					StaticＩＦ		    							*/
	/*==================================================================*/

    /**
     * Key authentication
     *
     */
    private static function authKey($controller, $tableGetter, $accessKey)
    {
    	$tokenTable   = $tableGetter->getAccessKeyTable($controller);
    	$row = $tokenTable->getAccessKeyByKey($accessKey);

    	// Returned if the key has been issued
    	if( $row ) {
    		return true;
    	}

    	return false;
    }



     /**
    * Function to authenticate every request
    * Checking if the request has valid api key in the 'Authorization' header
    */
    public static function authenticate($controller, $tableGetter )
    {

        // $authManager = new AuthManager();

        $request = $controller->getRequest();
        $headers = $request->getHeaders();
        // var_dump($request);

        $response = array();

        // Check Authorization header presence
        if (!$headers->has('Authorization')) {

           return false;

        }

        // get the API key
        $api_key = $headers->get('Authorization')->getFieldValue();
        // echo $api_key;

        // verify if API key exists
        if (!self::authKey($controller, $tableGetter, $api_key)){

            return false;

        }else {
            

            return SUCCESS;
        

        }

        
    }
    


}
