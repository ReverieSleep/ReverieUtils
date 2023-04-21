<?php
if(empty($_SESSION['userID'])) {
	session_destroy();
	header('location:index.php');
	exit;
}

function drawMenu() {
	echo '
	<a href="labelinfo.php">Create Label</a>
	<a href="labelhistory.php">Label History</a>
	<a href="logout.php" class="floatRight">Logout</a>
	';
}

function drawFooter() {
}
?>