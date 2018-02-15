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
		$this->addField('labour_personal_shift_hours')->type('number');
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
		$this->addField('is_pf_deduction')->type('boolean')->defaultValue(true);
		$this->addField('uan');
		$this->addField('pf_number');
		
		$this->add('xavoc\securityservices\Controller_ACLFields');
		$this->hasMany('xavoc\securityservices\Attendance','labour_id',null,'Attendance');
		
		$this->addHook('beforeSave',$this);
	}

	function beforeSave(){
		if($this['bank_account_no']){
			$labour = $this->add('xavoc\securityservices\Model_Labour');
			$labour->addCondition('bank_account_no',$this['bank_account_no']);
			if($this->loaded())
				$labour->addCondition('id','<>',$this->id);
			$labour->tryLoadAny();
			if($labour->loaded())
				throw $this->Exception("Account number already added to other labour ".$labour['name'],'ValidityCheck')->setField('bank_account_no');
		}
	}

	function importFromCSV($data){
		if(!is_array($data) OR !count($data) ) throw new \Exception("no one record found in your csv", 1);
		
		// make array of all clients
		$d_client = $this->add('xavoc\securityservices\Model_Client')->getRows();
		$client_data = [];
		foreach ($d_client as $key => $c_data) {
			$client_data[trim($c_data['name'])] = $c_data;
			
			$c_services = $this->add('xavoc\securityservices\Model_ClientService')->addCondition('client_id',$c_data['id'])->getRows();
			$temp_service = [];
			foreach ($c_services as $key => $service) {
				$temp_service[trim($service['name'])] = $service;
				
				$c_department = $this->add('xavoc\securityservices\Model_ClientDepartment');
				$c_department->addCondition('default_client_service_id',$service['id']);
				$c_department = $c_department->getRows();
				$department = [];
				foreach ($c_department as $key => $dept) {
					$department[trim($dept['name'])] = $dept;
				}
				$temp_service[trim($service['name'])]['departments'] = $department;
			}
			$client_data[trim($c_data['name'])]['services'] = $temp_service;
		}
			
		// echo "<pre>";
		// print_r($client_data);
		// echo "</pre>";
		// die();

		foreach ($data as $key => $labour_data) {

			$labour_model = $this->add('xavoc\securityservices\Model_Labour');
			foreach ($labour_data as $field_name => $value) {
				if(in_array($field_name, ['default_client','default_client_service','default_client_department'])) continue;

				if($field_name == "gender") $value = strtolower($value);
				$labour_model[trim($field_name)] = $value;
			}

			$d_c_id = 0; 
			if($labour_data['default_client'] AND isset($client_data[trim($labour_data['default_client'])]) ){
				$d_c_id = $client_data[$labour_data['default_client']]['id'];
			}

			$d_c_s_id = 0;
			if($labour_data['default_client_service'] && $d_c_id && isset($client_data[trim($labour_data['default_client'])]['services'][trim($labour_data['default_client_service'])])){
				$d_c_s_id = $client_data[trim($labour_data['default_client'])]['services'][trim($labour_data['default_client_service'])]['id'];
			}

			// echo "data department = ".$labour_data['default_client_department']."<br/>";
			// echo "<pre>";
			// print_r($client_data[trim($labour_data['default_client'])]['services'][trim($labour_data['default_client_service'])]['departments'][trim($labour_data['default_client_department'])]);
			// echo "</pre>";

			$d_c_d_id = 0;
			if($labour_data['default_client_department'] && $d_c_s_id && isset($client_data[trim($labour_data['default_client'])]['services'][trim($labour_data['default_client_service'])]['departments'][trim($labour_data['default_client_department'])])){
				$d_c_d_id = $client_data[trim($labour_data['default_client'])]['services'][trim($labour_data['default_client_service'])]['departments'][trim($labour_data['default_client_department'])]['id'];
			}

			// echo "def d id = ".$d_c_d_id."<br/>";

			$labour_model['default_client_id'] = $d_c_id;
			$labour_model['default_client_service_id'] = $d_c_s_id;
			$labour_model['default_client_department_id'] = $d_c_d_id;
			$labour_model->save();
		}
	}
}