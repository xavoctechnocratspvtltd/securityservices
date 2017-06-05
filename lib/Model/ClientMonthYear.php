<?php

namespace xavoc\securityservices;


class Model_ClientMonthYear extends \xepan\base\Model_Table{ 
	
	public $table = "secserv_client_monthyear_record";
	
	public $acl_type="Record Generate";

	public $status=['All'];

	public $actions=['All'=>['view','edit','delete','manage_attendance','generate_approval_sheet','approved_service_data','generate_invoice','print_invoice','print_invoice_attachment','labour_payment','delete_Attandance','manage_deductions']];

	function init(){
		parent::init();

		$this->hasOne('xavoc\securityservices\Client','client_id');
		$this->addField('name');
		$this->addField('month_year')->type('date');
		
		$this->addField('invoice_no')->defaultValue($this->newNumber());
		$this->addField('invoice_date')->type('date')->defaultValue($this->app->today);

		$this->addField('service_tax')->type('number');
		// $this->addField('service_tax_amount')->type('money');

		$this->add('misc/Field_Callback','status')->set(function($m){
			return "All";
		});

		$this->add('xavoc\securityservices\Controller_ACLFields');
		
		$this->hasMany('xavoc\securityservices\Attendance','client_month_year_id');
		$this->hasMany('xavoc\securityservices\ApprovalSheet','client_month_year_id');
		$this->hasMany('xavoc\securityservices\InvoiceDetail','client_month_year_id');

		$this->addExpression('month')->set(function($m,$q){
			return $q->expr('MONTH([0])',[$m->getElement('month_year')]);
		});

		$this->addExpression('year')->set(function($m,$q){
			return $q->expr('YEAR([0])',[$m->getElement('month_year')]);
		});

		$this->addExpression('gross_amount')->set(function($m,$q){
			return $q->expr('IFNULL([0],0)',[$m->refSQL('xavoc\securityservices\InvoiceDetail')->sum('amount')]);
		});

		$this->addExpression('service_tax_amount')->set(function($m,$q){
			return $q->expr('(IFNULL([service_tax],0) * IFNULL([gross_amount],0)/100)',['service_tax'=>$m->getElement('service_tax'),'gross_amount'=>$m->getElement('gross_amount')]);
		})->type('money');

		$this->addExpression('net_amount')->set(function($m,$q){
			return $q->expr('([0]- IFNULL([1],0))',[$m->getElement('gross_amount'),$m->getElement('service_tax_amount')]);
		})->type('money');

		$this->addExpression('status')->set('"All"');
		$this->is([
				'client_id|to_trim|required',
				'name|to_trim|required',
				'invoice_no|to_trim|required',
				'invoice_date|to_trim|required',
				'month_year|to_trim|required'
			]);

		$this->addHook('beforeSave',$this);
	}

	function beforeSave(){
		$old_cmy = $this->add('xavoc\securityservices\Model_ClientMonthYear');
		$old_cmy->addCondition('client_id',$this['client_id']);
		$old_cmy->addCondition('month_year',$this['month_year']);
		
		if($this->loaded())
			$old_cmy->addCondition('id','<>',$this->id);
		$old_cmy->tryLoadAny();
		if($old_cmy->loaded()){
			throw $this->Exception("client month record is already added",'ValidityCheck')->setField('name');
		}
	}

	function manage_attendance(){
		$this->app->redirect($this->app->url('xavoc_secserv_manageattendance',['client_monthyear_record_id'=>$this->id]));
	}

