<?php
namespace CALM\Controller;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
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

			case intervalTuned:
				$start= (string) $params()->fromRoute('Param1', 0);
				$finish= (string) $params()->fromRoute('Param2', 0);
				$interval = (integer) $params()->fromRoute('Param3', 0);
				return $this->intervalTuned($start,$finish,$interval);
				break;

			case intervalHS:
				$start= (string) $params()->fromRoute('Param1', 0);
				$finish= (string) $params()->fromRoute('Param2', 0);
				return $this->intervalHS($start,$finish);
				break;			
				
			case intervalHSGrouped:
				$start= (string) $params()->fromRoute('Param1', 0);
				$finish= (string) $params()->fromRoute('Param2', 0);
				$points = (integer) $params()->fromRoute('Param3', 0);
				return $this->intervalHSGrouped($start,$finish,$points);
				break;

			case allHS:
				$points = (integer) $params()->fromRoute('Param1', 0);
				return $this->allHS($points);
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

			case aux:
				$start= (string) $params()->fromRoute('Param1', 0);
				$finish= (string) $params()->fromRoute('Param2', 0);
				return $this->auxSearch($start,$finish);
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

	public function intervalTuned($start,$finish,$interval)
    {
		$start=str_replace('+',' ',$start);
		$finish=str_replace('+',' ',$finish);
		$start=str_replace('%20',' ',$start);
		$finish=str_replace('%20',' ',$finish);

		$result = $this->CALMori->getAdapter()->query("select count(*) as cnt, sum(length_time_interval_s) as length_time_interval_ssum, avg(measured_uncorrected) as measured_uncorrectedavg, avg(measured_corr_for_efficiency) as measured_corr_for_efficiencyavg, avg(measured_corr_for_pressure) as measured_corr_for_pressureavg, avg(measured_pressure_mbar) as measured_pressure_mbaravg, substring_index(substring_index(group_concat(start_date_time order by start_date_time),',',ceil(count(*)/2)),',',-1) as date from (select @i:=@i+1 as rownum,FLOOR(@i/".$interval.") as GGG,start_date_time,length_time_interval_s,measured_uncorrected,measured_corr_for_efficiency,measured_corr_for_pressure,measured_pressure_mbar from CALM_ori join (select @i:=-1) as dick  where start_date_time between '".$start."' and '".$finish."')as t group by GGG;")->execute();

		$resultSet = new ResultSet;
    	$resultSet->initialize($result);


		$rows = array();
		foreach ($resultSet as $CALM_oriModel){
				$rows[]=array(
					'start_date_time'=>$CALM_oriModel->date,
					'length_time_interval_s'=>$CALM_oriModel->length_time_interval_ssum,
					'measured_uncorrected'=>$CALM_oriModel->measured_uncorrectedavg,
					'measured_corr_for_efficiency'=>$CALM_oriModel->measured_corr_for_efficiencyavg,
					'measured_corr_for_pressure'=>$CALM_oriModel->measured_corr_for_pressureavg,
					'measured_pressure_mbar'=>$CALM_oriModel->measured_pressure_mbaravg,
				);
			}

		return 	$_GET['hola'].
				'('.
				Json::encode($rows).
				');'
				;
    }


	public function intervalHS($start,$finish)
    {
		$start = date("Y-m-d H:i:s",$start);
		$finish = date("Y-m-d H:i:s",$finish);

		$result = $this->CALMori->getAdapter()->query("select start_date_time as time, measured_uncorrected, measured_corr_for_pressure from CALM_ori where start_date_time between '".$start."' and '".$finish."';")->execute();

		$resultSet = new ResultSet;
    	$resultSet->initialize($result);

		$rows = array();
		foreach ($resultSet as $CALM_oriModel){
				$rows[0][]=array(
					strtotime($CALM_oriModel->time)*1000,
					(float)$CALM_oriModel->measured_uncorrected,
				);
				$rows[1][]=array(
					strtotime($CALM_oriModel->time)*1000,
					(float)$CALM_oriModel->measured_corr_for_pressure,
				);
				//echo("<script>console.log('".$CALM_oriModel->time."');</script>");
			}

		return 	$_GET['callback'].
				'('.
				Json::encode($rows).
				');'
				;
    }


	public function intervalHSGrouped($start,$finish,$points)
    {
		$start = date("Y-m-d H:i:s",$start);
		$finish = date("Y-m-d H:i:s",$finish);

		$interval =round((strtotime($finish)-strtotime($start))/($points-1));

		$result = $this->CALMori->getAdapter()->query("select start_date_time as time, 
	avg(measured_uncorrected)+std(measured_uncorrected) as measured_uncorrected_open, max(measured_uncorrected) as measured_uncorrected_max, min(measured_uncorrected) as measured_uncorrected_min, avg(measured_uncorrected)-std(measured_uncorrected) as measured_uncorrected_close, 
	avg(measured_corr_for_pressure)+std(measured_corr_for_pressure) as measured_corr_for_pressure_open, max(measured_corr_for_pressure) as measured_corr_for_pressure_max, min(measured_corr_for_pressure) as measured_corr_for_pressure_min, avg(measured_corr_for_pressure)-std(measured_corr_for_pressure) as measured_corr_for_pressure_close

	from (select CALM_ori.*, ROUND(UNIX_TIMESTAMP(start_date_time)/(".$interval.")) as timekey  from CALM_ori where start_date_time between '".$start."' and '".$finish."')as t1 group by timekey;")->execute();

		$resultSet = new ResultSet;
    	$resultSet->initialize($result);


		$rows = array();
		foreach ($resultSet as $CALM_oriModel){
				$rows[0][]=array(
					strtotime($CALM_oriModel->time)*1000,
					(float)$CALM_oriModel->measured_uncorrected_open,
					(float)$CALM_oriModel->measured_uncorrected_max,
					(float)$CALM_oriModel->measured_uncorrected_min,
					(float)$CALM_oriModel->measured_uncorrected_close,
				);
				$rows[1][]=array(
					strtotime($CALM_oriModel->time)*1000,
					(float)$CALM_oriModel->measured_corr_for_pressure_open,
					(float)$CALM_oriModel->measured_corr_for_pressure_max,
					(float)$CALM_oriModel->measured_corr_for_pressure_min,
					(float)$CALM_oriModel->measured_corr_for_pressure_close,
				);
				//echo("<script>console.log('".$CALM_oriModel->time."');</script>");
			}

		return 	$_GET['callback'].
				'('.
				Json::encode($rows).
				');'
				;
    }


	public function allHS($points)
    {

		$start='2012-12-11 00:00:00';
		$finish=(date("Y-m-d H:i:s",time()));
		
		return $this->intervalHSGrouped(strtotime($start),strtotime($finish),$points);

		$result = $this->CALMori->getAdapter()->query("select start_date_time as time, avg(measured_uncorrected)+std(measured_uncorrected) as measured_uncorrected_open, max(measured_uncorrected) as measured_uncorrected_max, min(measured_uncorrected) as measured_uncorrected_min, avg(measured_uncorrected)-std(measured_uncorrected) as measured_uncorrected_close from (select CALM_ori.*, ROUND(UNIX_TIMESTAMP(start_date_time)/(60*60*60)) as timekey  from CALM_ori)as t1 group by timekey;")->execute();

		$resultSet = new ResultSet;
    	$resultSet->initialize($result);


		$rows = array();
		foreach ($resultSet as $CALM_oriModel){
				$rows[]=array(
					strtotime($CALM_oriModel->time)*1000,
					(float)$CALM_oriModel->measured_uncorrected_open,
					(float)$CALM_oriModel->measured_uncorrected_max,
					(float)$CALM_oriModel->measured_uncorrected_min,
					(float)$CALM_oriModel->measured_uncorrected_close,
				);
				//echo("<script>console.log('".$CALM_oriModel->time."');</script>");
			}

		return 	$_GET['callback'].
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

	public function auxSearch($start,$finish)
    {
		$start = date("Y-m-d H:i:s",$start);
		$finish = date("Y-m-d H:i:s",$finish);

		$result = $this->CALMori->getAdapter()->query("select start_date_time as time, measured_uncorrected, measured_corr_for_pressure from CALM_ori where start_date_time between '".$start."' and '".$finish."';")->execute();

		$resultSet = new ResultSet;
    	$resultSet->initialize($result);

		$rows = array();
		foreach ($resultSet as $CALM_oriModel){
				$rows[0][]=array(
					strtotime($CALM_oriModel->time)*1000,
					(float)$CALM_oriModel->measured_uncorrected,
				);
				$rows[1][]=array(
					strtotime($CALM_oriModel->time)*1000,
					(float)$CALM_oriModel->measured_corr_for_pressure,
				);
				//echo("<script>console.log('".$CALM_oriModel->time."');</script>");
			}

		return 	$_GET['callback'].
				'('.
				Json::encode($rows).
				');'
				;
    }
}
