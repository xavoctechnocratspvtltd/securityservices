<?php

namespace xavoc\securityservices;


class Model_ClientService extends \xepan\base\Model_Table{ 
	
	public $table = "secserv_client_service";
	public $acl="xavoc\securityservices\Model_Client";

	function init(){
		parent::init();

		$this->hasOne('xavoc\securityservices\Client','client_id');
		$this->hasOne('xavoc\securityservices\BillingService','billing_service_id')->caption('For Invoicing');

		$this->addField('name')->caption('Our Internal Service name (Sub service for internal only)');
		$this->addField('invoice_base')->enum(['Hour','Shift','Month']);
		$this->addField('client_shift_hours')->type('number');
		$this->addField('invoice_rate')->hint('payment to charge in invoice per invoice_base');

		$this->addField('payment_base')->enum(['Hour','Shift','Month']);
		$this->addField('labour_shift_hours')->type('number');
		$this->addField('payment_rate')->hint('payment to pay for labour per payment_base');

		$this->add('xavoc\securityservices\Controller_ACLFields');
		$this->addHook('beforeDelete',[$this,'deleteClientDepartment']);

		$this->hasMany('xavoc\securityservices\ClientDepartment','default_client_service_id');
		$this->hasMany('xavoc\securityservices\Labour','default_client_service_id');
		$this->hasMany('xavoc\securityservices\Attendance','client_service_id');

	}

	function deleteClientDepartment($m){
		$client_labour = $this->ref('xavoc\securityservices\Labour')->count()->getOne();
		$client_department = $this->ref('xavoc\securityservices\ClientDepartment')->count()->getOne();
		if($client_department OR $client_labour){
			throw new \Exception("Client Services Can not delete, Please Delete the Client Department & Labour first");
		}
	}	
}