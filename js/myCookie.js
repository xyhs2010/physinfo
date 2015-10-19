function setMyCookie(id, passwd, expirehours) {
    var exdate=new Date()
    exdate.setDate(exdate.getDate()+expirehours)
    document.cookie="username="+ id+ escape(passwd)+((expirehours==null) ? "" : ";expires="+exdate.toGMTString());
}

function getMyCookie() {
    if (document.cookie.length>10) {
        var id = document.cookie.substring(9,19);
        var c_end=document.cookie.indexOf(";",19);
        if (c_end==-1) c_end=document.cookie.length
        var passwd = unescape(document.cookie.substring(19, c_end));
        var res = {
            id          :id,
            password    :passwd
        }
        return res;
    }
    return null;
}

function delMyCookie() {
    setMyCookie(getMyCookie().id,getMyCookie().password,-1);
}

function transform(url, str, cfunc)
{
	var xmlhttp;
	if (window.XMLHttpRequest)
	{// code for IE7+, Firefox, Chrome, Opera, Safari
		xmlhttp=new XMLHttpRequest();
	}
	else
	{// code for IE6, IE5
		xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	}
	xmlhttp.onreadystatechange=function () {
		cfunc(xmlhttp);
	}
	xmlhttp.open("POST",url,true);
	xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	xmlhttp.send(str);
}

function htmlspecialchars(str)    
{    
    str = str.replace(/&/g, 'xyhstamp');  
    str = str.replace(/</g, 'xyhstlt');  
    str = str.replace(/>/g, 'xyhstgt');  
    str = str.replace(/"/g, 'xyhstquot');  
    str = str.replace(/'/g, 'xyhst#039');  
    return str;  
}  
function htmlspecialchars_decode(str)    
{    
    str = str.replace(/xyhstamp/g, '&');  
    str = str.replace(/xyhstlt/g, '<');  
    str = str.replace(/xyhstgt/g, '>');  
    str = str.replace(/xyhstquot/g, '"');  
    str = str.replace(/xyhst#039/g, '\'');  
    return str;  
}  
