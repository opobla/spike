<?php
namespace CALM\Controller;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Where;

use CALM\Controller\binTableController;
//use CALM\Controller\binTableSimpleController;

class nmdadbController
{
	protected $binTableController;
	protected $binTableSimpleController;
	
    public function __construct($binTableController, $binTableSimpleController)
    {
		$this->binTableController=$binTableController;
		$this->binTableSimpleController=$binTableSimpleController;
    }

	public function index($params){
		$Table = (string) $params()->fromRoute('Table', 0);

		if(empty($Table)){
			return 'Opps.. You must choice a Table.Tables List: binTable, binTableSimple.';
		}		

		switch($Table){
			case binTable:
				return $this->binTableController->index($params);
				break;

			case binTableSimple:
				return 'Module to be Implemented';
				break;

			default:
				return 'Opps.. Wrong Table. Tables List: binTable, binTableSimple.';
				break;
		}
		
	}
}
