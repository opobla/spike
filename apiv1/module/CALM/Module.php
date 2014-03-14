<?php
namespace CALM;

use CALM\Model\binTableModel;
use CALM\Controller\binTableController;
use CALM\Controller\nmdadbController;

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

class Module
{
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
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
                'CALM\Controller\binTableController' =>  function($sm) {
                    $tableGateway = $sm->get('binTableGateway');
                    $binTableController = new binTableController($tableGateway);
                    return $binTableController;
                },
				'CALM\Controller\nmdadbController' =>  function($sm) {
                    $binTableController = $sm->get('CALM\Controller\binTableController');
					$binTableSimpleController=NULL;//null for now
                    $nmdadbController = new nmdadbController($binTableController/*,$binTableSimpleController*/);
                    return $nmdadbController;
                },
                'binTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('db1');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new binTableModel());
                    return new TableGateway('binTable', $dbAdapter, null, $resultSetPrototype);
                },
            ),
        );
    }


    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }
}
