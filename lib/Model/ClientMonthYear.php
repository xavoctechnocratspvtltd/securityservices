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
		$this->addField('name');
		
		$this->addField('month_year')->type('date');

		$this->add('misc/Field_Callback','status')->set(function($m){
			return "All";
		});

		$this->add('xavoc\securityservices\Controller_ACLFields');
		
		$this->hasMany('xavoc\securityservices\Attendance','client_month_year_id');
		$this->hasMany('xavoc\securityservices\ApprovalSheet','client_month_year_id');

		$this->is([
				'month_year|to_trim|required'
			]);
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

	function additionalLabour(){
		if(!$this->loaded()) throw new \Exception("model client month year must loaded");
				
		// used only for getting labour that not have same default_labour_id 
		$attendance_model = $this->add('xavoc\securityservices\Model_Attendance');
		$attendance_model->addExpression('client_id')->set(function($m,$q){
			return  $q->expr('IFNULL([0],0)',[$m->refSQL('client_month_year_id')->fieldQuery('client_id')]);
		});

		$attendance_model->addExpression('labour_default_client_id')->set(function($m,$q){
			return $q->expr('IFNULL([0],0)',[$m->refSQL('labour_id')->fieldQuery('default_client_id')]);
		});
		
		$attendance_model->addExpression('is_default_client')->set(function($m,$q){
			return $q->expr('IF(([0]=[1]),1,0)',[$m->getElement('client_id'),$m->getElement('labour_default_client_id')]);
		})->type('boolean');

		$attendance_model->addCondition('client_month_year_id',$this->id);
		$attendance_model->addCondition('is_default_client',false);
		$attendance_model->setOrder('labour','asc');	
		
		return $attendance_model;		
	}
}