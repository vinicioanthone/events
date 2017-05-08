<?php
/**
 * Category management model
 *
 */

namespace Application\Model;

class Category
{
	public $EventId;
	public $Category;


	public function exchangeArray($data)
	{
		$this->EventId 	 = (isset($data['event_id'])) ? (int)$data['event_id'] : 0;
		
		$this->Category = (isset($data['category'])) ? $data['category'] : "";
	}
}

