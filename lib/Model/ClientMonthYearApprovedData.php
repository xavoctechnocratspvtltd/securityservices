<?php

namespace xavoc\securityservices;


class Model_ClientMonthYearApprovedData extends \xepan\base\Model_Table{ 
	
	public $table = "secserv_client_monthyear_approved_data";
	public $acl="xavoc\securityservices\Model_ClientMonthYear";

	function init(){
		parent::init();

		$this->hasOne('xavoc\securityservices\ClientMonthYear','client_month_year_id');
		// $this->hasOne('xavoc\securityservices\ClientDepartment','client_department_id');
		$this->hasOne('xavoc\securityservices\BillingService','client_service_id');

		$this->addField('units_approved')->hint('Approved Hours mostly');
	}

	function manage_attendance(){
		$this->app->redirect($this->app->url('xavoc_secserv_manageattendance',['client_monthyear_record_id'=>$this->id]));
	}

	function page_generate_approval_sheet($page){

		if($this->ref('xavoc\securityservices\Attendance')->count()->getOne() == 0){
			$page->add('View_Error')->set('Please Generate Attendance First');
			return;
		}
		
		if($this->ref('xavoc\securityservices\ApprovalSheet')->count()->getOne() == 0){
			$g = $page->add('xepan\hr\Grid');
			$m = $this->add('xavoc\securityservices\Model_GroupedAttendance');
			$m->addCondition('client_month_year_id',$this->id);

			// ==== Model Way ====

			// ==== DSQL WAY =====
			$g->setSource($m->_dsql());
			$g->addColumn('client_month_year_id');
			$g->addColumn('client_month_year');
			$g->addColumn('client_department_id');
			$g->addColumn('client_department');
			$g->addColumn('client_service_id');
			$g->addColumn('client_service');
			$g->addColumn('units_work_sum');

		}

		// $this->app->redirect($this->app->url('xavoc_secserv_generateapprovalsheet',['client_monthyear_record_id'=>$this->id]));
		
	}

	function page_approved_service_data($page){
		
	}
}