<?php

namespace xavoc\securityservices;

class Model_PL extends \xepan\base\Model_Table{ 
	
	public $table = "secserv_pl";
	public $acl_type="xavoc\securityservices\Model_ClientMonthYear";

	function init(){
		parent::init();

		$this->hasOne('xavoc\securityservices\ClientMonthYear','client_month_year_id');
		$this->hasOne('xavoc\securityservices\Model_BillingService','client_billing_service_id');
		$this->hasOne('xavoc\securityservices\Labour','labour_id');
		
		for($i=1; $i <= 31; $i++){
			$this->addField('d'.$i)->defaultValue('L');
		}

		$this->addField('total_present')->type('int');

		$this->add('dynamic_model/Controller_AutoCreator');
	}
}