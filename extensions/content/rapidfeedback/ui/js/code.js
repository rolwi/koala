var elementCounter;
var singleChoiceCounter = 1;
var multipleChoiceCounter = 1;
var matrixCounter = 1;
var gradingCounter = 1;
var tendencyCounter = 1;

/*
 * function to initiate the datepicker
 */
function initiateDatepicker() {
	$( "#datepicker_begin" ).datepicker();
	$( "#datepicker_end" ).datepicker();
	$.datepicker.regional['de'] = {clearText: 'löschen', clearStatus: 'aktuelles Datum löschen',
             closeText: 'schließen', closeStatus: 'ohne Änderungen schließen',
             prevText: '&#x3c;Zurück', prevStatus: 'letzten Monat zeigen',
             nextText: 'Vor&#x3e;', nextStatus: 'nächsten Monat zeigen',
             currentText: 'heute', currentStatus: '',
             monthNames: ['Januar','Februar','März','April','Mai','Juni',
             'Juli','August','September','Oktober','November','Dezember'],
             monthNamesShort: ['Jan','Feb','Mär','Apr','Mai','Jun',
             'Jul','Aug','Sep','Okt','Nov','Dez'],
             monthStatus: 'anderen Monat anzeigen', yearStatus: 'anderes Jahr anzeigen',
             weekHeader: 'Wo', weekStatus: 'Woche des Monats',
             dayNames: ['Sonntag','Montag','Dienstag','Mittwoch','Donnerstag','Freitag','Samstag'],
             dayNamesShort: ['So','Mo','Di','Mi','Do','Fr','Sa'],
             dayNamesMin: ['So','Mo','Di','Mi','Do','Fr','Sa'],
             dayStatus: 'Setze DD als ersten Wochentag', dateStatus: 'Wähle D, M d',
             dateFormat: 'dd.mm.yy', firstDay: 1, 
             initStatus: 'Wähle ein Datum', isRTL: false};
    $.datepicker.setDefaults($.datepicker.regional['de']);
}

/*
 * function to show the create dialog
 */
function showCreateDialog() {
	resetCreateDialog();
	if (document.getElementById('editID').value != '-1') {
		$('#element'+document.getElementById('editID').value).show();
		document.getElementById('editID').value = '-1';
	}
	$('#newquestion').show();
	$('#newquestion_button').hide();
}

/*
 * function to show the layout dialog
 */
function showLayoutDialog() {
	$('#newlayout').show();
	$('#newquestion_button').hide();
}

/*
 * function to hide the layout dialog
 */
function hideLayoutDialog() {
	$('#newlayout').hide();
	$('#text_preview').show();
	$('#newquestion_button').show();
}

/*
 * function to hide the create dialog
 */
function hideCreateDialog() {
	if (document.getElementById('editID').value != '-1') {
		$('#element'+document.getElementById('editID').value).show();
		document.getElementById('editID').value = '-1';
	}

	$('#textarea_preview').hide();
	$('#singlechoice_preview').hide();
	$('#multiplechoice_preview').hide();
	$('#matrix_preview').hide();
	$('#grading_preview').hide();
	$('#tendency_preview').hide();
	$('#newquestion').hide();
	$('#text_preview').show();
	$('#newquestion_button').show();

}

function changeLayoutDialog(id) {
	switch (id) {
		case "3":
			$('#text_layout').show();
			$('#textarea_layout').hide();
			break;
		case "4":
			$('#text_preview').hide();
			$('#textarea_preview').show();
			break;
		default:
			$('#text_preview').hide();
			$('#textarea_preview').hide();
			break;	
	} 
}

/*
 * function to reset the create dialog
 */
