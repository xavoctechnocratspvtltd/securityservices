<?php

namespace xavoc\securityservices;


class page_labours extends \xepan\base\Page {
	
	public $title ="Labours";

	function init(){
		parent::init();

		$c = $this->add('xepan\hr\CRUD');
		$c->setModel('xavoc\securityservices\Labour');

	}
}