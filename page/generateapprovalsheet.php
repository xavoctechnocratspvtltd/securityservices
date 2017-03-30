<?php

namespace xavoc\securityservices;


class page_generateapprovalsheet extends \xepan\base\Page {
	
	public $title ="Approval Sheet";

	function init(){
		parent::init();

		$month_year_id = $this->app->stickyGET('client_monthyear_record_id');

		$month_year_model = $this->add('xavoc\securityservices\Model_ClientMonthYear');
		$month_year_model->addCondition('id',$month_year_id);
		$month_year_model->tryLoadAny();
		if(!$month_year_model->loaded())
			throw new \Exception("client month year not found", 1);
			
		$approval_model = $this->add('xavoc\securityservices\Model_ApprovalSheet');	
		$approval_model->addCondition('client_month_year_id',$month_year_id);

		$c = $this->add('xepan\hr\CRUD');
		$c->setModel($approval_model);
	}
}