<?php

namespace xavoc\securityservices;


class page_clients extends \xepan\base\Page {
	
	public $title ="Clients";

	function init(){
		parent::init();

		$c = $this->add('xepan\hr\CRUD');
		$model = $this->add('xavoc\securityservices\Model_Client');
		$model->addHook('afterSave',function($m){
			$debtor = $m->add('xepan\accounts\Model_Group')->load("Sundry Debtor");
			$m->add('xepan\accounts\Model_Ledger')
					->createNewLedger(
									$m['name'],
									$debtor->id,
									$m->id,
										[
											'ledger_type'=>'Client',
											'LedgerDisplayName'=>$m['name'],
											'contact_id'=>$m->id
										]
									);
		});

		$c->setModel($model,
						['name','owner_name','billing_name','address','tin_no','mobile_no','email_id','service_tax','generate_mannual_invoice','invoice_layout','status','invoice_layout_id'],
						['name','tin_no','mobile_no','email_id','service_tax','invoice_layout','status']
					);

		$c->grid->addQuickSearch(['name','mobile_no','email_id']);
		$c->grid->addPaginator($ipp=50);
		$c->grid->removeColumn('status');
		$c->grid->removeColumn('attachment_icon');
	}
}