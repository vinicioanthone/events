<?php

namespace Application\Com;

use Zend\Db\TableGateway\TableGateway;


/*==========================================================================*/
/*											    							*/
/*					Table management（tablegateway management） 				*/
/*											    							*/
/*==========================================================================*/
/**
 *	Table management utility
 *
 */
class TableGetter
{
	
	private $mEventTable;
	private $mCategoryTable;
	
	private $mAccessKeyTable;
	

	/*==================================================================*/
	/*					Public ＩＦ		    							*/
	/*==================================================================*/
	
	/**
	 * Get Event information table
	 *
	 */
	public function getEventTable($controller)
	{
		if( !$this->mEventTable ) {
			$sm = $controller->getServiceLocator();
			$this->mEventTable = $sm->get('Application/Model/EventTable');
		}
		
		return $this->mEventTable;
	}


	/**
	 * Get Category information table
	 *
	 */
	public function getCategoryTable($controller)
	{
		if( !$this->mCategoryTable ) {
			$sm = $controller->getServiceLocator();
			$this->mCategoryTable = $sm->get('Application/Model/CategoryTable');
		}
		
		return $this->mCategoryTable;
	}



	/**
	 * Get key table
	 *
	 */
	public function getAccessKeyTable($controller)
	{
		if( !$this->mAccessKeyTable ) {
			$sm = $controller->getServiceLocator();
			$this->mAccessKeyTable = $sm->get('Application/Model/AccessKeyTable');
		}
		
		return $this->mAccessKeyTable;
	}

	




	

	
	
}
