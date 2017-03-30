<?php

namespace xavoc\securityservices;


class page_monthrecords extends \xepan\base\Page {
	
	public $title ="Client Records";

	function init(){
		parent::init();

		$c = $this->add('xepan\hr\CRUD');
		$c->setModel('xavoc\securityservices\ClientMonthYear');

		$c->grid->removeColumn('status');
		$c->grid->removeColumn('created_by');
		$c->grid->removeColumn('attachment_icon');

	}
}