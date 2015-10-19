<?php 
if ($EMAIL_INC) return; 
$EMAIL_INC=  "defined"; 
define( "SmtpPort",25); 

class Pop3 { 
    var $subject;                          // 邮件主题  
    var $from_email;                       // 发件人地址
    var $from_name;                        // 发件人姓名
    var $to_email;                         // 收件人地址
    var $to_name;                          // 收件人姓名
    var $body;                             // 邮件内容
    var $filename;                         // 文件名
    var $socket;                 // 当前的 socket 
    var $Line; 
    var $Status; 

    function pop3_open($server, $port)   
    { 

        $this->Socket = fsockopen($server, $port); 
        if ($this->Socket <= 0){ 
            return false; 
        } 
    $this->Line = fgets($this->Socket, 1024); 
    $this->Status[ "LASTRESULT"] = substr($this->Line, 0, 1); 
    $this->Status[ "LASTRESULTTXT"] = substr($this->Line, 0, 1024); 

    if ($this->Status[ "LASTRESULT"] <>  "+") return false; 
    return true; 
    } 

    function pop3_user($user) 
    { 
        if ($this->Socket < 0){ 
          return false; 
        } 
        $this->user = $user;
        fputs($this->Socket,  "USER $this->user\r\n"); 
        $this->Line = fgets($this->Socket, 1024); 
        $this->Status[ "LASTRESULT"] = substr($this->Line, 0, 1); 
        $this->Status[ "LASTRESULTTXT"] = substr($this->Line, 0, 1024); 

        return $this->Line;
        /* if ($this->Status[ "LASTRESULT"] <>  "+") return false; */ 

        /* return true; */ 
    } 

    function pop3_pass($pass) 
    { 
        fputs($this->Socket,  "PASS $pass\r\n"); 
        $this->Line = fgets($this->Socket, 1024); 
        $this->Status[ "LASTRESULT"] = substr($this->Line, 0, 1); 
        $this->Status[ "LASTRESULTTXT"] = substr($this->Line, 0, 1024); 

        if ($this->Status[ "LASTRESULT"] <>  "+") return 0; 

        return 1; 
    } 

    function pop3_stat() 
    { 

        fputs($this->Socket,  "STAT\r\n"); 
        $this->Line = fgets($this->Socket, 1024); 
        $this->Status[ "LASTRESULT"] = substr($this->Line, 0, 1); 
        $this->Status[ "LASTRESULTTXT"] = substr($this->Line, 0, 1024); 

        if ($this->Status[ "LASTRESULT"] <>  "+") return 0; 

        if (!eregi( "+OK (.*) (.*)", $this->Line, $regs))  
            return 0; 

        return $regs[1]; 
    } 

    function pop3_list() 
    { 
        fputs($this->Socket,  "LIST\r\n"); 
        $this->Line = fgets($this->Socket, 1024); 
        $this->Status[ "LASTRESULT"] = substr($this->Line, 0, 1); 
        $this->Status[ "LASTRESULTTXT"] = substr($this->Line, 0, 1024); 

        if ($this->Status[ "LASTRESULT"] <>  "+") return 0; 

        $i = 0; 
        while  (substr($this->Line  =  fgets($this->Socket, 1024),  0,  1)  <>   ".") 
        { 
            $articles[$i] = $this->Line; 
            $i++; 
        } 

        return $articles; 
    } 

    function pop3_uidl($nr) {
        fputs($this->Socket,  "UIDL $nr\r\n"); 
        $this->Line = fgets($this->Socket, 1024); 
        $this->Status[ "LASTRESULT"] = substr($this->Line, 0, 1); 
        $this->Status[ "LASTRESULTTXT"] = substr($this->Line, 0, 1024); 

        if ($this->Status[ "LASTRESULT"] <>  "+") return 0; 

        return substr($this->Line, 8); 
    }

    function pop3_retr($nr) 
    { 

        fputs($this->Socket,  "RETR $nr\r\n"); 
        $this->Line = fgets($this->Socket, 1024); 
        $this->Status[ "LASTRESULT"] = substr($this->Line, 0, 1); 
        $this->Status[ "LASTRESULTTXT"] = substr($this->Line, 0, 1024); 

        if ($this->Status[ "LASTRESULT"] <>  "+") return 0; 

        $i = 0;
        while  (substr($this->Line  =  fgets($this->Socket, 1024),  0,  1)  <>   ".") 
        { 
            $data[$i] = $this->Line; 
            $i++; 
        } 

        return $data; 
    } 

