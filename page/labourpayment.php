<?php

namespace xavoc\securityservices;

class page_labourpayment extends \xepan\base\Page {
	
	public $title ="Labour Paymet";

	function init(){
		parent::init();
		
		$this->app->stickyGET('month_year');
		$client_id = $this->app->stickyGET('client_id');
		$having_bank_account = $this->app->stickyGET('labour_having_account');

		$form = $this->add('Form');
		$form->add('xepan\base\Controller_FLC')
			->showLables(true)
			->makePanelsCoppalsible(true)
			->layout([
					'month_year'=>'Filter~c1~2',
					'client'=>'c2~4',
					'labour_having_account'=>'c3~3',
					'FormButtons~&nbsp;'=>'c4~3'
				]);

		$m_y_picker = $form->addField('DatePicker','month_year');
		$m_y_picker->options = [
					'format'=> "yyyy-mm",
    				'startView'=> "months", 
    				'minViewMode'=> "months"
    			];
    	$c_f = $form->addField('DropDown','client');
    	$c_f->setModel('xavoc\securityservices\Model_Client');
    	$c_f->setEmptyText('All');

    	$form->addField('DropDown','labour_having_account')->setValueList(['both'=>'Both','yes'=>'Yes','no'=>'No']);

    	$grid = $this->add('Grid');

    	$form->addSubmit('Submit');
    	if($form->isSubmitted()){
			if(!$form['month_year']) $form->error('month_year','month year must not be empty');
    		$form->js(null,$grid->js()->reload(['month_year'=>$form['month_year'],'client_id'=>$form['client'],'labour_having_account'=>$form['labour_having_account']]))->univ()->execute();
    	}

    	$p_m = $this->add('xavoc\securityservices\Model_Payment');
    	$p_m->addExpression('account_no')->set($p_m->refSQL('labour_id')->fieldQuery('bank_account_no'));
    	$p_m->addExpression('ifsc_code')->set($p_m->refSQL('labour_id')->fieldQuery('bank_ifsc_code'));

    	$p_m->addExpression('sum_gross_amount')->set('sum(gross_amount)')->caption('gross amount');
    	$p_m->addExpression('sum_pf_amount')->set('sum(pf_amount)')->caption('pf amount');
    	$p_m->addExpression('sum_uniform_deduction')->set('sum(uniform_deduction)')->caption('uniform deduction');
    	$p_m->addExpression('sum_other_deduction')->set('sum(other_deduction)')->caption('other deduction');
    	$p_m->addExpression('sum_fine')->set('sum(fine)')->caption('fine');
    	$p_m->addExpression('sum_advance')->set('sum(advance)')->caption('advance');
    	$p_m->addExpression('sum_allowances')->set('sum(allowances)')->caption('allowances');
    	$p_m->addExpression('sum_net_payable')->set('sum(net_payable)')->caption('net payable');
    	$p_m->addExpression('remarks')->set('GROUP_CONCAT(remark)');

    	if($_GET['month_year']){
			$p_m->addCondition('date',$_GET['month_year']);
		}else{
			$p_m->addCondition('date',-1);
		}

		if($client_id){
			$p_m->addCondition('client_id',$client_id);	
		}

		$p_m->addCondition('net_payable','>',0);
		$p_m->_dsql()->group('labour_id');
		
		if($having_bank_account == 'yes'){
    		$p_m->addCondition('account_no','>',0);
    	}
    	if($having_bank_account == 'no'){
    		$p_m->addCondition([['account_no',0],['account_no',null]]);
    	}

    	$grid->setModel($p_m,['labour','account_no','ifsc_code','sum_gross_amount','sum_pf_amount','sum_uniform_deduction','sum_other_deduction','sum_fine','sum_advance','sum_allowances','sum_net_payable','remarks']);
    	// $grid->add('misc/Export');

    	// if($having_bank_account == 'no'){
    	// 	$grid->removeColumn('account_no');
    	// 	$grid->removeColumn('ifsc_code');
    	// }


    	$export = $grid->addButton('Export CSV');
    	$export->js('click')->univ()->newWindow($grid->app->url(null,['generate_csv'=>true]));

		if($_GET['generate_csv']){
			$salary = 'Salary '.date('F Y',strtotime($_GET['month_year']));
			
			$record = $p_m->getRows();
			$csv_rows = [];

			$temp = [];
			foreach ($record as $data){
				$temp['month_year'] = $salary;
				$temp['labour'] = trim($data['labour'],',');
				$temp['account_no'] = trim($data['account_no'],',');
				$temp['ifsc_code'] = trim($data['ifsc_code'],',');
				$temp['deposite_bank_code'] = 10;
				$temp['net_amount'] = $data['sum_net_payable'];
				$temp['now'] = str_replace("-", "",$this->app->today);
				$csv_rows[] = $temp;
			}

			$file_name = str_replace(" ", "", $salary).".csv";
			header("Pragma: public");
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Cache-Control: private",false);
			header("Content-Type: application/octet-stream");
			header("Content-Disposition: attachment; filename=\"$file_name\";" );
			header("Content-Transfer-Encoding: binary");

			// $output = implode(",", $header);
			$fp = fopen("php://output", "w");
			// fputcsv ($fp, $header, ",");

			foreach($csv_rows as $key=>$row){
				foreach ($row as $field => $value) {
				 	if($field == "now")
				 		echo $value;
				 	elseif($field == 'net_amount')
				 		echo number_format((float)$value, 2, '.', '').",";
				 	else
				 		echo $value.",";
				}
				echo "\r\n";
				// fputcsv($fp,$row, ",");
			}
			fclose($fp);
			exit;
		}

	}
}