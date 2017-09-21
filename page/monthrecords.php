<?php

namespace xavoc\securityservices;


class page_monthrecords extends \xepan\base\Page {
	
	public $title ="Client Records";

	function init(){
		parent::init();

		$month_year = date('Y-m-01',strtotime($this->app->today));
		if($this->app->stickyGET('month_year'))
			$month_year = $_GET['month_year'];

		$form = $this->add('Form');
		$form->add('xepan\base\Controller_FLC')
				->addContentSpot()
				->makePanelsCoppalsible()
				->layout([
					'month_year'=>'Filter~c1~6~closed',
					'FormButtons'=>'c2~6',
				]);

		$month_year_field = $form->addField('DatePicker','month_year')->validate('required')->set($month_year);
		$month_year_field->options = [
								'format'=> "yyyy-mm",
			    				'startView'=> "months", 
			    				'minViewMode'=> "months"
			    			];
		
		$form->addSubmit('Filter')->addClass('btn btn-primary');

		$c = $this->add('xepan\hr\CRUD');

		if($form->isSubmitted()){
			$form->js(null,$c->js()->reload(['month_year'=>$form['month_year']]))->execute();
		}

		$model = $this->add('xavoc\securityservices\Model_ClientMonthYear');
		$model->addCondition('month_year',$month_year);
		$model->setOrder('id','desc');

		$c->setModel($model,['client_id','name','invoice_no','invoice_date','month_year','gross_amount','service_tax','service_tax_amount','net_amount'],['client','name','invoice_no','invoice_date','month_year','gross_amount','service_tax_amount','net_amount','status']);

		if($c->isEditing()){
			$form = $c->form;
			$field_month_year = $form->getElement('month_year');
			$field_month_year->options = [
								'format'=> "yyyy-mm",
			    				'startView'=> "months", 
			    				'minViewMode'=> "months"
			    			];
		}

		$c->grid->removeColumn('status');
		$c->grid->removeColumn('created_by');
		$c->grid->removeColumn('attachment_icon');
		$c->js(true)->find('.main-box-body > div.table')->removeClass('table-responsive');
	}
}