    function pop3_dele( $nr) 
    { 

        fputs($this->Socket,  "DELE $nr\r\n"); 
        $this->Line = fgets($this->Socket, 1024); 
        $this->Status[ "LASTRESULT"] = substr($this->Line, 0, 1); 
        $this->Status[ "LASTRESULTTXT"] = substr($this->Line, 0, 1024); 

        if ($this->Status[ "LASTRESULT"] <>  "+") return 0; 
        return 1; 
    } 

    function pop3_quit() 
    { 

        fputs($this->Socket,  "QUIT\r\n"); 
        $this->Line = fgets($this->Socket, 1024); 
        $this->Status[ "LASTRESULT"] = substr($this->Line, 0, 1); 
        $this->Status[ "LASTRESULTTXT"] = substr($this->Line, 0, 1024); 

        if ($this->Status[ "LASTRESULT"] <>  "+") return 0; 

        return 1; 
    } 
} 

class Smtp { 

    var $Subject;              // string the email's subject  
    var $FromName;                 // string sender's name (opt)  
    var $ToName;                   // string recipient's name (opt)  
    var $Body;                     // string body copy  
    var $Attachment;         // attachment (optional) 
    var $AttachmentType; 
    var $Socket; 
    var $Line; 
    var $Status; 
    var $user;

    function Smtp($Server =  "smtp.163.com",$Port = 25) 
    {     
        return $this->Open($Server, $Port); 
    } 

    function SmtpMail($FromEmail, $FromName, $ToEmail, $ToName, $Subject, $Body, $Attachment=null, $AttachmentType= "TEXT") 
    { 
        $this->Subject   = $Subject; 
        $this->ToName    = $ToName; 

        $this->FromName    = $FromName; 
        $this->Body      = $Body; 

        $this->Attachment = $Attachment; 
        $this->AttachmentType = $AttachmentType; 

        if ($this->MailFrom($FromEmail) == false){ 
            echo "FromEmail Error: ".$FromName."<br>";
            return false; 
        } 
        if ($this->RcptTo($ToEmail) == false){ 
            echo "ToEmail Error: ".$ToName."<br>";
            return false; 
        } 
        
        if ($this->Body() == false){ 
            echo "SendEmail Error. From ".$FromName."; To ".$ToName."<br>";
            return false; 
        } 
        if ($this->Quit() == false){ 
            echo "Quit Error<br>";
            return false; 
        } 
        return true;
    } 

    function Open($Server, $Port) 
    { 

     $this->Socket = fsockopen($Server, $Port); 
     if ($this->Socket < 0) return false; 

     $this->Line = fgets($this->Socket, 1024); 

     $this->Status[ "LASTRESULT"] = substr($this->Line, 0, 1); 
     $this->Status[ "LASTRESULTTXT"] = substr($this->Line, 0, 1024); 

     if ($this->Status[ "LASTRESULT"] <>  "2") {
         echo $this->Line.'<br>';
         return false; 
     }

     return true; 
    } 

    function Helo() 
    { 
        if (fputs($this->Socket,  "HELO "."www.thinkful.cn'.'\r\n") < 0 ){ 
            return false; 
        } 
        $this->Line = fgets($this->Socket, 1024); 

        $this->Status[ "LASTRESULT"] = substr($this->Line, 0, 1); 
        $this->Status[ "LASTRESULTTXT"] = substr($this->Line, 0, 1024); 

        if ($this->Status[ "LASTRESULT"] <>  "2") return false; 

        return true;   
    } 

    function Auth($user = "physinfo1@163.com", $passwd="pbhxehvkioqjoaye")
    {
        $this->user = $user;
        if ($this->Helo() == false){ 
            return false; 
        } 
        if (fputs($this->Socket, "AUTH LOGIN ".base64_encode($user)."\r\n") <0 ) {
            return false;
        }
        $this->Line = fgets($this->Socket, 1024); 

        $this->Status[ "LASTRESULT"] = substr($this->Line, 0, 1); 
        $this->Status[ "LASTRESULTTXT"] = substr($this->Line, 0, 1024); 

        if ($this->Status[ "LASTRESULT"] <>  "3") {
            echo $this->Line.'<br>';
            return false; 
        }

        if (fputs($this->Socket, base64_encode($passwd)."\r\n") <0 ) {
            return false;
        }
        $this->Line = fgets($this->Socket, 1024); 

        $this->Status[ "LASTRESULT"] = substr($this->Line, 0, 1); 
        $this->Status[ "LASTRESULTTXT"] = substr($this->Line, 0, 1024); 

        if ($this->Status[ "LASTRESULT"] <>  "2") {
            echo $this->Line.'<br>';
            return false; 
        }

        return true;
    }

