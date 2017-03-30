<?php

namespace xavoc\securityservices;


class Controller_ACLFields extends \AbstractController {

	function init(){
		parent::init();

		$this->owner->hasOne('xepan\hr\Employee','created_by_id')->defaultValue($this->app->employee->id)->system(true);
		$this->owner->addField('created_at')->type('datetime')->defaultValue($this->app->now)->system(true);

	}
}