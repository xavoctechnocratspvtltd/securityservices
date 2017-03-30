<?php

namespace xavoc\securityservices;


class page_invoices extends \xepan\base\Page {
	
	public $title ="Client Invoices";

	function init(){
		parent::init();

		$c = $this->add('xepan\hr\CRUD');
		$c->setModel('xavoc\securityservices\ClientMonthYear');

	}
}