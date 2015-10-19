#-*- coding:utf-8 -*-
import re
import urllib2
import codecs
import json
def phys():
	url = 'http://www.phys.tsinghua.edu.cn/publish/phy/5286/index.html'
	data = urllib2.urlopen(url).read().decode('utf-8')
	start = data.find("<div class=\"box_info_list\">")
	start = data[start:].find("<ul>")+start
	end = data[start:].find("<script src=\"/publish/phy/js/porto.js\" type=\"text/javascript\">")
	content1 = data[start:start+end]
	content1 = content1.replace("href=\"/publish/phy/5286/","target=\"_blank\" href=\"http://www.phys.tsinghua.edu.cn/publish/phy/5286/")
	p = re.compile(r"(<a target=.*?)\n.*?([0-9]{4}-[0-9]{1,2}-[0-9]{1,2}).*?\n(.*?<\/a>)")
	grp1 = p.findall(content1)
	grp1n = []
	for i in grp1:
		grp1n.append([i[0]+i[2], i[1]])
	name1 = "物理系".decode('utf-8')
	return (grp1n, name1)

def castu():
	url='http://www.castu.tsinghua.edu.cn/publish/cas/944/index.html'
	data = urllib2.urlopen(url).read().decode('utf-8')
	start = data.find("<div class=\"box_list\">")
	end = data[start:].find("</div>")
	content2 = data[start:start+end]
	content2 = content2.replace("href=\"/publish/cas/944/","target=\"_blank\" href=\"http://www.castu.tsinghua.edu.cn/publish/cas/944/")
	p = re.compile(r"(<a target=.*?)<p>([0-9]{4}-[0-9]{1,2}-[0-9]{1,2})<\/p>")
	grp2 = p.findall(content2)
	name2 = "高等研究院".decode('utf-8')
	return (grp2, name2)

def quantum():
	url='http://quantum.phys.tsinghua.edu.cn/'
	data = urllib2.urlopen(url).read().decode('gbk')
	start = data.find("<td height=\"316\" width=\"351\" bgcolor=\"#FFFFFF\"><ul>")+47
	end = data[start:].find('</td>')
	content3 = data[start:start+end]
	content3 = content3.replace("href=\"/xsjlMore.asp/","target=\"_blank\" href=\"http://quantum.phys.tsinghua.edu.cn/xsjlMore.asp/")
	p = re.compile(r"<li>.*?(<A target=.*?)<span.*?>([0-9]{4}-[0-9]{1,2}-[0-9]{1,2})<\/span>")
	grp3 = p.findall(content3)
	grp3n = []
	for i in grp3:
		grp3n.append([i[0]+"</A>", i[1]])
	name3 = "低维量子实验室".decode('utf-8')
	return (grp3n, name3)

# def info1():
# 	url='http://oars.tsinghua.edu.cn/ztg/92390.nsf/1be?ReadForm&TemplateType=2&AutoFramed'
# 	content4 = urllib2.urlopen(url).read().decode('gbk')
# 	content4 = content4.replace("/ztg/","http://oars.tsinghua.edu.cn/ztg/")
# 	p = re.compile(ur"(<a href =.*?_blank>)\uff08([0-9]{4}\.[0-9]{1,2}\.[0-9]{1,2})\uff09(.*?<\/a>)")
# 	grp4 = p.findall(content4)
# 	grp4n = []
# 	for i in grp4:
# 		grp4n.append([i[0]+i[2], i[1]])
# 	name4 = "综合通告".decode('utf-8')
# 	return (grp4n, name4)

# def info2():
# 	url='http://oars.tsinghua.edu.cn/zzh/30630.nsf/2de?ReadForm&TemplateType=2&AutoFramed'
# 	content5 = urllib2.urlopen(url).read().decode('gbk')
# 	content5 = content5.replace("/zzh/","http://oars.tsinghua.edu.cn/zzh/")
# 	p = re.compile(ur"(<a href =.*?_blank>)\uff08([0-9]{4}\.[0-9]{1,2}\.[0-9]{1,2})\uff09(.*?<\/a>)")
# 	grp5 = p.findall(content5)
# 	grp5n = []
# 	for i in grp5:
# 		grp5n.append([i[0]+i[2], i[1]])
# 	name5 = "教务信息".decode('utf-8')
# 	return (grp5n, name5)

contentList=[]
contentList.append({"name":phys()[1], "content":phys()[0]})
contentList.append({"name":castu()[1], "content":castu()[0]})
contentList.append({"name":quantum()[1], "content":quantum()[0]})
# contentList.append({"name":info1()[1], "content":info1()[0]})
# contentList.append({"name":info2()[1], "content":info2()[0]})

jsonOb = json.dumps(contentList)
f = codecs.open('lectures.json','w','utf-8')
f.write(jsonOb)
f.close
