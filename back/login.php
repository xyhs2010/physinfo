<?php
header('Access-Control-Allow-Origin: *');
if(!isset($_POST['password'])){
    exit('非法访问!');
}
$id = htmlspecialchars_decode(trim($_POST['id']));
$password = MD5(trim($_POST['password']));

//包含数据库连接文件
include('../Connections/phys_ims.php');
mysql_select_db($database_phys_ims, $phys_ims);

//检测用户名及密码是否正确
$check_query = mysql_query("select 1 from info where ID='$id' and passwd='$password' 
limit 1");
if($result = mysql_fetch_array($check_query)){
	//登录成功
	echo 'success';
} else {
	echo '学号或密码错误';
}
?>