function resetCreateDialog() {
	document.getElementById('questionText').value = '';
	document.getElementById('helpText').value = '';
	document.getElementById('required').checked = false;
	document.getElementById('questionType')[0].selected = true;
	
	// remove single choice options
	document.getElementById('singleChoiceColumns').value = 1;
	for (var i=1; i < singleChoiceCounter; i++) {
		if (document.getElementsByName('singlechoice_' + i)[0] != undefined) {
			$('#singlechoice_'+i).remove();
		}
	}
	document.getElementsByName('singlechoice_0')[0].value = '';
	singleChoiceCounter = 1;
	
	// remove multiple choice options
	document.getElementById('multipleChoiceColumns').value = 1;
	for (var i=1; i < multipleChoiceCounter; i++) {
		if (document.getElementsByName('multiplechoice_' + i)[0] != undefined) {
			$('#multiplechoice_'+i).remove();
		}
	}
	document.getElementsByName('multiplechoice_0')[0].value = '';
	multipleChoiceCounter = 1;
	
	// reset matrix
	showMatrixColumn(1);
	document.getElementById('matrixColumns').value = 1;
	for (var i=1; i <= 5; i++) {
		document.getElementsByName('matrix_column_'+i)[0].value = '';
	}
	for (var i=1; i < matrixCounter; i++) {
		if (document.getElementsByName('matrix_' + i)[0] != undefined) {
			$('#matrix_'+i).remove();
		}
	}
	document.getElementsByName('matrix_0')[0].value = '';
	matrixCounter = 1;
	
	// reset grading
	for (var i=1; i < gradingCounter; i++) {
		if (document.getElementById('grading_' + i) != undefined) {
			$('#grading_'+i).remove();
		}
	}
	document.getElementsByName('grading_0')[0].value = '';
	gradingCounter = 1;
	
	// reset tendency
	for (var i=1; i < tendencyCounter; i++) {
		if (document.getElementById('tendency_' + i) != undefined) {
			$('#tendency_'+i).remove();
		}
	}
	document.getElementsByName('tendency_0_0')[0].value = '';
	document.getElementsByName('tendency_0_1')[0].value = '';
	document.getElementById('tendency_steps').value = 2;
	tendencyCounter = 1;
	
	// move newquestion dialog to the end of sortable
	$('#newquestion').appendTo('#sortable');
}

/*
 * change the create dialog to display the dialog corresponding to type
 */
function changeCreateDialog(type) {
	switch (type) {
		case "0":
			$('#text_preview').show();
			$('#textarea_preview').hide();
			$('#singlechoice_preview').hide();
			$('#multiplechoice_preview').hide();
			$('#matrix_preview').hide();
			$('#grading_preview').hide();
			$('#tendency_preview').hide();
			break;
		case "1":
			$('#text_preview').hide();
			$('#textarea_preview').show();
			$('#singlechoice_preview').hide();
			$('#multiplechoice_preview').hide();
			$('#matrix_preview').hide();
			$('#grading_preview').hide();
			$('#tendency_preview').hide();
			break;
		case "2":
			$('#text_preview').hide();
			$('#textarea_preview').hide();
			$('#singlechoice_preview').show();
			$('#multiplechoice_preview').hide();
			$('#matrix_preview').hide();
			$('#grading_preview').hide();
			$('#tendency_preview').hide();
			break;
		case "3":
			$('#text_preview').hide();
			$('#textarea_preview').hide();
			$('#singlechoice_preview').hide();
			$('#multiplechoice_preview').show();
			$('#matrix_preview').hide();
			$('#grading_preview').hide();
			$('#tendency_preview').hide();
			break;
		case "4":
			$('#text_preview').hide();
			$('#textarea_preview').hide();
			$('#singlechoice_preview').hide();
			$('#multiplechoice_preview').hide();
			$('#matrix_preview').show();
			$('#grading_preview').hide();
			$('#tendency_preview').hide();
			break;
		case "5":
			$('#text_preview').hide();
			$('#textarea_preview').hide();
			$('#singlechoice_preview').hide();
			$('#multiplechoice_preview').hide();
			$('#matrix_preview').hide();
			$('#grading_preview').show();
			$('#tendency_preview').hide();
			break;
		case "6":
			$('#text_preview').hide();
			$('#textarea_preview').hide();
			$('#singlechoice_preview').hide();
			$('#multiplechoice_preview').hide();
			$('#matrix_preview').hide();
			$('#grading_preview').hide();
			$('#tendency_preview').show();
			break;
	}
}

// initiate sortable
$(function() {
	$( "#sortable" ).sortable({
		placeholder: "ui-state-highlight",
		axis: "y",
		forcePlaceholderSize: true
	});
});

/*
 * function to create single choice option
 */
