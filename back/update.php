<?php 
header('Access-Control-Allow-Origin: *');
if(!isset($_POST['password'])){
    exit('非法访问!');
}

include("RSA.php");
$password = MD5(trim($_POST['password']));
$id = htmlspecialchars_decode(trim($_POST['id']));
//包含数据库连接文件
include('../Connections/phys_ims.php');
mysql_select_db($database_phys_ims, $phys_ims);

if ($_POST['no']=='1') {
	
	if (isset($_POST['name']))
	{
		$name=trim($_POST['name']);
		$grade=trim($_POST['grade']);
		$department=trim($_POST['department']);
		$class=trim($_POST['class']);
		$sql = "UPDATE info set name='$name', grade='$grade', department='$department', class='$class' where passwd='$password' and ID='$id'";
		if(mysql_query($sql,$phys_ims)){
    		echo 'success';
		} else {
			echo mysql_error();
		}
	}
	else
	{
		$sql = "select name, grade, department, class from info where passwd='$password'";
		$result = mysql_query($sql, $phys_ims) or die('Query failed: ' . mysql_error());
		$line1 = mysql_fetch_array($result, MYSQL_ASSOC);
		echo json_encode($line1);
	}
}
else if ($_POST['no']==2)
{
	if (isset($_POST['phone']))
	{
		$new_phone=trim($_POST['phone']);
		$new_email=trim($_POST['email']);
		$new_wechat=trim($_POST['wechat']);
		$sql = "UPDATE info set phone='$new_phone', email='$new_email',wechat='$new_wechat' where passwd='$password' and ID='$id'";
		if(mysql_query($sql,$phys_ims)){
    		echo 'success';
		} else {
			echo mysql_error();
		}
	}
	else {
		$sql = "select phone,email,wechat from info where passwd='$password'";
		$result = mysql_query($sql, $phys_ims) or die('Query failed: ' . mysql_error());
		$line2 = mysql_fetch_array($result, MYSQL_ASSOC);
		echo json_encode($line2);
	}
}
else if ($_POST['no']==3)
{
	if (isset($_POST['country']))
	{
		$new_country=trim($_POST['country']);
		$new_province=trim($_POST['province']);
		$new_mid_sch=trim($_POST['mid_sch']);
		$sql = "UPDATE info set country='$new_country', province='$new_province',mid_sch='$new_mid_sch' where passwd='$password' and ID='$id'";
		if(mysql_query($sql,$phys_ims)){
    		echo 'success';
		} else {
			echo mysql_error();
		}
	}
	else {
		$sql = "select country,province,mid_sch from info where passwd='$password'";
		$result = mysql_query($sql, $phys_ims) or die('Query failed: ' . mysql_error());
		$line3 = mysql_fetch_array($result, MYSQL_ASSOC);
		echo json_encode($line3);
	}
}
else if ($_POST['no']==4) {
    $newpassword = MD5(trim($_POST['newpasswd']));
    $sql = "update info set passwd='$newpassword' where ID='$id'";
    if(mysql_query($sql,$phys_ims)){
        echo 'success';
    } else {
        echo mysql_error();
    }
}
?>
