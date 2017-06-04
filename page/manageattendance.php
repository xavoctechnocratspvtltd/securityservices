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
		
		$this->js(true)->_load('select2.min');
		$this->app->jui->addStaticStylesheet('libs/select2');
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

		// $total_days_in_month = date("t",strtotime($month_year_model['month_year']));
		$default_labours_ids = [];
		//associating month attendance
		foreach ($default_labours as $key => $d_l) {
			$att_m = $this->add('xavoc\securityservices\Model_Attendance');
			$att_m_array = $att_m->addCondition('labour_id',$d_l['id'])
				->addCondition('client_month_year_id',$month_year_model->id)
				->addCondition('client_department_id',$dept_id)
				// ->addCondition('client_department_id',$dept_id)
				->addCondition('month',$month_year_model['month'])
				->addCondition('year',$month_year_model['year'])
				->getRows();

			$month_record = [];
			foreach ($att_m_array as $index => $day_record) {
				$month_record[$day_record['day']] = $day_record['units_work'];
			}
			$default_labours[$key]['month_attendance'] = $month_record;
			$default_labours_ids[$d_l['id']] = $d_l['id'];
		}

		$add_labours = $month_year_model->additionalLabour($dept_id)->getRows();
		//associating month attendance for additional labours
		$additional_labours = [];

		foreach ($add_labours as $key => $a_l) {
			if(isset($default_labours_ids[$a_l['labour_id']])) continue;

			$labour = $this->add('xavoc\securityservices\Model_Labour');
			$labour->addCondition('id',$a_l['labour_id']);
			$labour->tryLoadAny();
			if(!$labour->loaded()){
				throw new \Exception("labour not found or deleted");
			};

			$temp = $labour->getRows();
			
			$att_m = $this->add('xavoc\securityservices\Model_Attendance');
			$att_m_array = $att_m->addCondition('labour_id',$a_l['labour_id'])
				->addCondition('client_month_year_id',$month_year_model->id)
				->addCondition('client_department_id',$dept_id)
				// ->addCondition('client_department_id',$dept_id)
				->addCondition('month',$month_year_model['month'])
				->addCondition('year',$month_year_model['year'])
				->getRows();
			$month_record = [];
			foreach ($att_m_array as $index => $day_record) {
				$month_record[$day_record['day']] = $day_record['units_work'];
			}

			$temp[0]['month_attendance'] = $month_record;
			$additional_labours[] = $temp[0];
		}

		// echo "<pre>";
		// print_r($additional_labours);
		// echo "</pre>";
		// exit;
		// echo count($default_labours)." + ".count($additional_labours);
		// die();

		$return['status'] = "success";
		$return['data']['default_labours'] = $default_labours;
		$return['data']['additional_labours'] = $additional_labours;

		// echo "<pre>";
		// print_r($return);
		// echo "</pre>";

		echo json_encode($return);
		exit;
	}

	/**
	POST Attendance Data = [
			'client_id':,
			'client_month_year_id':,
			'department_id':,
			'attendance':[
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
	function page_save(){
		$return = ['status'=>'failed'];

		$attendance_data = $_POST['attendance_data'];
		$attendance_data = json_decode($attendance_data,true);

		$client_id = $attendance_data['client_id'];
		$department_id = $attendance_data['department_id'];
		$client_month_year_id = $attendance_data['client_month_year_id'];
		$attendance = $attendance_data['attendance'];

		$client_month_year_model = $this->add('xavoc\securityservices\Model_ClientMonthYear')->tryLoad($client_month_year_id);
		if(!$client_month_year_model->loaded()){
			return $return;
		}

		$client_department_model = $this->add('xavoc\securityservices\Model_ClientDepartment')->tryLoad($department_id);
		if(!$client_department_model->loaded()){
			return $return;
		}

		// default_client_service_id
		//first delete all attendance data based on department_id and client_month_id
		$attendance_model = $this->add('xavoc\securityservices\Model_Attendance');
		$attendance_model->addCondition('client_month_year_id',$client_month_year_id)
					->addCondition('client_department_id',$department_id)
					->addCondition('month',$client_month_year_model['month'])
					->addCondition('year',$client_month_year_model['year'])
					;
		$attendance_model->deleteAll();

		$month = date('m', strtotime($client_month_year_model['month_year']));
		$year = date('Y', strtotime($client_month_year_model['month_year']));

		// all labours
		$labours = [];
		foreach ($this->add('xavoc\securityservices\Model_Labour')->getRows() as $key => $t_labour) {
			if(!$t_labour['is_active']) continue;

			$labours[$t_labour['id']] = $t_labour;
		}

		//insert data
		// INSERT INTO table_name (column1, column2, column3, ...)
		// VALUES (value1, value2, value3, ...);
		$dept_client_shift_hours = $client_department_model['client_shift_hours'];

		$insert_sql = "INSERT INTO secserv_attendance (`labour_id`, `client_month_year_id`, `client_department_id`, `client_service_id`, `units_work`, `date`,`overtime_units_work`, `shift_units_work`)VALUES";
		foreach ($attendance as $labour_id => $sheet) {
			foreach ($sheet as $day => $units_work) {

				$shift_work = $units_work;
				$overtime_work = 0;

				$labour_shift_hours = 0;
				if(isset($labours[$labour_id])){
					$labour_shift_hours = $labours[$labour_id]['labour_shift_hours'];
				}

				if($shift_work > $labour_shift_hours){
					$overtime_work = $shift_work - $labour_shift_hours;
					$shift_work = $labour_shift_hours;
				}

				$date = $year.'-'.$month.'-'.$day;
				$attendance_date = date("Y-m-d H:i:s", strtotime($date));
				$insert_sql .= '("'.$labour_id.'", "'.$client_month_year_id.'", "'.$client_department_model->id.'", "'.$client_department_model['default_client_service_id'].'", "'.$units_work.'", "'.$attendance_date.'", "'.$overtime_work.'", "'.$shift_work.'"),';
			}
		}
		$insert_sql = trim($insert_sql,',');
		$insert_sql .= ";";
		
		// echo $insert_sql;
		// die();

		try{
			$this->app->db->dsql()->expr($insert_sql)->execute();
			$return['status'] = 'success';

		}catch(Exception $e){
			throw new \Exception($e->getMessage());
		}

		echo json_encode($return);
		exit;
	}

	function getAllRemainingLabours($month_year_model){

		// commented for passing all labour
		// client default labours
		// $default_labours = $this->add('xavoc\securityservices\Model_ClientLabour',['client_month_year_id'=>$month_year_model->id,'client_id'=>$month_year_model['client_id']]);
		// $default_labours = $default_labours->_dsql()->del('fields')->field('id')->getAll();
		// $default_labours = iterator_to_array(new \RecursiveIteratorIterator(new \RecursiveArrayIterator($default_labours)),false);
		$default_labours = [];
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
			$all_labour[$labour['id']]['month_attendance']=[];
		}

		return $all_labour;
	}
}