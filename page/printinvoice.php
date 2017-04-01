<?php

namespace xavoc\securityservices;


class page_printinvoice extends \Page {

	function init(){
		parent::init();

		$c_month_year_id = $_GET['record_id'];

		$invoice = $this->add('xavoc\securityservices\Model_InvoiceDetail');
		$invoice->addCondition('client_month_year_id',$c_month_year_id);
		$invoice->tryLoadAny();

		if(!$invoice->loaded())
			throw new \Exception("invoice not found");

		$cl = $this->add('completeLister',null,null,['view\invoice']);
		$cl->setModel($invoice);
	}
}