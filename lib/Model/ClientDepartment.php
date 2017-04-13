<?php

namespace xavoc\securityservices;


class Model_ClientDepartment extends \xepan\base\Model_Table{ 
	
	public $table = "secserv_client_department";
	public $acl="xavoc\securityservices\Model_Client";

	function init(){
		parent::init();

		$this->hasOne('xavoc\securityservices\Client','client_id');
		$this->hasOne('xavoc\securityservices\ClientService','default_client_service_id');

		$this->addField('name');
		$this->addExpression('client_shift_hours')->set(function($m,$q){
			return $q->expr('IFNULL([0],0)',[$m->refSQL('default_client_service_id')->fieldQuery('client_shift_hours')]);
		});

		$this->add('xavoc\securityservices\Controller_ACLFields');

		$this->hasMany('xavoc\securityservices\Attendance','client_department_id');
		$this->hasMany('xavoc\securityservices\Labour','default_client_department_id');
		$this->addHook('beforeDelete',$this);

	}

	function beforeDelete($m){
		if($this->ref('xavoc\securityservices\Labour')->count()->getOne()){
			throw new \Exception("Department Can not delete, Please Delete the Labour, first");
		}
	}
}