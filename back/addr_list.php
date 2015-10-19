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
$sql = "select class from info where passwd='$password' and ID='$id'";
$result = mysql_query($sql, $phys_ims) or die('Query failed: ' . mysql_error());
$line1 = mysql_fetch_array($result, MYSQL_ASSOC);
$class = $line1['class'];

$sql = "select ID, name, grade, department, class, phone, email, wechat from info where class='$class'";
$result = mysql_query($sql, $phys_ims) or die('Query failed: ' . mysql_error());
$list = array();
while($line = mysql_fetch_array($result, MYSQL_ASSOC))
{
	array_push($list, $line);
}
echo json_encode(array("list"=>$list));
?>
