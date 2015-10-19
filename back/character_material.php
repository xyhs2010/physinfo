<?php 
header('Access-Control-Allow-Origin: *');
if(!isset($_POST['password'])){
    exit('非法访问!');
}
$password = MD5(trim($_POST['password']));
$id = htmlspecialchars_decode(trim($_POST['id']));
$opt = htmlspecialchars_decode(trim($_POST['opt']));

//包含数据库连接文件
include('../Connections/phys_ims.php');
mysql_select_db($database_phys_ims, $phys_ims);

if($opt == 'obtain')
{
    $sql = "select * from person_material where id='$id'";
    $result = mysql_query($sql, $phys_ims) or die('Query failed: ' . mysql_error());
    $list = array();
    while($line = mysql_fetch_array($result, MYSQL_ASSOC))
    {
    	array_push($list, $line);
    }
    echo json_encode($list[0]);
}
else if($opt == 'update') {
    $science = htmlspecialchars_decode(trim($_POST['science']));
    $pe = htmlspecialchars_decode(trim($_POST['pe']));
    $social_work = htmlspecialchars_decode(trim($_POST['social_work']));
    $practice = htmlspecialchars_decode(trim($_POST['practice']));
    $service = htmlspecialchars_decode(trim($_POST['service']));
    $work = htmlspecialchars_decode(trim($_POST['work']));
    $strong = htmlspecialchars_decode(trim($_POST['strong']));
    $sql = "select * from person_material where id='$id'";
    $result = mysql_query($sql, $phys_ims) or die('Query failed: ' . mysql_error());
    if(! mysql_fetch_array($result, MYSQL_ASSOC)){
        $sql = "insert into person_material values('$id','$science','$pe','$social_work','$practice','$service','$work','$strong')";
    } else {
        $sql = "update person_material set science='$science',pe='$pe',social_work='$social_work',practice='$practice',service='$service',work='$work',strong='$strong' where id='$id'";
    }
    if(mysql_query($sql, $phys_ims)){
        echo 'success';
    } else {
        echo '抱歉！添加数据失败：',mysql_error();
    }
}
?>
