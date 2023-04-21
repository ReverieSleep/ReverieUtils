<?php
include('mysql.php');

$labelID = $mysql->escape_string($_GET['labelID']);
$query = "
SELECT 
	labels.po_number,
	labels.upc,
	labels.dc_name,
	labels.dc_number,
	labels.dc_address,
	labels.store_name,
	labels.store_number,
	labels.carrierID,
	labels.pro,
	labels.bol,
	labels.num_labels,
    labels.customer,
    labels.dept,
    labels.item_id,
    labels.line_no,
	counter.countID,
    carriers.carrier_name,
    carriers.scac
FROM labels
LEFT JOIN counter ON counter.labelID = labels.labelID
LEFT JOIN carriers ON labels.carrierID = carriers.carrierID
WHERE labels.labelID = '$labelID'";
$result = $mysql->query($query);



$html = '<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Label Portal</title>
</head>';
?>
<script type="text/javascript" src="scripts/jquery.js"></script>
<script type="text/javascript" src="scripts/jquery-barcode.js"></script>

<script type="text/javascript">
		window.matchMedia('print').addListener(function (media) {
			if(media.matches==false) {
				$.post('utils.php', {
					action: 'printed',
					labelID: $('#labelID').val(),
                  	po_no: $('#poValue').val(),
                  	upc: $('#upcValue').val(),
                  	line_no: $('#line_no').val()
				},function(data) {
					//window.opener.$("#itemsOnPO").load(location.href + " #itemsOnPO");
                  opener.location.reload();
				});
			}
		});
		
		window.onafterprint = function(e) {
			console.log(e);
			$.post('utils.php', {
				action: 'printed',
				labelID: $('#labelID').val(),
              	po_no: $('#poValue').val(),
	            upc: $('#upcValue').val()
			},function(data) {
				//window.opener.$("#itemsOnPO").load(location.href + " #itemsOnPO");
                  opener.location.reload();
			});
		}
		
	$(document).ready(function(e) {
        $('.labelBody').each(function(s) {
			var ssccValue = '(00)'+$(this).find('#ssccValue').val();
			var ssccReference = $(this).find('#ssccBarCode');
		
			ssccReference.barcode(ssccValue, "code128", {'barWidth':'2','barHeight':'90', 'fontSize':'20', 'addQuietZone': 'true' });

			var upcValue = $(this).find('#upcValue').val();
			var upcReference = $(this).find('#upcBlock');
			upcReference.barcode(upcValue, "ean13", {'barWidth':'1','barHeight':'50', 'fontSize':'12'});
          
          	var zipValue = '420'+$(this).find('#dc_zip').val();
          	var postalReference = $(this).find('#postalCodeBar');
          	postalReference.barcode(zipValue,"code128", {'barWidth':'2','barHeight':'40','fontSize':'0','addQuietZone':'true' });
		});
		
    });
</script>

<?php
$html.='
<style>
@font-face {
    font-family: code128;
    src: url("assets/fonts/code128.ttf");
}

* {
	padding: 0in;
	margin: 0in;
}

@page {
	margin: 0in;
}

body {
	font-family: Arial;
	height: 8.5in;
	width: 11in;
}

.floatRight {
	float: right;
}

b {
	display: block;
	margin-bottom: .085in;
}
.labelBody {
	display: inline-block;
	height: 6in;
	width: 4in;
	text-transform: uppercase;
	background-color: white;
	font-size: 9pt;
	margin-top: 1.25in;
	margin-bottom: 1.25in;
	margin-left: 1.375in;

}

.labelBody:nth-child(even) {
	margin-left: 0in;
	margin-left: .25in;
}

.labelBody:last-of-type {
	
}

#hDivider {
	height: 0in;
	width: 100%;
	border-top: 1px solid black;
	clear: both;
}

#fromBlock {
	float: left;
	padding: .085in;
	width: 1.405in;
	height: .83in;
	border-right: 1px solid black;
}

#tooBlock {
	float: left;
	padding: .085in;
	white-space: pre-line;
}

#postalCodeBlock {
	float: left;
	padding: .085in;
	width: 2.33in;
	height: .83in;
	border-right: 1px solid black;
}

#postalCodeText {
	width: 100%;
	text-align: center;
}

#postalCodeBar {
	width: 100%;
	text-align: center;
	//font-family: code128;
	//font-size: 64pt;
	height: .55in;
	overflow: hidden;
//	margin-top: -.085in;
	margin: 0.05in .25in 0 .15in;
}

#carrierBlock {
	float: left;
	padding: .085in;
}

#poBlock {
	float: left;
	padding: .085in;
	font-size: 24pt;
	font-weight: bold;
}

#upcBlock {
	float: right;
	margin-right: .125in;
	margin-top: .125in;
}

#storeBlock {
	float: right;
	padding: .085in;
	height: .83in;
	width: 1.83in;
	border-left: 1px solid black;
}

#storeBlock b {
	display: inline;
}

#storeBlock span {
	font-size: 18pt;
	font-weight: bold;
}

#storeNumber {
	font-size: 19pt;
	font-weight: bold;
}