    function Ehlo() 
    { 

         /* Well, let's use "helo" for now.. Until we need the 
        extra func's   [Unk] 
        */ 
        if(fputs($this->Socket,  "helo localhost\r\n")<0){ 
            return false; 
        } 
        $this->Line = fgets($this->Socket, 1024); 

        $this->Status[ "LASTRESULT"] = substr($this->Line, 0, 1); 
        $this->Status[ "LASTRESULTTXT"] = substr($this->Line, 0, 1024); 

        if ($this->Status[ "LASTRESULT"] <>  "2") return false; 

        return true; 
    } 

    function MailFrom($FromEmail) 
    { 
        for ($i=0; $i < count($FromEmail); $i++) {
            if (fputs($this->Socket,  "MAIL FROM: <$FromEmail[$i]>\r\n")<0){ 
                echo "Write for sender Error.<br>";
                return false; 
            } 

            $this->Line = fgets($this->Socket, 1024); 
            /* echo $this->Line.'<br>'; */

            $this->Status[ "LASTRESULT"] = substr($this->Line, 0, 1); 
            $this->Status[ "LASTRESULTTXT"] = substr($this->Line, 0, 1024); 

            if ($this->Status[ "LASTRESULT"] <>  "2") {
                echo $this->Line.'<br>';
                return false; 
            }
        }
        return true; 
    } 

    function RcptTo($ToEmail) 
    { 
        for ($i=0; $i < count($ToEmail); $i++) {
            if(fputs($this->Socket,  "RCPT TO: <$ToEmail[$i]>\r\n")<0){ 
                echo "Write for receiver Error.<br>";
                return false; 
            } 
            $this->Line = fgets($this->Socket, 1024); 
            /* echo $this->Line.'<br>'; */

            $this->Status[ "LASTRESULT"] = substr($this->Line, 0, 1); 
            $this->Status[ "LASTRESULTTXT"] = substr($this->Line, 0, 1024); 

            if ($this->Status[ "LASTRESULT"] <>  "2") {
                echo $this->Line.'<br>';
                return false; 
            }
        } 
        return true; 
    }

