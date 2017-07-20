<?php

namespace xavoc\securityservices;


class page_payments extends \xepan\base\Page {
	
	public $title ="Labour Payments";

	function init(){
		parent::init();

		$this->app->stickyGET('month_year');
    	$client_id = $this->app->stickyGET('client_id');
    	$client_service_id = $this->app->stickyGET('client_service_id');

		$form = $this->add('Form')->addClass('row');

		$m_y_picker = $form->addField('DatePicker','month_year');
		$client_field = $form->addField('Dropdown','client')->setEmptyText('Please Select');
		$client_field->setModel('xavoc\securityservices\Client');
		$client_serv_field = $form->addField('Dropdown','client_services')->setEmptyText('Please Select');
		$m_y_picker->options = [
					'format'=> "yyyy-mm",
    				'startView'=> "months", 
    				'minViewMode'=> "months"
    			];
    			
    	$serv_m = $this->add('xavoc\securityservices\Model_ClientService');
    	if($client_id)
    		$serv_m->addCondition('client_id',$client_id);
    	$client_serv_field->setModel($serv_m);
    	$client_field->js('change',$form->js()->atk4_form('reloadField','client_services',[$this->app->url(),'client_id'=>$client_field->js()->val()]));

		$generate_btn = $form->addSubmit('Generate Payment')->addClass('btn btn-success');
		$delete_btn = $form->addSubmit('Delete Payment')->addClass('btn btn-warning');
		
		$crud = $this->add('CRUD',['allow_add'=>false,'allow_del'=>false]);

		$salary_model = $this->add('xavoc\securityservices\Model_Payment');
		$month_year = date('Y-m-01',strtotime($this->app->today));

		if($_GET['month_year']) $month_year = $_GET['month_year'];

		$salary_model->addCondition('date',$month_year);
		
		if($client_id){
			$salary_model->addCondition('client_id',$client_id);
		}	
		if($client_service_id){
			$salary_model->addCondition('client_service_id',$client_service_id);
		}
			
		
		$crud->setModel($salary_model);

		$crud->grid->addQuickSearch(['labour']);

		if($form->isSubmitted()){

			if(!$form['month_year']) $form->error('month_year','required field');

			// delete button
			if($form->isClicked($delete_btn)){
				$payment_model = $this->add('xavoc\securityservices\Model_Payment');
				$payment_model->addCondition('date',$form['month_year']);
				if($form['client'])
					$payment_model->addCondition('client_id',$form['client']);
				if($form['client_services'])
					$payment_model->addCondition('client_service_id',$form['client_service_id']);
				$count = $payment_model->count()->getOne();
				$payment_model->deleteAll();
				$form->js(null,$crud->js()->reload(['month_year'=>$form['month_year']]))->univ()->errorMessage($count.' records deleted')->execute();
			}

			// generate button
			if($form->isClicked($generate_btn)){
				$payment_model = $this->add('xavoc\securityservices\Model_Payment');
				$payment_model->addCondition('date',$form['month_year']);
				if($form['client'])
					$payment_model->addCondition('client_id',$form['client']);
				if($form['client_services'])
					$payment_model->addCondition('client_service_id',$form['client_services']);
				if($payment_model->count()->getOne()){
					$form->js(null,$crud->js()->reload(['month_year'=>$form['month_year'],'client_id'=>$form['client'],'client_service_id'=>$form['client_services']]))->univ()->errorMessage($form['month_year']." payment is generated previously, if you want again then first delete payment and Generate Payment Again")->execute();
				}
				
				$cmy_model = $this->add('xavoc\securityservices\Model_ClientMonthYear');
				$cmy_model->addCondition('month_year',$form['month_year']);
				
				// for all month year record for all labours
				foreach ($cmy_model as $key => $cmy) {

					$client_services = $this->add('xavoc\securityservices\Model_ClientService');
					$client_services->addCondition('client_id',$cmy['client_id']);
					
					foreach ($client_services as $cs) {

						$labour_payments = $this->add('xavoc\securityservices\Model_LabourPaymant',['client_month_year_record_id'=>$cmy->id,'client_service_id'=>$cs->id]);
												
						foreach ($labour_payments as $key => $monthly_payment) {
							$payment = $this->add('xavoc\securityservices\Model_Payment');
							$payment->addCondition('labour_id',$monthly_payment['id']);
							$payment->addCondition('client_month_year_id',$cmy->id);
							$payment->addCondition('client_service_id',$cs->id);

							$payment->tryLoadAny();
							$pf_amount = 0;

							$total_unit_work = $monthly_payment['total_unit_work'];
							$payment_rate = $monthly_payment['payment_rate'];

							$days_of_month = date('t',strtotime($form['month_year']));
							$gross_amount = 0;

							if($monthly_payment['payment_base'] == "Month"){
								$per_day_salary = $payment_rate / $days_of_month ;
								$gross_amount = $per_day_salary * ($total_unit_work / $monthly_payment['labour_shift_hours']);
							}elseif ($monthly_payment['payment_base'] == "Shift") {
								$gross_amount = $payment_rate * ($total_unit_work / $monthly_payment['labour_shift_hours']);
							}else{
								$gross_amount = $total_unit_work * $payment_rate;
							}

							if($monthly_payment['is_pf_deduction']){
								// 12 % of gross amount
								$pf_amount = (12 /100) * $gross_amount;
								// $gross_amount = $gross_amount - $pf_amount;
							}
							$payment['pf_amount'] = $pf_amount;
							$payment['gross_amount'] = $gross_amount;
							$payment['total_unit_work'] = $total_unit_work;
							$payment['payment_base'] = $monthly_payment['payment_base'];
							$payment['payment_rate'] = $payment_rate;
							$payment->save();
						}
					}
				}
				$form->js(null,$crud->js()->reload(['month_year'=>$form['month_year'],'client_id'=>$form['client'],'client_service_id'=>$form['client_services']]))->univ()->successMessage('Payment Generated')->execute();
			}
		}

	}
}