<?php
header('Access-Control-Allow-Origin: *');
if (isset($_POST['password']))
{
	//包含数据库连接文件
	include('../Connections/phys_ims.php');
	mysql_select_db($database_phys_ims, $phys_ims);
	include("RSA.php");
	$password = MD5(trim($_POST['password']));
	$check_query = mysql_query("select 1 from info where passwd='$password' limit 1");
	if($result = mysql_fetch_array($check_query))
	{
		echo "success";
	}
	else
	{
		echo $_POST['password'];
	}
}
?>