    function Body() 
    { 
        $FileSize = 0; 
        $Attachment = null; 
        $fp = null; 

        $buffer = sprintf( "From: %s\r\nTo: %s\r\nSubject: %s\r\n", $this->FromName, $this->ToName, $this->Subject); 

        if(fputs($this->Socket,  "DATA\r\n")<0){ 
            echo "Write for data Error.<br>";
            return false; 
        } 
        $this->Line = fgets($this->Socket, 1024); 
        /* echo $this->user.": ".$this->Line."<br>"; */

        $this->Status[ "LASTRESULT"] = substr($this->Line, 0, 1); 
        $this->Status[ "LASTRESULTTXT"] = substr($this->Line, 0, 1024); 

        if ($this->Status[ "LASTRESULT"] <>  "3") {
            echo $this->Line.'<br>';
            return false; 
        }

        if(fputs($this->Socket, $buffer)<0){ 
            echo "Write for header Error.<br>";
            return false; 
        } 

        if ($this->Attachment == "forward") {
            if(fputs($this->Socket, "$this->Body\r\n\r\n")<0) {
                echo "Write for body Error.<br>";
                return false;
            }

            if(fputs($this->Socket,  ".\r\n")<0){ 
                echo "Write for endNode Error.<br>";
                return false; 
            } 

            $this->Line = fgets($this->Socket, 1024); 
            /* echo $this->Line.'<br>'; */
            if (substr($this->Line, 0, 1) <>  "2"){ 
                echo $this->Line.'<br>';
                return false;  
            }else{ 
                echo "Send success!<br>&nbspFROM: ".$this->user."<br>&nbspTO: ".$this->ToName."<br><br>";
                return true; 
            } 
        }
        else if ($this->Attachment == null){ 

            if(fputs($this->Socket,  "MIME-Version: 1.0\r\nContent-Type: text/plain; charset=ISO-8859-1\r\nContent-Transfer-Encoding: 7bit\r\n\r\n")<0){ 
                return false; 
            } 
            if(fputs($this->Socket,  "$this->Body\r\n\r\n")<0){ 
                return false; 
            } 

            if(fputs($this->Socket,  ".\r\n")<0){ 
                return false; 
            } 

            $this->Line = fgets($this->Socket, 1024); 
            if (substr($this->Line, 0, 1) <>  "2"){ 
                return false;  
            }else{ 
                return true; 
            } 
        }else{ 
            if(fputs($this->Socket, "MIME-Version: 1.0\r\nContent-Type: multipart/mixed; boundary=\"----=_NextPart_000_01BCFA61.A3697360\"\r\n". 
                 "Content-Transfer-Encoding: 7bit\r\n\r\n". 
                 "This is a multi-part message in MIME format.\r\n". 
                 "\r\n------=_NextPart_000_01BCFA61.A3697360\r\n". 
                 "Content-Type: text/plain; charset=ISO-8859-1\r\n". 
                 "Content-Transfer-Encoding: 7bit\r\n". 
                 "\r\n")<0){ 
                return false; 
            } 

             /* 输出邮件内容 */ 
            if(fputs($this->Socket,  "$this->Body\r\n\r\n")<0){ 
                return false; 
            } 

            if ( fputs($this->Socket, "\r\n------=_NextPart_000_01BCFA61.A3697360\r\n")<0){ 
                return false; 
            } 
            $FileSize = filesize($this->Attachment); 
            if ($FileSize == false){ 
                return false; 
            } 
            if (($fp = fopen($this->Attachment, "r"))== false) { 
                return false; 
            }else{ 
                $Attachment = fread($fp,$FileSize);     
            } 

             // 如果没有附件的目录 
            if (($AttachName = strrchr($this->Attachment, '/')) == false){ 

                $AttachName = $this->Attachment; 
            } 

            if( fputs($this->Socket, 
                 "Content-Type: application/octet-stream; \r\nname=\"$AttachName\"\r\n". 
                 "Content-Transfer-Encoding: quoted-printable\r\n". 
                 "Content-Description: $AttachName\r\n". 
                 "Content-Disposition: attachment; \r\n\tfilename=\"$AttachName\"\r\n". 
                 "\r\n")<0){ 
                return false; 
            } 

             /* 输出附件*/ 
            if( fputs($this->Socket, $Attachment)<0){ 
                return false; 
            } 
            if ( fputs($this->Socket, "\r\n\r\n------=_NextPart_000_01BCFA61.A3697360--\r\n")<0){ 
                return false; 
            } 

            if( fputs($this->Socket, ".\r\n")<0){ 
                return false; 
            } 

            $this->Line = fgets($this->Socket, 1024); 
            if (substr($this->Line, 0, 1) <>  "2") 
                return false;  

            return true; 

        } 
    } 

    function Quit() 
    { 

        if(fputs($this->Socket,  "QUIT\r\n")<0){ 
            return false; 
        } 
        $this->Line = fgets($this->Socket, 1024); 

        $this->Status[ "LASTRESULT"] = substr($this->Line, 0, 1); 
        $this->Status[ "LASTRESULTTXT"] = substr($this->Line, 0, 1024); 

        if ($this->Status[ "LASTRESULT"] <>  "2") return 0; 

        return 1; 
    }  
    function Close() 
    { 
        fclose($this->Socket); 
    } 
} 
/* 

怎样使用这个程序的一个示例 

$MailTo = new Smtp(); 
$MailTo->SmtpMail("Dave@micro-automation.net","Dave Cramer", 
           "Dave@micro-automation.net","David", 
           "Test Mail",$MailMessage,"service.tab",0); 
$MailTo->Close(); 
$MailTo=null; 

*/ 
/* 
 $pop3 = pop3_open("localhost", "110"); 
 if (!$pop3) { 
                printf("[ERROR] Failed to connect to localhost<BR>\n"); 
                return 0; 
 } 

 if (!pop3_user($pop3, "unk")) { 
                printf("[ERROR] Username failed!<BR>\n"); 
                return 0; 
 } 

 if (!pop3_pass($pop3, "secret")) { 
                printf("[ERROR] PASS failed!<BR>\n"); 
                return 0; 
 } 

 $articles = pop3_list($pop3); 
 if (!$articles) { 
                printf("[ERROR] LIST failed!<BR>\n"); 
                return 0; 
 } 

 for ($i = 1; $i < $articles ["count"] + 1; $i++) 
 { 
                printf("i=$i<BR>\n"); 
                $data = pop3_retr($pop3,$i); 
                if (!$data) { 
                                printf("data goes wrong on '$i'<BR>\n"); 
                                return 0; 
                } 

                for ($j = 0; $j < $data["count"]; $j++) 
                { 
                                printf("$data[$j]<BR>\n"); 
                } 
 } 
*/ 
?> 
