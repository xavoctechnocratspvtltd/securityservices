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

		$c->setModel($model);

	}
}