<?php

namespace xavoc\securityservices;


class Model_ClientMonthYear extends \xepan\base\Model_Table{ 
	
	public $table = "secserv_client_monthyear_record";
	
	public $acl_type="Record Generate";

	public $status=['All'];

	public $actions=['All'=>['view','edit','delete','manage_attendance','generate_approval_sheet','approved_service_data','generate_invoice','print_invoice','print_invoice_attachment','labour_payment','delete_Attandance','generate_PL']];

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
			return $q->expr('ROUND(IFNULL([0],0),2)',[$m->refSQL('xavoc\securityservices\InvoiceDetail')->sum('amount')]);
		});

		$this->addExpression('service_tax_amount')->set(function($m,$q){
			return $q->expr('ROUND((IFNULL([service_tax],0) * IFNULL([gross_amount],0)/100),2)',['service_tax'=>$m->getElement('service_tax'),'gross_amount'=>$m->getElement('gross_amount')]);
		})->type('money');

		$this->addExpression('net_amount')->set(function($m,$q){
			return $q->expr('CAST(ROUND(([0] + IFNULL([1],0)),0) AS DECIMAL(10,2))',[$m->getElement('gross_amount'),$m->getElement('service_tax_amount')]);
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

				$export->js('click')->univ()->newWindow($c->app->url(null,['service'=>$bs['name']]));
				if($_GET['service']===$bs['name']){

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
								$csv_rows['total_overtime']['total_hours'] += $unit_work;
							}else{
								$csv_rows['total_hours'][$day_number] += $unit_work;
								$csv_rows['total_hours']['total_hours'] += $unit_work;
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

					$file_name = $this['invoice_no']."_".$this['client']."_".$this['month']."_".$this['year']."_".str_replace(" ", "", $bs['name']).".csv";
					header("Pragma: public");
					header("Expires: 0");
					header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
					header("Cache-Control: private",false);
					header("Content-Type: application/octet-stream");
					header("Content-Disposition: attachment; filename=\"$file_name\";" );
					header("Content-Transfer-Encoding: binary");

					$output = implode(",", $header);
					$fp = fopen("php://output", "w");
					fputcsv ($fp, $header, ",");
					foreach($csv_rows as $key=>$row){
						fputcsv($fp, $row, ",");
					}
					fclose($fp);
	
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

	function page_generate_approval_sheet_csv(){
		$model = $this->add('xavoc\securityservices\Model_ApprovalSheet');
		$model->addExpression('billing_service_id')->set($model->refSQL('client_service_id')->fieldQuery('billing_service_id'));
		$model->addCondition('client_month_year_id',$this->id);
		$model->addCondition('billing_service_id',$bs->id);
	}

	function page_approved_service_data($page){
		$c = $page->add('xepan\base\CRUD');
		$m=$this->add('xavoc\securityservices\Model_ClientMonthYearApprovedData');
		$m->addCondition('client_month_year_id', $this->id);
		$c->setModel($m);
	}

	function page_generate_invoice($page){
		$this->app->stickyGET('service_id');

		$tabs = $page->add('Tabs');
		$invoice_tab = $tabs->addTab('Invoice Data');
		$approved_tab = $tabs->addTab('Approved Units');

		$form = $invoice_tab->add('Form');
		$form->add('View')->setElement('h3')->set('Invoice Information');
		$col = $form->add('Columns')->addClass('row');
		$col1 = $col->addColumn('4')->addClass('col-md-4 col-lg-4 col-sm-12 col-xs-12');
		$col2 = $col->addColumn('4')->addClass('col-md-4 col-lg-4 col-sm-12 col-xs-12');
		$col3 = $col->addColumn('4')->addClass('col-md-4 col-lg-4 col-sm-12 col-xs-12');

		$col1->addField('invoice_no')->validate('required')->set($this['invoice_no']);
		$col2->addField('DatePicker','invoice_date')->validate('required')->set($this['invoice_date']);		
		$col3->addSubmit('Save');

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

		$m = $this->add('xavoc\securityservices\Model_InvoiceDetail');
		$m->addCondition('client_month_year_id', $this->id);
		$c->setModel($m,['billing_service_id','units','rate'],['billing_service_id','units','rate','amount']);
		if($c->isEditing()){
			$form = $c->form;
			$bs_id_field = $form->getElement('billing_service_id');
			$rate_field = $form->getElement('rate');
			$bs_id_field->js('change',$form->js()->atk4_form('reloadField','rate',[$this->app->url(),'service_id'=>$bs_id_field->js()->val()]));
			
			if($_GET['service_id']){
				$cs = $this->add('xavoc\securityservices\Model_ClientService');
				$cs->addCondition('client_id',$this['client_id']);
				$cs->addCondition('billing_service_id',$_GET['service_id']);

				$cs->tryLoadAny();
				if($cs->loaded())
					$rate_field->set($cs['invoice_rate']);
				else
					$rate_field->set(0);
			}

		}
		// approved tabs
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

	function page_generate_PL($page){

		$view = $page->add('View')->addClass('main-box');
		$view->add('Button')->set('Click to Remove All PL for Re-generate')->addClass('btn btn-primary')->on('click',function($js,$data){
			$pl_model = $this->add('xavoc\securityservices\Model_PL');
			$pl_model->addCondition('client_month_year_id',$this->id);
			$pl_model->deleteAll();
			return $js->univ()->successMessage('please re-run action to "Generate PL"')->closeDialog();
		});

		$m = $page->add('xavoc\securityservices\Model_ClientMonthYearApprovedData');
		$m->addCondition('client_month_year_id', $this->id);

		$labour_array = [];
		$sheet_array = [];
		$days_in_month = date('t',strtotime($this['month_year']));

		for ($i=1; $i <= $days_in_month; $i++) {
			$sheet_array[$i] = 'L';
		}
		

		foreach ($m as $approved_data) {
			$billing_service_id = $approved_data['client_service_id'];
			if(!isset($labour_array[$billing_service_id])) $labour_array[$billing_service_id] = [];

			// $atten_m = $this->add('xavoc\securityservices\Model_Attendance');
			// $atten_m->addExpression('billing_service_id')->set(function($m,$q){
			// 	return $m->refSql('client_service_id')->fieldQuery('billing_service_id');
			// });

			// $atten_m->addCondition('client_month_year_id',$this->id);
			// $atten_m->addCondition('billing_service_id',$billing_service_id);
			
			$units_approved = $m['units_approved'];
			$client_shift_hour = 1;
			$duty_to_implement = $units_approved / $client_shift_hour;
			$duty_implemented = 0;
			$remaining_duty = 0;

			$last_rand_no = 0;
			$labour_used = [];

			// foreach($atten_m as $a) {

			// 	$labour_id = $a['labour_id'];
			// 	if(!isset($labour_array[$billing_service_id][$labour_id])) $labour_array[$billing_service_id][$labour_id] = $sheet_array;

			// 	$start_leave_day = rand(1,6);

			// 	if($last_rand_no == $start_leave_day){
			// 		$last_rand_no += 1;
			// 		if($last_rand_no > 6)
			// 			$start_leave_day = rand(1,5);
			// 	}

			// 	$last_rand_no = $start_leave_day;

			// 	if($duty_implemented >= $duty_to_implement){
			// 		break;
			// 	} 

			// 	$labour_used[$labour_id] = $labour_id;				
				
			// 	$week_count = 0;
			// 	for ($i=1; $i <= $days_in_month; $i++) {
			// 		if($duty_implemented >= $duty_to_implement){
			// 			echo $i;
			// 			continue;
			// 		}

			// 	// 	if($start_leave_day == $i OR ($start_leave_day + (6 * $week_count)) == $i){
			// 	// 		$labour_array[$billing_service_id][$labour_id][$i] = 'L';
			// 	// 		$week_count++;
			// 	// 	}else{
			// 	// 		$labour_array[$billing_service_id][$labour_id][$i] = 'P';
			// 	// 		$duty_implemented++;
			// 	// 	}
			// 	}
			// }

			// echo "<pre>";
			// print_r($labour_array);
			// echo "</pre>";
			// die();
			// $remaining_duty = $duty_to_implement - $duty_implemented;

			// if($remaining_duty <= 0) continue;

			$extra_labour = $this->add('xavoc\securityservices\Model_Labour');
			$extra_labour->addCondition('is_active',true);
			// $extra_labour->addCondition('id','<>',$labour_used);
			$extra_labour_count = $extra_labour->count()->getOne();

			/*
			* remaining (1000-874 = 126)
			  client_shift_hour = 
			  minimum_labour_required = remaining / day_in_month;
			*/
			$last_rand_no = 0;
			foreach ($extra_labour as $labour) {
				$start_leave_day = rand(1,5);

				if($last_rand_no == $start_leave_day){
					$last_rand_no += 1;
					if($last_rand_no > 6)
						$start_leave_day = rand(1,5);
				}

				$last_rand_no = $start_leave_day;

				$labour_id = $labour->id;
				if($duty_implemented >= $duty_to_implement) break;

				$labour_used[$labour_id] = $labour_id;

				if(!isset($labour_array[$billing_service_id][$labour_id])) $labour_array[$billing_service_id][$labour_id] = $sheet_array;
				
				$week_count = 0;
				for ($i=1; $i <= $days_in_month; $i++) {
					if($duty_implemented >= $duty_to_implement) continue;

					if($start_leave_day == $i OR ($start_leave_day + (6 * $week_count)) == $i){
						$labour_array[$billing_service_id][$labour_id][$i] = "L";
						$week_count++;
					}else{
						$labour_array[$billing_service_id][$labour_id][$i] = "P";
						$duty_implemented++;
					}
				}
			}
		}

		// $page->add('View')->set("Duty implemented=".$duty_implemented." duty to implement=".$duty_to_implement);
		// $page->add('View')->set("total labour count.".count($labour_used));
		// echo "<pre>";
		// print_r($labour_array);
		// echo "</pre>";

		$pl_model = $this->add('xavoc\securityservices\Model_PL');
		$pl_model->addCondition('client_month_year_id',$this->id);
		$record_count = $pl_model->count()->getOne();

		if(!$record_count){

			$pl_query = "INSERT into secserv_pl (client_month_year_id,client_billing_service_id,labour_id,d1,d2,d3,d4,d5,d6,d7,d8,d9,d10,d11,d12,d13,d14,d15,d16,d17,d18,d19,d20,d21,d22,d23,d24,d25,d26,d27,d28,d29,d30,d31,total_present) VALUES ";
			foreach($labour_array as $billing_id => $labour) {
				foreach ($labour as $l_id => $atten_array){
					$pl_query .= "('".$this->id."','".$billing_id."','".$l_id."',";

					$temp = "";
					$total_present = 0;
					foreach ($atten_array as $day => $value) {
						$temp .= "'".$value."',";
						if($value == "P") $total_present++;
					}

					$temp .= "'".$total_present."',";
					$pl_query .= trim($temp,',')."),";
				}
			}

			$pl_query = trim($pl_query,",").";";
			$this->app->db->dsql()->expr($pl_query)->execute();
		}

		$grid = $page->add('Grid');
		$grid->setModel($pl_model);
		$grid->add('misc/Export');

		// $export = $grid->addButton('Export CSV');
		// $key = "export_csv_file_".$this->id;
		// $export->js('click')->univ()->newWindow($grid->app->url(null,['record'=>$this->id]));
		// if($_GET['record']===$this->id){
		// 	$rows = $pl_model->getRows();
		// 	$file_name = "PL_List_of "."_".str_replace(" ", "",$this['client'])."_".str_replace(" ", "",$this['month_year']).".csv";

		// 	header("Pragma: public");
		// 	header("Expires: 0");
		// 	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		// 	header("Cache-Control: private",false);
		// 	header("Content-Type: application/octet-stream");
		// 	header("Content-Disposition: attachment; filename=\"$file_name\";" );
		// 	header("Content-Transfer-Encoding: binary");

		// 	$header = [];
		// 	$csv_rows = $rows;
		// 	$output = implode(",", $header);
		// 	$fp = fopen("php://output", "w");
		// 	fputcsv ($fp, $header, ",");
		// 	foreach($csv_rows as $key=>$row){
		// 		fputcsv($fp, $row, ",");
		// 	}
		// 	fclose($fp);
		// }

	}

}