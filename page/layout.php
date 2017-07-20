<?php

namespace xavoc\securityservices;


class page_layout extends \xepan\base\Page {
	
	public $title ="Layout";

	function init(){
		parent::init();

		$this->app->stickyGET('id');
		if(!$_GET['id']){
			throw new \Exception("id not found");
		}

		$layout = $this->add('xavoc\securityservices\Model_Layout');
		$layout->load($_GET['id']);

		$form = $this->add('Form');
		$form->addField('line','name')->set($layout['name']);
		$form->addField('xepan\base\RichText','master')->set($layout['master']);
		$form->addField('xepan\base\RichText','detail')->set($layout['detail']);
		$form->addSubmit('Save');

		if($form->isSubmitted()){
			$layout['master'] = $form['master'];
			$layout['detail'] = $form['detail'];
			$layout->save();

			$form->js()->univ()->redirect($this->app->url(null,['id'=>$layout->id]))->successMessage('Saved')->execute();
		}

	}
}