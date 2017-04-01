jQuery.widget("ui.xavoc_secserv_attendance",{
	client_data_page:'index.php?page=xavoc_secserv_manageattendance_labours',
	save_page:'index.php?page=xavoc_secserv_manageattendance_save',
	options:{
		client_month_year_id:0,
		client_id:0,
		client_name:undefined,
		client_departments:[],
		selected_department_id:0,
		month_days:0,
		default_labours:{},
		additional_labours:{},
		remaining_all_labours:{},
		used_labours:{}
	},

	_create:function(){
		var self = this;

		self.setupEnvironment();
		// self.loadCSS();
		self.addLiveEvent();
	},

	setupEnvironment: function(){
		var self = this;

		self.wrapper = wrapper = $('<div class="sec-attendance-widget"></div>').appendTo(self.element);
		self.widget_header = $('<div class="sec-widget-header main-box">').appendTo(self.wrapper);
		self.table = table = $('<table class="sec-employee-attendance data" border="1"></table>').appendTo(self.wrapper);
		self.thead = thead = $('<thead class="sec-header">').appendTo(table);
		self.thead_tr = thead_tr = $('<tr>').appendTo(thead);
		self.tbody = tbody = $('<tbody class="sec-attendance">').appendTo(table);
		self.widget_footer = $('<div class="sec-widget-footer">').appendTo(self.wrapper);

		var department = $("<select>").appendTo(self.widget_header);
		var dept_option = '<option value="0">Please Select Client Department</option>';
		$.each(self.options.client_departments,function(index,dept){
			dept_option += '<option value="'+dept.id+'">'+dept.name+'</option>';
		});

		$(dept_option).appendTo(department);
		$(department).change(function(){
			selected_dept_id = $(this).val();
			self.options.selected_department_id = selected_dept_id;

			self.clearData();
			if(selected_dept_id == 0){
				return;
			}
			// ajax calling for default labour and additional labour
			$.ajax({
				url:self.client_data_page,
				data:{
					dept_id:selected_dept_id,
					record_id:self.options.client_month_year_id
				},
				success: function( data ) {
					labour_data = JSON.parse(data);
					if(labour_data.status == "failed")
						return;
					
					self.options.default_labours = labour_data.data.default_labours;
					self.options.additional_labours = labour_data.data.additional_labours;
					self.loadData();
					self.addSaveButton();
	          	},
	          	error: function(XMLHttpRequest, textStatus, errorThrown) {
	              alert("Error getting prospect list: " + textStatus);
	            }
			});
		});

	},

	addSaveButton: function(){
		var self = this;

		$save_btn = $('<button class="btn btn-primary">Save</button>').appendTo(self.widget_footer);

		$save_btn.click(function(){
			var inserted_data = {};
			inserted_data.client_id = self.options.client_id;
			inserted_data.client_month_year_id = self.options.client_month_year_id;
			inserted_data.department_id = self.options.selected_department_id;
			inserted_data.attendance = {};

			$(self.element).find('tbody.sec-attendance tr').each(function(index,current_tr){
				if($(current_tr).hasClass('additional-labour-form-tr')){
					return;
				} ;

				curr_labour_id = $(current_tr).attr('data-labour_id');
				inserted_data.attendance[""+curr_labour_id] = {};

				$(current_tr).find('td').each(function(index,current_td){
					curr_date = $(current_td).attr('data-date');
					inserted_data['attendance'][""+curr_labour_id][""+curr_date] = ($(current_td).find('input').val())?$(current_td).find('input').val():0;
				});
			});

			$.ajax({
				url:self.save_page,
				type: 'POST',
				data:{
					attendance_data:JSON.stringify(inserted_data),
					dept_id:self.options.selected_department_id,
					record_id:self.options.client_month_year_id
				},
				success: function( data ) {
					labour_data = JSON.parse(data);
					if(labour_data.status == "failed"){
						$.univ().errorMessage('not saved');
						return;
					}else{
						$.univ().successMessage('saved successfully');
					}
	          	},
	          	error: function(XMLHttpRequest, textStatus, errorThrown) {
	              alert("Error getting prospect list: " + textStatus);
	            }
			});
		});
	},

	clearData: function(){
		var self = this;
		//department options set to empty
		self.options.default_labours = '{}';
		self.options.additional_labours = '{}';
		self.options.used_labours = [];

		// html content remove
		$(self.thead_tr).html("");
		$(self.tbody).html("");
		$(self.widget_footer).html("");

	},

	loadData: function(){
		var self = this;
		// default_labours = JSON.parse(self.options.default_labours);
		// additional_labours = JSON.parse(self.options.additional_labours);

		default_labours = self.options.default_labours;
		additional_labours = self.options.additional_labours;
		remaining_labours = JSON.parse(self.options.remaining_all_labours);

		// for header 
		var thead_html = "<th>Labour Name</th>";
		for (var i =1; i <= self.options.month_days; i++) {
			thead_html += '<th>'+i+'</th>';
		}
		$(thead_html).appendTo(self.thead_tr);

		if(default_labours.length == undefined || default_labours.length == 0){
			$('<div>No One Default Labour Added</div>').appendTo(self.tbody);
		}

		// for all employee and it's unit's work
		$.each(default_labours,function(index,labour_data){
			self.addRow(labour_data);
		});

		var add_tr = $('<tr class="additional-labour-form-tr"></tr>').appendTo(self.tbody);
		var add_form =  $('<td colspan="'+(self.options.month_days+1)+'">add additional labour</td>').appendTo(add_tr);
		var group = $('<div class="input-group">').appendTo(add_form);
		var labour_field = $('<select class="form-control"></select>').appendTo(group);
		var add_labour_btn = $('<span class="input-group-btn"><button class="btn btn-primary" type="button">Add</button></span></div>').appendTo(group);
		
		var additional_options = '<option value="0">Please Select Additional Labour</option>';
		// console.log(self.options.remaining_all_labours);
		$.each(remaining_labours,function(index,labour){
			additional_options += '<option value="'+labour.id+'">'+labour.name+'</option>';
		});
		$(additional_options).appendTo(labour_field);

		$(add_labour_btn).click(function(){
			var selected_labour_id = $(labour_field).val();
			if(selected_labour_id == 0 || selected_labour_id == undefined || selected_labour_id == ""){
				$.univ().errorMessage('please select additional labour');
				return false;
			}

			// check if labour is not used
			if(self.options.used_labours[selected_labour_id] === undefined){
				self.addRow(remaining_labours[selected_labour_id]);
			}else{
				$.univ().errorMessage('this labour is already added');
			}

		});
	},

	addRow: function(labour_data){
		var self = this;
		tr_html = '<tr data-labour_id="'+labour_data.id+'">';
		tr_html += "<th>"+labour_data.name+"</th>";
		
		for (var i =1; i <= self.options.month_days; i++) {
			tr_html += '<td data-date="'+i+'" class="labour_units_work" ><input /></td>';
		}

		tr_html += "</tr>";
		$(tr_html).appendTo(self.tbody);

		self.options.used_labours[labour_data.id] = labour_data.id;
	},

	addLiveEvent: function(){
		var self = this;

		$(self.element).find('input').keydown(function(e){
			switch(e.which){
				case 37: //left arrow key
					// alert('left');
				break;

				case 38: //up arrow key
					var col = $(this).closest('tr').find('input').index($(this));
					var prev_row = $(this).closest('tr').prev();
					if(prev_row.length ===0) return;
					$(prev_row).find('input').eq(col).focus();
				break;

				case 39: //right arrow key
					// alert('right');
				break;

				case 40: //down arrow key
					var col = $(this).closest('tr').find('input').index($(this));
					var next_row = $(this).closest('tr').next();
					if(next_row.length ===0) return;
					$(next_row).find('input').eq(col).focus();
				break;
			}
		});
	},

	loadCSS: function(){
		var self = this;

		var css = [
			'<style>',
			'.sec-attendance-widget { ',
		    	'overflow-x:scroll;',
		    	'overflow-y:visible;',
		    	'width:900px;',
		    	'margin-left: 150px;',
		    	// 'border:5px solid red;',
			'}',

			'.sec-attendance-widget td, .sec-attendance-widget th {',
			    'width: 200px;',
			'}',

			'.sec-attendance-widget th:first-child {',
		    	'position: fixed;',
		    	'margin-left:-150px;',
		    	'width:150px;',
		    	// 'margin-left: 5px;',
			'}',

			'.sec-attendance-widget input {',
				'width:30px;',
			'}',

			'.sec-attendance-widget input:focus {',
				'background-color:yellow;',
				// 'color:white;',
				// 'font-size:20px;',
			'}',
			'</style>'
		].join("");

		$(css).appendTo(self.element);
	}
});