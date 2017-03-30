<?php

namespace xavoc\securityservices;


class Model_ClientMonthYear extends \xepan\base\Model_Table{ 
	
	public $table = "secserv_client_monthyear_record";
	public $acl_type="Record Generate";

	public $statue=['All'];

	public $actions=['All'=>['manege_attandance']];

	function init(){
		parent::init();

		$this->hasOne('xavoc\securityservices\Client','client_id');
		$this->addField('name')->caption('Month Year');

		$this->add('xavoc\securityservices\Controller_ACLFields');
		
	}
}