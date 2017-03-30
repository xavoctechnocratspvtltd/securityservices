<?php

namespace xavoc\securityservices;


class Model_BillingService extends \xepan\base\Model_Table{ 
	
	public $table = "secserv_billing_service";

	public $acl_type="billing_service";

	function init(){
		parent::init();

		$this->addField('name');

		$this->add('xavoc\securityservices\Controller_ACLFields');
	}
}