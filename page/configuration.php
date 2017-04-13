<?php

namespace xavoc\securityservices;


class page_configuration extends \xepan\base\Page {
	
	public $title ="Configuration";

	function init(){
		parent::init();


		$layout_model = $this->add('xavoc\securityservices\Model_Layout');
		$crud = $this->add('xepan\hr\CRUD');
		$crud->setModel($layout_model);
	}
}