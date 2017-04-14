<?php

namespace xavoc\securityservices;


class Model_Client extends \xepan\base\Model_Table{ 
	
	public $table = "secserv_client";
	public $acl_type="client";

	public $status=['Active','InActive'];

	public $actions=[
		'Active'=>['view','edit','delete','services','departments'],
		'InActive'=>['view','edit','delete']
	];

	function init(){
		parent::init();

		$this->hasOne('xavoc\securityservices\Layout','invoice_layout_id');

		$this->addField('name');
		$this->addField('status')->enum(['Active','InActive'])->defaultValue('Active');
		$this->addField('service_tax')->type('number');
		$this->addField('generate_mannual_invoice')->type('boolean');

		$this->hasMany('xavoc\securityservices\ClientService','client_id');
		$this->hasMany('xavoc\securityservices\ClientDepartment','client_id');
		$this->hasMany('xavoc\securityservices\Labour','default_client_id');

		$this->add('xavoc\securityservices\Controller_ACLFields');

		$this->addHook('beforeDelete',[$this,'deleteClientServiceAndDepartment']);
	}

	function page_services($page){
		$m = $this->add('xavoc\securityservices\Model_ClientService')->addCondition('client_id',$this->id);
		$c = $page->add('xepan\base\CRUD');
		$c->setModel($m);
		$c->grid->removeColumn('created_by');
	}

	function page_departments($page){
		$m = $this->add('xavoc\securityservices\Model_ClientDepartment')->addCondition('client_id',$this->id);
		$c = $page->add('xepan\base\CRUD');
		$c->setModel($m);
		$c->grid->removeColumn('created_by');
	}

	function deleteClientServiceAndDepartment($m){
		$client_l = $this->ref('xavoc\securityservices\Labour')->count()->getOne();
		$client_services = $this->ref('xavoc\securityservices\ClientService')->count()->getOne();
		$client_department = $this->ref('xavoc\securityservices\ClientDepartment')->count()->getOne();
		if($client_l OR $client_services OR $client_department){
			throw new \Exception("Client Can not delete, Please Delete the Client service & Department first");
			
		}
	}
}