function createSingleChoiceOption() {
	var asseturl = document.getElementById('asseturl').value;
	$('<li class="option_element" id="singlechoice_'+singleChoiceCounter+'" style="border:none;"><input name="singlechoice_'+singleChoiceCounter+'" type="text" value="" style="width: 95%;">&nbsp<input type="image" onclick="deleteSingleChoiceOption('+singleChoiceCounter+')" src="'+asseturl+'/delete.png" title="Löschen" width="12px" height="12px"></li>').appendTo($('#singlechoice_options'));
	singleChoiceCounter++;
}

/*
 * function to delete single choice option
 */
function deleteSingleChoiceOption(id) {
	$('#singlechoice_'+id).remove();
}

/*
 * function to create multiple choice option
 */
function createMultipleChoiceOption() {
	var asseturl = document.getElementById('asseturl').value;
	$('<li id="multiplechoice_'+multipleChoiceCounter+'" style="border:none;"><input name="multiplechoice_'+multipleChoiceCounter+'" type="text" value="" style="width: 95%;">&nbsp<input type="image" onclick="deleteMultipleChoiceOption('+multipleChoiceCounter+')" src="'+asseturl+'/delete.png" title="Löschen" width="12px" height="12px"></li>').appendTo($('#multiplechoice_options'));
	multipleChoiceCounter++;
}

/*
 * function to delete multiple choice option
 */
function deleteMultipleChoiceOption(id) {
	$('#multiplechoice_'+id).remove();
}

/*
 * function to create matrix option
 */
function createMatrixOption() {
	var asseturl = document.getElementById('asseturl').value;
	$('<li id="matrix_'+matrixCounter+'" style="border:none;"><input name="matrix_'+matrixCounter+'" type="text" value="" style="width: 95%;">&nbsp<input type="image" onclick="deleteMatrixOption('+matrixCounter+')" src="'+asseturl+'/delete.png" title="Löschen" width="12px" height="12px"></li>').appendTo($('#matrix_options'));
	matrixCounter++;
}

/*
 * function to delete matrix option
 */
function deleteMatrixOption(id) {
	$('#matrix_'+id).remove();
}

/*
 * function to show matrix column input fields (amount = count)
 */
function showMatrixColumn(count) {
	count = parseInt(count);
	for (var i=1; i <= count; i++) {
		document.getElementById('matrix_column_'+i).style.display = '';
	}
	for (var i=count+1; i <= 6; i++) {
		document.getElementById('matrix_column_'+i).style.display = 'none';
	}
}

/*
 * function to create grading option
 */
function createGradingOption() {
	var asseturl = document.getElementById('asseturl').value;
	$('<li id="grading_'+gradingCounter+'" style="border:none;"><input name="grading_'+gradingCounter+'" type="text" value="" style="width: 95%;">&nbsp<input type="image" onclick="deleteGradingOption('+gradingCounter+')" src="'+asseturl+'/delete.png" title="Löschen" width="12px" height="12px"></li>').appendTo($('#grading_options'));
	gradingCounter++;
}

/*
 * function to delete grading option
 */
function deleteGradingOption(id) {
	$('#grading_'+id).remove();
}

/*
 * function to create tendency option
 */
function createTendencyOption() {
	var asseturl = document.getElementById('asseturl').value;
	$('<li id="tendency_'+tendencyCounter+'" style="border:none;"><input type="text" name="tendency_'+tendencyCounter+'_0" value="" style="width: 30%;"> - - - <input type="text" name="tendency_'+tendencyCounter+'_1" value="" style="width: 30%;">&nbsp<input type="image" onclick="deleteTendencyOption('+tendencyCounter+')" src="'+asseturl+'/delete.png" title="Löschen" width="12px" height="12px"></li>').appendTo($('#tendency_options'));
	tendencyCounter++;
}

/*
 * function to delete tendency option
 */
function deleteTendencyOption(id) {
	$('#tendency_'+id).remove();
}

/*
 * function to initiate the element counter
 */
function initiateCounter() {
	elementCounter = document.getElementById("elementcounter").value;
}

/*
 * function to save the order in sortable
 */
function getSortables() {
	var result = $('#sortable').sortable('toArray');
	document.getElementById("sortable_array").value = result;
}

/*
 * function to delete a question
 */
