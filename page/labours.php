<?php

namespace xavoc\securityservices;


class page_labours extends \xepan\base\Page {
	
	public $title ="Labours";

	function init(){
		parent::init();

		$c = $this->add('xepan\hr\CRUD');
		$c->setModel('xavoc\securityservices\Labour');

		$c->grid->removeColumn('created_by');
		$c->grid->removeColumn('action');
		$c->grid->removeColumn('attachment_icon');
		
		$this->app->stickyGET('client_id');

		// if($c->isEditing()){

		// 	$form = $c->form;
		// 	$field_client = $form->getElement('default_client_id');
		// 	$field_service = $form->getElement('default_client_service_id');
		// 	$field_department = $form->getElement('default_client_department_id');
		// 	// if($_GET['client_id']){
		// 	$field_service->getModel()->addCondition('client_id',$_GET['client_id']);
		// 	// }
		// 	$field_client->js('change',$field_service->js()->reload(null,null,[$this->app->url(null,['cut_object'=>$field_service->name]),'selected_client_id'=>$field_client->js()->val()]));
		// 	// $field_client->js('change',$form->js()->atk4_form('reloadField',$field_department->name,[$this->app->url(null,['cut_object'=>$field_department->name]),'client_id'=>$field_client->js()->val()]));
		// }

	}
}