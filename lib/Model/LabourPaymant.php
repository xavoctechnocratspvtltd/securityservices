<?php

namespace xavoc\securityservices;


class Model_LabourPaymant extends \xavoc\securityservices\Model_Labour{ 

	public $client_month_year_record_id = 0;
	public $client_service_id = 0;

	function init(){
		parent::init();

		$month_year = $this->add('xavoc\securityservices\Model_ClientMonthYear')->tryLoad($this->client_month_year_record_id)->get('month_year');

		$this->addExpression('total_unit_work')->set(function($m,$q){
			$attendance =  $m->refSQL('Attendance')
					->addCondition('client_month_year_id',$this->client_month_year_record_id)
					->addCondition('client_service_id',$this->client_service_id)
					;
			return $q->expr('(IFNULL([0],0))',[$attendance->sum('units_work')]);
			// return $q->expr('(IFNULL([0],0) + IFNULL(0,0))',[$attendance->sum('units_work'),$attendance->sum('overtime_units_work')]);
		});

		$this->addExpression('payment_rate')->set(function($m,$q){
			$attendance =  $m->add('xavoc\securityservices\Model_Attendance')
					->addCondition('labour_id',$m->getElement('id'))
					->addCondition('client_month_year_id',$this->client_month_year_record_id)
					->addCondition('client_service_id',$this->client_service_id)
					->setLimit(1);
			return $q->expr('IFNULL([0],0)',[$attendance->fieldQuery('payment_rate')]);
		});

		$this->addExpression('payment_base')->set(function($m,$q){
			$attendance =  $m->add('xavoc\securityservices\Model_Attendance')
					->addCondition('labour_id',$m->getElement('id'))
					->addCondition('client_month_year_id',$this->client_month_year_record_id)
					->addCondition('client_service_id',$this->client_service_id)
					->setLimit(1);
			return $q->expr('IFNULL([0],0)',[$attendance->fieldQuery('payment_base')]);
		});

		$this->addExpression('labour_shift_hours')->set(function($m,$q){
			$attendance =  $m->add('xavoc\securityservices\Model_Attendance')
					->addCondition('labour_id',$m->getElement('id'))
					->addCondition('client_month_year_id',$this->client_month_year_record_id)
					->addCondition('client_service_id',$this->client_service_id)
					->setLimit(1);
			return $q->expr('IFNULL([0],0)',[$attendance->fieldQuery('labour_shift_hours')]);
		});

		$this->add('misc/Field_Callback','days_of_month')->set(function($m)use($month_year){
			return date('t',strtotime($month_year));			
		});

		$this->add('misc/Field_Callback','net_payable')->set(function($m){
			$total_unit_work = $m['total_unit_work'];
			$payment_rate = $m['payment_rate'];

			if($m['payment_base'] == "Month"){
				$per_day_salary = $m['payment_rate'] / $m['days_of_month'];
				$net_payable = $per_day_salary * $total_unit_work;
			}elseif($m['payment_base'] == "Shift") {
				$net_payable = $payment_rate * ($total_unit_work / $m['labour_shift_hours']);
			}else{
				$net_payable = $total_unit_work * $payment_rate;
			}

			return $net_payable;
		});

		$this->addCondition('is_active',1);
		$this->addCondition('total_unit_work','>',0);
	}
}