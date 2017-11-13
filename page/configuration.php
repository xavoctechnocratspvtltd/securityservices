<?php

namespace xavoc\securityservices;


class page_configuration extends \xepan\base\Page {
	
	public $title ="Configuration";

	function init(){
		parent::init();


		$layout_model = $this->add('xavoc\securityservices\Model_Layout');
		$crud = $this->add('xepan\hr\CRUD',['allow_edit'=>false]);
		$crud->setModel($layout_model,['name']);

		$crud->grid->addColumn('edit_layout');
		$crud->grid->addHook('formatRow',function($g){
			$g->current_row_html['edit_layout'] = '<a class="btn btn-info" href="?page=xavoc_secserv_layout&id='.$g->model['id'].'">Edit</a>';
		});

		// $crud->grid->removeColumn('action');
		$crud->grid->removeColumn('attachment_icon');
	}
}