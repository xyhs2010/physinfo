<?php
$userPasswd = array("physinfo1@163.com"=>"pbhxehvkioqjoaye","physinfo2@163.com"=>"jsdyuzdvdhkdbcip","physinfo3@163.com"=>"dgtixfqdvpqpcyuy");
include 'emailBack.php';

function decodeMail($str) {
    if(preg_match_all('/=\?([^?]+)\?(B|Q)\?([^?]+)\?=/i', $str, $tmp)) {
        $res = array();
        for ($i = 0; $i < count($tmp[0]); $i++) {
        /* foreach ($tmp as $i) { */
            if ($tmp[2][$i] == "B") {
                $tmp[3][$i] = base64_decode($tmp[3][$i]);
            } else {
                $tmp[3][$i] = quoted_printable_decode($tmp[3][$i]);
            }
            $res[$i] = subStr($tmp[1][$i],0,2)=='gb'||subStr($tmp[1][$i],0,2)=='GB' ? iconv('gbk', 'utf-8', $tmp[3][$i]) : $tmp[3][$i];
        }
        return $res;
    } else {
        return false;
    }
}

function mailGroups($groupName) {
    $mails = array();
    if ($groupName == "phys14") {
        include('../Connections/phys_ims.php');
        mysql_select_db($database_phys_ims, $phys_ims);
		$sql = "select email from info";
        $result = mysql_query($sql, $phys_ims) or die('Query failed: ' . mysql_error());
        $list = array();
        while($line = mysql_fetch_array($result, MYSQL_ASSOC))
        {
            array_push($mails, $line["email"]);
        }
        /* $mails = array("xyhs2010@gmail.com","xyhs2010@qq.com","xyhs14@mails.tsinghua.edu.cn","xyhs2010@sina.com","xyhs2010@126.com"); */
    }
    $groups = array();
    $LEN = 15;
    for ($i = 0; $i<count($mails); $i += $LEN) {
        $groups[] = array_slice($mails, $i, $LEN);
    }
    return $groups;
}


$pop3 = new Pop3();
if (!$pop3->pop3_open("pop.163.com", "110")) { 
            printf("[ERROR] Failed to connect to localhost<BR>\n"); 
            return 0; 
} 

if (!$pop3->pop3_user("physinfo1")) { 
            printf("[ERROR] Username failed!<BR>\n"); 
            return 0; 
} 

if (!$pop3->pop3_pass("pbhxehvkioqjoaye")) { 
            printf("[ERROR] PASS failed!<BR>\n"); 
            return 0; 
} 

$articles = $pop3->pop3_list(); 
if (!$articles) { 
            printf("[ERROR] LIST failed!<BR>\n"); 
            return 0; 
} 
$articlesCount = count($articles);
$achive = array();
$myfile = fopen("achiveEmail.txt", "r") or die("Unable to open file!");
while(!feof($myfile)) {
    $achive[]=fgets($myfile);
}
fclose($myfile);
/* echo json_encode($achive).'<br>'; */

