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
	//This TableGateway object should be changed to one Zend_Db_Adapter..
	//At the moment in order to execute a query what we do is **CALMori->getAdapter()->query("select * from ...;")->execute();
	//Should look like this: nmdbAdapter->query("select * from ...;")->execute();
    protected $CALMori;//Must change

    public function __construct(TableGateway $CALMori)
    {
        $this->CALMori = $CALMori;//Must change
    }

	public function index($params){
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

			default:
				return 'Opps.. Wrong Action. Actions List: interval, lastweek.';
				break;
		}
		
	}
	//This function returns data which is used by  Clientv1, a client which plots the data using the ExtJS chart API..
	//Ploting with ExtJS hasnt much future for our project so its not worth commenting this
	public function interval($start,$finish)
    {
		$start=str_replace('+',' ',$start);
		$finish=str_replace('+',' ',$finish);
		$start=str_replace('%20',' ',$start);
		$finish=str_replace('%20',' ',$finish);

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

	//This function returns data which is used by  Clientv1, a client which plots the data using the ExtJS chart API..
	//Ploting with ExtJS hasnt much future for our project so its not worth commenting this
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

	//The purpose of this function is return an interval of unrevised data. The data returned by this function is used to
	//plot an line chart. The interval is delimited by **$start** and **$finish**.
	//**$start** , **$finish**  <==> unix timestamp
	//The returned data has a format which is easy to read to HIGHSTOCK, the api which plots the chart.	
	public function intervalHS($start,$finish)
    {
		$start = date("Y-m-d H:i:s",$start);
		$finish = date("Y-m-d H:i:s",$finish);

		$result = $this->CALMori->getAdapter()->query("select start_date_time as time, measured_uncorrected, measured_corr_for_pressure, measured_corr_for_efficiency , measured_pressure_mbar from CALM_ori where start_date_time between '".$start."' and '".$finish."';")->execute();

		$resultSet = new ResultSet;
    	$resultSet->initialize($result);

		$rows = array();
		foreach ($resultSet as $CALM_oriModel){
				$rows[0][]=array(
					strtotime($CALM_oriModel->time)*1000,
					$this->handleFloat($CALM_oriModel->measured_uncorrected),
				);
				$rows[1][]=array(
					strtotime($CALM_oriModel->time)*1000,
					$this->handleFloat($CALM_oriModel->measured_corr_for_pressure),
				);
				$rows[2][]=array(
					strtotime($CALM_oriModel->time)*1000,
					$this->handleFloat($CALM_oriModel->measured_corr_for_efficiency),
				);
				$rows[3][]=array(
					strtotime($CALM_oriModel->time)*1000,
					$this->handleFloat($CALM_oriModel->measured_pressure_mbar),
				);
			}

		return 	Json::encode($rows);
    }

	//The purpose of this function is return an interval of unrevised data. The data returned by this function is used to
	//plot an OHLC(candellstick) chart. The interval is delimited by **$start** and **$finish**. The data is grouped in **$points**
	//number of groups. **$points** its determined by the chart width.
	//**$start** , **$finish**  <==> unix timestamp
	//**$points** <==> Integer
	//The returned data has a format which is easy to read to HIGHSTOCK, the api which plots the chart.
	public function intervalHSGrouped($start,$finish,$points)
    {
		$start = date("Y-m-d H:i:s",$start);
		$finish = date("Y-m-d H:i:s",$finish);

		$interval =round((strtotime($finish)-strtotime($start))/($points-1));

		$result = $this->CALMori->getAdapter()->query("select start_date_time as time, 
	avg(measured_uncorrected)+std(measured_uncorrected) as measured_uncorrected_open, max(measured_uncorrected) as measured_uncorrected_max, min(measured_uncorrected) as measured_uncorrected_min, avg(measured_uncorrected)-std(measured_uncorrected) as measured_uncorrected_close, 
	avg(measured_corr_for_pressure)+std(measured_corr_for_pressure) as measured_corr_for_pressure_open, max(measured_corr_for_pressure) as measured_corr_for_pressure_max, min(measured_corr_for_pressure) as measured_corr_for_pressure_min, avg(measured_corr_for_pressure)-std(measured_corr_for_pressure) as measured_corr_for_pressure_close,
	
	avg(measured_corr_for_efficiency)+std(measured_corr_for_efficiency) as measured_corr_for_efficiency_open, max(measured_corr_for_efficiency) as measured_corr_for_efficiency_max, min(measured_corr_for_efficiency) as measured_corr_for_efficiency_min, avg(measured_corr_for_efficiency)-std(measured_corr_for_efficiency) as measured_corr_for_efficiency_close,

	avg(measured_pressure_mbar) as measured_pressure_mbar_avg

	from (select CALM_ori.*, ROUND(UNIX_TIMESTAMP(start_date_time)/(".$interval.")) as timekey  from CALM_ori where start_date_time between '".$start."' and '".$finish."')as t1 group by timekey;")->execute();

		$resultSet = new ResultSet;
    	$resultSet->initialize($result);

		$rows = array();
		foreach ($resultSet as $CALM_oriModel){
				$rows[0][]=array(
					strtotime($CALM_oriModel->time)*1000,
					$this->handleFloat($CALM_oriModel->measured_uncorrected_open),
					$this->handleFloat($CALM_oriModel->measured_uncorrected_max),
					$this->handleFloat($CALM_oriModel->measured_uncorrected_min),
					$this->handleFloat($CALM_oriModel->measured_uncorrected_close),
				);
				$rows[1][]=array(
					strtotime($CALM_oriModel->time)*1000,
					$this->handleFloat($CALM_oriModel->measured_corr_for_pressure_open),
					$this->handleFloat($CALM_oriModel->measured_corr_for_pressure_max),
					$this->handleFloat($CALM_oriModel->measured_corr_for_pressure_min),
					$this->handleFloat($CALM_oriModel->measured_corr_for_pressure_close),
				);
				$rows[2][]=array(
					strtotime($CALM_oriModel->time)*1000,
					$this->handleFloat($CALM_oriModel->measured_corr_for_efficiency_open),
					$this->handleFloat($CALM_oriModel->measured_corr_for_efficiency_max),
					$this->handleFloat($CALM_oriModel->measured_corr_for_efficiency_min),
					$this->handleFloat($CALM_oriModel->measured_corr_for_efficiency_close),
				);
				$rows[3][]=array(
					strtotime($CALM_oriModel->time)*1000,
					$this->handleFloat($CALM_oriModel->measured_pressure_mbar_avg),
				);
			}

		return Json::encode($rows);

    }

	//The purpose of this function is to return all the unrevised data. The data returned by this function is used to
	//plot and OHLC(candellstick) chart. The data is grouped in **$points**
	//number of groups. **$points** its determined by the chart width.
	//**$points** <==> Integer
	public function allHS($points)
    {

		$start='2012-12-11 00:00:00';
		$finish=(date("Y-m-d H:i:s",time()));
		
		return $this->intervalHSGrouped(strtotime($start),strtotime($finish),$points);
    }

	//The purpose of this function is to handle the cast to float of the data returned my the sql query.
	//when a **(float)null** cast is done the returned value is **0** when what we need is **null**
	function handleFloat($value){
		if($value==null)return null;
		return (float)$value;
	}
}
