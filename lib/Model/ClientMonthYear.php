<?php

namespace xavoc\securityservices;


class Model_ClientMonthYear extends \xepan\base\Model_Table{ 
	
	public $table = "secserv_client_monthyear_record";
	
	public $acl_type="Record Generate";

	public $status=['All'];

	public $actions=['All'=>['view','edit','delete','manage_attandance']];

	function init(){
		parent::init();

		$this->hasOne('xavoc\securityservices\Client','client_id');
		$this->addField('name')->caption('Month Year');

		$this->add('misc/Field_Callback','status')->set(function($m){
			return "All";
		});

		$this->add('xavoc\securityservices\Controller_ACLFields');
		
	}
}