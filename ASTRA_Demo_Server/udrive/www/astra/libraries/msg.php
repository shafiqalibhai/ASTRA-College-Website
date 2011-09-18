<head><style>
.jc{
position:relative;
}
</style>

<script language="JavaScript1.2">
var ns6=document.getElementById&&!document.all
var ie=document.all

var customcollect=new Array()
var i=0

function jiggleit(num){
if ((!document.all&&!document.getElementById)) return;
customcollect[num].style.left=(parseInt(customcollect[num].style.left)==-1)? customcollect[num].style.left=1 : customcollect[num].style.left=-1
}

function init(){
if (ie){
while (eval("document.all.jiggle"+i)!=null){
customcollect[i]= eval("document.all.jiggle"+i)
i++
} 
}
else if (ns6){
while (document.getElementById("jiggle"+i)!=null){
customcollect[i]= document.getElementById("jiggle"+i)
i++
}
}

if (customcollect.length==1)
setInterval("jiggleit(0)",80)
else if (customcollect.length>1)
for (y=0;y<customcollect.length;y++){
var tempvariable='setInterval("jiggleit('+y+')",'+'100)'
eval(tempvariable)
}
}

window.onload=init
</script>
</head>
<script type="text/javascript">

var browser_type=navigator.appName
var browser_version=parseInt(navigator.appVersion)

//if NS 6
if (browser_type=="Netscape"&&browser_version>=5)
document.write('Nice to see people using Firefox <img src=\"images/mini_smile.gif\" align=\"absmiddle\" />');
//if IE 4+
else if (browser_type=="Microsoft Internet Explorer"&&browser_version>=4)
document.write("<font color=\"#FF0000\">You are using Internet Explorer<br> Please download &nbsp; </font><a href=\"http://www.firefox.com\" target=\"_blank\"><span id=\"jiggle0\" class=\"jc\">Firefox</span>&nbsp;</a>");
//if NS4+
else if (browser_type=="Netscape"&&browser_version>=4)
document.write('You are using Netscape<br> Please download &nbsp; <a href=\"http://www.firefox.com\" target=\"_blank\"><span id=\"jiggle0\" class=\"jc\">Firefox</span>&nbsp;</a>');
//Default (NOT NS 4+ and NOT IE 4+)
else if (browser_type=="Opera"&&browser_version>=4)
document.write('Nice to see people using Opera <img src=\"images/mini_smile.gif\" align=\"absmiddle\" />');
else
document.writeln('Please download &nbsp; <a href=\"http://www.firefox.com\"><span id=\"jiggle0\" class=\"jc\">Firefox</span></a> for a better experience')
</script>