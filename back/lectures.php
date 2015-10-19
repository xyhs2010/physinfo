<?php
header('Access-Control-Allow-Origin: *');
//注册用户
if(isset($_POST['password']))
{
	//包含数据库连接文件
	include('../Connections/phys_ims.php');
	mysql_select_db($database_phys_ims, $phys_ims);
	include("RSA.php");
	$password = MD5(trim($_POST['password']));
    $id = htmlspecialchars_decode(trim($_POST['id']));
	if (isset($_POST['sub']))
	//订阅
	{
		$sub=trim($_POST['sub']);
		$sql = "UPDATE info set subscribe='$sub' where passwd='$password' and ID='$id'";
		if(!mysql_query($sql,$phys_ims)){
		//} else {
			echo mysql_error();
			exit;
		}
	}
	else
	{
		$sql = "select subscribe from info where passwd='$password'";
		$result = mysql_query($sql, $phys_ims) or die('Query failed: ' . mysql_error());
		$line1 = mysql_fetch_array($result, MYSQL_ASSOC);
		$sub = $line1['subscribe'];
	}
	if($sub=="")
	{
		echo "[[][]]";
	}
	else
	{
		$list = explode('-', $sub);
		
		$content = file_get_contents("lectures.json"); 
		$content = json_decode($content);
		//echo json_encode($content);
		$res = array();
		foreach ($list as $num) {
			array_push($res, $content[(int)$num]);
			//array_push($res, $num);
		}
		echo json_encode(array($list,$res));
	}
}
//浏览
else if (isset($_POST['cont']))
{
	$content = file_get_contents("lectures.json"); 
	$content = json_decode($content);
	$num = (int)trim($_POST['cont']);
	echo json_encode($content[$num]);
}
else
{
	exit('非法访问!');
}
?>
