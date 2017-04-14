<?php

namespace xavoc\securityservices;


class Model_Attendance extends \xepan\base\Model_Table{ 
	public $table = "secserv_attendance";
	
	public $acl="xavoc\securityservices\Model_ClientMonthYear";	
	function init(){
		parent::init();

		$this->hasOne('xavoc\securityservices\Labour','labour_id');
		$this->hasOne('xavoc\securityservices\ClientMonthYear','client_month_year_id');
		$this->hasOne('xavoc\securityservices\ClientDepartment','client_department_id');
		$this->hasOne('xavoc\securityservices\ClientService','client_service_id');

		$this->addField('date')->type('datetime')->defaultValue($this->app->now);
		$this->addField('shift_units_work')->type('Number');
		$this->addField('overtime_units_work')->type('Number');
		$this->addField('units_work')->type('Number');

		$this->addExpression('day')->set(function($m,$q){
			return $q->expr('DAY([0])',[$m->getElement('date')]);
		});

		$this->addExpression('month')->set(function($m,$q){
			return $q->expr('MONTH([0])',[$m->getElement('date')]);
		});

		$this->addExpression('year')->set(function($m,$q){
			return $q->expr('YEAR([0])',[$m->getElement('date')]);
		});

		$this->addExpression('payment_rate')->set(function($m,$q){
			return $m->refSql('client_service_id')->fieldQuery('payment_rate');
		});

		$this->addExpression('payment_base')->set(function($m,$q){
			return $m->refSql('client_service_id')->fieldQuery('payment_base');
		});

		$this->addExpression('labour_shift_hours')->set(function($m,$q){
			return $m->refSql('client_service_id')->fieldQuery('labour_shift_hours');
		});

	}
}