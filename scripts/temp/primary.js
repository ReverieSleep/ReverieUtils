// JavaScript Document
$(document).ready(function(e) {
    window.onafterprint= function() {
		console.log('printed!');
	}
});

function validateForm() {
	console.log($('#carrierSelection option:selected').attr('value'));
	if($('#poNumber').val()=='') {
		alert('PO number is required');
		
	}else if($('#dcName').val()=='') {
		alert('DC name is required');
		
	}else if($('#dcNumber').val()=='') {
		alert('DC number is required');
		
	}else if($('#dcAddress').val()=='') {
		alert('DC address is required');
		
	}else if($('#storeName').val()=='') {
		alert('Store name is required');
		
	}else if($('#storeNumberTextBox').val()=='') {
		alert('Store number is required');
		
	}else if($('#bol').val()=='') {
		alert('BOL number is required');
		
	}else if($('#numLabels').val()=='') {
		alert('Number of labels is required');
		
	}else if($('#carrierSelection option:selected').attr('value')=='addNewCarrier' ||
			$('#carrierSelection option:selected').attr('value')=='') {
		alert('A carrier must be selected');
	}else{	
		var arrivalDate = $('#arrivalYear').val()+'-'+$('#arrivalMonth option:selected').val()+'-'+$('#arrivalDay option:selected').val();
	
		$.post('utils.php', {
			action: 'createLabels',
			poNumber: $('#poNumber').val(),
			upc: $('#upc').val(),
			dcName: $('#dcName').val(),
			dcNumber: $('#dcNumber').val(),
			dcAddress: $('#dcAddress').val(),
			storeName: $('#storeName').val(),
			storeNumber: $('#storeNumberTextBox').val(),
			pro: $('#pro').val(),
			bol: $('#bol').val(),
			numLabels: $('#numLabels').val(),
			carrierID: $('#carrierSelection option:selected').attr('value'),
			arrivalDate: arrivalDate
		},function(data) {
			console.log(data);
			data = JSON.parse(data);
			if(data.result=='success') {
				window.open('label.php?labelID='+data.labelID);
			}
		});
	}
}



function createProduct() {
	if($('#description').val()=='') {
		alert('A product description is required');
	}else if($('#upc').val()=='') {
		alert('A UPC is required');
	}else{
		$.post('utils.php', {
			action: 'createProduct',
			description: $('#description').val(),
			upc: $('#upc').val()
		},function(data) {
			$('#productList').append(data);
			$('#description').val('');
			$('#upc').val('');
		});
	}
}

function deleteProduct(productID) {
	$.post('utils.php', {
		action: 'deleteProduct',
		productID: productID
	},function(data) {
		$('#product_'+productID).remove();
	});
}

function createCarrierFromModal() {
	if($('#carrierName').val()=='') {
		alert('A carrier name is required');
	}else if($('#carrierSCAC').val()=='') {
		alert('An SCAC code is required');
	}else{
		var carrierName = $('#carrierName').val().toUpperCase();
		var scac = $('#carrierSCAC').val().toUpperCase();
		$.post('utils.php', {
			action: 'createCarrierFromModal',
			carrierName: carrierName,
			scac: scac,
		},function(data) {
			$('#carrierSelection').append(data);
			hideModal();
		});
	}
}

function createCarrier() {
	if($('#carrierName').val()=='') {
		alert('A carrier name is required');
	}else if($('#carrierSCAC').val()=='') {
		alert('An SCAC code is required');
	}else{
		var carrierName = $('#carrierName').val().toUpperCase();
		var scac = $('#carrierSCAC').val().toUpperCase();
		$.post('utils.php', {
			action: 'createCarrier',
			carrierName: carrierName,
			scac: scac,
		},function(data) {
			$('#carrierList').append(data);
			$('#carrierName').val('');
			$('#carrierSCAC').val('');
		});
	}
}

function deleteCarrier(carrierID) {
	$.post('utils.php', {
		action: 'deleteCarrier',
		carrierID: carrierID
	},function(data) {
		$('#carrier_'+carrierID).remove();
	});
}

function checkCarrierSelection() {
	if($('#carrierSelection option:selected').attr('value')=='addNewCarrier') {
		showModal('Create Carrier');
		$.post('utils.php', {
			action: 'showNewCarrierModal'
		},function(data) {
			$('#modalContent').html(data);
		});
	}
}




function createDC() {
	if($('#dcName').val()=='') {
		alert('A DC name is required');
	}else if($('#dcNumber').val()=='') {
		alert('A DC number is required');
	}else if($('#dcAddress').val()=='') {
		alert('A DC address is required');
	}else{
		$.post('utils.php', {
			action: 'createDC',
			dcName: $('#dcName').val(),
			dcNumber: $('#dcNumber').val(),
			dcAddress: $('#dcAddress').val(),
		},function(data) {
			$('#dcList').append(data);
			$('#dcName').val('');
			$('#dcNumber').val('');
			$('#dcAddress').val('');
		});
	}
}

function deleteDC(dcID) {
	$.post('utils.php', {
		action: 'deleteDC',
		dcID: dcID
	},function(data) {
		$('#dc'+dcID).remove();
	});
}

