<?php

namespace xavoc\securityservices;


class Model_BillingService extends \xepan\base\Model_Table{ 
	
	public $table = "secserv_billing_service";

	public $acl_type="billing_service";
	public $status = ['All'];
 	public $actions = [
 						'All'=>['view','edit','delete']
 					];

	function init(){
		parent::init();

		$this->addField('name');

		$this->add('xavoc\securityservices\Controller_ACLFields');

		$this->addHook('beforeDelete',[$this,'deleteClientServices']);

		$this->hasMany('xavoc\securityservices\ClientService','billing_service_id');
	}

	function deleteClientServices($m){
		$client_service = $this->ref('xavoc\securityservices\ClientService')->count()->getOne();
		if($client_service){
			throw new \Exception("Billing Services Can not delete, Please Delete the Client Services , first");
		}
	}	
}