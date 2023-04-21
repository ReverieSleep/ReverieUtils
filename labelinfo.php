<?php
session_start();
if(empty($_SESSION['userID'])) {
	session_destroy();
	header('location:index.php');
	exit;
}
include('functions.php');
include('mysql.php');
include('mssql.php');
?>


<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Label Portal</title>
<link href="styles/primary.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="scripts/jquery.js"></script>
<script type="text/javascript" src="scripts/primary.js"></script>
</head>

<body>
    <div id="mypopup" style="position:absolute;top:20%;width:300px;height:200px;padding-left:35%;text-align:center;display:none;z-index:100">
      <button id="continuebutton" onclick="continueNext();">Continue?</button>
    </div>
	<div id="modalContainer">
    	<div id="modalBody">
        	<div id="modalHeader">
            	<div id="modalTitle">Modal Title</div>
                <div id="modalClose">
                	<button onclick="hideModal()">Close</button>
                </div>
            </div>
            <div id="modalContent">
            </div>
        </div>
    </div>

	<div id="heading">
    	<div id="menu">
    	<?php drawMenu(); ?>
        </div>
    </div>

    <div id="container">
    	<div id="content">
        	<h2>Label Details</h2>
            All fields marked with an * are required.
            <divider></divider>
            <table>
            	<thead>
                    <tr>
                        <th colspan="2">Order</th>
                        
                    </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>Macy's/Bloomingdale's PO</td><td><select id="poNumber" onchange="populateHeaderData()"><option>-Select PO-</option>
                    <?php
					$query="SELECT * from cust_view_open_macys_label_hdr";
                    $result=sqlsrv_query($mssqlConnection, $query);
					if($result) {
                      while($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
                        echo '
						<option customer="'.$row['customer'].'" value="'.$row['po_no'].'" order_no="'.$row['order_no'].'" dept="'.$row['dept'].'" storeNo="'.$row['store_number'].'" storeName="'.$row['store_name'].'">'.$row['customer']. ' PO: ' .$row['po_no'].'</option>';
                      }
                    }
					?>
                    </select>
                    </td>
                </tbody>
			</table>
            <divider></divider>
            
            <table>                
				<thead>
                    <tr>
                        <th colspan="2">
                        	Store<!-- - Choose a store from the Presets, store and DC information will automatically populate-->
                        </th>
                    </tr>
                </thead>
                
                <tbody>
                   
                    <tr>
                        <td>Store Name:</td>
                        <td><input type="text" id="storeName" /> *</td>
                    </tr>
                    <tr>
                        <td>Store Number:</td>
                        <td><input type="text" id="storeNumberTextBox" /> *</td>
                    </tr>
                  	<tr>
                      <td>Dept:</td>
                      <td><input type="text" id="deptCode" value="0671"/> *</td>
                  </tr>

                </tbody>
			</table>
           
			<divider></divider>
            
            <table>                
				<thead>
                    <tr>
                        <th colspan="3">DC</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>DC Name:</td>
                        <td>
                        	
                        	<input type="text" id="dcName" /> *
                           	
                            &nbsp;
                            &nbsp;
                            <select onChange="populateDC()" id="dcPreset">
                            	<option value="">Presets</option>
                            	<?php
								$query = "
								SELECT 
									dc_name,
									dc_number,
									dc_address,
									dc_code
								FROM dc";
								$result = $mysql->query($query);
								if($result) {
									while($row = $result->fetch_assoc()) {
										//$number = str_pad($row['dc_number'],4,"0",STR_PAD_LEFT);
										$number = $row['dc_number'];
										$address = $row['dc_address'];
										$name = $row['dc_name'];
										$code = $row['dc_code'];
										echo '
										<option address="'.$address.'" number="'.$number.'" dcname="'.$name.'" dccode="'.$code.'">'.$name.' - '.$number.'</option>';
									}
								}
								?>
                            </select>
                            
                            </td>
                    </tr>
                    <tr>
                        <td>DC Number:</td>
                        <td><input type="text" id="dcNumber" /> *</td>
                    </tr>
                    <tr>
                        <td>DC Address:</td>
                        <td>
                            <textarea colw="20" rows="3" id="dcAddress"></textarea> *
                        </td>
                    </tr>
                </tbody>
			</table>
            <divider></divider>
            
            
            <divider></divider>
            
            <table>                
				<thead>
                    <tr>
                        <th colspan="3">Carrier</th>
                    </tr>
                </thead>
                
                <tbody>
                    <tr>
                        <td>Carrier Name:</td>
                        <td>
                        	<select id="carrierSelection" onchange="checkCarrierSelection()">
                            	<option value="0"></option>
                                <option value="addNewCarrier">Add New Carrier</option>
                            	<?php
								$query = "
								SELECT 
									carrierID,
									carrier_name,
									scac
								FROM carriers";
								$result = $mysql->query($query);
								if($result) {
									while($row = $result->fetch_assoc()) {
										echo '<option value="'.$row['carrierID'].'">'.$row['carrier_name'].' - '.$row['scac'].'</option>';
									}
								}
								?>
                            </select> *
                        </td>
                    </tr>
                    <tr>
                        <td>PRO:</td>
                        <td><input type="text" id="pro" /></td>
                    </tr>
                    <tr>
                        <td>BOL:</td>
                        <td><input type="text" id="bol" /> *</td>
                    </tr>
                    <tr style="display:none">
                        <td>Est. Arrival Date:</td>
                        <td>
                        	<!--<input type="text" id="arrivalDate" placeholder="YYYY-MM-DD"/>-->
                            Day: 
                            <select id="arrivalDay">
                            	<option value="01">01</option>
                                <option value="02">02</option>
                                <option value="03">03</option>
                                <option value="04">04</option>
                                <option value="05">05</option>
                                <option value="06">06</option>
                                <option value="07">07</option>
                                <option value="08">08</option>
                                <option value="09">09</option>
                                <option value="10">10</option>
                                <option value="11">11</option>
                                <option value="12">12</option>
                                <option value="13">13</option>
                                <option value="14">14</option>
                                <option value="15">15</option>
                                <option value="16">16</option>
                                <option value="17">17</option>
                                <option value="18">18</option>
                                <option value="19">19</option>
                                <option value="20">20</option>
                                <option value="21">21</option>
                                <option value="22">22</option>
                                <option value="23">23</option>
                                <option value="24">24</option>
                                <option value="25">25</option>
                                <option value="26">26</option>
                                <option value="27">27</option>
                                <option value="28">28</option>
                                <option value="29">29</option>
                                <option value="30">30</option>
                                <option value="31">31</option>
                            </select>
                            &nbsp;&nbsp;&nbsp;&nbsp;
                            Month:
                            <select id="arrivalMonth">
                            	<option value="01">01 - Jan</option>
                                <option value="02">02 - Feb</option>
                                <option value="03">03 - Mar</option>
                                <option value="04">04 - Apr</option>
                                <option value="05">05 - May</option>
                                <option value="06">06 - Jun</option>
                                <option value="07">07 - Jul</option>
                                <option value="08">08 - Aug</option>
                                <option value="09">09 - Sep</option>
                                <option value="10">10 - Oct</option>
                                <option value="11">11 - Nov</option>
                                <option value="12">12 - Dec</option>
                            </select>
                            &nbsp;&nbsp;&nbsp;&nbsp;
                            Year:
                            <!--<input type="text" disabled value="2017" id="arrivalYear" />	-->
                          	<select id="arrivalYear">
                              <option value="2018">2018</option>
                              <option value="2019">2019</option>
                            </select>
                        </td>
                    </tr>
                  <div id="itemsOnPO">
                  	<tr>
                      <td>Item: </td>
                      <td><select id="itemNo" onchange="updateQty()">
                        <option value="" disabled selected>-Item-</option>
                        </select>
                      </td>
                    </tr>
                     <tr>
                        <td>Number of Labels:</td>
                        <td><input type="number" id="numLabels" value="1"/> *</td>
                    </tr>
                  </div>
                </tbody>
            </table>
            <button onclick="validateForm()" id="validateForm">Create Label(s)</button>
            <divider></divider>
            
        </div>
    </div>

    
    <div id="footer">
    </div>
</body>
</html>
