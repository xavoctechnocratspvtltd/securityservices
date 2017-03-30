<?php

namespace xavoc\securityservices;


class Model_Labour extends \xepan\base\Model_Table{ 
	public $table = "secserv_labour";

	public $acl_type="labour";

	function init(){
		parent::init();

		$this->addField('name');
		
		$this->hasOne('xavoc\securityservices\Client','default_client_id');
		$this->hasOne('xavoc\securityservices\ClientService','default_client_department_id');
		$this->hasOne('xavoc\securityservices\ClientService','default_client_service_id');

		$this->addField('is_active')->type('boolean')->defaultValue(true);

		$this->add('xavoc\securityservices\Controller_ACLFields');

	}
}