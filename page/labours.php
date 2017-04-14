<?php

namespace xavoc\securityservices;


class page_labours extends \xepan\base\Page {
	
	public $title ="Labours";

	function init(){
		parent::init();

		$c = $this->add('xepan\hr\CRUD');
		$c->setModel('xavoc\securityservices\Labour');

		$c->grid->removeColumn('created_by');
		$c->grid->removeColumn('action');
		$c->grid->removeColumn('attachment_icon');
	}
}