<?php

namespace xavoc\securityservices;


class Model_Attendance extends \xepan\base\Model_Table{ 
	public $table = "secserv_attendance";
	
	public $acl="xavoc\securityservices\Model_ClientMonthYear";
	public $acl_type = "attendance";
	
	function init(){
		parent::init();

		$this->hasOne('xavoc\securityservices\Labour','labour_id');
		$this->hasOne('xavoc\securityservices\ClientMonthYear','client_month_year_id');
		$this->hasOne('xavoc\securityservices\ClientDepartment','client_department_id');
		$this->hasOne('xavoc\securityservices\ClientService','client_service_id');

		$this->addField('date')->type('datetime')->defaultValue($this->app->now);
		$this->addField('unit');
		$this->addField('unit_work')->type('Number');

	}
}