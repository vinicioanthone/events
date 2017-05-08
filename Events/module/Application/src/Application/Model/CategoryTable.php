<?php
/**
 * Category Table DB access
 *
 */
namespace Application\Model;

use Zend\Db\TableGateway\TableGateway;
use Application\Com\DebugLog;

class CategoryTable
{
	protected $mTableGateway;
	
	/*==========================================================================*/
	/*											    							*/
	/*					Constructor			    								*/
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
	/*					category public IF			    						*/
	/*											    							*/
	/*==========================================================================*/
	/*==================================================================*/
	/*					entry／update／delete		   							*/
	/*==================================================================*/
	

	 public function entryCategory(Category $category)
	 {


	 	$data = array(
			'event_id' => $category->EventId,
			'category' => $category->Category,
			
		);

		// var_dump($data);

		
		// if(!$this->getCategoriesByEventId($category->EventId)){

		// 	return false;
		// }
		
		$this->mTableGateway->insert($data);
		
		return true;
	 }

	


	/*==================================================================*/
	/*					Get 				   							*/
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
		$resultSet = $this->mTableGateway->select();
		return $resultSet;
	}
	

	/**
	 * get by eventID
	 *
	 */
	public function getCategoriesByEventId($eventId)
	{
	 	$eventId = (int)$eventId;
		$rowset = $this->mTableGateway->select(array('event_id' => $eventId));
		
		return $rowset;
	}


	

}

