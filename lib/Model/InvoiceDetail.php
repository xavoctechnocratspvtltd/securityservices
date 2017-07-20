<?php

namespace xavoc\securityservices;


class Model_InvoiceDetail extends \xepan\base\Model_Table{ 
	
	public $table = "secserv_client_monthyear_invoice_detail";
	public $acl="xavoc\securityservices\Model_ClientMonthYear";

	function init(){
		parent::init();

		$this->hasOne('xavoc\securityservices\ClientMonthYear','client_month_year_id');
		$this->hasOne('xavoc\securityservices\BillingService','billing_service_id');
		$this->addField('units');
		$this->addField('rate');
		
		$this->addField('amount');
		$this->addHook('beforeSave',$this);
	}

	function beforeSave(){
		$this['amount'] = $this['units'] * $this['rate'];
	}

}