function deleteElement(id) {
	var data = document.getElementsByName('element'+id)[0].value;
	data = data.split(',');
	var check = confirm('Frage "'+decodeURIComponent(data[1])+'" wirklich löschen?');
	if (check == true) {
		$('#element'+id).remove();
		$('input[name=element'+id+']').remove();
		$('input[name=element'+id+'_options]').remove();
		$('input[name=element'+id+'_rows]').remove();
		$('input[name=element'+id+'_columns]').remove();
	}
}

/*
 * function to open the edit question dialog for question id
 */
function editElement(id) {
	var data = document.getElementsByName('element'+id)[0].value;
	data = data.split(',');
	resetCreateDialog();
	document.getElementById('newquestion_button').style.display = '';
	document.getElementById('editID').value = id;
	$('#newquestion').insertBefore($('#element'+id));
	$('#element'+id).hide();
	changeCreateDialog(data[0]);
	document.getElementById('questionType').value = data[0];
	document.getElementById('questionText').value = decodeURIComponent(data[1]);
	document.getElementById('helpText').value = decodeURIComponent(data[2]);
	if (data[3] == 1) {
		document.getElementById('required').checked = true;
	}
	
	switch (data[0]) {
		case "2":
			var options = document.getElementsByName('element'+id+'_options')[0].value;
			options = options.split(',');
			for (var i=0; i < options.length; i++) {
				if (i != 0) {
					createSingleChoiceOption();
				}
				document.getElementsByName('singlechoice_' + i)[0].value = decodeURIComponent(options[i]);
			}
			document.getElementById('singleChoiceColumns').value = data[4];
			$('#newquestion').show();
			break;
		case "3":
			var options = document.getElementsByName('element'+id+'_options')[0].value;
			options = options.split(',');
			for (var i=0; i < options.length; i++) {
				if (i != 0) {
					createMultipleChoiceOption();
				}
				document.getElementsByName('multiplechoice_' + i)[0].value = decodeURIComponent(options[i]);
			}
			document.getElementById('multipleChoiceColumns').value = data[4];
			$('#newquestion').show();
			break;
		case "4":
			var columns = document.getElementsByName('element'+id+'_columns')[0].value;
			columns = columns.split(',');
			showMatrixColumn(columns.length);
			document.getElementById('matrixColumns').value = columns.length;
			for (var i=1; i <= columns.length; i++) {
				document.getElementsByName('matrix_column_'+i)[0].value = decodeURIComponent(columns[i-1]);
			}
			
			var rows = document.getElementsByName('element'+id+'_rows')[0].value;
			rows = rows.split(',');
			for (var i=0; i < rows.length; i++) {
				if (i != 0) {
					createMatrixOption();
				}
				document.getElementsByName('matrix_' + i)[0].value = decodeURIComponent(rows[i]);
			}
			$('#newquestion').show();
			break;
		case "5":
			var options = document.getElementsByName('element'+id+'_rows')[0].value;
			options = options.split(',');
			for (var i=0; i < options.length; i++) {
				if (i != 0) {
					createGradingOption();
				}
				document.getElementsByName('grading_' + i)[0].value = decodeURIComponent(options[i]);
			}
			$('#newquestion').show();
			break;
		case "6":
			var options = document.getElementsByName('element'+id+'_options')[0].value;
			options = options.split(',');
			for (var i=0; i < options.length; i=i+2) {
				if (i != 0) {
					createTendencyOption();
				}
				document.getElementsByName('tendency_' + (i/2) + '_0')[0].value = decodeURIComponent(options[i]);
				document.getElementsByName('tendency_' + (i/2) + '_1')[0].value = decodeURIComponent(options[i+1]);
			}
			document.getElementById('tendency_steps').value = data[4];
			$('#newquestion').show();
			break;
		default:
			$('#newquestion').show();
			break;
	}
}

/*
 * function to copy question id
 */
