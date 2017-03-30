<?php

namespace xavoc\securityservices;


class page_monthrecords extends \xepan\base\Page {
	
	public $title ="Client Records";

	function init(){
		parent::init();

		$c = $this->add('xepan\hr\CRUD');
		$c->setModel('xavoc\securityservices\ClientMonthYear');

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

	}
}