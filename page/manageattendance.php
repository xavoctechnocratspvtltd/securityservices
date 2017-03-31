<?php

namespace xavoc\securityservices;


class page_manageattendance extends \xepan\base\Page {
	
	public $title ="Attendance";

	function page_index(){
		// parent::init();

		$month_year_id = $this->app->stickyGET('client_monthyear_record_id');

		$month_year_model = $this->add('xavoc\securityservices\Model_ClientMonthYear');
		$month_year_model->addCondition('id',$month_year_id);
		$month_year_model->tryLoadAny();
		if(!$month_year_model->loaded())
			throw new \Exception("client month year not found", 1);

		$last_date = date("t",strtotime($month_year_model['month_year']));
		
		$all_labour = $this->getAllRemainingLabours($month_year_model);

		$client_departments = $this->add('xavoc\securityservices\Model_ClientDepartment')->addCondition('client_id',$month_year_model['client_id']);
		$this->title = $month_year_model['client']." ".$month_year_model['name']." (".$month_year_model['month_year'].") Attendance";
		
		$this->js(true)->_load('jquery.livequery');
		$this->js(true)->_load('attendance')
						->xavoc_secserv_attendance(
							[
								'client_month_year_id'=>$month_year_model->id,
								'client_id'=>$month_year_model['client_id'],
								'client_name'=>$month_year_model['client'],
								'client_departments'=>$client_departments->getRows(['id','name']),
								'month_days'=>$last_date,
								'default_labours'=>'{}',
								'additional_labours'=>'{}',
								'remaining_all_labours'=>json_encode($all_labour)
							]);
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
		//];
	}

	function page_labours(){
		$record_id = $_GET['record_id'];
		$dept_id = $_GET['dept_id'];

		$return = ['status'=>'failed','data'=>[]];

		if($record_id <= 0){
			echo json_encode($return);
			exit;
		}

		$month_year_model = $this->add('xavoc\securityservices\Model_ClientMonthYear');
		$month_year_model->addCondition('id',$record_id);
		$month_year_model->tryLoadAny();
		if(!$month_year_model->loaded()){
			echo json_encode($return);
			exit;
		}

		$labour_model = $this->add('xavoc\securityservices\Model_ClientLabour',['client_month_year_id'=>$month_year_model->id,'client_id'=>$month_year_model['client_id'],'department_id'=>$dept_id]);
		$default_labours = $labour_model->getRows();

		$additional_labours = $month_year_model->additionalLabour()->getRows();
		
		$return['status'] = "success";
		$return['data']['default_labours'] = $default_labours;
		$return['data']['additional_labours'] = $additional_labours;

		echo json_encode($return);
		exit;
	}

	function page_save(){
		$record_id = $_POST['record_id'];
		$dept_id = $_POST['dept_id'];		
		$attendance_data = $_POST['attendance'];
		
		$attendance_data = json_decode($attendance_data);
		
		/**
		[
			'client_id':,
			client_month_year_id:,
			department_id:,
			attendance:[
					'labour_id_1'=>[
							'date1'=>12
							'date3'=>90
							'date4'=>1
						]
					'labour_id_2'=>[
							'date1'=>12
							'date3'=>90
							'date4'=>1
						]
					]
		]
		*/

		$return = ['status'=>'failed'];

		if($record_id <= 0){
			echo json_encode($return);
			exit;
		}

		echo json_encode($return);
		exit;
	}

	function getAllRemainingLabours($month_year_model){

		// client default labours
		$default_labours = $this->add('xavoc\securityservices\Model_ClientLabour',['client_month_year_id'=>$month_year_model->id,'client_id'=>$month_year_model['client_id']]);
		$default_labours = $default_labours->_dsql()->del('fields')->field('id')->getAll();
		$default_labours = iterator_to_array(new \RecursiveIteratorIterator(new \RecursiveArrayIterator($default_labours)),false);
		
		$additional_labours = $month_year_model->additionalLabour();
		$additional_labours = $additional_labours->_dsql()->del('fields')->field('id')->getAll();
		$additional_labours = iterator_to_array(new \RecursiveIteratorIterator(new \RecursiveArrayIterator($additional_labours)),false);

		//get all labours
		$labours = $this->add('xavoc\securityservices\Model_Labour')
						->addCondition('is_active',true)
						->getRows()
						;
		$all_labour = [];
		foreach ($labours as $labour) {
			if(in_array($labour['id'],$default_labours) OR in_array($labour['id'],$additional_labours) ) continue;
			
			$all_labour[$labour['id']] = $labour;
		}

		return $all_labour;
	}
}