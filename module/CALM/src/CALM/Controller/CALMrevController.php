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
	//This TableGateway objects should be changed to one Zend_Db_Adapter..
	//At the moment in order to execute a query what we do is **CALMori->getAdapter()->query("select * from ...;")->execute();
	//Should look like this: nmdbAdapter->query("select * from ...;")->execute();
    protected $CALMori;//Must change
	protected $CALMrev;//Must change

    public function __construct(TableGateway $CALMori,TableGateway $CALMrev)//Must change
    {
        $this->CALMori = $CALMori;//Must change
		$this->CALMrev = $CALMrev;//Must change
    }

	public function index($params,$aux){
		date_default_timezone_set("UTC"); 
		$Action = (string) $params()->fromRoute('Action', 0);

		if(empty($Action)){
			return 'Opps.. You must choice an Action. Actions List: interval, lastweek.';
		}		

		switch($Action){
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

			default:
				return 'Opps.. Wrong Action. Actions List: interval, lastweek. plof';
				break;
		}
		
	}

	//The purpose of this function is return an interval of revised data. The data returned by this function is used to
	//plot an line chart. The interval is delimited by **$start** and **$finish**.
	//**$start** , **$finish**  <==> unix timestamp
	//The returned data has a format which is easy to read to HIGHSTOCK, the api which plots the chart.	
	public function intervalHS($start,$finish)
    {
		$start = date("Y-m-d H:i:s",$start);
		$finish = date("Y-m-d H:i:s",$finish);

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
			}

		return 	Json::encode($rows);
    }

	//The purpose of this function is return an interval of revised data. The data returned by this function is used to
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
			}

		return Json::encode($rows);

    }
	
	

	//The purpose of this function is to return all the revised data. The data returned by this function is used to
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

	//The purpose of this function is inserting data into the CALM_rev table by request from the **Client**
	//The function first checks if the request is a post or not, then it checks if there is posted data. If everything
	//is correct the data is inserted in CALM_rev table and a success messege is returned, if there is some problem an error messege
	//is returned. 
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
}
