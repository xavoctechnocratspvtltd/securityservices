<?php

namespace xavoc\securityservices;


class Model_ClientMonthYear extends \xepan\base\Model_Table{ 
	
	public $table = "secserv_client_monthyear_record";
	
	public $acl_type="Record Generate";

	public $status=['All'];

	public $actions=['All'=>['view','edit','delete','manage_attendance','generate_approval_sheet','approved_service_data','generate_invoice','print_invoice','print_invoice_attachment']];

	function init(){
		parent::init();

		$this->hasOne('xavoc\securityservices\Client','client_id');
		$this->addField('name')->caption('Month Year');

		$this->add('misc/Field_Callback','status')->set(function($m){
			return "All";
		});

		$this->add('xavoc\securityservices\Controller_ACLFields');
		
		$this->hasMany('xavoc\securityservices\Attendance','client_month_year_id');
		$this->hasMany('xavoc\securityservices\ApprovalSheet','client_month_year_id');

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
		$c = $page->add('xepan\base\CRUD');
		$m=$this->add('xavoc\securityservices\Model_ClientMonthYearApprovedData');
		$m->addCondition('client_month_year_id', $this->id);
		$c->setModel($m);
	}

	function page_generate_invoice($page){
		

		$tabs = $page->add('Tabs');
		$invoice_tab = $tabs->addTab('Invoice Data');
		$approved_tab = $tabs->addTab('Approved Units');

		$c = $invoice_tab->add('xepan\base\CRUD');
		
		$recalc_btn = $c->addButton('Re Calculate')->addClass('btn btn-primary');
		$recalc_btn->on('click',function($js,$data)use($c){
			$this->calculateInvoiceData();
			return $c->grid->js()->reload();
		});

		$m=$this->add('xavoc\securityservices\Model_InvoiceDetail');
		$m->addCondition('client_month_year_id', $this->id);
		$c->setModel($m);

		$g = $approved_tab->add('xepan\base\Grid');
		$m=$this->add('xavoc\securityservices\Model_ClientMonthYearApprovedData');
		$m->addCondition('client_month_year_id', $this->id);
		$g->setModel($m);

		$page->add('View')->set('DO CALCULATION HERE IF NO RECORD IS GENERATED');
	}

	function calculateInvoiceData(){
		throw new \Exception("Error Processing Request", 1);
		
	}

	function print_invoice(){
		throw new \Exception("TODO", 1);
		
	}

	function print_invoice_attachment(){
		throw new \Exception("TODO", 1);

	}
}