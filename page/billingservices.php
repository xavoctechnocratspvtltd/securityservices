<?php

namespace xavoc\securityservices;


class page_billingservices extends \xepan\base\Page {
	
	public $title ="Company Services (For Billing)";

	function init(){
		parent::init();

		$c = $this->add('xepan\hr\CRUD');
		$c->setModel('xavoc\securityservices\BillingService');

	}
}