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
    $sql = "select class from basic_info where id='$id'";
    $result = mysql_query($sql, $phys_ims) or die('Query failed: ' . mysql_error());
    $line1 = mysql_fetch_array($result, MYSQL_ASSOC);
    $class = $line1['class'];
    
    $sql = "select id, name from basic_info where class='$class'";
    $result = mysql_query($sql, $phys_ims) or die('Query failed: ' . mysql_error());
    $list = array();
    while($line = mysql_fetch_array($result, MYSQL_ASSOC))
    {
    	array_push($list, $line);
    }
    for ($i = 0; $i < sizeof($list); $i++) {
        $reviewee = $list[$i]['id'];
        $sql = "select * from peer_review where reviewer='$id' and reviewee='$reviewee'";
        $result = mysql_query($sql, $phys_ims) or die('Query failed: ' . mysql_error());
        if(mysql_num_rows($result)) {
            $list[$i]['filled'] = 'yes';
        }
        else {
            $list[$i]['filled'] = 'no';
        }
    }
    echo json_encode($list);
}
else if($opt == 'update') {
    $reviewee = htmlspecialchars_decode(trim($_POST['reviewee']));
    $respons = htmlspecialchars_decode(trim($_POST['respons']));
    $moral = htmlspecialchars_decode(trim($_POST['moral']));
    $social = htmlspecialchars_decode(trim($_POST['social']));
    $life = htmlspecialchars_decode(trim($_POST['life']));
    $study = htmlspecialchars_decode(trim($_POST['study']));
    $quality = htmlspecialchars_decode(trim($_POST['quality']));
    $sql = "select * from peer_review where reviewer='$id' and reviewee='$reviewee'";
    $result = mysql_query($sql, $phys_ims) or die('Query failed: ' . mysql_error());
    if(! mysql_fetch_array($result, MYSQL_ASSOC)){
        $sql = "insert into peer_review values('$id','$reviewee',$respons,$moral,$social,$life,$study,$quality)";
    } else {
        $sql = "update peer_review set respons=$respons,moral=$moral,social=$social,life=$life,study=$study,quality=$quality where reviewer='$id' and reviewee='$reviewee'";
    }
    if(mysql_query($sql, $phys_ims)){
        echo 'success';
    } else {
        echo '抱歉！添加数据失败：',mysql_error();
    }
}
else if($opt == 'change') {
    $reviewee = htmlspecialchars_decode(trim($_POST['reviewee']));
    $sql = "select respons, moral, social, life, study, quality from peer_review where reviewer='$id' and reviewee='$reviewee'";
    $result = mysql_query($sql, $phys_ims) or die('Query failed: ' . mysql_error());
    if ($list = mysql_fetch_array($result, MYSQL_ASSOC)) {
        echo json_encode($list);
    } else {
        echo 'none';
    }
}
?>
