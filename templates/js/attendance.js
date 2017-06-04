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
		used_labours:{},
		content_changed:false
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
		self.widget_header = $('<div class="sec-widget-header">').appendTo(self.wrapper);
		self.widget_row = $('<div class="row">').appendTo(self.widget_header);
		self.widget_row_col1 = $('<div class="col-md-6 col-sm-6">').appendTo(self.widget_row);
		self.widget_row_col2 = $('<div class="col-md-6 col-sm-6">').appendTo(self.widget_row);

		self.table = table = $('<table class="sec-employee-attendance data" border="1"></table>').appendTo(self.wrapper);
		self.thead = thead = $('<thead class="sec-header">').appendTo(table);
		self.thead_tr = thead_tr = $('<tr>').appendTo(thead);
		self.tbody = tbody = $('<tbody class="sec-attendance">').appendTo(table);
		self.widget_footer = $('<div class="sec-widget-footer">').appendTo(self.wrapper);

		// auto save section
		$('<div class="autosave-box">').appendTo(self.widget_row_col2);

		// client department section
		$("<p>Client Department: </p>").appendTo(self.widget_row_col1);
		var department = $('<select class="main-box">').appendTo(self.widget_row_col1);
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

		$save_btn = $('<button class="btn btn-primary secser-savebutton">Save</button>').appendTo(self.widget_footer);

		$save_btn.click(function(){
			self.save();
		});
	},

	save: function(auto_save=false){

		var self = this;
		
		var inserted_data = {};
		inserted_data.client_id = self.options.client_id;
		inserted_data.client_month_year_id = self.options.client_month_year_id;
		inserted_data.department_id = self.options.selected_department_id;
		inserted_data.attendance = {};

		$(self.element).find('tbody.sec-attendance tr').each(function(index,current_tr){
			if($(current_tr).hasClass('additional-labour-form-tr') || $(current_tr).hasClass('day-total-working-hours') ){
				return;
			};

			curr_labour_id = $(current_tr).attr('data-labour_id');
			inserted_data.attendance[""+curr_labour_id] = {};

			$(current_tr).find('td.labour_units_work').each(function(index,current_td){
				curr_date = $(current_td).attr('data-date');
				inserted_data['attendance'][""+curr_labour_id][""+curr_date] = ($(current_td).find('input').val())?$(current_td).find('input').val():0;
			});
		});

		$.ajax({
			url:self.save_page,
			type: 'POST',
			data:{
				attendance_data:JSON.stringify(inserted_data)
			},
			success: function( data ) {
				labour_data = JSON.parse(data);
				if(labour_data.status == "failed"){
					if(auto_save){
						$('.autosave-box').html('<div class="text-danger">Content Not Saved..</div>');
					}else{
						$.univ().errorMessage('not saved');	
					}
					return;
				}else{
					if(auto_save){
						$('.autosave-box').html('<div class="text-success">Content Auto Saved.</div>');
					}else{
						$.univ().successMessage('saved successfully');
					}
				}
          	},
          	error: function(XMLHttpRequest, textStatus, errorThrown) {
              alert("Error getting prospect list: " + textStatus);
            }
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
		thead_html += '<th>Total Hours</th>';

		$(thead_html).appendTo(self.thead_tr);

		if(default_labours.length == undefined || default_labours.length == 0){
			$('<div>No One Default Labour Added</div>').appendTo(self.tbody);
		}

		// for all employee and it's unit's work
		$.each(default_labours,function(index,labour_data){
			self.addRow(labour_data);
		});

		// additional section
		var add_tr = $('<tr class="additional-labour-form-tr"></tr>').appendTo(self.tbody);
		var add_form =  $('<td colspan="'+(self.options.month_days+1)+'">add additional labour</td>').appendTo(add_tr);
		var group = $('<div class="input-group">').appendTo(add_form);
		var labour_field = $('<select class="form-control additional-labour-selector"></select>').appendTo(group);
		var add_labour_btn = $('<span class="input-group-btn"><button class="btn btn-primary" type="button">Add</button></span></div>').appendTo(group);
		
		var additional_options = '<option value="0">Please Select Additional Labour</option>';
		// console.log(self.options.remaining_all_labours);
		$.each(remaining_labours,function(index,labour){
			additional_options += '<option value="'+labour.id+'">'+labour.name+'</option>';
		});
		$(additional_options).appendTo(labour_field);
		$('.additional-labour-selector').select2({
			data:remaining_labours
		});

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

		// TODO show added additional labours
		// for all employee and it's unit's work
		$.each(additional_labours,function(index,labour_data){
			self.addRow(labour_data);
		});

		// add col sum row
		td_total_html = '<tr class="day-total-working-hours">';
		td_total_html += "<th>Total Hours</th>";
		for(var i=1; i<=self.options.month_days; i++){
			td_total_html += '<td style="text-align:right;" data-date-sum="'+i+'" class="labour_day_total_units_work" ><div class="secser-total-day-hour">0</div></td>';
		}
		td_total_html += '<td style="text-align:right;" class="td-tr-sum" ></td>';
		td_total_html += '</tr>';
		$(td_total_html).appendTo(self.tbody);
		self.updateTotalDayHours();
		self.updateTotalSum();


	},

	addRow: function(labour_data){
		var self = this;
		tr_html = '<tr data-labour_id="'+labour_data.id+'">';
		tr_html += "<th>"+labour_data.name+"</th>";
		
		month_attendance = labour_data.month_attendance;

		i = 1
		row_total_hours = 0;
		for (var i =1; i <= self.options.month_days; i++) {
			units_work = month_attendance[i];
			if(units_work == undefined)
				units_work = "0";
			units_work = units_work.replace(/\.00$/,'');
			tr_html += '<td style="height:65px;" data-date="'+i+'" class="labour_units_work" ><p class="hover-day" style="background-color:black;color:white;text-align:center;font-weight:bold;display:none;">'+i+'</p><input class="secser-attendance-hour" type="text" value="'+units_work+'"/></td>';
			row_total_hours += parseFloat(units_work);
		}

		// total hours row wise
		tr_html += '<td class="labour_units_work_row_total" style="text-align:right;"><div class="secser-attendance-total-hour">'+row_total_hours+'</div></td>';
		tr_html += "</tr>";

		if($(self.tbody).find('.day-total-working-hours').length > 0){
			$(tr_html).insertBefore($(self.tbody).find('.day-total-working-hours'));
			// $(tr_html).after('.day-total-working-hours').appendTo(self.tbody);
		}else{
			$(tr_html).appendTo(self.tbody);
		}

		self.options.used_labours[labour_data.id] = labour_data.id;
	},

	addLiveEvent: function(){
		var self = this;

		$(self.element).find('input').livequery(function(){
			$(this).keydown(function(e){

				$(this).closest('tr').removeClass('bg-info');
				$(this).closest('td').removeClass('bg-primary');
				$(this).siblings('.hover-day').hide();

				switch(e.which){
					case 37: //left arrow key
					// console.log("left arrow");
						var curr_index = $(this).closest('tr').find('input').index($(this));
						var curr_row = $(this).closest('tr');
						var prev_col = $(this).closest('td').prev();
						if(prev_col.length === 0) return;
						$(curr_row).find('input').eq(curr_index-1).focus();
						e.preventDefault();
					break;

					case 38: //up arrow key
						// console.log("up arrow");
						var col = $(this).closest('tr').find('input').index($(this));
						var prev_row = $(this).closest('tr').prev();
						if(prev_row.length ===0) return;
						$(prev_row).find('input').eq(col).focus();
					break;

					case 39: //right arrow key
						// console.log("right arrow");
						var col = $(this).closest('tr').find('input').index($(this));
						var curr_row = $(this).closest('tr');
						var next_col = $(this).closest('td').next();
						if(next_col.length === 0) return;
						$(curr_row).find('input').eq(col+1).focus();
						e.preventDefault();
					break;

					case 40: //down arrow key
						// console.log("down arrow");
						var col = $(this).closest('tr').find('input').index($(this));
						var next_row = $(this).closest('tr').next();
						if(next_row.length ===0) return;
						$(next_row).find('input').eq(col).focus();
					break;
				}
			});
		});

		$(self.element).find('input').livequery(function(){
			$(this).change(function(event) {
				/* Act on the event */
				self.options.content_changed = true;
				$(self.element).find('.autosave-box').html('content changed ...');
				self.updateTotalAmount($(this));
			});

			$(this).focus(function(event) {
				// $(this).closest('.sec-attendance').find('tr').removeClass('bg-info');
				// $('.labour_units_work').removeClass('bg-primary');
				$(this).closest('tr').addClass('bg-info');
				$(this).closest('td').addClass('bg-primary');
				$(this).select();
				$(this).siblings('.hover-day').show();
				// $(this).css('border','2px solid red');
			});
		});
		//calling auto save after 30 sec interval
		setInterval(function () { self.autoSave(); }, (30000));

		//side navbar samll
		$('#theme-wrapper > #page-wrapper').addClass('nav-small'); 
	},

	updateTotalAmount: function(curr_input){
		var self = this;
		// tr total sum
		$current_row = $(curr_input).closest('tr');
		tr_total_sum = 0;
		$current_row.find('input').each(function(index,current_col){
			tr_total_sum += parseFloat($(current_col).val());
		});	
		$current_row.find('div.secser-attendance-total-hour').html(tr_total_sum);

		// td total sum
		self.updateTotalDayHours(curr_input);
		self.updateTotalSum();
	},

	updateTotalSum: function(){
		var self = this;
		// check update total sum of sum
		labour_total_sum = 0;
		$(self.tbody).find('td.labour_units_work_row_total > .secser-attendance-total-hour').each(function(index,total_row){
			labour_total_sum = parseFloat(labour_total_sum) + parseFloat($(total_row).html());
		});
		$('.td-tr-sum').html(labour_total_sum);
	},

	// column wise sum actually day wise total hours works
	updateTotalDayHours:function(curr_input=null){
		var self = this;
		if($(curr_input).length > 0){
			var current_date = $(curr_input).parent('td').attr('data-date');
			td_total_sum = 0;
			$current_row.parent('tbody').find('tr[data-labour_id]').each(function(index,row){
				val = $(row).find('td[data-date='+current_date+']').find('input').val();
				td_total_sum += parseFloat(val);
			});
			$(self.tbody).find('tr.day-total-working-hours').find('td[data-date-sum='+current_date+']').find('.secser-total-day-hour').html(td_total_sum);
		}else{
			sum_array = [];
			$(self.tbody).find('tr[data-labour_id]').each(function(index,row){
				$(row).find('td[data-date]').each(function(index,col){
					date = $(col).attr('data-date');
					val = $(col).find('input').val();
					if(sum_array[date] == undefined){
						sum_array[date] = parseFloat(val);
					} else{
						sum_array[date] = parseFloat(sum_array[date]) + parseFloat(val);
					}
				});
			});

			$.each(sum_array, function(date, total_hour) {
				$(self.tbody).find('tr.day-total-working-hours').find('td[data-date-sum='+date+']').find('.secser-total-day-hour').html(total_hour);
			});
		}
	},

	autoSave: function(){
		var self = this;
		if(self.options.content_changed){
			$(self.element).find('.autosave-box').html('<div class="text-primary">auto saving your content ...</div>');
			// $('.secser-savebutton').trigger('click');
			self.save(true);
			self.options.content_changed = false;
		}else{
			$(self.element).find('.autosave-box').html('');	
		}
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