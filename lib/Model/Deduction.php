<?php

namespace xavoc\securityservices;


/**
* 
*/
class Model_Deduction extends \xepan\base\Model_Table{
	public $table = "secserv_deduction";
	public $acl="xavoc\securityservices\Model_ClientMonthYear";
	function init(){
		parent::init();

		$this->hasOne('xavoc\securityservices\ClientMonthYear','client_month_year_id');
		$this->hasOne('xavoc\securityservices\Labour','labour_id');

		$this->addField('pf');
		$this->addField('uniform_deduction');
		$this->addField('fine');
		$this->addField('advance');
		$this->addField('alowances');

		$this->add('dynamic_model/Controller_AutoCreator');
	}
}