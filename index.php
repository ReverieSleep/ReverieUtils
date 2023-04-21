<?php
if(isset($_SESSION['userID'])) {
	header('location:labelinfo.php');
	exit;
}
?>

<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Label Portal</title>
<link href="styles/primary.css" rel="stylesheet" type="text/css">
</head>

<body>
	<div id="heading">
    </div>
    
    <div id="container">
    	<div id="content">
        	<form action="utils.php" method="post">
            	<input type="hidden" name="action" value="login"/>
                <table>
                    <tr>
                        <td>Username:</td>
                        <td><input type="text" name="username" /></td>
                    </tr>
                    <tr>
                        <td>Password:</td>
                        <td><input type="password" name="password" /></td>
                    </tr>
                </table>
                <input type="submit" value="Login" />
            </form>
        </div>
    </div>
    
    <div id="footer">
    </div>
</body>
</html>
