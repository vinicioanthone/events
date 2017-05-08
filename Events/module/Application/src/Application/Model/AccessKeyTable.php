<?php
/**
 * AccessKeyTable DB access
 *
 */
namespace Application\Model;

use Zend\Db\TableGateway\TableGateway;
use Application\Com\DebugLog;
use Zend\Db\Sql\Expression;

class AccessKeyTable
{
	protected $mTableGateway;
	

	/*==========================================================================*/
	/*											    							*/
	/*					Constructor			    							*/
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
	/*					Key public IF		    								*/
	/*											    							*/
	/*==========================================================================*/
	/*==================================================================*/
	/*					entry／update／delete 							*/
	/*==================================================================*/
	/**
	 * Key entry
	 *
	 */
	public function entryAccessKey(AccessKey $accessKey)
	{
	 	$data = array(
			'access_user'	    => $accessKey->AccessUser,
			'access_key'	    => new Expression("SHA1(".$accessKey->AccessKey.")"),
			// 'expire'	=> $accessKey->Expire,
		);

		// 既にユーザーＩＤがあれば失敗
		if( $this->getAccessKeyByUser($accessKey->AccessUser) ) {

			DebugLog::debugLog(__CLASS__, __FUNCTION__, __LINE__, "entryAccessKey accessKey ERR:User=".$accessKey->AccessUser);
			return false;
		}
		
		$this->mTableGateway->insert($data);

		return true;
	}


	/**
	 * Key update
	 *
	 */
	public function updateAccessKey(AccessKey $accessKey)
	{
		// ＩＤが無ければエラー
		if( ! $this->getAccessKeyByUser($accessKey->AccessUser) ) {

			DebugLog::warningLog(__CLASS__, __FUNCTION__, __LINE__, "updateAccessKey ERR:User=".$accessKey->AccessUser);
			return false;
		}

	 	$data = array(
			'access_key'	=> new Expression("SHA1(".$accessKey->AccessKey.")"),
			// 'expire'	=> $accessKey->expire,
		);

		$this->mTableGateway->update($data, array('access_user' => $accessKey->AccessUser));

		return true;
	}


	/**
	 * Key delete
	 *
	 */
	public function deleteAccessKey(AccessKey $accessKey)
	{
		// error if no Id
		if( ! $this->getAccessKeyByUUID($accessKey->AccessUser) ) {
			
			DebugLog::warningLog(__CLASS__, __FUNCTION__, __LINE__, "delete UUID ERR:User=".$accessKey->AccessUser);
			return false;
		}

		$this->mTableGateway->delete(array('access_user' => $accessKey->AccessUser));
		return true;
	}


	/*==================================================================*/
	/*					get 				   							*/
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
	 * get all
	 *
	 */
	public function fetchAll()
	{
		$resultSet = $this->mTableGateway->select();
		return $resultSet;
	}
	

	/**
	 * Get by user
	 *
	 */
	public function getAccessKeyByUser($accessUser)
	{
	 	$accessUser = (string)$accessUser;
		$rowset = $this->mTableGateway->select(array('access_user' => $accessUser));

		$row = $rowset->current();
		
		return $row;
	}

	/**
	 * Get by user and key
	 *
	 */
	public function getAccessKeyByKey($accessKey)
	{
	 	// $accessUser = (string)$accessUser;
		$rowset = $this->mTableGateway->select(array('access_key' => $accessKey	));

		$row = $rowset->current();
		
		return $row;
	}
}

