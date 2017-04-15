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
}