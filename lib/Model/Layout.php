<?php

namespace xavoc\securityservices;

class Model_Layout extends \xepan\base\Model_Table{ 
	
	public $table = "secserv_layout";
	public $acl_type="Configuration";
	// public $acl="xavoc\securityservices\Model_ClientMonthYear";

	function init(){
		parent::init();

		$this->addField('name');
		$this->addField('master')->type('text')->display(['form'=>'xepan\base\RichText'])->hint('{$client_name}, {$client_address}, {$client_email}, {$client_contact}, {$client_tin_no}, {$invoice_no}, {$invoice_date}, {$month_year}, {$service_tax}, {$service_tax_amount}, {$net_amount}, {$net_amount_in_words}');
		$this->addField('detail')->type('text')->display(['form'=>'xepan\base\RichText'])->hint('{$billing_service}, {$units}, {$rate}, {$amount}');
				
	}
}