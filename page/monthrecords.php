<?php

namespace xavoc\securityservices;


class page_monthrecords extends \xepan\base\Page {
	
	public $title ="Client Records";

	function init(){
		parent::init();

		$c = $this->add('xepan\hr\CRUD');
		$model = $this->add('xavoc\securityservices\Model_ClientMonthYear');
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