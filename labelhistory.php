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
            <h2>Printed Labels</h2>
            
            <table id="history">
                <thead>
                    <tr>
                        <th></th>
                      <th>Customer</th>
                        <th>LabelID</th>
                        <th>Date Printed</th>
                        <th>PO#</th>
                        <th>BOL</th>
                        <th>DC</th>
                        <th>Store</th>
                        <th>Count</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                <?php
                    $query = "
                    SELECT
                        labelID,
                        DATE(date_created) AS date_created,
                        po_number,
						bol,
                        dc_name,
                        store_name,
                        num_labels,
                        customer
                    FROM labels 
                    WHERE printed=1
                    ORDER BY date_created DESC";
                    $result = $mysql->query($query);
                    if($result) {
                        while($row = $result->fetch_assoc()) {
                            echo '
                        <tr>
                            <td>
                                <button onclick="viewLabelDetails(\''.$row['labelID'].'\')">View Details</button>
                            </td>
                            <td>'.$row['customer'].'</td>
                            <td>'.$row['labelID'].'</td>
                            <td>'.$row['date_created'].'</td>
                            <td>'.$row['po_number'].'</td>
							<td>'.$row['bol'].'</td>
                            <td>'.$row['dc_name'].'</td>
                            <td>'.$row['store_name'].'</td>
                            <td>'.$row['num_labels'].'</td>
                            <td>
                                <button onclick="createASN(\''.$row['labelID'].'\')">Create ASN</button>
                            </td>
                        </tr>
                            ';
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