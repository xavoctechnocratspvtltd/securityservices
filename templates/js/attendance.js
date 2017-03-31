jQuery.widget("ui.xavoc_secserv_attendance",{
	client_data_page:'index.php?page=xavoc_secserv_manageattendance_labours',
	options:{
		client_month_year_id:0,
		client_id:0,
		client_name:undefined,
		client_departments:[],
		selected_department_id:0,
		month_days:0,
		default_labours:{},
		additional_labours:{}
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
		self.table = table = $('<table class="sec-employee-attendance data" border="1"></table>').appendTo(wrapper);
		self.thead = thead = $('<thead class="sec-header">').appendTo(table);
		self.thead_tr = thead_tr = $('<tr>').appendTo(thead);
		self.tbody = tbody = $('<tbody class="sec-results">').appendTo(table);

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
	          	},
	          	error: function(XMLHttpRequest, textStatus, errorThrown) {
	              alert("Error getting prospect list: " + textStatus);
	            }
			});

			// self.loadData();
		});

		self.addSaveButton();
	},

	addSaveButton: function(){
		var self = this;

		$save_btn = $('<button class="btn btn-primary">Save</button>').appendTo(self.wrapper);
		$save_btn.click(function(){
			alert('save click todo');
		});
	},

	clearData: function(){
		var self = this;
		//department options set to empty
		self.options.default_labours = '{}';
		self.options.additional_labours = '{}';

		// html content remove
		$(self.thead_tr).html("");
		$(self.tbody).html("");

	},

	loadData: function(){
		var self = this;
		// default_labours = JSON.parse(self.options.default_labours);
		// additional_labours = JSON.parse(self.options.additional_labours);

		default_labours = self.options.default_labours;
		additional_labours = self.options.additional_labours;

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
	},

	addRow: function(labour_data){
		var self = this;
		tr_html = '<tr>';
		tr_html += "<th>"+labour_data.name+"</th>";
		
		for (var i =1; i <= self.options.month_days; i++) {
			tr_html += '<td class="labour_units_work" ><input /></td>';
		}

		tr_html += "</tr>";
		$(tr_html).appendTo(self.tbody);
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