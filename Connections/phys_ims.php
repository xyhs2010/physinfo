<?php
# FileName="Connection_php_mysql.htm"
# Type="MYSQL"
# HTTP="true"
$hostname_phys = "114.215.108.145";
$username_phys = "xyhs14";
$password_phys = "xyhs19911208";
$phys_ims = mysql_pconnect($hostname_phys, $username_phys, $password_phys) or trigger_error(mysql_error(),E_USER_ERROR); 
$database_phys_ims = "IMS";
?>
