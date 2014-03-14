<?php
namespace CALM\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Where;

class binTable
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

	public function interval($start,$finish)
    {
		$where= new Where();
		$where->between('start_date_time',$start,$finish);
		$resultSet = $this->tableGateway->select($where);
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
		return $rows;
    }


    public function auxSearch()
    {
		return 'holaaaaas';
    }
}
