<?php

namespace xavoc\securityservices;


class Model_Labour extends \xepan\base\Model_Table{ 
	public $table = "secserv_labour";

	public $acl_type="labour";

	function init(){
		parent::init();

		
		$this->hasOne('xavoc\securityservices\Client','default_client_id');//->display(['form'=>'xepan\base\DropDownNormal']);
		$this->hasOne('xavoc\securityservices\ClientService','default_client_service_id');//->display(['form'=>'xepan\base\DropDownNormal']);
		$this->hasOne('xavoc\securityservices\ClientDepartment','default_client_department_id');//->display(['form'=>'xepan\base\DropDownNormal']);
		
		$this->addField('name');
		$this->addField('labour_shift_hours')->type('number');
		$this->addField('address')->type('text');
		$this->addField('dob')->type('date');
		$this->addField('gender')->enum(['male','female','other']);
		$this->addField('mobile_no');
		$this->addField('email_id');
		$this->addField('guardian_name')->hint('FATHER NAME (OR HUSBANDS NAME IN CASE OF MARRIED WOMEN)');
		$this->addField('bank_name');
		$this->addField('bank_account_no');
		$this->addField('bank_ifsc_code');
		$this->addField('bank_branch');
		$this->addField('is_active')->type('boolean')->defaultValue(true);
		
		$this->add('xavoc\securityservices\Controller_ACLFields');
		$this->hasMany('xavoc\securityservices\Attendance','labour_id',null,'Attendance');
		
	}

	function importFromCSV($data){
		if(!is_array($data) OR !count($data) ) throw new \Exception("no one record found in your csv", 1);
		
		// make array of all clients
		$d_client = $this->add('xavoc\securityservices\Model_Client')->addCondition('status','Active')->getRows();
		$client_data = [];
		foreach ($d_client as $key => $c_data) {
			$client_data[$c_data['name']] = $c_data;
			
			$c_services = $this->add('xavoc\securityservices\Model_ClientService')->addCondition('client_id',$c_data['id'])->getRows();
			$temp_service = [];
			foreach ($c_services as $key => $service) {
				$temp_service[$service['name']] = $service;
				
				$c_department = $this->add('xavoc\securityservices\Model_ClientDepartment');
				$c_department->addCondition('default_client_service_id',$service['id']);
				$c_department = $c_department->getRows();
				$department = [];
				foreach ($c_department as $key => $dept) {
					$department[$dept['name']] = $dept;
				}
				$temp_service['departments'] = $department;
			}
			$client_data['services'] = $temp_service;
		}
		
		foreach ($data as $key => $labour_data) {

			$labour_model = $this->add('xavoc\securityservices\Model_Labour');
			foreach ($labour_data as $field_name => $value) {
				if(in_array($field_name, ['default_client','default_client_service','default_client_department'])) continue;
				$labour_model[$field_name] = $value;
			}

			$d_c_id = 0; 
			if($labour_data['default_client'] AND isset($client_data[$labour_data['default_client']]) ){
				$d_c_id = $client_data[$labour_data['default_client']]['id'];
			}

			$d_c_s_id = 0;
			if($labour_data['default_client_service'] && $d_c_id && isset($client_data[$labour_data['default_client']]['services'][$labour_data['default_client_service']])){
				$d_c_s_id = $client_data[$labour_data['default_client']]['services'][$labour_data['default_client_service']]['id'];
			}

			$d_c_d_id = 0;
			if($labour_data['default_client_department'] && $d_c_s_id && isset($client_data[$labour_data['default_client']]['services'][$labour_data['default_client_service']]['departments'][$labour_data['default_client_department']] )){
				$d_c_s_id = $client_data[$labour_data['default_client']]['services'][$labour_data['default_client_service']]['departments'][$labour_data['default_client_department']]['id'];
			}

			$labour_model['default_client_id'] = $d_c_id;
			$labour_model['default_client_service_id'] = $d_c_s_id;
			$labour_model['default_client_department_id'] = $d_c_d_id;
			$labour_model->save();
		}
	}
}