function copyElement(id) {
	var data = document.getElementsByName('element'+id)[0].value;
	data = data.split(',');
	switch(data[0]) {
		case "0":
			createTextQuestion(data, '#element'+id);
			break;
		case "1":
			createTextareaQuestion(data, '#element'+id);
			break;
		case "2":
			var options = document.getElementsByName('element'+id+'_options')[0].value;
			options = options.split(',');
			createSingleChoiceQuestion(data, options, '#element'+id);
			break;
		case "3":
			var options = document.getElementsByName('element'+id+'_options')[0].value;
			options = options.split(',');
			createMultipleChoiceQuestion(data, options, '#element'+id);
			break;
		case "4":
			var columns = document.getElementsByName('element'+id+'_columns')[0].value;
			columns = columns.split(',');
			var rows = document.getElementsByName('element'+id+'_rows')[0].value;
			rows = rows.split(',');
			createMatrixQuestion(data, columns, rows, '#element'+id);
			break;
		case "5":
			var options = document.getElementsByName('element'+id+'_rows')[0].value;
			options = options.split(',');
			createGradingQuestion(data, options, '#element'+id);
			break;
		case "6":
			var options = document.getElementsByName('element'+id+'_options')[0].value;
			options = options.split(',');
			createTendencyQuestion(data, options, '#element'+id);
			break;
	}
}

/*
 * function to add a new question
 */
function addElement() {
	var question = encodeURIComponent(document.getElementById('questionText').value);
	if (jQuery.trim(question) == '') {
		alert("Bitte einen Fragetext eingeben.");
		return;
	}
	var type = document.getElementById('questionType').value;
	if (document.getElementById('editID').value != '-1') {
		var deleteid = document.getElementById('editID').value;
		$('#element'+deleteid).remove();
		$('input[name=element'+deleteid+']').remove();
		$('input[name=element'+deleteid+'_options]').remove();
		$('input[name=element'+deleteid+'_rows]').remove();
		$('input[name=element'+deleteid+'_columns]').remove();
		document.getElementById('editID').value = '-1';
	}
	var helpText = encodeURIComponent(document.getElementById('helpText').value);
	if (document.getElementById('required').checked == true) {
		var required_text = '*';
		var required = 1;
	} else {
		var required_text = '';
		var required = 0;
	}
	switch (type) {
		case "0":
			var data = new Array(type, question, helpText, required);
			createTextQuestion(data, '#newquestion');
			hideCreateDialog();
			break;
		case "1":
			var data = new Array(type, question, helpText, required);
			createTextareaQuestion(data, '#newquestion');
			hideCreateDialog();
			break;
		case "2":
			var options = new Array();
			var arrangement = document.getElementById('singleChoiceColumns').value;
			for (var i=0; i <= singleChoiceCounter; i++) {
				if (document.getElementsByName('singlechoice_' + i)[0] != undefined) {
					options.push(encodeURIComponent(document.getElementsByName('singlechoice_' + i)[0].value));
				}
			}
			var data = new Array(type, question, helpText, required, arrangement);
			createSingleChoiceQuestion(data, options, '#newquestion');
			hideCreateDialog();
			break;
		case "3":
			var options = new Array();
			var arrangement = document.getElementById('multipleChoiceColumns').value;
			for (var i=0; i <= multipleChoiceCounter; i++) {
				if (document.getElementsByName('multiplechoice_' + i)[0] != undefined) {
					options.push(encodeURIComponent(document.getElementsByName('multiplechoice_' + i)[0].value));
				}
			}
			var data = new Array(type, question, helpText, required, arrangement);
			createMultipleChoiceQuestion(data, options, '#newquestion');
			hideCreateDialog();
			break;
		case "4":
			var countRows = document.getElementById('matrixColumns').value;
			var columns = new Array();
			for (var i=1; i <= countRows; i++) {
				columns.push(encodeURIComponent(document.getElementsByName('matrix_column_'+i)[0].value));
			}
			var rows = new Array();
			for (var i=0; i <= matrixCounter; i++) {
				if (document.getElementsByName('matrix_' + i)[0] != undefined) {
					rows.push(encodeURIComponent(document.getElementsByName('matrix_' + i)[0].value));
				}
			}
			var data = new Array(type, question, helpText, required);
			createMatrixQuestion(data, columns, rows, '#newquestion');
			hideCreateDialog();
			break;
		case "5":
			var options = new Array();
			for (var i=0; i <= gradingCounter; i++) {
				if (document.getElementsByName('grading_' + i)[0] != undefined) {
					options.push(encodeURIComponent(document.getElementsByName('grading_' + i)[0].value));
				}
			}
			var data = new Array(type, question, helpText, required);
			createGradingQuestion(data, options, '#newquestion');
			hideCreateDialog();
			break;
		case "6":
			var options = new Array();
			var steps = document.getElementById('tendency_steps').value;
			for (var i=0; i <= tendencyCounter; i++) {
				if (document.getElementById('tendency_' + i) != undefined) {
					options.push(encodeURIComponent(document.getElementsByName('tendency_' + i + '_0')[0].value));
					options.push(encodeURIComponent(document.getElementsByName('tendency_' + i + '_1')[0].value));
				}
			}
			var data = new Array(type, question, helpText, required, steps);
			createTendencyQuestion(data, options, '#newquestion');
			hideCreateDialog();
	}
}

