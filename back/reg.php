<?php
header('Access-Control-Allow-Origin: *');
$id = trim($_POST['id']);
$username = trim($_POST['username']);
$password = MD5(trim($_POST['passwd']));
$email = trim($_POST['email']);

//注册信息判断
if(!preg_match('/^[\w\x80-\xff]{3,15}$/', $username)){
    echo '错误：姓名不符合规定。';
	exit;
}
if(strlen($password) < 6){
    echo '错误：密码长度不符合规定。';
	exit;
}
if(!preg_match('/^[\w-]+(\.[\w-]+)*@[\w-]+(\.[\w-]+)+$/', $email)){
    echo '错误：电子邮箱格式错误。';
	exit;
}
//包含数据库连接文件
include('../Connections/phys_ims.php');
mysql_select_db($database_phys_ims, $phys_ims);
//检测学号是否存在
$check_query1 = mysql_query("select ID from basic_info where ID='$id' limit 1");
if(! mysql_fetch_array($check_query1)){
    echo '错误：学号'.$id.' 不存在。';
    exit;
}
$check_query1 = mysql_query("select ID from basic_info where ID='$id' and name='$username' limit 1");
if(! mysql_fetch_array($check_query1)){
    echo '错误：学号与姓名不符。';
    exit;
}
$check_query2 = mysql_query("select ID from info where id='$id' limit 1");
if(mysql_fetch_array($check_query2)){
    echo '错误：学号'.$id.' 已注册。';
    exit;
}
//写入数据
include("RSA.php");
$password = trim($password);
$regdate = time();

$sql = "select class from basic_info where ID='$id'";
$result = mysql_query($sql, $phys_ims) or die('Query failed: ' . mysql_error());
$line = mysql_fetch_array($result, MYSQL_ASSOC);
$department="物理";
$class=$line['class'];
$sql = "INSERT INTO info(ID,name,passwd,email,department,class)VALUES('$id','$username','$password','$email','$department','$class')";
if(mysql_query($sql,$phys_ims)){
    echo 'success';
} else {
    echo '抱歉！添加数据失败：',mysql_error();
}
?>