#storeName {
	font-size: 9pt;
	width: 100%;
}

#division {
	font-weight: bold;
	font-size: 16pt;
}

#ssccBlock {
    width:100%;
	float: left;
	padding: .085in;
}

#sscc {
    width: calc(100% - .17in);
	text-align: center;
	margin-bottom: .25in;
}

#ssccBarCode {
	margin: -.175in .175in 0in -.25in;
	text-align: center;
}

#ssccBarCode img {
	height: 1.25in;
	width: 3.25in;
}
#ftr {
	padding-top:250px;
	padding-left:40%;
}
</style>
<body>';

while($row = $result->fetch_assoc()) {
	$row['po_number'];
	$row['dc_name'];
	$row['dc_number'];
	$row['dc_address'];
	$row['store_name'];
	$row['store_number'];
	$row['carrier_name'];
	$row['pro'];
	$row['bol'];
	$row['num_labels'];
	$row['countID'];
  	$row['customer'];
  	$row['dept'];
	$row['item_id'];
  
    $customer = $row['customer'];
	$dcaddress = array();
  	$dcaddress = explode(' ',$row['dc_address']);
    $dczip = substr($dcaddress[count($dcaddress)-1],strlen($dcaddress[count($dcaddress)-1])-5);
  
	$pro = $row['pro'];
	if($row['pro']=='') {
		$pro = $row['po_number'];
	}
	
	$increment = 10000000+$row['countID'];
	$sscc = sscc($increment);
	
	
	$html.='
	
	<div class="labelBody">
	
		<input type="hidden" value="'.$labelID.'" id="labelID" />
		<input type="hidden" value="'.$row['upc'].'" id="upcValue" />
        <input type="hidden" value="'.$row['po_number'].'" id="poValue" />
        <input type="hidden" value="'.$row['line_no'].'" id="line_no" />
        <input type="hidden" value="'.$dczip.'" id="dc_zip"/>
        <div id="fromBlock">';
  	//if ($customer == 'Bloomingdales'){
    /*	$html.='
        	<b>FROM</b>
            120 DART STREET<br />
            BUFFALO, NY<br />
            14213<br />';
    //}
    else {*/
    $html.='<b>FROM</b>
       2495 Walden Avenue<br />
       Suite 600<br/>
       Cheektowaga, NY<br />
       14225<br />';
    //}
    $html.='
        </div>
        <div id="tooBlock"><b>TO</b>'.$row['dc_name'].' #'.$row['dc_number'].'
			'.$row['dc_address'].'
        </div>
        <div id="hDivider"></div>
        
        <div id="postalCodeBlock">
        	<b>SHIP TO POSTAL CODE</b>
            <div id="postalCodeText">(420) '.$dczip.'</div>
            <div id="postalCodeBar"></div> <!-- 420'.$dczip.'</div>  -->
        </div>
        <div id="carrierBlock">
        	<b>CARRIER</b>
            '.$row['carrier_name'].'<br />
            PRO: '.$pro.'<br />
            BOL: '.$row['bol'].'
        </div>
        <div id="hDivider"></div>

        <div id="poBlock">
        	PO: '.$row['po_number'].'<br />
            DEPT: '.$row['dept'].'<br />
        </div>
		
		<div id="upcBlock">
		</div>
		
        <div id="hDivider"></div>
        
        <div id="storeBlock">
        	<div>
        		<b>FOR</b> '.$customer.'
			</div>
			
			<div>
				<b>ST#:</b>
				<span id="storeNumber">'.$row['store_number'].'</span>
			</div>
			<div>'.$row['store_name'].'</div>
            
            
            <div id="division">BEDDING</div>
        </div>
        <div id="hDivider"></div>
        
        <div id="ssccBlock">
			<input type="hidden" id="ssccValue" value="'.$sscc.'" />
        	<b>SERIAL SHIPPING CONTAINER</b>
            <div id="sscc">'.formatSscc($sscc).'</div>
            <div id="ssccBarCode">
            <!--
				<img src="https://barcode.tec-it.com/barcode.ashx?translate-esc=off&data=(00)'.$sscc.'&code=Code128&unit=Fit&dpi=96&imagetype=Gif&rotation=0&color=%23000000&bgcolor=%23ffffff&qunit=Mm&quiet=.25" alt="Barcode Generator TEC-IT"/>-->
            </div>
        </div>
        <div id="ftr">INTERNAL USE ONLY:<br/>'.$row['item_id'].'</div>
    </div>';
}
$html.='</body>
</html>';
echo $html;



function sscc($number) {
  	$prefix = '081746602';
	$num = $prefix.$number;
	$total = 0;
	$even = true;
	for($n=0;$n<18;$n++) {
		$char = intval(substr($num, $n,1));
		if($even==true) {
			$total = $total + ($char*3);
			$even = false;
		}else{
			$total = $total + $char;
			$even = true;
		}
	}
	
	$nearestTen = ceil($total/10)*10;
	$checkDigit = $nearestTen - $total;
	$num.=$checkDigit;
	return $num;
}

function formatSscc($sscc) {
	return '(00) '.substr($sscc,0,9).' '.substr($sscc,9);
}

?>