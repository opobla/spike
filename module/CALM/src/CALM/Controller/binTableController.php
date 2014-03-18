<?php
namespace CALM\Controller;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Where;
use Zend\Json\Json;

class binTableController
{
    protected $binTable;

    public function __construct(TableGateway $binTable)
    {
        $this->binTable = $binTable;
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
		$resultSet = $this->binTable->select($where);
		$rows = array();
		foreach ($resultSet as $binTableModel){
				$rows[]=array(
					'start_date_time'=>$binTableModel->start_date_time,
					'ch01'=>$binTableModel->ch01,
					'ch02'=>$binTableModel->ch02,
					'ch03'=>$binTableModel->ch03,
					'ch04'=>$binTableModel->ch04,
					'ch05'=>$binTableModel->ch05,
					'ch06'=>$binTableModel->ch06,
					'ch07'=>$binTableModel->ch07,
					'ch08'=>$binTableModel->ch08,
					'ch09'=>$binTableModel->ch09,
					'ch10'=>$binTableModel->ch10,
					'ch11'=>$binTableModel->ch11,
					'ch12'=>$binTableModel->ch12,
					'ch13'=>$binTableModel->ch13,
					'ch14'=>$binTableModel->ch14,
					'ch15'=>$binTableModel->ch15,
					'ch16'=>$binTableModel->ch16,
					'ch17'=>$binTableModel->ch17,
					'ch18'=>$binTableModel->ch18,
					'hv1'=>$binTableModel->hv1,
					'hv2'=>$binTableModel->hv2,
					'hv3'=>$binTableModel->hv3,
					'temp_1'=>$binTableModel->temp_1,
					'temp_2'=>$binTableModel->temp_2,
					'atmPressure'=>$binTableModel->atmPressure,
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
		$oneWeekAgo=$now-(8*24*60*60);
		return $this->interval(date("Y-m-d H:i:s",$oneWeekAgo),date("Y-m-d H:i:s",$now));
    }

	public function getDataEntry($start_date_time){
		$rowset=$this->binTable->select(array('start_date_time' => $start_date_time));
		$row = $rowset->current();
        return $row;
	}

	public function update($start_date_time,$column,$value){
		if ($this->getDataEntry($start_date_time)){
			$this->binTable->update(array($column => $value),array('start_date_time' => $start_date_time));
			return $this->lastweek();
		}
		return 'Incorrect data entry--> '.$start_date_time;
	}

	public function auxSearch()
    {
		return 'holaaaaas';
    }
}
