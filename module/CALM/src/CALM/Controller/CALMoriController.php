<?php
namespace CALM\Controller;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Where;
use Zend\Json\Json;

class CALMoriController
{
    protected $CALMori;

    public function __construct(TableGateway $CALMori)
    {
        $this->CALMori = $CALMori;
    }

	public function index($params){
		$Action = (string) $params()->fromRoute('Action', 0);

		if(empty($Action)){
			return 'Opps.. You must choice an Action. Actions List: interval, lastweek.';
		}		

		switch($Action){
			case interval:
				$start= (string) $params()->fromRoute('Param1', 0);
				$finish= (string) $params()->fromRoute('Param2', 0);
				return $this->interval($start,$finish);
				break;
	
			case lastweek:
				return $this->lastweek();
				break;

			case update:
				$start_date_time= $params()->fromRoute('Param1', 0);
				$start_date_time=str_replace('%20',' ',$start_date_time);
				$column= (string) $params()->fromRoute('Param2', 0);
				$value= (integer) $params()->fromRoute('Param3', 0);
				
				return $this->update($start_date_time,$column,$value);
				break;

			default:
				return 'Opps.. Wrong Action. Actions List: interval, lastweek.';
				break;
		}
		
	}

	public function interval($start,$finish)
    {
		$start=str_replace('+',' ',$start);
		$finish=str_replace('+',' ',$finish);
		$start=str_replace('%20',' ',$start);
		$finish=str_replace('%20',' ',$finish);
		//return $start.'  ---  '.$finish;
		$where= new Where();
		$where->between('start_date_time',$start,$finish);
		$resultSet = $this->CALMori->select($where);
		$rows = array();
		foreach ($resultSet as $CALM_oriModel){
				$rows[]=array(
					'start_date_time'=>$CALM_oriModel->start_date_time,
					'length_time_interval_s'=>$CALM_oriModel->length_time_interval_s,
					'measured_uncorrected'=>$CALM_oriModel->measured_uncorrected,
					'measured_corr_for_efficiency'=>$CALM_oriModel->measured_corr_for_efficiency,
					'measured_corr_for_pressure'=>$CALM_oriModel->measured_corr_for_pressure,
					'measured_pressure_mbar'=>$CALM_oriModel->measured_pressure_mbar,
				);
			}

		return 	$_GET['hola'].
				'('.
				Json::encode($rows).
				');'
				;
    }

	public function lastweek() //no needed params.
    {
		$now=time();
		$oneWeekAgo=$now-(7*24*60*60);
		return $this->interval(date("Y-m-d H:i:s",$oneWeekAgo),date("Y-m-d H:i:s",$now));
    }

	public function getDataEntry($start_date_time){
		$rowset=$this->CALMori->select(array('start_date_time' => $start_date_time));
		$row = $rowset->current();
        return $row;
	}

	public function update($start_date_time,$column,$value){
		if ($this->getDataEntry($start_date_time)){
			$this->CALMori->update(array($column => $value),array('start_date_time' => $start_date_time));
			return $this->lastweek();
		}
		return 'Incorrect data entry--> '.$start_date_time;
	}

	public function auxSearch()
    {
		return 'holaaaaas';
    }
}
