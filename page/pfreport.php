<?php

namespace xavoc\securityservices;

class page_pfreport extends \xepan\base\Page {
	
	public $title ="PF Amount";

	function init(){
		parent::init();
		
		$this->app->stickyGET('month_year');

		$form = $this->add('Form');
		$m_y_picker = $form->addField('DatePicker','month_year');
		$m_y_picker->options = [
					'format'=> "yyyy-mm",
    				'startView'=> "months", 
    				'minViewMode'=> "months"
    			];

    	$grid = $this->add('xepan\base\Grid');

    	$form->addSubmit('Submit');
    	if($form->isSubmitted()){
			if(!$form['month_year']) $form->error('month_year','month year must not be empty');
    		$form->js(null,$grid->js()->reload(['month_year'=>$form['month_year']]))->univ()->execute();
    	}

    	$p_m = $this->add('xavoc\securityservices\Model_Payment');
    	$p_m->addExpression('account_no')->set($p_m->refSQL('labour_id')->fieldQuery('bank_account_no'));
    	$p_m->addExpression('ifsc_code')->set($p_m->refSQL('labour_id')->fieldQuery('bank_ifsc_code'));
    	$p_m->addExpression('uan')->set($p_m->refSQL('labour_id')->fieldQuery('uan'));

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

		$p_m->addCondition('net_payable','>',0);
		$p_m->addCondition('pf_amount','>',0);
		$p_m->_dsql()->group('labour_id');
    	$grid->setModel($p_m,['labour','uan','sum_gross_amount','sum_pf_amount','sum_net_payable']);
    	// $grid->add('misc/Export');

    	$export = $grid->addButton('Export CSV');
    	$export->js('click')->univ()->newWindow($grid->app->url(null,['generate_csv'=>true]));

		if($_GET['generate_csv']){
			$salary = 'PF Report '.date('F Y',strtotime($_GET['month_year']));
			
			$record = $p_m->getRows();
			$csv_rows = [];

			$temp = [];
			$s_no = 1;
			foreach ($record as $data){
				$temp['s_no'] = $s_no;
				$temp['labour'] = trim($data['labour'],',');
				$temp['uan'] = $data['uan'];
				$temp['gross_amount'] = $data['sum_gross_amount'];
				$temp['pf_amount'] = $data['sum_pf_amount'];
				$temp['net_amount'] = $data['sum_net_payable'];
				$csv_rows[] = $temp;
				$s_no = $s_no + 1;
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
			fputcsv ($fp, ['S no',"Labour",'UAN',"Gross Amount","PF Amount","Net Amount"], ",");

			foreach($csv_rows as $key=>$row){
				foreach ($row as $field => $value) {
				 	// elseif($field == 'net_amount')
				 	// 	echo number_format((float)$value, 2, '.', '').",";
				 	// else
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