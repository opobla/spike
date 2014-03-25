<?php
namespace CALM\Controller;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Zend\Json\Json;

class CALMrevController
{
    protected $CALMori;
	protected $CALMrev;

    public function __construct(TableGateway $CALMori,TableGateway $CALMrev)
    {
        $this->CALMori = $CALMori;
		$this->CALMrev = $CALMrev;
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

			case intervalTuned:
				$start= (string) $params()->fromRoute('Param1', 0);
				$finish= (string) $params()->fromRoute('Param2', 0);
				$interval = (integer) $params()->fromRoute('Param3', 0);
				return $this->intervalTuned($start,$finish,$interval);
				break;
	
			case lastweek:
				return $this->lastweek();
				break;

			case update:
				$start_date_time= $params()->fromRoute('Param1', 0);
				$start_date_time=str_replace('%20',' ',$start_date_time);
				$column= (string) $params()->fromRoute('Param2', 0);
				$value= (string) $params()->fromRoute('Param3', 0);
				
				return $this->update($start_date_time,$column,$value);
				break;

			default:
				return 'Opps.. Wrong Action. Actions List: interval, lastweek.';
				break;
		}
		
	}

	public function interval($start,$finish)
    {
		//return 'dfdsf';
		$start=str_replace('+',' ',$start);
		$finish=str_replace('+',' ',$finish);
		$start=str_replace('%20',' ',$start);
		$finish=str_replace('%20',' ',$finish);
		//return $start.'  ---  '.$finish;

		$result = $this->CALMrev->getAdapter()->query("select * from (select CALM_rev.* from CALM_rev where start_date_time between '2014-01-01 00:00:00' and '2014-01-01 00:10:00' union select CALM_ori.*,null,null  from CALM_ori where start_date_time between '".$start."' and '".$finish."') as t1 group by start_date_time;")->execute();

		$resultSet = new ResultSet;
    	$resultSet->initialize($result);

		$rows = array();
		foreach ($resultSet as $CALM_revModel){
				$rows[]=array(
					'start_date_time'=>$CALM_revModel->start_date_time,
					'length_time_interval_s'=>$CALM_revModel->length_time_interval_s,
					'measured_uncorrected'=>$CALM_revModel->measured_uncorrected,
					'measured_corr_for_efficiency'=>$CALM_revModel->measured_corr_for_efficiency,
					'measured_corr_for_pressure'=>$CALM_revModel->measured_corr_for_pressure,
					'measured_pressure_mbar'=>$CALM_revModel->measured_pressure_mbar,
					'version'=>$CALM_revModel->version,
					'last_change'=>$CALM_revModel->last_change,
				);
			}

		return 	$_GET['hola'].
				'('.
				Json::encode($rows).
				');'
				;
    }

	public function intervalTuned($start,$finish,$interval)
    {
		return 'To be inmplemented';
	
		$start=str_replace('+',' ',$start);
		$finish=str_replace('+',' ',$finish);
		$start=str_replace('%20',' ',$start);
		$finish=str_replace('%20',' ',$finish);

		$result = $this->CALMori->getAdapter()->query("")->execute();

		$resultSet = new ResultSet;
    	$resultSet->initialize($result);
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
		$rowset=$this->CALMrev->select(array('start_date_time' => $start_date_time));
		$row = $rowset->current();
        return $row;
	}

	public function update($start_date_time,$column,$value){
		if($this->getDataEntry($start_date_time)){
			//update.....................
			return 'Updated';
		}
			//create new entry...........
			$result = $this->CALMrev->getAdapter()->query("insert into CALM_rev (start_date_time,revised_uncorrected)  values('2014-01-01 00:03:00',60);")->execute();

		return 'Incorrect data entry--> '.$start_date_time;
	}

	public function auxSearch()
    {
		return 'holaaaaas';
    }
}
