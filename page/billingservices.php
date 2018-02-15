<?php

namespace xavoc\securityservices;


class page_billingservices extends \xepan\base\Page {
	
	public $title ="Company Services (For Billing)";

	function init(){
		parent::init();

		$c = $this->add('xepan\hr\CRUD');
		$c->setModel('xavoc\securityservices\BillingService',['name']);
		$c->grid->addQuickSearch(['name']);
		$c->grid->addPaginator($ipp=50);

		// $c->grid->removeColumn('action');
		$c->grid->removeColumn('attachment_icon');
	}
}