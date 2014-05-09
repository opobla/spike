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

	public function index($params,$aux){
		date_default_timezone_set("UTC"); 
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

			case postRevisedData:
				return $this->postRevisedData($aux);
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

			case auxSearch:
				return $this->auxSearch();
				break;

			default:
				return 'Opps.. Wrong Action. Actions List: interval, lastweek. plof';
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


	public function intervalHS($start,$finish)
    {
		$start = date("Y-m-d H:i:s",$start);
		$finish = date("Y-m-d H:i:s",$finish);


		/*"SELECT o.start_date_time,
	CASE WHEN r.start_date_time IS NULL THEN o.measured_uncorrected ELSE r.revised_uncorrected END AS uncorrected, 
	CASE WHEN r.start_date_time IS NULL THEN o.measured_corr_for_pressure ELSE r.revised_corr_for_pressure END AS corr_for_pressure,
	CASE WHEN r.start_date_time IS NULL THEN o.measured_corr_for_efficiency ELSE r.revised_corr_for_efficiency END AS corr_for_efficiency,
	CASE WHEN r.start_date_time IS NULL THEN o.measured_pressure_mbar ELSE r.revised_pressure_mbar END AS pressure_mbar

FROM CALM_ori o LEFT JOIN CALM_rev r ON o.start_date_time = r.start_date_time WHERE o.start_date_time >= '".$start."' AND o.start_date_time < '".$finish."' ORDER BY start_date_time ASC;"*/

		$result = $this->CALMori->getAdapter()->query("SELECT o.start_date_time,
	CASE WHEN r.start_date_time IS NULL THEN o.measured_uncorrected ELSE r.revised_uncorrected END AS uncorrected, 
	CASE WHEN r.start_date_time IS NULL THEN o.measured_corr_for_pressure ELSE r.revised_corr_for_pressure END AS corr_for_pressure,
	CASE WHEN r.start_date_time IS NULL THEN o.measured_corr_for_efficiency ELSE r.revised_corr_for_efficiency END AS corr_for_efficiency,
	CASE WHEN r.start_date_time IS NULL THEN o.measured_pressure_mbar ELSE r.revised_pressure_mbar END AS pressure_mbar

FROM CALM_ori o LEFT JOIN CALM_rev r ON o.start_date_time = r.start_date_time WHERE o.start_date_time >= '".$start."' AND o.start_date_time < '".$finish."' ORDER BY start_date_time ASC;")->execute();

		$resultSet = new ResultSet;
    	$resultSet->initialize($result);

		$rows = array();
		foreach ($resultSet as $CALM_oriModel){
				$rows[0][]=array(
					strtotime($CALM_oriModel->start_date_time)*1000,
					$this->handleFloat($CALM_oriModel->uncorrected),
				);
				$rows[1][]=array(
					strtotime($CALM_oriModel->start_date_time)*1000,
					$this->handleFloat($CALM_oriModel->corr_for_pressure),
				);
				$rows[2][]=array(
					strtotime($CALM_oriModel->start_date_time)*1000,
					$this->handleFloat($CALM_oriModel->corr_for_efficiency),
				);
				$rows[3][]=array(
					strtotime($CALM_oriModel->start_date_time)*1000,
					 $this->handleFloat($CALM_oriModel->pressure_mbar),
				);
				//echo("<script>console.log('".$CALM_oriModel->time."');</script>");
			}

		return 	//$_GET['callback'].
				//'('.
				Json::encode($rows)//.
				//');'
				;
    }


	public function intervalHSGrouped($start,$finish,$points)
    {
		$start = date("Y-m-d H:i:s",$start);
		$finish = date("Y-m-d H:i:s",$finish);

		$interval =round((strtotime($finish)-strtotime($start))/($points-1));

		/*"select t2.*,ROUND(UNIX_TIMESTAMP(t2.start_date_time)/(".$interval.")) as timekey from(SELECT o.start_date_time,
	CASE WHEN r.start_date_time IS NULL THEN o.measured_uncorrected ELSE r.revised_uncorrected END AS uncorrected, 
	CASE WHEN r.start_date_time IS NULL THEN o.measured_corr_for_pressure ELSE r.revised_corr_for_pressure END AS corr_for_pressure,
	CASE WHEN r.start_date_time IS NULL THEN o.measured_corr_for_efficiency ELSE r.revised_corr_for_efficiency END AS corr_for_efficiency,
	CASE WHEN r.start_date_time IS NULL THEN o.measured_pressure_mbar ELSE r.revised_pressure_mbar END AS pressure_mbar

FROM CALM_ori o LEFT JOIN CALM_rev r ON o.start_date_time = r.start_date_time WHERE o.start_date_time >= '".$start."' AND o.start_date_time < '".$finish."' ORDER BY start_date_time ASC) as t2 group by timekey;"*/


		$result = $this->CALMori->getAdapter()->query("select start_date_time as time, 
	avg(uncorrected)+std(uncorrected) as uncorrected_open, max(uncorrected) as uncorrected_max, min(uncorrected) as uncorrected_min, avg(uncorrected)-std(uncorrected) as uncorrected_close, 
	avg(corr_for_pressure)+std(corr_for_pressure) as corr_for_pressure_open, max(corr_for_pressure) as corr_for_pressure_max, min(corr_for_pressure) as corr_for_pressure_min, avg(corr_for_pressure)-std(corr_for_pressure) as corr_for_pressure_close,
	
	avg(corr_for_efficiency)+std(corr_for_efficiency) as corr_for_efficiency_open, max(corr_for_efficiency) as corr_for_efficiency_max, min(corr_for_efficiency) as corr_for_efficiency_min, avg(corr_for_efficiency)-std(corr_for_efficiency) as corr_for_efficiency_close,

	avg(pressure_mbar) as pressure_mbar_avg

	from(select t2.*,ROUND(UNIX_TIMESTAMP(t2.start_date_time)/(".$interval.")) as timekey from(SELECT o.start_date_time,
	CASE WHEN r.start_date_time IS NULL THEN o.measured_uncorrected ELSE r.revised_uncorrected END AS uncorrected, 
	CASE WHEN r.start_date_time IS NULL THEN o.measured_corr_for_pressure ELSE r.revised_corr_for_pressure END AS corr_for_pressure,
	CASE WHEN r.start_date_time IS NULL THEN o.measured_corr_for_efficiency ELSE r.revised_corr_for_efficiency END AS corr_for_efficiency,
	CASE WHEN r.start_date_time IS NULL THEN o.measured_pressure_mbar ELSE r.revised_pressure_mbar END AS pressure_mbar

FROM CALM_ori o LEFT JOIN CALM_rev r ON o.start_date_time = r.start_date_time WHERE o.start_date_time >= '".$start."' AND o.start_date_time < '".$finish."' ORDER BY start_date_time ASC) as t2) as t3 group by timekey;")->execute();

		$resultSet = new ResultSet;
    	$resultSet->initialize($result);

		$rows = array();
		foreach ($resultSet as $CALM_oriModel){
				$rows[0][]=array(
					strtotime($CALM_oriModel->time)*1000,
					$this->handleFloat($CALM_oriModel->uncorrected_open),
					$this->handleFloat($CALM_oriModel->uncorrected_max),
					$this->handleFloat($CALM_oriModel->uncorrected_min),
					$this->handleFloat($CALM_oriModel->uncorrected_close),
				);
				$rows[1][]=array(
					strtotime($CALM_oriModel->time)*1000,
					$this->handleFloat($CALM_oriModel->corr_for_pressure_open),
					$this->handleFloat($CALM_oriModel->corr_for_pressure_max),
					$this->handleFloat($CALM_oriModel->corr_for_pressure_min),
					$this->handleFloat($CALM_oriModel->corr_for_pressure_close),
				);
				$rows[2][]=array(
					strtotime($CALM_oriModel->time)*1000,
					$this->handleFloat($CALM_oriModel->corr_for_efficiency_open),
					$this->handleFloat($CALM_oriModel->corr_for_efficiency_max),
					$this->handleFloat($CALM_oriModel->corr_for_efficiency_min),
					$this->handleFloat($CALM_oriModel->corr_for_efficiency_close),
				);
				$rows[3][]=array(
					strtotime($CALM_oriModel->time)*1000,
					$this->handleFloat($CALM_oriModel->pressure_mbar_avg),
				);
				//echo("<script>console.log('".$CALM_oriModel->time."');</script>");
			}

		return //	$_GET['callback'].
				//'('.
				Json::encode($rows)//.
				//');'
				;

    }
	
	function handleFloat($value){
		if($value==null)return null;
		return (float)$value;
	}

	public function allHS($points)
    {

		$start='2012-12-11 00:00:00';
		$finish=(date("Y-m-d H:i:s",time()));
		
		return $this->intervalHSGrouped(strtotime($start),strtotime($finish),$points);
    }

	public function postRevisedData($aux){
		$request = $aux->getRequest();
		if ($request->isPost()){
			if ($request->getContent() != false){
				$postParams = Json::decode($request->getContent());
				if ($postParams->start_date_time != false){
					$this->CALMrev->getAdapter()->query("insert into CALM_rev (start_date_time,revised_uncorrected,revised_corr_for_efficiency, revised_corr_for_pressure, revised_pressure_mbar, version, last_change ) values ('".$postParams->start_date_time."',null,null,null,null,1,now()) on duplicate key update revised_uncorrected = null, revised_corr_for_efficiency=null, revised_corr_for_pressure=null, revised_pressure_mbar=null, version=version+1, last_change=now();")->execute();
				}else{	
					foreach($postParams as $row){	
						$this->CALMrev->getAdapter()->query("insert into CALM_rev (start_date_time, revised_uncorrected, revised_corr_for_efficiency, revised_corr_for_pressure, revised_pressure_mbar, version, last_change ) values ('".$row->start_date_time."',null,null,null,null,1,now()) on duplicate key update revised_uncorrected = null, revised_corr_for_efficiency=null, revised_corr_for_pressure=null, revised_pressure_mbar=null, version=version+1, last_change=now();")->execute();
					}
				}
				return Json::encode(array('Data correctly updated'));
			}else{
				return Json::encode(array('There is no data to update'));
			}
		}else{
			return Json::encode(array('Its not a post'));
		}
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
		return 'dsfdsf '.null;
    }
}
