<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Application\Model\Event;
use Application\Model\EventTable;

use Application\Model\Category;
use Application\Model\CategoryTable;
use Application\Model\AccessKey;
use Application\Model\AccessKeyTable;



class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
	
	public function getServiceConfig()
	{
		return array(
			'factories' => array(
						
				'Application\Model\EventTable' => function($sm) {
					$tableGateway = $sm->get('EventTableGateway');
					$table        = new EventTable($tableGateway);
					return $table;
				},
				'EventTableGateway' => function($sm) {
					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
					$resultSetPrototype = new ResultSet();
					$resultSetPrototype->setArrayObjectPrototype(new Event());
					$tableGateway =  new TableGateway('events', $dbAdapter, null, $resultSetPrototype);
					return $tableGateway;
				},

				'Application\Model\CategoryTable' => function($sm) {
					$tableGateway = $sm->get('CategoryTableGateway');
					$table        = new CategoryTable($tableGateway);
					return $table;
				},
				'CategoryTableGateway' => function($sm) {
					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
					$resultSetPrototype = new ResultSet();
					$resultSetPrototype->setArrayObjectPrototype(new Category());
					$tableGateway =  new TableGateway('categories', $dbAdapter, null, $resultSetPrototype);
					return $tableGateway;
				},


				'Application\Model\AccessKeyTable' => function($sm) {
					$tableGateway = $sm->get('AccessKeyTableGateway');
					$table        = new AccessKeyTable($tableGateway);
					return $table;
				},
				'AccessKeyTableGateway' => function($sm) {
					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
					$resultSetPrototype = new ResultSet();
					$resultSetPrototype->setArrayObjectPrototype(new AccessKey());
					$tableGateway =  new TableGateway('administrator', $dbAdapter, null, $resultSetPrototype);
					return $tableGateway;
				},


			)
		);	
	}
}