for ($j = 0; $j < $articlesCount; $j++) {
    $uidl = $pop3->pop3_uidl($articlesCount - $j);
    /* echo $uidl."<br>"; */
    $flag = true;
    for ($i = count($achive); $i > 0; $i--) {
        if ($uidl."" == $achive[$i-1]) {
            $flag = false;
            break;
        }
    }
    if ($flag) {
        $myfile = fopen("achiveEmail.txt", "a+") or die("Unable to open file for writing!");
        fwrite($myfile, $uidl);
        fclose($myfile);
    } else break;
    $data = $pop3->pop3_retr($articlesCount - $j);
    if (!$data) {
        echo "data goes wrong<BR>\n";
    }
    $dataCount = count($data);
    $headerCount = 0;
    $boundary = "";
    for ($i = 0; $i < $dataCount; $i++) {
        if (preg_match('/boundary="(.*?)"\r\n/i', $data[$i], $tmp)) {
            $boundary = $tmp[1];
            break;
        }
    }
    for ($i = $i; $i < $dataCount; $i++) {
        if (strpos($data[$i],'--'.$boundary)>-1) {
            $headerCount = $i;
            break;
        }
    }

    $from="";
    $subject='邮件标题';
    //Sparse the header
    for ($i = 0; $i < $headerCount; $i++) {
        /* echo $data[$i]."<BR>"; */
        if (preg_match('/^To: *(.*?)\r\n/',$data[$i], $tmp)) {
            $data[$i] = '';
            while (preg_match('/^\s.*?/i', $data[$i+1])) {
                $i += 1;
                /* echo $data[$i]."<BR>"; */
                $data[$i] = '';
            }
        }
        if (preg_match('/^Received: *(.*?)\r\n/',$data[$i], $tmp)) {
            /* echo $data[$i]."<BR>"; */
            $data[$i] = '';
            while (preg_match('/^\s.*?/i', $data[$i+1])) {
                $i += 1;
                /* echo $data[$i]."<BR>"; */
                $data[$i] = '';
            }
        }
        if (preg_match('/^From: *(.*?)\r\n/',$data[$i], $tmp)) {
            $data[$i] = '';
            $from = $tmp[1];
        }
        /* if (preg_match('/^Sender: (.*?)\r\n/',$data[$i], $tmp)) { */
        /*     $data[$i] = ''; */
        /*     $from = $tmp[1]; */
        /* } */

        // 中文标题的处理
        if( preg_match('/^Subject: *=\?.*?=/i', $data[$i])) {
            /* echo $data[$i]; */
            $tmp = decodeMail($data[$i]);
            $data[$i] = '';
            if ($tmp) {
                $subject = implode('',$tmp);
            }
            while (preg_match('/^\s=\?.*?=/i', $data[$i+1])) {
                $i += 1;
                /* echo $data[$i]."<BR>"; */
                $tmp = decodeMail($data[$i]);
                $data[$i] = '';
                if ($tmp) {
                    $subject = $subject.implode('',$tmp);
                }
            }
            /* echo "<br>"; */
        }
        // 英文标题
        else if( preg_match('/Subject: *(.*?)\r\n/i', $data[$i], $tmp) ){
            /* echo $data[$i]."<BR>"; */
            $subject = $tmp[1];
            $data[$i] = '';
            
        }
    }
    /* echo $subject."<BR>".$from."<br><br>\r\n"; */
    $body = implode('', $data);


    if (substr($subject,0,3) == '[g:') {
        preg_match('/\[g:(.*?)\](.*)/', $subject, $tmp);
        $toMails = mailGroups($tmp[1]); 
        /* echo json_encode($toMails); */
        $subject = $tmp[2];
        echo "Subject: ".$subject."<br>";
        $user = "physinfo1@163.com";
        $passwd = $userPasswd[$user];
        $toMail = array("physinfo2@163.com","physinfo3@163.com");
        $to = implode(',', $toMail);
        $MailTo = new Smtp(); 
        $MailTo->Auth($user, $passwd);
        $test = $MailTo->SmtpMail(array($user),$from, 
                   $toMail,$to, 
                   '=?utf-8?B?'.base64_encode($subject).'?=',$body,"forward"); 
        $MailTo->Close(); 
        $MailTo=null; 
        sleep(60);

        for ($i=0; $i < count($toMails); $i++) {
            /* if ($i == "0" || $i == "1" || $i == "3") {continue;} */
            $toMail=$toMails[$i];
            $to = implode(',', $toMail);
            $n = (string)$i%3+1;
            $user = "physinfo".$n."@163.com";
            $passwd = $userPasswd[$user];

            $MailTo = new Smtp(); 
            $MailTo->Auth($user, $passwd);
            $test = $MailTo->SmtpMail(array($user),$from, 
                       $toMail,$to, 
                       '=?utf-8?B?'.base64_encode($subject).'?=',$body,"forward"); 
            $MailTo->Close(); 
            $MailTo=null; 
            sleep(60);
            }
        }
    }
}
?>
