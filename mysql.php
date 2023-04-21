<?php

//production
/*
$host = 'jcaholdings.com';
$user = 'jcaholdi_label';
$password = 'Dream_2016';
$db = 'jcaholdi_label';
*/

//testing
$host = 'localhost';
$user = 'web';
$password = 'Dr3@m_2017';
//$db = 'label_portal';
$db = 'bloomingdales';
//$db = 'tmp_macy';

$mysql = new mysqli($host,$user,$password);
$mysql->select_db($db);
?>