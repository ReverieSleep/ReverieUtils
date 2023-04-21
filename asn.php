<?php
//set up the connection to the database
/*
$host = 'jcaholdings.com';
$user = 'jcaholdi_label';
$password = 'Dream_2016';
$db = 'jcaholdi_label';
*/
$host = 'localhost';
$user = 'web';
$password = 'Dr3@m_2017';
$db = 'bloomingdales';


$mysql = new mysqli($host,$user,$password);
$mysql->select_db($db);

//get all items that haven't been process yet
$query = "
		SELECT 
			labels.labelID,
			labels.po_number,
			labels.dc_number,
			labels.dc_address,
			labels.store_number,
			labels.pro,
			labels.bol,
			labels.upc,
			DATE(labels.date_created) AS date_created,
			labels.arrival_date,
			carriers.carrierID,
			carriers.carrier_name,
			carriers.scac,
			counter.countID
		FROM labels 
		LEFT JOIN counter ON labels.labelID = counter.labelID
		LEFT JOIN carriers ON carriers.carrierID = labels.carrierID
		WHERE labels.asn_sent=0";
$result = $mysql->query($query);

$pos = array();
if($result) {
	while($row = $result->fetch_assoc()) {
		$poNumber = $row['po_number'];
		if(!isset($pos[$poNumber])) {
			$pos[$poNumber] = array();
		}
		array_push($pos[$poNumber],$row);
	}
}

$completed = array();

//step through each po number
foreach($pos as $key=>$value) {
	$items = array();
	$poNumber = $key;
	$controlNumber;
    $dc_number;
    $dc_address;
    $store_number;
    $pro;
    $bol;
    $upc;
    $num_labels;
    $date_created;
    $arrival_date;
    $carrierID;
    $carrier_name;
    $scac;
		
	//step through each entry for the po number
	//grab the upc and number of labels printed
	//for that upc and push into the items array
	for($n=0;$n<count($pos[$key]);$n++) {
		$controlNumber = $pos[$key][$n]['labelID'];
		$dc_number = $pos[$key][$n]['dc_number'];
		$dc_address = $pos[$key][$n]['dc_address'];
		$store_number = $pos[$key][$n]['store_number'];
		$pro = $pos[$key][$n]['pro'];
		$bol = $pos[$key][$n]['bol'];
		$date_created = str_replace('-','',$pos[$key][$n]['date_created']);
		$arrival_date = str_replace('-','',$pos[$key][$n]['arrival_date']);
		$carrierID = $pos[$key][$n]['carrierID'];
		$carrier_name = $pos[$key][$n]['carrier_name'];
		$scac = $pos[$key][$n]['scac'];

		$upc = $pos[$key][$n]['upc'];
		$temp = array();
		$temp['upc'] = $upc;
		$temp['count'] = $pos[$key][$n]['countID'];
		array_push($items,$temp);
	}
	$controlNumberPadded = str_pad($controlNumber,9,'0',STR_PAD_LEFT  );
	
	//now, we generate the ASN
	//start with the header
	$asn = 'ISA*00*          *00*          *01*079471969TPC   *08*6113310072     *161208*0847*U*00501*'.

$controlNumberPadded.'*1*P*>~
GS*SH*079471969TPC*6113310072*'.$date_created.'*1000*'.$controlNumberPadded.'*X*005010VICS~
ST*856*'.$controlNumberPadded.'~
BSN*00*'.$controlNumberPadded.'*'.$arrival_date.'*100000*0002~
HL*1**S~
TD1*CTN*5*****250*LB~
TD5**2*'.$scac.'**'.$carrier_name.'~
REF*BM*'.$bol.'~
DTM*011*'.$date_created.'*08472964~
DTM*067*'.$arrival_date.'~
N1*ST**92*'.$dc_number.'~
N1*SF*Ascion LLC, dba Reverie*92*reverie~
N3*8800 S Main~
N4*Eden*NY*14057~
HL*2*1*O~
PRF*'.$poNumber.'~
PID*S**VI*FL~
TD1*CTN25*1~
N1*BY**'.$store_number.'~'.PHP_EOL;
	
	$segmentCount = 19;
	$count = 3;
	//step through each item and create the item entry
	for($n=0;$n<count($items);$n++) {
		$increment = 10000000+$items[$n]['count'];
		$sscc = sscc($increment);
		$upc = $items[$n]['upc'];
		$itemLevel = $count;
		$packLevel = $count+1;
		$asn.='HL*'.$itemLevel.'**I~
LIN**UP*'.$upc.'~
SN1**1*EA~
HL*'.$packLevel.'**P~
PO4**1*EA~
MAN*GM*00'.$sscc.'~'.PHP_EOL;
		$count = $count+2;
		$segmentCount = $segmentCount+6;
	}
	
$asn.='CTT*'.$segmentCount.'~
SE*'.$segmentCount.'*'.$controlNumberPadded.'~
GE*1*'.$controlNumberPadded.'~
IEA*1*'.$controlNumberPadded.'~';
	
	//write the file to the asn folder
	$filepath = 'C:\\labels\\asn\\'.$poNumber.'.edi';
	$file = fopen($filepath, "w");
	fwrite($file, $asn);
	fclose($file);
	
	//add the labelID to the completed array
	array_push($completed, $poNumber);
}


//mark all the labels as asn_sent=1
$list = implode("','",$completed);
$list = "'".$list."'";
$query = "UPDATE labels SET asn_sent=1, asn_date=CURRENT_TIMESTAMP WHERE po_number IN ($list)";
$mysql->query($query);

//step through all the files in the asn folder
//copy each file to the outbound messages folder
//then move the file in to the completed folder



$path = "C:\\labels\\asn\\";

if ($handle = opendir($path)) {
    while (false !== ($file = readdir($handle))) {
        if ('.' === $file) continue;
        if ('..' === $file) continue;
		if($file!='complete') {//only grab the files, ignore the foler
			echo 'File: '.$file.PHP_EOL;
			$source = 'C:\\labels\\asn\\'.$file;
			$target = 'C:\\TPCXSpoke\\tpcx\\spoke0\\messages\\outbound\\'.$file;
			copy($source,$target);

			$target = 'C:\\labels\\asn\\complete\\'.$file;
			copy($source,$target);
		}
    }
    closedir($handle);
}










function sscc($number) {
	$num = '081746602'.$number;
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