/*
 * function to create textquestion
 */
function createTextQuestion(data, insertPoint) {
	params = {};
	params.questionType = data[0];
	params.questionText = data[1];
	params.questionHelp = data[2];
	params.questionRequired = data[3];
	params.questionID = elementCounter;	 
	var successFunction = function(response){
		responseData = jQuery.parseJSON(response); 
		$(responseData.html).insertBefore($(insertPoint));
	}
	var response = sendRequest('AddQuestion', params, 0, 'data', '', successFunction);
	elementCounter++;   			
}

/*
 * function to create textareaquestion
 */
function createTextareaQuestion(data, insertPoint) {
	var asseturl = document.getElementById('asseturl').value;
	var required_text = '';
	if (data[3] == 1) {
		required_text = '<font color="red"> *</font>';
	}
	$('<li class="ui-state-default" id="element'+elementCounter+'"><table width="100%" class="rapidfeedback_style"><tr><td align="right"><img onclick="editElement('+elementCounter+');" src="'+asseturl+'/edit.png" title="Bearbeiten" width="12px" height="12px" style="cursor: pointer; cursor: hand;">&nbsp<img onclick="copyElement('+elementCounter+');" src="'+asseturl+'/copy.png" title="Kopieren" width="12px" height="12px" style="cursor: pointer; cursor: hand;">&nbsp<img onclick="deleteElement('+elementCounter+');" src="'+asseturl+'/delete.png" title="Löschen" width="12px" height="12px" style="cursor: pointer; cursor: hand;"></td></tr><tr><td><h1>'+decodeURIComponent(data[1])+required_text+'</h1>'+decodeURIComponent(data[2])+'<br><textarea style="width: 99%;" disabled rows="4"></textarea></td></tr></table></li>').insertBefore($(insertPoint));
	$('<input type="hidden" name="element'+elementCounter+'" value="'+data+'">').insertBefore($(insertPoint));
	elementCounter++;
}

/*
 * function to create singlechoice question
 */
function createSingleChoiceQuestion(data, options, insertPoint) {
	var asseturl = document.getElementById('asseturl').value;
	var required_text = '';
	if (data[3] == 1) {
		required_text = '<font color="red"> *</font>';
	}
	var html = '<li class="ui-state-default" id="element'+elementCounter+'"><table width="100%" class="rapidfeedback_style"><tr><td align="right"><img onclick="editElement('+elementCounter+');" src="'+asseturl+'/edit.png" title="Bearbeiten" width="12px" height="12px" style="cursor: pointer; cursor: hand;">&nbsp<img onclick="copyElement('+elementCounter+');" src="'+asseturl+'/copy.png" title="Kopieren" width="12px" height="12px" style="cursor: pointer; cursor: hand;">&nbsp<img onclick="deleteElement('+elementCounter+');" src="'+asseturl+'/delete.png" title="Löschen" width="12px" height="12px" style="cursor: pointer; cursor: hand;"></td></tr><tr><td><h1>'+decodeURIComponent(data[1])+required_text+'</h1>'+decodeURIComponent(data[2])+'<br>';
	for (var i=0; i < options.length; i++) {
		html = html + '<input type="radio" name="singlechoice_'+elementCounter+'_'+i+'">'+ decodeURIComponent(options[i]) + '<br>';
	}
	html = html + '</td></tr></table></li>';
	$(html).insertBefore($(insertPoint));
	$('<input type="hidden" name="element'+elementCounter+'" value="'+data+'">').insertBefore($(insertPoint));
	$('<input type="hidden" name="element'+elementCounter+'_options" value="'+options+'">').insertBefore($(insertPoint));
	elementCounter++;
}

/*
 * function to create multiple choice question
 */
