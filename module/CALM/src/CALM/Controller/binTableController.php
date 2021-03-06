<?php
namespace CALM\Controller;


use Zend\Db\TableGateway\TableGateway;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Zend\Json\Json;

class binTableController
{
	//This TableGateway object should be changed to one Zend_Db_Adapter..
	//At the moment in order to execute a query what we do is **binTable->getAdapter()->query("select * from ...;")->execute();
	//Should look like this: nmdadbAdapter->query("select * from ...;")->execute();
    protected $binTable;//Must change

    public function __construct(TableGateway $binTable)
    {
        $this->binTable = $binTable;//Must change
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

	//This function returns data which is used by  Clientv1, a client which plots the data using the ExtJS chart API..
	//Ploting with ExtJS hasnt much future for our project so its not worth commenting this
	public function intervalTuned($start,$finish,$interval)
    {
		$start=str_replace('+',' ',$start);
		$finish=str_replace('+',' ',$finish);
		$start=str_replace('%20',' ',$start);
		$finish=str_replace('%20',' ',$finish);
	
		$result = $this->binTable->getAdapter()->query("select count(*) as cnt, avg(ch01) as ch01avg, avg(ch02) as ch02avg, avg(ch03) as ch03avg, avg(ch04) as ch04avg, avg(ch05) as ch05avg, avg(ch06) as ch06avg, avg(ch07) as ch07avg, avg(ch08) as ch08avg, avg(ch09) as ch09avg, avg(ch10) as ch10avg, avg(ch11) as ch11avg, avg(ch12) as ch12avg, avg(ch13) as ch13avg, avg(ch14) as ch14avg, avg(ch15) as ch15avg, avg(ch16) as ch16avg, avg(ch17) as ch17avg, avg(ch18) as ch18avg, avg(hv1) as hv1avg, avg(hv2) as hv2avg, avg(hv3) as hv3avg, avg(temp_1) as temp_1avg, avg(temp_2) as temp_2avg, avg(atmPressure) as atmPressureavg, substring_index(substring_index(group_concat(start_date_time order by start_date_time),',',ceil(count(*)/2)),',',-1) as date from (select @i:=@i+1 as rownum,FLOOR(@i/".$interval.") as GGG,start_date_time,ch01,ch02,ch03,ch04,ch05,ch06,ch07,ch08,ch09,ch10,ch11,ch12,ch13,ch14,ch15,ch16,ch17,ch18,hv1,hv2,hv3,temp_1,temp_2,atmPressure from binTable join (select @i:=-1) as dick  where start_date_time between '".$start."' and '".$finish."')as t group by GGG;")->execute();

		$resultSet = new ResultSet;
    	$resultSet->initialize($result);

		$rows = array();
		foreach ($resultSet as $binTableModel){
				$rows[]=array(
					'start_date_time'=>$binTableModel->date,
					'ch01'=>$binTableModel->ch01avg,
					'ch02'=>$binTableModel->ch02avg,
					'ch03'=>$binTableModel->ch03avg,
					'ch04'=>$binTableModel->ch04avg,
					'ch05'=>$binTableModel->ch05avg,
					'ch06'=>$binTableModel->ch06avg,
					'ch07'=>$binTableModel->ch07avg,
					'ch08'=>$binTableModel->ch08avg,
					'ch09'=>$binTableModel->ch09avg,
					'ch10'=>$binTableModel->ch10avg,
					'ch11'=>$binTableModel->ch11avg,
					'ch12'=>$binTableModel->ch12avg,
					'ch13'=>$binTableModel->ch13avg,
					'ch14'=>$binTableModel->ch14avg,
					'ch15'=>$binTableModel->ch15avg,
					'ch16'=>$binTableModel->ch16avg,
					'ch17'=>$binTableModel->ch17avg,
					'ch18'=>$binTableModel->ch18avg,
					'hv1'=>$binTableModel->hv1avg,
					'hv2'=>$binTableModel->hv2avg,
					'hv3'=>$binTableModel->hv3avg,
					'temp_1'=>$binTableModel->temp_1avg,
					'temp_2'=>$binTableModel->temp_2avg,
					'atmPressure'=>$binTableModel->atmPressureavg,
				);
			}

			Json::encode($rows);

			return 	$_GET['hola'].
				'('.
				Json::encode($rows).
				');'
				;
	}

	//The purpose of this function is return an interval of raw data. The data returned by this function is used to
	//plot an line chart. The interval is delimited by **$start** and **$finish**.
	//**$start** , **$finish**  <==> unix timestamp
	//The returned data has a format which is easy to read to HIGHSTOCK, the api which plots the chart.
	public function intervalHS($start,$finish)
    {
		$start = date("Y-m-d H:i:s",$start);
		$finish = date("Y-m-d H:i:s",$finish);

		$where= new Where();
		$where->between('start_date_time',$start,$finish);
		$resultSet = $this->binTable->select($where);
		$rows = array();
		
		foreach ($resultSet as $binTableModel){
				$rows[0][]=array(
					strtotime($binTableModel->start_date_time)*1000,
					(int)$binTableModel->ch01,
				);
				$rows[1][]=array(
					strtotime($binTableModel->start_date_time)*1000,
					(int)$binTableModel->ch02,
				);
				$rows[2][]=array(
					strtotime($binTableModel->start_date_time)*1000,
					(int)$binTableModel->ch03,
				);
				$rows[3][]=array(
					strtotime($binTableModel->start_date_time)*1000,
					(int)$binTableModel->ch04,
				);
				$rows[4][]=array(
					strtotime($binTableModel->start_date_time)*1000,
					(int)$binTableModel->ch05,
				);
				$rows[5][]=array(
					strtotime($binTableModel->start_date_time)*1000,
					(int)$binTableModel->ch06,
				);
				$rows[6][]=array(
					strtotime($binTableModel->start_date_time)*1000,
					(int)$binTableModel->ch07,
				);
				$rows[7][]=array(
					strtotime($binTableModel->start_date_time)*1000,
					(int)$binTableModel->ch08,
				);
				$rows[8][]=array(
					strtotime($binTableModel->start_date_time)*1000,
					(int)$binTableModel->ch09,
				);
				$rows[9][]=array(
					strtotime($binTableModel->start_date_time)*1000,
					(int)$binTableModel->ch10,
				);
				$rows[10][]=array(
					strtotime($binTableModel->start_date_time)*1000,
					(int)$binTableModel->ch11,
				);
				$rows[11][]=array(
					strtotime($binTableModel->start_date_time)*1000,
					(int)$binTableModel->ch12,
				);
				$rows[12][]=array(
					strtotime($binTableModel->start_date_time)*1000,
					(int)$binTableModel->ch13,
				);
				$rows[13][]=array(
					strtotime($binTableModel->start_date_time)*1000,
					(int)$binTableModel->ch14,
				);
				$rows[14][]=array(
					strtotime($binTableModel->start_date_time)*1000,
					(int)$binTableModel->ch15,
				);
				$rows[15][]=array(
					strtotime($binTableModel->start_date_time)*1000,
					(int)$binTableModel->ch16,
				);
				$rows[16][]=array(
					strtotime($binTableModel->start_date_time)*1000,
					(int)$binTableModel->ch17,
				);
				$rows[17][]=array(
					strtotime($binTableModel->start_date_time)*1000,
					(int)$binTableModel->ch18,
				);
			}

		return 	Json::encode($rows);
    }
}
