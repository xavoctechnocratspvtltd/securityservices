<?php

namespace xavoc\securityservices;


class page_manageattendance extends \xepan\base\Page {
	
	public $title ="Attendance";

	function init(){
		parent::init();

		$month_year_id = $this->app->stickyGET('client_monthyear_record_id');

		$month_year_model = $this->add('xavoc\securityservices\Model_ClientMonthYear');
		$month_year_model->addCondition('id',$month_year_id);
		$month_year_model->tryLoadAny();
		if(!$month_year_model->loaded())
			throw new \Exception("client month year not found", 1);

		$data = [
				'month_days'=>31,
				'labours'=>[
						'labour_id'=>[
								'name'=>'labour_name',
								'units_work'=>20
							]
					],
				'additional_labours'=>[
						'labour_id'=>[
							'name'=>'labour_name',
							'units_work'=>20
						]		
					]
			];

		$default_labour = $this->add('xavoc\securityservices\Model_ClientLabour',['client_month_year_id'=>$month_year_model->id,'client_id'=>$month_year_model['client_id']])->getRows();
		echo "<pre>";
		print_r($default_labour);
		echo "</pre>";

		$additional_labour = $month_year_model->additionalLabour()->getRows();
		echo "<pre>";
		print_r($additional_labour);
		echo "</pre>";		
		exit;

		foreach($default_labour as $labour_id => $labour_data) {

		}

		$this->js(true)->_load('jquery.livequery');
		$this->js(true)->_load('attendance')->xavoc_secserv_attendance([]);
	}
}