<?php
namespace CALM\Controller;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Where;

use CALM\Controller\CALM_oriController;
//use CALM\Controller\CALM_1hController;
//use CALM\Controller\CALM_envController;
//use CALM\Controller\CALM_metaController;
//use CALM\Controller\CALM_revController;

class nmdbController
{
	protected $CALM_oriController;
	protected $CALM_1hController;
	protected $CALM_envController;
	protected $CALM_metaController;
	protected $CALM_revController;
	
    public function __construct($CALM_oriController, $CALM_1hController,$CALM_envController,$CALM_metaController,$CALM_revController)
    {
		$this->CALM_oriController=$CALM_oriController;
		$this->CALM_1hController=$CALM_1hController;
		$this->CALM_envController=$CALM_envController;
		$this->CALM_metaController=$CALM_metaController;
		$this->CALM_revController=$CALM_revController;
    }

	public function index($params){
		$Table = (string) $params()->fromRoute('Table', 0);
		//return 'dsfdsfdsf';
		if(empty($Table)){
			return 'Opps.. You must choice a Table.Tables List: CALM_ori, CALM_1h, CALM_env, CALM_meta, CALM_rev.';
		}		

		switch($Table){
			case CALM_ori:
				return $this->CALM_oriController->index($params);
				break;

			case CALM_1h:
				return 'Module to be Implemented';
				break;

			case CALM_env:
				return 'Module to be Implemented';
				break;

			case CALM_meta:
				return 'Module to be Implemented';
				break;

			case CALM_rev:
				return 'Module to be Implemented';
				break;

			default:
				return 'Opps.. Wrong Table. Tables List: binTable, binTableSimple.';
				break;
		}
		
	}
}