function populateDC() {
	if($('#dcPreset').val()!='') {
		var dcName = $('#dcPreset option:selected').attr('dcname');
		var dcAddress = $('#dcPreset option:selected').attr('address');
		var dcNumber = $('#dcPreset option:selected').attr('number');
		
		$('#dcName').val(dcName);
		$('#dcNumber').val(dcNumber);
		$('#dcAddress').val(dcAddress);
	}else{
		$('#dcName').val('');
		$('#dcNumber').val('');
		$('#dcAddress').val('');
	}
}

function viewLabelDetails(labelID) {
	showModal('Label Details - '+labelID);
	$.post('utils.php', {
		action: 'getLabelDetails',
		labelID: labelID
	},function(data) {
		
	});
}

function createASN(labelID) {
	showModal('Create ASN');
	$.post('utils.php', {
		action: 'createASN',
		labelID: labelID
	},function(data) {
		$('#modalContent').html(data);
	});
}

function generateASN() {
	if($('#asn_controlNumberPadded').val()=='') {
		alert('An invoice number is required')
		
	}else if($('#asn_scac').val()=='') {
		alert('An SCAC is required')
		
	}else if($('#asn_poNumber').val()=='') {
		alert('A PO number is required')
		
	}else if($('#asn_pro').val()=='') {
		alert('A PRO number is required')
		
	}else if($('#asn_dc').val()=='') {
		alert('A DC number is required')
		
	}else if($('#asn_bol').val()=='') {
		alert('A BOL is required')
		
	}else if($('#asn_storeNumber').val()=='') {
		alert('A store number is required')
		
	}else if($('#asn_shipDate').val()=='') {
		alert('A ship date is required')
		
	}else if($('#asn_carrier').val()=='') {
		alert('A carrier is required')
		
	}else if($('#asn_arrivalDate').val()=='') {
		alert('A arrival date is required')
		
	}else{
		//format ship and arrival dates
		var controlNumberPadded = $('#asn_controlNumberPadded').val();
		var controlNumber = $('#asn_controlNumber').val();
		var shipDate = $('#asn_shipDate').val().replace(/ /g,'').replace(/-/g,'');
		var arriveDate = $('#asn_arrivalDate').val().replace(/ /g,'').replace(/-/g,'');
		var scac = $('#asn_scac').val();
		var carrier = $('#asn_carrier').val();
		var bol = $('#asn_bol').val();
		var poNumber = $('#asn_poNumber').val();
		var dc = $('#asn_dc').val();
		
		var stCount = 19;
		var edi = 'ISA*00*          *00*          *01*079471969TPC   *08*6113310072     *161208*0847*U*00501*'+controlNumberPadded+'*1*P*>~\n';
			edi+= 'GS*SH*079471969TPC*6113310072*'+shipDate+'*1000*'+controlNumberPadded+'*X*005010VICS~\n';
			edi+= 'ST*856*'+controlNumberPadded+'~\n';
			edi+= 'BSN*00*'+controlNumberPadded+'*'+arriveDate+'*100000*0002~\n';
			edi+= 'HL*1**S~\n';
			edi+= 'TD1*CTN*5*****250*LB~\n';
			edi+= 'TD5**2*'+scac+'**'+carrier+'~\n';
			edi+= 'REF*BM*'+bol+'~\n';
			edi+= 'DTM*011*'+shipDate+'*08472964~\n';
			edi+= 'DTM*067*'+arriveDate+'~\n';
			edi+= 'N1*ST**92*KM~\n';
			edi+= 'N1*SF*Ascion LLC, dba Reverie*92*reverie~\n';
			edi+= 'N3*8800 S Main~\n';
			edi+= 'N4*Eden*NY*14057~\n';
			edi+= 'HL*2*1*O~\n';
			edi+= 'PRF*'+poNumber+'~\n';
			edi+= 'PID*S**VI*FL~\n';
			edi+= 'TD1*CTN25*1~\n';
			edi+= 'N1*BY**'+dc+'~\n';
		var count = 3;

		$('#itemList').find('tr').each(function() {
			var upc = $(this).children('#upcRow').html();
			var sscc = $(this).children('#ssccRow').html();
			var itemLevel = count;
			var packLevel = count+1;
			edi+= 'HL*'+itemLevel+'**I~\n';
			edi+= 'LIN**UP*'+upc+'~\n';
			edi+= 'SN1**1*EA~\n';
			edi+= 'HL*'+packLevel+'**P~\n';
			edi+= 'PO4**1*EA~\n';
			edi+= 'MAN*GM*00'+sscc+'~\n';
			count++;
			count++
			stCount+=6
		});
		
		count = count-1;
		edi+= 'CTT*'+count+'~\n';
		edi+= 'SE*'+stCount+'*'+controlNumberPadded+'~\n';
		edi+= 'GE*1*'+controlNumberPadded+'~\n';
		edi+= 'IEA*1*'+controlNumberPadded+'~\n';
		
		$('#ediTextDisplay').html(edi);
	}
}








function showModal(title) {
	$('#modalContainer').css({
		'height':'100%',
		'width':'100%'
	});
	$('#modalTitle').html(title);
}

function hideModal() {
	$('#modalContainer').css({
		'height':'0px',
		'width':'0px'
	});
	$('#modalTitle').html('');
	$('#modalContent').html('');
}


function pad(pad, str, padLeft) {
  if (typeof str === 'undefined') 
    return pad;
  if (padLeft) {
    return (pad + str).slice(-pad.length);
  } else {
    return (str + pad).substring(0, pad.length);
  }
}