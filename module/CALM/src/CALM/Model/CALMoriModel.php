<?php
namespace CALM\Model;

class CALMoriModel
{
    public $start_date_time;

    public $length_time_interval_s;
    public $measured_uncorrected;
    public $measured_corr_for_efficiency; 
    public $measured_corr_for_pressure;
    public $measured_pressure_mbar;



    public function exchangeArray($data)
    {
		$this->start_date_time	= (isset($data['start_date_time'])) ? $data['start_date_time'] : null;

		$this->length_time_interval_s	= (isset($data['length_time_interval_s'])) ? $data['length_time_interval_s'] : null;
		$this->measured_uncorrected	= (isset($data['measured_uncorrected'])) ? $data['measured_uncorrected'] : null;
		$this->measured_corr_for_efficiency	= (isset($data['measured_corr_for_efficiency'])) ? $data['measured_corr_for_efficiency'] : null;
		$this->measured_corr_for_pressure	= (isset($data['measured_corr_for_pressure'])) ? $data['measured_corr_for_pressure'] : null;
		$this->measured_pressure_mbar	= (isset($data['measured_pressure_mbar'])) ? $data['measured_pressure_mbar'] : null;
    }
}