	function page_generate_approval_sheet($page){

		if($this->ref('xavoc\securityservices\Attendance')->count()->getOne() == 0){
			$page->add('View_Error')->set('Please Generate Attendance First');
			return;
		}

		$view = $page->add('View')->addClass('main-box');
		$view->add('Button')->set('Click to Remove All Approval Sheet for Re-generate')->addClass('btn btn-primary')->on('click',function($js,$data){
			$model = $this->add('xavoc\securityservices\Model_ApprovalSheet');
			$model->addCondition('client_month_year_id',$this->id);
			$model->deleteAll();

			return $js->univ()->successMessage('please re-run action "Generate Approval sheet"')->closeDialog();
		});

		if($this->ref('xavoc\securityservices\ApprovalSheet')->count()->getOne() == 0){
			$m = $this->add('xavoc\securityservices\Model_GroupedAttendance');
			$m->addCondition('client_month_year_id',$this->id);
			
			$sum_array = [];

			foreach ($m->_dsql() as $d) {

				$day = (int)date('d',strtotime($d['date']));
				$myid = $d['client_month_year_id'];
				$dep = $d['client_department_id'];
				$service = $d['client_service_id'];
				$key = $myid.'_'.$dep.'_'.$service;
				$key_over = $key.'_over';

				if(!isset( $sum_data[$key])) $sum_data[$key] =[];
				if(!isset( $sum_data[$key_over])) $sum_data[$key_over] =[];

				$sum_data[$key]['name']=$d['client_month_year'];
				$sum_data[$key]['client_month_year_id']=$d['client_month_year_id'];
				$sum_data[$key]['client_department_id']=$d['client_department_id'];
				$sum_data[$key]['client_service_id']=$d['client_service_id'];
				$sum_data[$key]['is_overtime_record']=0;
				$sum_data[$key]['d'.$day] = $d['units_work_sum'];

				$sum_data[$key_over]['name']=$d['client_month_year'];
				$sum_data[$key_over]['client_month_year_id']=$d['client_month_year_id'];
				$sum_data[$key_over]['client_department_id']=$d['client_department_id'];
				$sum_data[$key_over]['client_service_id']=$d['client_service_id'];
				$sum_data[$key_over]['is_overtime_record']=1;
				$sum_data[$key_over]['d'.$day] = $d['overtime_units_work_sum'];
			}

			$this->add('xavoc\securityservices\Model_ApprovalSheet')
				->addCondition('client_month_year_id',$this->id)
				->deleteAll()
				;

			$this->app->db->dsql()->table('secserv_approval_sheet')->insertAll($sum_data);
		}

		$tabs = $page->add('Tabs');

		$billing_services = $this->add('xavoc\securityservices\Model_BillingService');

		$tabs_array=[];
		foreach ($billing_services as $bs) {
			$tab = $tabs->addTab($bs['name']);
		
			$model = $this->add('xavoc\securityservices\Model_ApprovalSheet');
			$model->addExpression('billing_service_id')->set($model->refSQL('client_service_id')->fieldQuery('billing_service_id'));
			$model->addCondition('client_month_year_id',$this->id);
			$model->addCondition('billing_service_id',$bs->id);

			if($model->count()->getOne() == 0){
				$tab->add('View_Error')->set('no record found');
			}else{
				$c = $tab->add('xepan\hr\CRUD');
				$c->setModel($model);
				$c->grid->removeColumn('action');
				$c->grid->removeColumn('attachment_icon');
				$c->grid->removeColumn('name');
				$export = $c->grid->addButton('Export CSV');
				$key = "export_csv_file_".$bs->id;

				$export->js('click')->univ()->newWindow($c->app->url(null,[$key=>true]));
				if($_GET[$key]){
					$rows = $model->getRows();
					
					$days_in_month = date('t',strtotime($this['month_year']));

					// final csv_row array
					$csv_rows = [];
					$csv_rows['total_hours'] = ['s_no'=>'','name'=>'total_hours'];
					$csv_rows['total_overtime'] = ['s_no'=>'','name'=>'total_overtime'];
					$csv_rows['total']= ['s_no'=>'','name'=>'total'];
					$header = ['s no','name'];
					for ($i=1; $i <=$days_in_month; $i++) { 
						$csv_rows['total_hours'][$i] = 0;	
						$csv_rows['total_overtime'][$i] = 0;	
						$csv_rows['total'][$i] = 0;	
						$header[]= $i;	
					}
					$header[] = 'total_hours';
					

					$s_no = 1;
					foreach ($rows as $key => $month_record) {
						$csv_rows[$key] = [
											's_no'=>$s_no,
											'name'=>$month_record['client_department']
										];

						$total_hours = 0;
						$is_overtime_record = $month_record['is_overtime_record'];

						foreach($month_record as $day => $unit_work) {
							if(!in_array($day, ['d1','d2','d3','d4','d5','d6','d7','d8','d9','d10','d11','d12','d13','d14','d15','d16','d17','d18','d19','d20','d21','d22','d23','d24','d25','d26','d27','d28','d29','d30','d31'])) continue;
							
							$day_number = str_replace("d", "", $day);
							if($day_number > $days_in_month) continue;

							$csv_rows[$key][$day_number] = $unit_work;
	
							// row total
							$total_hours += $unit_work;

							// day total
							if($is_overtime_record){
								$csv_rows['total_overtime'][$day_number] += $unit_work;
								$csv_rows['total_overtime']['total_hours'] += $csv_rows['total_overtime'][$day_number];
							}else{
								$csv_rows['total_hours'][$day_number] += $unit_work;
								$csv_rows['total_hours']['total_hours'] += $csv_rows['total_hours'][$day_number];
							}

							$csv_rows['total'][$day_number] += $unit_work;
						}

						$csv_rows[$key]['total_hours'] = $total_hours;
						$csv_rows['total']['total_hours'] += $total_hours;

						if($is_overtime_record){
							$csv_rows[$key]['s_no'] = "";
							$csv_rows[$key]['name'] = "overtime ".$month_record['client_department'];
						}else{
							$s_no ++;
						}
					}

					// move total row to at last row
					$temp = $csv_rows['total_hours'];
					unset($csv_rows['total_hours']);
					$csv_rows['total_hours'] = $temp;

					$temp = $csv_rows['total_overtime'];
					unset($csv_rows['total_overtime']);
					$csv_rows['total_overtime'] = $temp;
					
					$temp = $csv_rows['total'];
					unset($csv_rows['total']);
					$csv_rows['total'] = $temp;

					$output = implode(",", $header);
					$fp = fopen("php://output", "w");
					fputcsv ($fp, $header, ",");
					foreach($csv_rows as $key=>$row){
						fputcsv($fp, $row, ",");
					}
					fclose($fp);

	
					$file_name = $this['invoice_no']."_".$this['client']."_".$this['month']."_".$this['year']."_".str_replace(" ", "", $bs['name']).".csv";
					header("Pragma: public");
					header("Expires: 0");
					header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
					header("Cache-Control: private",false);
					header("Content-Type: application/octet-stream");
					header("Content-Disposition: attachment; filename=\"$file_name\";" );
					header("Content-Transfer-Encoding: binary"); 
			    	// header("Content-type: text/csv");
			    	// header("Content-type: text/comma-separated-values, text/csv, application/csv, application/excel, application/vnd.ms-excel, application/vnd.msexcel");
			     //    header('Content-disposition: attachment; filename="'+$file_name+'"');
			     //    // header("Content-Length: " . strlen($output));
			     //    header("Content-Transfer-Encoding: binary");
			        // print $output;
			        exit;
				}
			}
		}

		// $this->app->redirect($this->app->url('xavoc_secserv_generateapprovalsheet',['client_monthyear_record_id'=>$this->id]));
		
	}

