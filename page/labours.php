<?php

namespace xavoc\securityservices;


class page_labours extends \xepan\base\Page {
	
	public $title ="Labours";

	function page_index(){
		// parent::init();

		$c = $this->add('xepan\hr\CRUD');
		$c->setModel('xavoc\securityservices\Labour',
				['name','address','dob','gender','mobile_no','email_id','guardian_name','bank_name','bank_account_no','bank_ifsc_code','bank_branch','default_client_id','default_client_service_id','default_client_department_id','labour_personal_shift_hours','is_active','is_pf_deduction'],
				['name','labour_personal_shift_hours','mobile_no','bank_name','bank_account_no','bank_ifsc_code','bank_branch','labour_personal_shift_hours','is_active','default_client','default_client_service','default_client_department','is_pf_deduction']
			);

		$c->grid->addQuickSearch(['name','bank_account_no','mobile_no','bank_name','bank_ifsc_code']);
		$c->grid->addPaginator($ipp=50);
		$c->grid->removeColumn('status');

		$c->grid->removeColumn('created_by');
		$c->grid->removeColumn('action');
		$c->grid->removeColumn('attachment_icon');
		$c->grid->addPaginator($ipp=50);
		$this->app->stickyGET('client_id');

		if($c->isEditing()){
			$form = $c->form;
			$field_client = $form->getElement('default_client_id');
			$field_service = $form->getElement('default_client_service_id');
			$field_department = $form->getElement('default_client_department_id');
			
			if($_GET['d_client_id'])
				$field_service->getModel()->addCondition('client_id',$_GET['d_client_id']);
			if($_GET['d_service_id'])
				$field_department->getModel()->addCondition('default_client_service_id',$_GET['d_service_id']);
			
			$field_client->js('change',$form->js()->atk4_form('reloadField','default_client_service_id',[$this->app->url(),'d_client_id'=>$field_client->js()->val()]));
			$field_service->js('change',$form->js()->atk4_form('reloadField','default_client_department_id',[$this->app->url(),'d_service_id'=>$field_service->js()->val()]));
			
		}

		/**			
		CSV Importer
		*/
		$import_btn=$c->grid->addButton('Import CSV')->addClass('btn btn-primary');
		$import_btn->setIcon('ui-icon-arrowthick-1-n');
		$import_btn->js('click')
			->univ()
			->frameURL(
					'Import CSV',
					$this->app->url('./import')
					);
	}

	function page_import(){

		$form = $this->add('Form');
		$form->addSubmit('Download Sample File');
		
		if($_GET['download_sample_csv_file']){
			$output = ['name','labour_personal_shift_hours','address','dob','gender','mobile_no','email_id','guardian_name','bank_name','bank_account_no','bank_ifsc_code','bank_branch','is_active','default_client','default_client_service','default_client_department'];

			$output = implode(",", $output);
	    	header("Content-type: text/csv");
	        header("Content-disposition: attachment; filename=\"sample_labour_import.csv\"");
	        header("Content-Length: " . strlen($output));
	        header("Content-Transfer-Encoding: binary");
	        print $output;
	        exit;
		}

		if($form->isSubmitted()){
			$form->js()->univ()->newWindow($form->app->url('xavoc_secserv_labours_import',['download_sample_csv_file'=>true]))->execute();
		}

		$this->add('View')->setElement('iframe')->setAttr('src',$this->api->url('./execute',array('cut_page'=>1)))->setAttr('width','100%');
	}

	function page_import_execute(){

		ini_set('max_execution_time', 0);
		$form= $this->add('Form');
		$form->template->loadTemplateFromString("<form method='POST' action='".$this->api->url(null,array('cut_page'=>1))."' enctype='multipart/form-data'>
			<input type='file' name='csv_labour_file'/>
			<input type='submit' value='Upload'/>
			</form>"
			);

		if($_FILES['csv_labour_file']){
			if ( $_FILES["csv_labour_file"]["error"] > 0 ) {
				$this->add( 'View_Error' )->set( "Error: " . $_FILES["csv_labour_file"]["error"] );
			}else{
				$mimes = ['text/comma-separated-values', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.ms-excel', 'application/vnd.msexcel', 'text/anytext'];
				if(!in_array($_FILES['csv_labour_file']['type'],$mimes)){
					$this->add('View_Error')->set('Only CSV Files allowed');
					return;
				}

				$importer = new \xepan\base\CSVImporter($_FILES['csv_labour_file']['tmp_name'],true,',');
				$data = $importer->get();
				$lead = $this->add('xavoc\securityservices\Model_Labour');
				$old_record_count = $lead->count()->getOne();
				try{
					$this->api->db->beginTransaction();
					
					$lead->importFromCSV($data);
					
					$this->api->db->commit();
					
					$new_record_count = $lead->count()->getOne();
					$this->add('View_Info')->set('Total Records : '.($new_record_count - $old_record_count));
				}catch(\Exception $e){
					$this->api->db->rollback();

					$this->add('View_Error')->set($e->getMessage());
				}

			}
		}
	}
}