<?php
include('mysql.php');
include('mssql.php');

$action = $_POST['action'];
switch($action) {
	case 'createLabels':
		$poNumber = $mysql->escape_string($_POST['poNumber']);
		$upc = $mysql->escape_string($_POST['upc']);
		$dcName = $mysql->escape_string($_POST['dcName']);
		$dcNumber = $mysql->escape_string($_POST['dcNumber']);
		$dcAddress = $mysql->escape_string($_POST['dcAddress']);
		$storeName = $mysql->escape_string($_POST['storeName']);
		$storeNumber = $mysql->escape_string($_POST['storeNumber']);
		$pro = $mysql->escape_string($_POST['pro']);
		$bol = $mysql->escape_string($_POST['bol']);
		$numLabels = $mysql->escape_string($_POST['numLabels']);
  		$customer = $mysql->escape_string($_POST['customer']);
  		$dept = $mysql->escape_string($_POST['deptCode']);
		$itemID = $mysql->escape_string($_POST['itemID']);
		$carrierID = $mysql->escape_string($_POST['carrierID']);
		$arrivalDate = $mysql->escape_string($_POST['arrivalDate']);
  		$line_no = $mysql->escape_string($_POST['line_no']);
  
  		
		$query = "
		INSERT INTO labels (
			po_number,
			upc,
			dc_name,
			dc_number,
			dc_address,
			store_name,
			store_number,
			pro,
			bol,
			num_labels,
			carrierID,
			arrival_date,
            customer,
            dept,
            item_id,
            line_no
		) VALUES (
			'$poNumber',
			'$upc',
			'$dcName',
			'$dcNumber',
			'$dcAddress',
			'$storeName',
			'$storeNumber',
			'$pro',
			'$bol',
			$numLabels,
			'$carrierID',
			'$arrivalDate',
            '$customer',
            '$dept',
            '$itemID',
            '$line_no'
		)";

		$result = $mysql->query($query);
		$labelID = $mysql->insert_id;

		$query = "
		INSERT INTO counter (
			labelID
		) VALUES ";
		for($n=0;$n<$numLabels;$n++) {
			if($n!=$numLabels-1) {
				$comma = ',';
			}else{
				$comma = '';
			}
			$query.="
		($labelID)$comma";
		}
		
		$mysql->query($query);
		$data = array();
		$data['result'] = 'success';
		$data['labelID'] = $labelID;

		$query = "
			SELECT
				labels.labelID,
				counter.countID
			FROM labels
			LEFT JOIN counter ON labels.labelID = counter.labelID
			WHERE labels.labelID = '$labelID'";
		$result = $mysql->query($query);
        $mysscc = '';
		if($result) {
			while($row = $result->fetch_assoc()) {
				$increment = 10000000+$row['countID'];
				$sscc = sscc($increment);
                if($mysscc == ''){
                  $mysscc.=$sscc;
                }
                else{
                  $mysscc.=','.$sscc;
                }
               $ssccQry = "INSERT INTO sscc (po,upc,sscc) VALUES ('$poNumber','$upc','$sscc')";
               $mysql->query($ssccQry);
             }
             $myqry = "UPDATE labels SET sscc = '$mysscc' WHERE labelID = '$labelID'";
             $mysql->query($myqry);
		}
  
		echo json_encode($data);

	break;

	case 'login':
		$username = $mysql->escape_string($_POST['username']);
		$password = $mysql->escape_string($_POST['password']);
		/*$query = "SELECT userID FROM users WHERE username='$username' AND password='$password'";
		$result = $mysql->query($query);
		if($result) {
			if($result->num_rows==1) {
				$row = $result->fetch_assoc();
				session_start();
				$_SESSION['userID'] = $row['userID'];
				include('browser.php');
				header('location:labelinfo.php');
			}
		}else{
			session_start();
			session_destroy();
			//header('location:index.php?error=invalid');
		}*/
	    $query = "SELECT userID, password FROM users WHERE username='$username'";
        $result = $mysql->query($query) or die($mysql->error);
        $row = $result->fetch_assoc();
        $passwordHash = $row['password'];
	    if ($result){
  			if(password_verify($password, $passwordHash)) {
        		session_destroy();
	        	session_start();
	        	$_SESSION['userID'] = $row['userID'];
				include('browser.php');
				header('location:labelinfo.php');
	        }else{
    	    	session_destroy();
        		header('location:../index.php?message=invalid_password');
	        }
		}else{
			session_start();
			session_destroy();
			header('location:index.php?error=invalid');
		}

	break;
	
  	case 'getLineItems':
  		$po_no = $_POST['po'];
	  		$query = "SELECT line_no, item_id, extended_desc, customer_part_number, qty_ordered FROM p21_view_oe_line join p21_view_oe_hdr on p21_view_oe_line.order_no = p21_view_oe_hdr.order_no where p21_view_oe_hdr.order_no='$po_no' and p21_view_oe_line.delete_flag <> 'Y' and not exists (select null from cust_macys_po_portal_history a where a.upc= p21_view_oe_line.customer_part_number and a.po_no = p21_view_oe_hdr.po_no and a.line_no = p21_view_oe_line.line_no) order by line_no desc";
			$result=sqlsrv_query($mssqlConnection, $query);
  			$data = '';
			if($result) {
    	       while($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
					$data .= '
            	    <option value="'.$row['customer_part_number'].'" itemdesc="'.$row['extended_desc'].'" itemid="'.$row['item_id'].'" qty="'.$row['qty_ordered'].'" line="'.$row['line_no'].'">'.$row['item_id'].' - '.$row['customer_part_number'].'</option>';
	           }
    	    }
          
  		echo $data;
	break;
  
	case 'printed':
		$labelID = $mysql->escape_string($_POST['labelID']);
  		$po = $mysql->escape_string($_POST['po_no']);
  		$upc = $mysql->escape_string($_POST['upc']);
  		$line_no = $mysql->escape_string($_POST['line_no']);
		$query = "UPDATE labels SET printed=1 WHERE labelID = '$labelID'";
		$result = $mysql->query($query);
  		$msquery = "INSERT INTO cust_macys_po_portal_history (po_no,upc,line_no) VALUES ('$po','$upc','$line_no')";
		$msresult=sqlsrv_query($mssqlConnection, $msquery);
			
		$to = 'tim.munson@reverie.com';
		$subject = 'LABEL PORTAL - Label Printed';
		$message = 'Label ID '.$labelID.' printed';
		$header = 'From: Label Portal <label-portal@reverie.com>' . "\r\n";
		mail($to,$subject,$message,$header);
		
		mail('amanda@reverie.com',$subject,$message,$header);
	break;
  
	case 'createProduct':
		$description = $mysql->escape_string($_POST['description']);
		$upc = $mysql->escape_string($_POST['upc']);
  		$customer = $mysql->escape_string($_POST['customer']);
		$query = "
		INSERT INTO products (
			product_name,
			upc,
            customer
		) VALUES (
			'$description',
			'$upc',
            '$customer'
		)";
		$result = $mysql->query($query);
		$productID = $mysql->insert_id;
		
		echo '
		<tr id="product_'.$productID.'">
			<td>'.$description.'</td>
			<td>'.$upc.'</td>
			<td>'.$customer.'
			<button onclick="deleteProduct(\''.$productID.'\')">Delete</button></td>
		</tr>';
		
	break;
	
	case 'deleteProduct':
		$productID = $mysql->escape_string($_POST['productID']);
		if($productID!='') {
			$query = "DELETE FROM products WHERE productID = '$productID'";
			$result = $mysql->query($query);
		}
	break;
	
	case 'createCarrier':
		$carrierName = $mysql->escape_string($_POST['carrierName']);
		$scac = $mysql->escape_string($_POST['scac']);
		$query = "
		INSERT INTO carriers (
			carrier_name,
			scac
		) VALUES (
			'$carrierName',
			'$scac'
		)";
		$result = $mysql->query($query);
		$carrierID = $mysql->insert_id;
		echo '
		<tr id="carrier_'.$carrierID.'">
			<td>'.$carrierName.'</td>
			<td>
				'.$scac.'
				<button onclick="deleteCarrier(\''.$carrierID.'\')">Delete</button>
			</td>
		</tr>';
	break;
	
	case 'createCarrierFromModal':
		$carrierName = $mysql->escape_string($_POST['carrierName']);
		$scac = $mysql->escape_string($_POST['scac']);
		$query = "
		INSERT INTO carriers (
			carrier_name,
			scac
		) VALUES (
			'$carrierName',
			'$scac'
		)";
		$result = $mysql->query($query);
		$carrierID = $mysql->insert_id;
		echo '<option value="'.$carrierID.'">'.$carrierName.' - '.$scac.'</option>';
	break;
	
	case 'deleteCarrier':
		$carrierID = $mysql->escape_string($_POST['carrierID']);
		$query = "DELETE FROM carriers WHERE carrierID = '$carrierID'";
		$mysql->query($query);
	break;
	
	case 'showNewCarrierModal':
		echo '
		<table>
            	<thead>
                	<tr>
                    	<th colspan="2">New Carrier</th>
                    </tr>
                </thead>
            	<tr>
                	<td>Carrier Name</td>
                    <td><input type="text" id="carrierName" /> *</td>
                </tr>
                <tr>
                	<td>SCAC Code</td>
                    <td>
                    	<input type="text" id="carrierSCAC" /> *
                        <button id="buttonAddCarrier" onclick="createCarrierFromModal()">Add Carrier</button>
                    </td>
                </tr>
            </table>';
	break;
	
	case 'viewASNDetails':
	break;	
	
  	case 'getLabelDetails':
	  echo $mysql->escape_string($_POST['labelID']);
  	break;
  
	case 'createASN':
		$labelID = $mysql->escape_string($_POST['labelID']);
		$query = "
		SELECT 
			labels.po_number,
			labels.dc_number,
			labels.dc_address,
			labels.store_number,
			labels.pro,
			labels.bol,
			DATE(labels.date_created) AS date_created,
			labels.arrival_date,
			carriers.carrierID,
			carriers.carrier_name,
			carriers.scac
		FROM labels 
		LEFT JOIN carriers ON carriers.carrierID = labels.carrierID
		WHERE labels.labelID = '$labelID'";
		$result = $mysql->query($query);
		$response = '';
		if($result) {
			$row = $result->fetch_assoc();
			$poNumber = $row['po_number'];
			$controlNumber = $labelID;
			$controlNumberPadded = str_pad($labelID,9,'0',STR_PAD_LEFT  );
			
			$pro = $row['pro'];
			if($row['pro']=='') {
				$pro = $row['po_number'];
			}
			
			
			$response.= '
			<input type="hidden" id="asn_controlNumber" value="'.$controlNumber.'"/>
			<table>
				<thead>
					<tr>
						<th colspan="4">Order Details</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>Control Number #:</td>
						<td><input type="text" id="asn_controlNumberPadded" value="'.$controlNumberPadded.'"/></td>
						
						<td>SCAC Code:</td>
						<td><input type="text" id="asn_scac" value="'.$row['scac'].'"/></td>
					</tr>
					<tr>
						<td>PO Number</td>
						<td><input type="text" id="asn_poNumber" value="'.$row['po_number'].'"/></td>
						
						<td>Pro:</td>
						<td><input type="text" id="asn_pro" value="'.$pro.'"/></td>
					</tr>
					<tr>
						<td>DC Number:</td>
						<td><input type="text" id="asn_dc" value="'.$row['dc_number'].'"/></td>
						
						<td>BOL:</td>
						<td><input type="text" id="asn_bol" value="'.$row['bol'].'"/></td>
					</tr>
					<tr>
						<td>Store Number:</td>
						<td><input type="text" id="asn_storeNumber" value="'.$row['store_number'].'"/></td>
						
						<td>Ship Date:</td>
						<td><input type="text" id="asn_shipDate" value="'.$row['date_created'].'"/></td>
					</tr>
					<tr>
						<td>Carrier:</td>
						<td><input type="text" id="asn_carrier" value="'.$row['carrier_name'].'"/></td>
						
						<td>Arrival Date:</td>
						<td><input type="text" id="asn_arrivalDate" value="'.$row['arrival_date'].'" placeholder="YYYY-MM-DD"/></td>
					</tr>

				</tbody>
			</table>
			<table>
				<thead>
					<tr>
						<th>UPC</th>
						<th>MAN</th>
					</tr>
				</thead>
				<tbody id="itemList">';
			
			$query = "
			SELECT
				labels.labelID,
				labels.upc,
				labels.num_labels,
				counter.countID
			FROM labels
			LEFT JOIN counter ON labels.labelID = counter.labelID
			WHERE labels.labelID = '$labelID'";
			$result = $mysql->query($query);
          	$mysscc = '';
			if($result) {
				while($row = $result->fetch_assoc()) {
					$increment = 10000000+$row['countID'];
					$sscc = sscc($increment);
                  if($mysscc == ''){
                    $mysscc.=$sscc;
                  }
                  else{
                    $mysscc.=','.$sscc;
                  }
					$response.= '
					<tr>
						<td id="upcRow">'.$row['upc'].'</td>
						<td id="ssccRow">'.$sscc.'</td>
					</tr>';
					
				}
            $myqry = "UPDATE labels SET sscc = '$mysscc' WHERE labelID = '$labelID'";
            $mysql->query($myqry);
            }

			$response.= '
				</tbody>
			</table>
			<button onclick="generateASN()">Generate ASN</button>
			<textarea id="ediTextDisplay" placeholder="EDI Output Text"></textarea>';
		}
		
		echo $response;
	break;
	
	case 'createDC':
		$dcName = $mysql->escape_string($_POST['dcName']);
		$dcNumber = $mysql->escape_string($_POST['dcNumber']);
		$dcAddress = $mysql->escape_string($_POST['dcAddress']);
		$query = "
		INSERT INTO dc (
			dc_name,
			dc_number,
			dc_address
		) VALUES (
			'$dcName',
			$dcNumber,
			'$dcAddress'
		)";
		$result = $mysql->query($query);
		$dcID = $mysql->insert_id;
		$dcAddress = str_replace('\n','',$dcAddress);
		$dcAddress = str_replace('\r','',$dcAddress);
		echo '
		<tr id="dc'.$dcID.'">
			<td>'.$dcName.'</td>
			<td>'.$dcNumber.'</td>
			<td>'.$dcAddress.'</td>
			<td>
				<button onclick="deleteDC(\''.$dcID.'\')">Delete</button>
			</td>
		</tr>';
	
	break;
	
	case 'deleteDC':
		$dcID = $mysql->escape_string($_POST['dcID']);
		$query = "DELETE FROM dc WHERE dcID='$dcID'";
		echo $query;
		$result = $mysql->query($query);
	break;
  
}


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

?>