	function page_approved_service_data($page){
		$c = $page->add('xepan\base\CRUD');
		$m=$this->add('xavoc\securityservices\Model_ClientMonthYearApprovedData');
		$m->addCondition('client_month_year_id', $this->id);
		$c->setModel($m);
	}

	function page_generate_invoice($page){

		$tabs = $page->add('Tabs');
		$invoice_tab = $tabs->addTab('Invoice Data');
		$approved_tab = $tabs->addTab('Approved Units');

		$form = $invoice_tab->add('Form')->addClass('main-box')->setStyle('padding','10px;');
		$form->add('View')->setElement('h3')->set('Invoice Information');

		$form->addField('invoice_no')->validate('required')->set($this['invoice_no']);
		$form->addField('DatePicker','invoice_date')->validate('required')->set($this['invoice_date']);
		$form->addSubmit('Save');

		if($form->isSubmitted()){
			$this['invoice_no'] = $form['invoice_no'];
			$this['invoice_date'] = $form['invoice_date'];
			$this->save();
			return $form->js(true,$form->js()->univ()->successMessage('Saved'))->reload();
		}

		$invoice_tab->add('View')->setElement('h3')->set('Invoice Item');
		$c = $invoice_tab->add('xepan\base\CRUD');
		$recalc_btn = $c->addButton('Re Calculate')->addClass('btn btn-primary');
		$recalc_btn->on('click',function($js,$data)use($c){
			$this->calculateInvoiceData();
			return $c->grid->js()->reload();
		});

		$m=$this->add('xavoc\securityservices\Model_InvoiceDetail');
		$m->addCondition('client_month_year_id', $this->id);
		$c->setModel($m);

		$g = $approved_tab->add('xepan\base\Grid');
		$m=$this->add('xavoc\securityservices\Model_ClientMonthYearApprovedData');
		$m->addCondition('client_month_year_id', $this->id);
		$g->setModel($m);

		$page->add('View')->set('DO CALCULATION HERE IF NO RECORD IS GENERATED');
	}

	function calculateInvoiceData(){
		throw new \Exception("Error Processing Request", 1);
		
	}

	function print_invoice(){
		$this->app->page_action_result = $this->app->js()->univ()->newWindow($this->app->url('xavoc_secserv_printinvoice',['record_id'=>$this->id,'cut_page'=>1]))->execute();
	}

	function print_invoice_attachment(){
		throw new \Exception("TODO", 1);

	}