function createMultipleChoiceQuestion(data, options, insertPoint) {
	var asseturl = document.getElementById('asseturl').value;
	var required_text = '';
	if (data[3] == 1) {
		required_text = '<font color="red"> *</font>';
	}
	var html = '<li class="ui-state-default" id="element'+elementCounter+'"><table width="100%" class="rapidfeedback_style"><tr><td align="right"><img onclick="editElement('+elementCounter+');" src="'+asseturl+'/edit.png" title="Bearbeiten" width="12px" height="12px" style="cursor: pointer; cursor: hand;">&nbsp<img onclick="copyElement('+elementCounter+');" src="'+asseturl+'/copy.png" title="Kopieren" width="12px" height="12px" style="cursor: pointer; cursor: hand;">&nbsp<img onclick="deleteElement('+elementCounter+');" src="'+asseturl+'/delete.png" title="Löschen" width="12px" height="12px" style="cursor: pointer; cursor: hand;"></td></tr><tr><td><h1>'+decodeURIComponent(data[1])+required_text+'</h1>'+decodeURIComponent(data[2])+'<br>';
	for (var i=0; i < options.length; i++) {
		html = html + '<input type="checkbox" name="multiplechoice_'+elementCounter+'_'+i+'">'+ decodeURIComponent(options[i]) + '<br>';
	}
	html = html + '</td></tr></table></li>';
	$(html).insertBefore($(insertPoint));
	$('<input type="hidden" name="element'+elementCounter+'" value="'+data+'">').insertBefore($(insertPoint));
	$('<input type="hidden" name="element'+elementCounter+'_options" value="'+options+'">').insertBefore($(insertPoint));
	elementCounter++;
}

/*
 * function to create matrix question
 */
function createMatrixQuestion(data, columns, rows, insertPoint) {
	var asseturl = document.getElementById('asseturl').value;
	var required_text = '';
	if (data[3] == 1) {
		required_text = '<font color="red"> *</font>';
	}
	var html = '<li class="ui-state-default" id="element'+elementCounter+'"><table width="100%" class="rapidfeedback_style"><tr><td colspan="'+(columns.length+1)+'" align="right"><img onclick="editElement('+elementCounter+');" src="'+asseturl+'/edit.png" title="Bearbeiten" width="12px" height="12px" style="cursor: pointer; cursor: hand;">&nbsp<img onclick="copyElement('+elementCounter+');" src="'+asseturl+'/copy.png" title="Kopieren" width="12px" height="12px" style="cursor: pointer; cursor: hand;">&nbsp<img onclick="deleteElement('+elementCounter+');" src="'+asseturl+'/delete.png" title="Löschen" width="12px" height="12px" style="cursor: pointer; cursor: hand;"></td></tr><tr><td colspan="'+(columns.length+1)+'"><h1>'+decodeURIComponent(data[1])+required_text+'</h1>'+decodeURIComponent(data[2])+'</td></tr><tr><td></td>';
	for (var i=0; i < columns.length; i++) {
		html = html + '<td>'+decodeURIComponent(columns[i])+'</td>';
	}
	html = html + '</tr>';
	var currentRow = 0;
	for (var i=0; i < rows.length; i++) {
			html = html + '<tr>';
			for (var j=0; j <= columns.length; j++) {
				if (j == 0) {
					html = html + '<td>'+decodeURIComponent(rows[i])+'</td>';
				} else {
					html = html + '<td><input type="radio" name="matrix_'+elementCounter+'_'+currentRow+'"></td>';
				}
			}
		currentRow++;
		html = html + '</tr>';
	}
	html = html + '</table></li>';
	$(html).insertBefore($(insertPoint));
	$('<input type="hidden" name="element'+elementCounter+'" value="'+data+'">').insertBefore($(insertPoint));
	$('<input type="hidden" name="element'+elementCounter+'_columns" value="'+columns+'">').insertBefore($(insertPoint));
	$('<input type="hidden" name="element'+elementCounter+'_rows" value="'+rows+'">').insertBefore($(insertPoint));
	elementCounter++;
}

/*
 * function to create grading question
 */
