<?php
header('Access-Control-Allow-Origin: *');
if(!isset($_POST['password'])){
    exit('非法访问!');
}
$password = MD5(trim($_POST['password']));
$id = htmlspecialchars_decode(trim($_POST['id']));

//包含数据库连接文件
include('../Connections/phys_ims.php');
mysql_select_db($database_phys_ims, $phys_ims);
$sql = "select * from eval_result where id='$id'";
$result = mysql_query($sql, $phys_ims) or die('Query failed: ' . mysql_error());
$line1 = mysql_fetch_array($result, MYSQL_ASSOC);
foreach ($line1 as $i=>$val) {
    if ($i != 'id' && $i != 'grade') {
        $line1[$i] = substr($val, 0, 4);
    }
}

$sql = "select gpa,rank from gpa where id='$id'";
$result = mysql_query($sql, $phys_ims) or die('Query failed: ' . mysql_error());
$line2 = mysql_fetch_array($result, MYSQL_ASSOC);

$line = array_merge($line1, $line2);
echo json_encode($line);
