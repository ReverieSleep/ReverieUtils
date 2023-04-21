<?php
session_start();
if(empty($_SESSION['userID'])) {
	session_destroy();
	header('location:index.php');
	exit;
}
include('functions.php');
include('mysql.php');

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
	<div id="heading">
    	<div id="menu">
    	<?php drawMenu(); ?>
        </div>
    </div>
    
    <div id="container">
    	<div id="content">
        	<h2>Manage Users</h2>
            <table>
            	<thead>
                    <tr>
                        <th colspan="2">New User</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Username</td>
                        <td><input type="text" id="username" /></td>
                    </tr>
                    <tr>
                        <td>Password</td>
                        <td>
                        	<input type="text" id="password" />
                            <button>Create User</button>
                        </td>
                    </tr>
                </tbody>
            </table>
            <br />

            <table>
            	<thead>
                    <tr>
                        <th colspan="2">Current Users</th>
                    </tr>
                </thead>
                <tbody>
					<?php
                    
                    $query = "SELECT username, userID FROM users";
                    $result = $mysql->query($query);
                    if($result) {
                        while($row = $result->fetch_assoc()) {
                    echo '
                    <tr>
                        <td>'.$row['username'].'</td>
                        <td>
							<!--<button onclick="deleteUser(\''.$row['userID'].'\')">Delete User</button>-->
						</td>
                    </tr>';
                        }
                    }
                    ?>
                <tbody>
            </table>
            <divider></divider>
            
           <!-- <h2>Manage Products</h2>
            <table>
            	<thead>
                	<tr>
                    	<th colspan="2">New Product</th>
                    </tr>
                </thead>
              	<tr>
                  <td>Customer</td>
                  <td>
                    <select id="customer">
                    	<option value="Bloomingdales">Bloomingdale's</option>
                    	<option value="Macys">Macy's</option>
                    </select>
                </tr>
            	<tr>
                	<td>Product Description</td>
                    <td><input type="text" id="description" /></td>
                </tr>
                <tr>
                	<td>UPC</td>
                    <td>
                    	<input type="text" id="upc" />
                        <button id="buttonAddProduct" onclick="createProduct()">Add Product</button>
					</td>
                </tr>
            </table>
            
            <table>
            	<thead>
                    <tr>
                        <th>Description</th>
                        <th>UPC</th>
                      <th>Customer</th>
                    </tr>
                </thead>
                <tbody id="productList">
                	<?php
				
					$query = "SELECT productID, product_name, upc, customer FROM products";
					$result = $mysql->query($query);
					if($result) {
						while($row = $result->fetch_assoc()) {
					echo '
					<tr id="product_'.$row['productID'].'">
						<td>'.$row['product_name'].'</td>
						<td>'.$row['upc'].'</td>
                        <td>'.$row['customer'].'<button onclick="deleteProduct(\''.$row['productID'].'\')">Delete</button>
						</td>
					</tr>';
						}
					}
					?>
                </tbody>
            </table>-->
            <divider></divider>
            
            <h2>Manage DCs</h2>
            <table>
            	<thead>
                	<tr>
                    	<th colspan="2">New DC</th>
                    </tr>
                </thead>
            	<tr>
                	<td>DC Name</td>
                    <td><input type="text" id="dcName" /> *</td>
                </tr>
                <tr>
                	<td>DC Number</td>
                    <td><input type="text" id="dcNumber" /> *</td>
                </tr>
                <tr>
                	<td>DC Address</td>
                    <td>
                    	<textarea colw="20" rows="3" id="dcAddress"></textarea> *
                        <button id="buttonAddProduct" onclick="createDC()">Add DC</button>
					</td>
                </tr>
            </table>
            
            <table>
            	<thead>
                    <tr>
                        <th>DC Name</th>
                        <th>Number</th>
                        <th colspan="2">Address</th>
                    </tr>
                </thead>
                <tbody id="dcList">
                	<?php
				
					$query = "SELECT dcID, dc_name, dc_number, dc_address FROM dc";
					$result = $mysql->query($query);
					if($result) {
						while($row = $result->fetch_assoc()) {
							$dc_address = str_replace('\n','',$row['dc_address']);
							$dc_address = str_replace('\r','',$dc_address);
					echo '
					<tr id="dc'.$row['dcID'].'">
						<td>'.$row['dc_name'].'</td>
						<td>'.$row['dc_number'].'</td>
						<td>'.$dc_address.'</td>
						<td>
							
						<!--	<button onclick="deleteDC(\''.$row['dcID'].'\')">Delete</button>	-->
						</td>
					</tr>';
						}
					}
					?>
                </tbody>
            </table>
            
            
            <h2>Manage Carriers</h2>
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
                        <button id="buttonAddCarrier" onclick="createCarrier()">Add Carrier</button>
                    </td>
                </tr>
            </table>
            
            <table>
            	<thead>
                    <tr>
                        <th>Carrier Name</th>
                        <th>SCAC</th>
                    </tr>
                </thead>
                <tbody id="carrierList">
                	<?php
				
					$query = "SELECT carrierID, carrier_name, scac FROM carriers";
					$result = $mysql->query($query);
					if($result) {
						while($row = $result->fetch_assoc()) {
							
					echo '
					<tr id="carrier_'.$row['carrierID'].'">
						<td>'.$row['carrier_name'].'</td>
						<td>
							'.$row['scac'].'
						<!--	<button onclick="deleteCarrier(\''.$row['carrierID'].'\')">Delete</button>	-->
						</td>
					</tr>';
						}
					}
					?>
                </tbody>
            </table>
        </div>
    </div>

    
    <div id="footer">
    </div>
</body>
</html>