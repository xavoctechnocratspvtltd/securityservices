<?php

namespace xavoc\securityservices;


class Model_ClientLabour extends \xavoc\securityservices\Model_Labour{
	public $client_month_year_id = 0;
	public $client_id = 0;
	public $department_id = 0;
	
	function init(){
		parent::init();

		$this->addCondition('default_client_id',$this->client_id);
		if($this->department_id)
			$this->addCondition('default_client_department_id',$this->department_id);

		$this->addExpression('client_month_units_work')->set(function($m,$q){
			if(!$this->client_month_year_id)
				return "'0'";
			$model = $m->add('xavoc\securityservices\Model_Attendance')
					->addCondition('client_month_year_id',$this->client_month_year_id)
					->addCondition('labour_id',$m->getElement('id'));
			return $q->expr('IFNULL([0],0)',[$model->sum('units_work')]);
		})->type('Number');

		// $this->setOrder('name','asc');
	}
}