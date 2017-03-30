<?php

namespace xavoc\securityservices;


class Model_Client extends \xepan\base\Model_Table{ 
	
	public $table = "secserv_client";
	public $acl_type="client";

	function init(){
		parent::init();

		$this->addField('name');
		$this->addField('service_tax')->type('number');
		$this->addField('generate_mannual_invoice')->type('boolean');

		$this->hasMany('xavoc\securityservices\ClientService','client_id');
		$this->hasMany('xavoc\securityservices\ClientDepartment','client_department_id');

		$this->add('xavoc\securityservices\Controller_ACLFields');
	}
}