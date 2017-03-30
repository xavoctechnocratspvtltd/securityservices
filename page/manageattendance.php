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

		$client_labour_model = $this->add('xavoc\securityservices\Model_ClientLabour',['client_month_year_id'=>$month_year_model->id,'client_id'=>$month_year_model['client_id']]);
		$default_labours = $client_labour_model->getRows();

		$additional_labours = $month_year_model->additionalLabour()->getRows();
		
		$last_date = date("t",strtotime($month_year_model['month_year']));
		// $data = [
		// 		'month_days'=>31,
		// 		'labours'=>[
		// 				'any_key'=>[
		// 						'name'=>'labour_name',
		// 						'units_work'=>20
		// 					]
		// 			],
		// 		'additional_labours'=>[
		// 				'any_key'=>[
		// 					'name'=>'labour_name',
		// 					'units_work'=>20
		// 				]		
		// 			]
		// 	];
		$this->js(true)->_load('jquery.livequery');
		$this->js(true)->_load('attendance')
						->xavoc_secserv_attendance(
							[
								'month_days'=>$last_date,
								'default_labours'=>json_encode($default_labours),
								'additional_labours'=>json_encode($additional_labours)
							]);
	}
}