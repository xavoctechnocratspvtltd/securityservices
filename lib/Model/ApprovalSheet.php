<?php

namespace xavoc\securityservices;


class Model_ApprovalSheet extends \xepan\base\Model_Table{ 
	public $table = "secserv_approval_sheet";

	public $acl="xavoc\securityservices\Model_ClientMonthYear";

	function init(){
		parent::init();

		$this->addField('name');
		
		$this->hasOne('xavoc\securityservices\ClientMonthYear','client_month_year_id');
		$this->hasOne('xavoc\securityservices\ClientDepartment','client_department_id');
		$this->hasOne('xavoc\securityservices\ClientService','client_service_id');

		$this->addField('is_overtime_record')->type('boolean')->defaultValue(false);
		
		for($i=1; $i<=31; $i++){
			$this->addField('d'.$i)->caption($i);
		}

	}
}