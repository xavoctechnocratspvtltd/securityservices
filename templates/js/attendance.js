jQuery.widget("ui.xavoc_secserv_attendance",{
	options:{
		month_days:0,
		default_labours:{},
		additional_labours:{}
	},

	_create:function(){
		var self = this;

		self.setupEnvironment();
		self.loadData();
		self.loadCSS();
		self.addLiveEvent();
	},

	setupEnvironment: function(){
		var self = this;
		self.wrapper = wrapper = $('<div class="sec-attendance-widget"></div>').appendTo(self.element);
		self.table = table = $('<table class="sec-employee-attendance data" border="1"></table>').appendTo(wrapper);
		self.thead = thead = $('<thead class="sec-header">').appendTo(table);
		self.thead_tr = thead_tr = $('<tr>').appendTo(thead);
		self.tbody = tbody = $('<tbody class="sec-results">').appendTo(table);
	},

	loadData: function(){
		var self = this;
		default_labours = JSON.parse(self.options.default_labours);
		additional_labours = JSON.parse(self.options.additional_labours);

		// for header 
		var thead_html = "<th>Labour Name</th>";
		for (var i =1; i <= self.options.month_days; i++) {
			thead_html += '<th>'+i+'</th>';
		}
		$(thead_html).appendTo(self.thead_tr);
		// console.log(self.thead_tr);
		// console.log(self.tbody);
		// console.log(default_labours);
		// console.log(additional_labours);


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
			tr_html += '<td><input /></td>';
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