function createGradingQuestion(data, options, insertPoint) {
	var asseturl = document.getElementById('asseturl').value;
	var columns = new Array('sehr gut', 'gut', 'befriedigend', 'ausreichend', 'mangelhaft', 'ungenügend');
	var required_text = '';
	if (data[3] == 1) {
		required_text = '<font color="red"> *</font>';
	}
	var html = '<li class="ui-state-default" id="element'+elementCounter+'"><table width="100%" class="rapidfeedback_style"><tr><td colspan="'+(columns.length+1)+'" align="right"><img onclick="editElement('+elementCounter+');" src="'+asseturl+'/edit.png" title="Bearbeiten" width="12px" height="12px" style="cursor: pointer; cursor: hand;">&nbsp<img onclick="copyElement('+elementCounter+');" src="'+asseturl+'/copy.png" title="Kopieren" width="12px" height="12px" style="cursor: pointer; cursor: hand;">&nbsp<img onclick="deleteElement('+elementCounter+');" src="'+asseturl+'/delete.png" title="Löschen" width="12px" height="12px" style="cursor: pointer; cursor: hand;"></td></tr><tr><td colspan="'+(columns.length+1)+'"><h1>'+decodeURIComponent(data[1])+required_text+'</h1>'+decodeURIComponent(data[2])+'</td></tr><tr><td></td>';
	for (var i=0; i < columns.length; i++) {
		html = html + '<td>'+decodeURIComponent(columns[i])+'</td>';
	}
	html = html + '</tr>';
	var currentRow = 0;
	for (var i=0; i < options.length; i++) {
			html = html + '<tr>';
			for (var j=0; j <= columns.length; j++) {
				if (j == 0) {
					html = html + '<td>'+decodeURIComponent(options[i])+'</td>';
				} else {
					html = html + '<td><input type="radio" name="grading_'+elementCounter+'_'+currentRow+'"></td>';
				}
			}
		currentRow++;
		html = html + '</tr>';
	}
	html = html + '</table></li>';
	$(html).insertBefore($(insertPoint));
	$('<input type="hidden" name="element'+elementCounter+'" value="'+data+'">').insertBefore($(insertPoint));
	$('<input type="hidden" name="element'+elementCounter+'_rows" value="'+options+'">').insertBefore($(insertPoint));
	elementCounter++;
}

function createTendencyQuestion(data, options, insertPoint) {
	var asseturl = document.getElementById('asseturl').value;
	var required_text = '';
	if (data[3] == 1) {
		required_text = '<font color="red"> *</font>';
	}
	var html = '<li class="ui-state-default" id="element'+elementCounter+'"><table width="100%" class="rapidfeedback_style"><tr><td align="right"><img onclick="editElement('+elementCounter+');" src="'+asseturl+'/edit.png" title="Bearbeiten" width="12px" height="12px" style="cursor: pointer; cursor: hand;">&nbsp<img onclick="copyElement('+elementCounter+');" src="'+asseturl+'/copy.png" title="Kopieren" width="12px" height="12px" style="cursor: pointer; cursor: hand;">&nbsp<img onclick="deleteElement('+elementCounter+');" src="'+asseturl+'/delete.png" title="Löschen" width="12px" height="12px" style="cursor: pointer; cursor: hand;"></td></tr><tr><td><h1>'+decodeURIComponent(data[1])+required_text+'</h1>'+decodeURIComponent(data[2])+'<br>';
	var counter = 0;
	for (var i=0; i < options.length; i = i+2) {
		html = html + options[i];
		for (var j=0; j < data[4]; j++) {
			html = html + '<input type="radio" name="tendency_'+elementCounter+'_'+counter+'">';
		}
		html = html + options[i+1] +'<br>';
		counter++;
	}
	html = html + '</td></tr></table></li>';
	$(html).insertBefore($(insertPoint));
	$('<input type="hidden" name="element'+elementCounter+'" value="'+data+'">').insertBefore($(insertPoint));
	$('<input type="hidden" name="element'+elementCounter+'_options" value="'+options+'">').insertBefore($(insertPoint));
	elementCounter++;
}

/*
 * function to initialize admin action fields for post request
 */
function admin_action(action, id, name) {
	if (action == 4) {
		var check = confirm('Umfrage '+name+' wirklich löschen?');
		if (check == true) {
			document.getElementById("admin_action").value = action;
			document.getElementById("element_id").value = id;
			document.getElementById("admin_option").submit();
		}
	} else {
		document.getElementById("admin_action").value = action;
		document.getElementById("element_id").value = id;
		document.getElementById("admin_option").submit();
	}
}