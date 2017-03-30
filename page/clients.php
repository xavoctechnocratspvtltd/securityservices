<?php

namespace xavoc\securityservices;


class page_clients extends \xepan\base\Page {
	
	public $title ="Clients";

	function init(){
		parent::init();

		$c = $this->add('xepan\hr\CRUD');
		$c->setModel('xavoc\securityservices\Client');

	}
}