	function additionalLabour($department_id=null){
		if(!$this->loaded()) throw new \Exception("model client month year must loaded");
		
		// used only for getting labour that not have same default_labour_id 
		$attendance_model = $this->add('xavoc\securityservices\Model_Attendance');
		$attendance_model->addExpression('client_id')->set(function($m,$q){
			return  $q->expr('IFNULL([0],0)',[$m->refSQL('client_month_year_id')->fieldQuery('client_id')]);
		});
		// $attendance_model->addExpression('labour_default_client_id')->set(function($m,$q){
		// 	return $q->expr('IFNULL([0],0)',[$m->refSQL('labour_id')->fieldQuery('default_client_id')]);
		// });
		
		// $attendance_model->addExpression('is_default_client')->set(function($m,$q){
		// 	return $q->expr('IF(([0]=[1]),1,0)',[$m->getElement('client_id'),$m->getElement('labour_default_client_id')]);
		// })->type('boolean');

		if($department_id)
			$attendance_model->addCondition('client_department_id',$department_id);

		$attendance_model->addCondition('month',$this['month']);
		$attendance_model->addCondition('year',$this['year']);
		$attendance_model->addCondition('client_month_year_id',$this->id);
		
		// commented bacause we want any labour can/may work in any department of client 
		// $attendance_model->addCondition('is_default_client',false);
		$attendance_model->_dsql()->group('labour_id');
		$attendance_model->setOrder('labour','asc');
		return $attendance_model;
	}

	function newNumber(){
		return $this->_dsql()->del('fields')->field('max(CAST(invoice_no AS decimal))')->getOne() + 1 ;
	}

	function page_labour_payment($page){
		$page->add('View')->set('Labour Payment sheet');


		$tabs = $page->add('Tabs');
		$client_services = $this->add('xavoc\securityservices\Model_ClientService');
		$client_services->addCondition('client_id',$this['client_id']);
		
		foreach ($client_services as $cs) {

			$tab = $tabs->addTab($cs['name']);

			$labour_payment = $this->add('xavoc\securityservices\Model_LabourPaymant',['client_month_year_record_id'=>$this->id,'client_service_id'=>$cs['id']]);
			$grid = $tab->add('xepan\hr\Grid');
			$grid->setModel($labour_payment,['name','total_unit_work','payment_rate','payment_base','days_of_month','labour_shift_hours','net_payable']);
			$grid->add('misc/Export');
			
			$grid->removeColumn('action');
			$grid->removeColumn('attachment_icon');
			$grid->removeColumn('days_of_month');
		}

	}

	function page_delete_Attandance($page){
		$dept_model = $page->add('xavoc\securityservices\Model_ClientDepartment');
		$dept_model->addCondition('client_id',$this['client_id']);
		
		$service_model = $this->add('xavoc\securityservices\Model_ClientService');	
		$service_model->addCondition('client_id',$this['client_id']);
		

		$form = $page->add('Form');
		$services_field = $form->addField('Dropdown','services')->setEmptyText('Please Select');
		$services_field->setModel($service_model);
		
		$dept_field = $form->addField('Dropdown','department')->setEmptyText('Please Select');
		if($_GET['client_services_id']){
			$dept_model->addCondition('default_client_service_id',$_GET['client_services_id']);
		}
		$dept_field->setModel($dept_model);
		$services_field->js('change',$form->js()->atk4_form('reloadField','department',[$this->app->url(),'client_services_id'=>$services_field->js()->val()]));
		$form->addSubmit('Delete Attandance');
		if($form->isSubmitted()){
			if(!$form['department'] AND !$form['services']){
				$form->displayError('services','Please Select at List on Options');
			}
			$attendance = $page->add('xavoc\securityservices\Model_Attendance');
			$attendance->addCondition('client_month_year_id',$this['id']);

			if($form['services'])
				$attendance->addCondition('client_service_id',$form['services']);
			if($form['department'])
				$attendance->addCondition('client_department_id',$form['department']);
			
			$remove_count = $attendance->count()->getOne();
			// throw new \Exception($attendance->count()->getOne(), 1);
			$attendance->deleteAll();
			$this->app->page_action_result = $form->js(null,$form->js()->univ()->successMessage($remove_count." ".'Remove Attandance SuccessFully'))->reload();		
		}
	}

	function page_manage_deductions($page){
		$deduction_m = $this->add('xavoc\securityservices\Model_Deduction')
						->addCondition('client_month_year_id',$this->id);
		$c = $page->add('xepan\hr\CRUD');
		$c->setModel($deduction_m);
	}
}