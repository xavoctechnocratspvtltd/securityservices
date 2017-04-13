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
		$this->addField('status');
		$this->addField('service_tax')->type('number');
		$this->addField('generate_mannual_invoice')->type('boolean');

		$this->hasMany('xavoc\securityservices\ClientService','client_id');
		$this->hasMany('xavoc\securityservices\ClientDepartment','client_department_id');

		$this->add('xavoc\securityservices\Controller_ACLFields');
	}

	function page_services($page){
		$m = $this->add('xavoc\securityservices\Model_ClientService')->addCondition('client_id',$this->id);
		$c = $page->add('xepan\base\CRUD');
		$c->setModel($m);
	}

	function page_departments($page){
		$m = $this->add('xavoc\securityservices\Model_ClientDepartment')->addCondition('client_id',$this->id);
		$c = $page->add('xepan\base\CRUD');
		$c->setModel($m);
	}
}