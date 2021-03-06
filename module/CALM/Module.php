<?php
namespace CALM;

use CALM\Controller\nmdadbController;
use CALM\Controller\nmdbController;

use CALM\Controller\binTableController;
use CALM\Controller\CALMoriController;
use CALM\Controller\CALMrevController;

use CALM\Model\binTableModel;
use CALM\Model\CALMoriModel;
use CALM\Model\CALMrevModel;

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
				'CALM\Controller\CALMoriController' =>  function($sm) {
                    $tableGateway = $sm->get('CALMoriGateway');
                    $CALMoriController = new CALMoriController($tableGateway);
                    return $CALMoriController;
                },
				'CALM\Controller\CALMrevController' =>  function($sm) {
					$tableGatewayori = $sm->get('CALMoriGateway');                    
					$tableGatewayrev = $sm->get('CALMrevGateway');					
                    $CALMrevController = new CALMrevController($tableGatewayori,$tableGatewayrev);
                    return $CALMrevController;
                },
				'CALM\Controller\nmdadbController' =>  function($sm) {
                    $binTableController = $sm->get('CALM\Controller\binTableController');
					$binTableSimpleController=NULL;//null for now
                    $nmdadbController = new nmdadbController($binTableController, $binTableSimpleController);
                    return $nmdadbController;
                },
				'CALM\Controller\nmdbController' =>  function($sm) {
                    $CALMoriController = $sm->get('CALM\Controller\CALMoriController');
					$CALM1hController = NULL;//null for now
					$CALMenvController = NULL;//null for now
					$CALMmetaController = NULL;//null for now
					$CALMrevController = $sm->get('CALM\Controller\CALMrevController');
                    $nmdbController = new nmdbController($CALMoriController, $CALM1hController, $CALMenvController, $CALMmetaController, $CALMrevController);
                    return $nmdbController;
                },
                'binTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('db1');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new binTableModel());
                    return new TableGateway('binTable', $dbAdapter, null, $resultSetPrototype);
                },
				'CALMoriGateway' => function ($sm) {
                    $dbAdapter = $sm->get('db2');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new CALMoriModel());
                    return new TableGateway('CALM_ori', $dbAdapter, null, $resultSetPrototype);
                },
				'CALMrevGateway' => function ($sm) {
                    $dbAdapter = $sm->get('db2');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new CALMrevModel());
                    return new TableGateway('CALM_rev', $dbAdapter, null, $resultSetPrototype);
                },
            ),
        );
    }


    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }
}
