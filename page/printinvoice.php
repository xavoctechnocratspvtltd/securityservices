<?php

namespace xavoc\securityservices;


class page_printinvoice extends \Page {

	function init(){
		parent::init();

		$c_month_year_id = $_GET['record_id'];

		$month_year_model = $this->add('xavoc\securityservices\Model_ClientMonthYear');
		$month_year_model->addCondition('id',$c_month_year_id);
		$month_year_model->tryLoadAny();
		if(!$month_year_model->loaded()) throw new \Exception("record not found");

		$client_model = $this->add('xavoc\securityservices\Model_Client');
		$client_model->addCondition('id',$month_year_model['client_id']);
		$client_model->tryLoadAny();
		if(!$client_model->loaded()) throw new \Exception("record not found");

		$layout_m = $this->add('xavoc\securityservices\Model_Layout')->load($client_model['invoice_layout_id']);
		$master_layout = $this->add('GiTemplate');
		$master_layout->loadTemplateFromString($layout_m['master']);

		//  from client model
		$master_array['client_name'] = $client_model['name'];
		$master_array['billing_name'] = $client_model['billing_name'];
		$master_array['client_address'] = $client_model['address'];
		$master_array['client_email'] = $client_model['email_id'];
		$master_array['client_contact'] = $client_model['mobile_no'];
		$master_array['client_tin_no'] = $client_model['tin_no'];

		$master_array['invoice_no'] = $month_year_model['invoice_no'];
		$master_array['invoice_date'] = $month_year_model['invoice_date'];
		$master_array['month_year'] = $month_year_model['month_year'];
		$master_array['service_tax'] = $month_year_model['service_tax'];
		$master_array['service_tax_amount'] = $month_year_model['service_tax_amount'];
		$master_array['gross_amount'] = $month_year_model['gross_amount'];
		$master_array['net_amount'] = $month_year_model['net_amount'];

		$document = $this->add('xepan\base\Model_Document');
		$master_array['net_amount_in_words'] = $document->amountInWords($month_year_model['net_amount']);

		$view = $this->add('View',null,null,$master_layout);
		$view->template->setHtml($master_array);
		
		$invoice = $this->add('xavoc\securityservices\Model_InvoiceDetail');
		$invoice->addCondition('client_month_year_id',$c_month_year_id);
		$invoice->tryLoadAny();
		if(!$invoice->loaded())
			throw new \Exception("invoice not found");

		$detail_config = $layout_m['detail'];
		$detail_layout = $this->add('GiTemplate');
		$detail_layout->loadTemplateFromString($detail_config);	

		$cl = $view->add('completeLister',null,'item_info',$detail_layout);
		$cl->setModel($invoice);

		echo $view->getHtml();
		exit;

	}
}