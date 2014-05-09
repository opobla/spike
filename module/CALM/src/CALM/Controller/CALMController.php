<?php
namespace CALM\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Json\Json;

use CALM\Controller\nmdbController;
use CALM\Controller\nmdadbController;

class CALMController extends AbstractActionController
{

	protected $nmdbController;
	protected $nmdadbController;

	public function getnmdbController(){
		if (!$this->nmdbController) {
            $sm = $this->getServiceLocator();
            $this->nmdbController = $sm->get('CALM\Controller\nmdbController');
        }
        return $this->nmdbController;
	}

	public function getnmdadbController(){
		if (!$this->nmdadbController) {
		    $sm = $this->getServiceLocator();
		    $this->nmdadbController = $sm->get('CALM\Controller\nmdadbController');
		}
        	return $this->nmdadbController;
	}


///////////////////////////////////////////////////////////////////////////////////////////////////

	public function indexAction()
    {
		$db = (string) $this->params()->fromRoute('DataBase', 0);
		if(empty($db)){
			return $this->getResponse()->setContent(
				'Opps.. You must choice a DataBase. DataBases List: nmdb, nmdadb.'
			);
		}		
		switch($db){
			case nmdb:
				return $this->getResponse()->setContent(
					$this->getnmdbController()->index($this->params(),$this)	
				);
				break;

			case nmdadb:
				return $this->getResponse()->setContent(
					$this->getnmdadbController()->index($this->params())	
				);
				break;

			default:
				return $this->getResponse()->setContent(
					'Opps.. Wrong DataBase. DataBases List: nmdb, nmdadb.'
				);
				break;
		}
    }
}
