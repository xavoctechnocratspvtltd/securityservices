<?php

namespace xavoc\securityservices;


class Model_GroupedAttendance extends Model_Attendance{ 
	
	function init(){
		parent::init();

		// ===== DSQL WAY =====
		$this->_dsql()->del('fields')
			->field('client_month_year_id')
			->field('date')
			->field($this->dsql()->expr('[0] client_month_year',[$this->getElement('client_month_year')]))
			->field('client_department_id')
			->field($this->dsql()->expr('[0] client_department',[$this->getElement('client_department')]))
			->field('client_service_id')
			->field($this->dsql()->expr('[0] client_service',[$this->getElement('client_service')]))

			->field($this->dsql()->expr('SUM([0]) as units_work_sum',[$this->getElement('units_work')]))
			;
		
		$this->_dsql()->group('client_month_year_id,client_department_id,client_service_id,date');

	}
}