<?php
namespace CALM\Model;

class binTableModel
{
    public $start_date_time;

    public $ch01;	public $ch02;	public $ch03;	public $ch04;
	public $ch05;	public $ch06;	public $ch07;	public $ch08;
	public $ch09;	public $ch10;	public $ch11;	public $ch12;
	public $ch13;	public $ch14;	public $ch15;	public $ch16;
	public $ch17;	public $ch18;

	public $hv1;	public $hv2;	public $hv3;

	public $temp_1;	public $temp_2;

	public $atmPressure;



    public function exchangeArray($data)
    {
		$this->start_date_time	= (isset($data['start_date_time'])) ? $data['start_date_time'] : null;

		$this->ch01	= (isset($data['ch01'])) ? $data['ch01'] : null;
		$this->ch02	= (isset($data['ch02'])) ? $data['ch02'] : null;
		$this->ch03	= (isset($data['ch03'])) ? $data['ch03'] : null;
		$this->ch04	= (isset($data['ch04'])) ? $data['ch04'] : null;
		$this->ch05	= (isset($data['ch05'])) ? $data['ch05'] : null;
		$this->ch06	= (isset($data['ch06'])) ? $data['ch06'] : null;
		$this->ch07	= (isset($data['ch07'])) ? $data['ch07'] : null;
		$this->ch08	= (isset($data['ch08'])) ? $data['ch08'] : null;
		$this->ch09	= (isset($data['ch09'])) ? $data['ch09'] : null;
		$this->ch10	= (isset($data['ch10'])) ? $data['ch10'] : null;
		$this->ch11	= (isset($data['ch11'])) ? $data['ch11'] : null;
		$this->ch12	= (isset($data['ch12'])) ? $data['ch12'] : null;
		$this->ch13	= (isset($data['ch13'])) ? $data['ch13'] : null;
		$this->ch14	= (isset($data['ch14'])) ? $data['ch14'] : null;
		$this->ch15	= (isset($data['ch15'])) ? $data['ch15'] : null;
		$this->ch16	= (isset($data['ch16'])) ? $data['ch16'] : null;
		$this->ch17	= (isset($data['ch17'])) ? $data['ch17'] : null;
		$this->ch18	= (isset($data['ch18'])) ? $data['ch18'] : null;

		$this->hv1     = (isset($data['hv1'])) ? $data['hv1'] : null;
		$this->hv2     = (isset($data['hv2'])) ? $data['hv2'] : null;
		$this->hv3     = (isset($data['hv3'])) ? $data['hv3'] : null;

		$this->temp_1     = (isset($data['temp_1'])) ? $data['temp_1'] : null;
		$this->temp_2     = (isset($data['temp_2'])) ? $data['temp_2'] : null;

		$this->atmPressure     = (isset($data['atmPressure'])) ? $data['atmPressure'] : null;
    }
}
