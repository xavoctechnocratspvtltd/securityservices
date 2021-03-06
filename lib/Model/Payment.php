<?php

namespace xavoc\securityservices;

class Model_Payment extends \xepan\base\Model_Table{
	public $table = "secserv_payment";
	public $acl_type="secserv_payment";

	function init(){
		parent::init();

		$this->hasOne('xavoc\securityservices\Labour','labour_id');
		$this->hasOne('xavoc\securityservices\ClientMonthYear','client_month_year_id');
		// $this->hasOne('xavoc\securityservices\ClientDepartment','client_department_id');
		$this->hasOne('xavoc\securityservices\ClientService','client_service_id');
		
		$this->addExpression('date')->set(function($m,$q){
			return $q->expr('IFNULL([0],0)',[$m->refSql('client_month_year_id')->fieldQuery('month_year')]);
		});

		$this->addExpression('client_id')->set($this->refSQL('client_month_year_id')->fieldQuery('client_id'));

		$this->addField('total_unit_work');
		$this->addField('payment_base');
		$this->addField('payment_rate');
		
		$this->addField('gross_amount')->defaultValue(0);
		$this->addField('pf_amount')->defaultValue(0);
		$this->addField('uniform_deduction')->defaultValue(0);
		$this->addField('other_deduction')->defaultValue(0);
		$this->addField('fine')->defaultValue(0);
		$this->addField('advance')->defaultValue(0);
		$this->addField('allowances')->defaultValue(0);
		$this->addField('net_payable')->defaultValue(0);
		$this->addField('remark')->type('text');
		
		$this->addHook('beforeSave',$this);

		$this->add('dynamic_model/Controller_AutoCreator');
	}

	function beforeSave(){
		$this['gross_amount'] = round($this['gross_amount']);
		$this['pf_amount'] = round($this['pf_amount']);

		$net_payable = $this['gross_amount'];
		if($this['pf_amount'] > 0)
			$net_payable -= $this['pf_amount'];
		if($this['uniform_deduction'] > 0)
			$net_payable -= $this['uniform_deduction'];
		if($this['other_deduction'] > 0)
			$net_payable -= $this['other_deduction'];
		if($this['fine'] > 0)
			$net_payable -= $this['fine'];
		if($this['advance'] > 0)
			$net_payable -= $this['advance'];
		if($this['allowances'] > 0)
			$net_payable += $this['allowances'];
		
		$this['net_payable'] = round($net_payable);
	}
}