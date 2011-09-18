
function init_editor(textarea_Id,dojoType,attr) {
	dojo.require("dojo.widget.Editor2Plugin.bFilters");
	dojo.require("dojo.widget.Editor2Plugin.BlockQuote");
	dojo.require("dojo.widget.Editor2Plugin.TableOperation");
	dojo.require("dojo.widget.Editor2Plugin.DropDownList");
	dojo.require("dojo.widget.Editor2Plugin.ColorPicker");
	dojo.require("dojo.widget.Editor2");	
	var edbutton=document.getElementById('button_for_'+textarea_Id);
	
	if(edbutton) {
		edbutton.style.display='none'; 	attr.easyEditClicked=true;
	}

	var formnode,pnode;
	formnode = pnode = document.getElementById(textarea_Id);
	
	for(var i=2;i--;) {
		pnode=pnode.parentNode;
 		if(dojo.html.hasClass(pnode,'InputFormRight')) {
 			dojo.html.replaceClass(pnode,'InputFormRightEd','InputFormRight');
 			break;
 		}
	}
	
	attr.toolbarAlwaysVisible=true;
	attr.toolbarTemplatePath=dojo.uri.dojoUri('src/widget/templates/InteractEditorToolbar.html');
	attr.preFilterTextarea=true;

	attr.contentPreFilters=[dojo.widget.Editor2Plugin.bFilters.untidy_tags];
	attr.contentPostFilters=[dojo.widget.Editor2Plugin.bFilters.tidy_tags];

	//match css of window...  just grabs first stylesheet, so not very smart
	var i = 0, a, els = window.document.getElementsByTagName("link");
	while (a = els[i++]) {
		if (a.getAttribute("rel").indexOf("style") != -1 && !a.disabled) {
			attr.styleSheets=a.getAttribute("href")+';'+dojo.uri.dojoUri('src/widget/templates/Editor2/Editor-only_styles.css');
			break;
		}
	}
	return dojo.widget.createWidget(dojoType, attr, formnode);
}

function load_inline_edit(contentNode, bodyUrl) {
	
	dojo.byId(contentNode+'EditLink').style["display"]='none';
	dojo.byId(contentNode+'Save').style["display"]='block';
	dojo.byId('message').innerHTML='';
	dojo.io.bind({
    	url: fullUrl+bodyUrl,
    	handle: function(type, data, evt){

			if(type == 'load' && data!=0){
				dojo.byId(contentNode).innerHTML = data;
				current_content = dojo.byId(contentNode).innerHTML;
				inline_editor = init_editor(contentNode,'Editor2', {toolbarAlwaysVisible: true,toolbarTemplatePath: dojo.uri.dojoUri('src/widget/templates/InteractEditorToolbar.html'),styleSheets:'{PATH}/skins/skin.php?skin_key=1',easyEditClicked:true});
   			}else {
				
				dojo.html.setClass('message','error');
   				dojo.byId('message').innerHTML='There was a problem loading the page content into editor';
   			}
   	 	},
   	 	method: 'post',
    	mimetype: 'text/plain',
    	formNode: dojo.byId(contentNode+'SaveForm'),
    	content: '' 
    });
	

	//if (dojo.render.html.moz) {
		//dojo.event.connect(inline_editor, 'toolbarLoaded',inline_editor,'load_finished');
	//}
	
}
function load_finished() {
	dojo.byId('message').innerHTML='';
	dojo.html.setClass('message','message');
}
function save_inline_edit(contentNode,saveUrl) {
	
	dojo.html.setClass('message','loading');
	dojo.byId('message').innerHTML='Saving ...';
	new_content = inline_editor.getEditorContent();
	dojo.byId(contentNode+'SaveNode').value=new_content;
	dojo.io.bind({
    	url: fullUrl+saveUrl,
    	handle: function(type, data, evt){
    		
    		if(type == 'load' && data!=0){
				inline_editor.destroy();
	  			dojo.byId(contentNode).innerHTML=data;
	  			dojo.byId(contentNode+'EditLink').style["display"]='block';
				dojo.byId(contentNode+'Save').style["display"]='none';
				dojo.html.setClass('message','message');
				dojo.byId('message').innerHTML='Changes saved';
   			}else {
				dojo.html.setClass('message','error');
   				dojo.byId('message').innerHTML='Save failed, please try again';
   			}
   	 	},
   	 	method: 'post',
    	mimetype: 'text/plain',
    	formNode: dojo.byId(contentNode+'SaveForm'),
    	content: '' 
    });

}

function cancel_inline_edit(contentNode) {

	if (confirm('Are you sure - any changes will be lost')) {
		inline_editor.destroy();
		dojo.byId(contentNode+'EditLink').style["display"]='block';
		dojo.byId(contentNode+'Save').style["display"]='none';
		dojo.byId('message').innerHTML='';
	} else {
		return;
	}
}


function make_bold(textarea_Id) {
        bwords = prompt("Enter the words you would like in bold:","");
        if (bwords != null) {
        document.getElementById(textarea_Id).value += ' <b>'+bwords+'</b> '; 
                

        }
        document.getElementById(textarea_Id).focus();
}
function make_italics(textarea_Id) {
        iwords = prompt("Enter the words you would like in italics:","");
        if (iwords != null) {
                document.getElementById(textarea_Id).value += ' <i>'+iwords+'</i> ';
        }
        document.getElementById(textarea_Id).focus();
}
function make_link(textarea_Id) {
        alink = prompt("Enter the url of the link:", "http://");
        linktext = prompt("Enter the text to display for link:", "");
        if ((alink != null) && (alink != "http://")) {
            if (linktext != null) {
                document.getElementById(textarea_Id).value += ' <a href="'+alink+'">'+linktext+'</a> ';
            } 
        }
        document.getElementById(textarea_Id).focus();
}
function make_email_link(textarea_Id) {
        alink = prompt("enter the email adress:", "");
        if ((alink != null) && (alink != "http://")) {
                document.getElementById(textarea_Id).value += ' <a href="mailto:'+alink+'">'+alink+'</a> ';
        }
        document.getElementById(textarea_Id).focus();
}

function get_image(path,tid) {
	return Dialog(path+'/includes/editor/popups/insert_image.php?basic=1', function(param) {
		if (param) make_image(tid,param);
	}, null);
}

function make_image(textarea_Id,param) {
	document.getElementById(textarea_Id).value += '<img src="'+param.f_url+'"'+(param.f_width? ' width="'+param.f_width+'"':'')+(param.f_height? ' height="'+param.f_height+'"':'')+'>';
	document.getElementById(textarea_Id).focus();
}

function display_html(textarea_Id) {
    var bottom = document.getElementById(textarea_Id).value;
    bottom = bottom.replace(/\n/g, '<br />'); 
    win = window.open("", "popup", "width=400,height=300,scrollbars=yes");
    win.document.write("<p>",bottom,"</p>");
    win.document.write("<p align='center'><a href='javascript:close()'>Close Preview</a></p>");
    win.document.write(tagParaClose);
    win.document.close();
}

function confirmDelete(message)
{
return confirm(message);
}

var newWindow = null;

function open_window(the_url,window_name,height,width) { 
  if(!height || height==0) {
	  height = '500';

  }
  if(!width || width==0) {
  	width = '400';
  }
  newWindow = window.open(the_url,window_name,'scrollbars=yes,width='+width+',height='+height+',resizable=yes');
  newWindow.focus();
}

function openWin(the_url) {
	newWindow = window.open (the_url,"progress","toolbar=no,scrollbars=no,width=200,height=150,menubar=no,location=no,resizable=no");
}
function openHelpWin(the_url) {
	newWindow = window.open (the_url,"progress","toolbar=no,scrollbars=yes,width=640,height=400,menubar=no,location=no,resizable=yes");
}

function closeWin() {
    if (newWindow) newWindow.close();
	
}
function openBrWindow(theURL,winName,features) { 
  window.open(theURL,winName,features);
}

var form='form1' //Give the form name here
var val=''
function SetChecked(val,chkName) {
    dml=document.forms[form];
    len = dml.elements.length;
    var i=0;
    for( i=0 ; i<len ; i++) {
        if (dml.elements[i].name==chkName) {
            dml.elements[i].checked=val;
        }
    }
}
var _w = null;
function createNew(writeText) {
  _w = window.open('',
'popUpWin', 'toolbar=no,location=no,directories=no,status=no,menub ar=no,scrollbar=no,resizable=no,copyhistory=yes,width=400,height=400');
  _w.document.open();
  _w.document.write('<html><head><title>Help Window</title></head><body>');
  _w.document.write(writeText);
  _w.document.write('<br /><div align="center"><a href="javascript:self.close()">Close this window</a></div>');
  _w.document.write('</body></html>');

}
function openDir(form) { 

	var newIndex = form.URL.selectedIndex; 

	if ( newIndex != 1 ) { 
		window.location.href=form.URL.options[ newIndex ].value ; 
	} 

} 
//var colourOpeningField;
function colorPicker_callBack(strColor) {
	document.getElementById(colourOpeningField).value = '#'+strColor;
	document.getElementById(colourOpeningField).focus();
	if(document.getElementById(colourOpeningField).onchange) {
		document.getElementById(colourOpeningField).onchange();
	}
}

function openColorPickerAdv(formField) {
	colourOpeningField = formField;
	window.open(fullUrl+'/includes/colourpicker/colorPickerAdv.html',
'colourPicker', 'toolbar=no,location=no,directories=no,status=no,menub ar=no,scrollbar=no,resizable=no,copyhistory=yes,width=350,height=142,screenX=400,screenY=400,top=400,left=400');
}

function closeColorPickerAdv() {
	window.colourPicker.close();
}
function changeStyle(element, value ,elementStyle) {

	if (elementStyle=='backgroundImage') {
		if(value.substr(0,3)!='url' && value!='none' && value!='') value = 'url('+value+')';
	}
	document.getElementById(element).style[elementStyle]=value;
}
// function setActiveStyleSheet(title) {
//   var i, a, main;
//   for(i=0; (a = document.getElementsByTagName("link")[i]); i++) {
//     if(a.getAttribute("rel").indexOf("style") != -1 && a.getAttribute("title")) {
//       a.disabled = true;
//       if(a.getAttribute("title") == title) a.disabled = false;
//     }
//   }
// }
function changeStyleSheet(t,vars,version) { // t can be a skin_key or a template name
	if(!version) {
		var d=new Date();
		version=d.getTime();
	}
	var skin_link=document.getElementById("skinLink");
	skin_link.href=fullUrl+'/skins/skin.php?'+ 
		(parseInt(t)? 'skin_key=':'template=')+
		t+'&skin_version='+version+(vars?'&'+vars:"");
}

function admin_toggle() {
	var togImg=dojo.byId('admin_tool_button');
	if(togImg) {
		if(togImg.src.indexOf('closed')==-1) {
			setStyleByClass('*','admin_tool','display','none');
			togImg.src=togImg.src.replace("open","closed");
			sendDiscloseStatus('admin_tool_button',1);
		} else {
			setStyleByClass('*','admin_tool','display','');
			togImg.src=togImg.src.replace("closed","open");
			sendDiscloseStatus('admin_tool_button',0);
		}
	}
}




// setStyleByClass: given an element type and a class selector,
// style property and value, apply the style.
// args:
//  t - type of tag to check for (e.g., SPAN)
//  c - class name
//  p - CSS property
//  v - value  (prefix of ! means 'toggle value'

function setStyleByClass(t,c,p,v){
	var elements;
	var ie = (document.all) ? true : false;
	var toggle=false;

	if(v.charAt(0)=="!"){toggle=true;v=v.substr(1);}
	var vval=v;

	if(t == '*') {
		// '*' not supported by IE/Win 5.5 and below
		elements = (ie) ? document.all : document.getElementsByTagName('*');
	} else {
		elements = document.getElementsByTagName(t);
	}
	for(var i = 0; i < elements.length; i++){
		var node = elements.item(i);
		if(dojo.html.hasClass(node,c)) {
			if (toggle) {vval=((eval('node.style.'+p))!=v)?v:''}
			eval('node.style.' + p + " = '" +vval + "'");
		}
	}
}


// disclosee:     id of object to show/hide
// discloser_img_obj: object containing open/close string/url tag
// discloser_tag: name of tag in disclosee object (e.g: 'src', 'backgroundImage')
// sendStatus: (optional) set to 0 to NOT send status back to server
function disclose_it(disclosee,discloser_img_obj,discloser_tag,sendStatus) {

 	var disclosee_o = dojo.byId(disclosee);
	var opening=(disclosee_o.style.display == "none" || 
		(dojo.html.getClass(disclosee_o)=='jsHide' && disclosee_o.style.display==''));

	discloser_img_obj[discloser_tag] = 
		discloser_img_obj[discloser_tag].replace(
			opening?'closed':'open',
			opening?'open':'closed');
	
	disclosee_o.style.display=opening?'block':'none';
	
	if (sendStatus!==0) {sendDiscloseStatus(disclosee,opening?0:1);}
	return opening;
}

function disclose_and_select(disclosee,discloser_img_obj,discloser_tag){
	if(disclose_it(disclosee,discloser_img_obj,discloser_tag)) {
		var tnode=document.getElementById(disclosee+'_text');
		tnode.focus(); tnode.select();
	}
}

function show_it(disclosee,discloser,classPrefix) {
	if (dojo.html.toggleShowing(disclosee)==true){
		dojo.html.setClass(discloser,classPrefix + 'Open');
		obj=new Object;
		obj[discloser.id]=1;
		dojo.io.cookie.setObjectCookie('disclosures', obj, 1, '', '', '', false);
	} else {
		dojo.html.setClass(discloser.id,classPrefix + 'Closed');
		obj=new Object;
		obj[discloser.id]=0;
		dojo.io.cookie.setObjectCookie('disclosures', obj, 1, '', '', '', false);
	}
	
}
function stretchTextArea(stretchee,stretcher,max,min,classPrefix) {
	if (document.getElementById(stretchee).rows==min) {
		document.getElementById(stretchee).rows=max;
		dojo.html.setClass(stretcher,classPrefix + 'Up');	
	} else {
		document.getElementById(stretchee).rows=min;
		dojo.html.setClass(stretcher,classPrefix + 'Down');
	}
}
function showhide(obj,lyr,showhide)
{
	if (showhide=='hidden') {
		clearTimeout(show_timeout);
		hide_timeout = setTimeout("showhide2('"+lyr+"','"+showhide+"')",200);
	} else {
		show_timeout = setTimeout("showhide2('"+lyr+"','"+showhide+"')",2000);
		setLyr(obj,lyr);
	}
	//over_event=0;
}
over_event=0;
function keeppopup(lyr) {
	clearTimeout(hide_timeout);

}

function killpopup(e,lyr) {

	if (!e) var e = window.event;
	var tg = (window.event) ? e.srcElement : e.target;
	if (tg.nodeName != 'DIV') return;
	var reltg = (e.relatedTarget) ? e.relatedTarget : e.toElement;
	while (reltg != tg && reltg.nodeName != 'BODY')
		reltg= reltg.parentNode
	//alert(reltg.nodeName+' '+tg.nodeName);
		if (reltg== tg) return;
	// Mouseout took place when mouse actually left layer
	// Handle event
		var x = dojo.byId(lyr);
		x.style.visibility = 'hidden';	

}
function killpopup2(e,lyr) {

	if (!e) var e = window.event;
	var reltg = (e.relatedTarget) ? e.relatedTarget : e.toElement;
	if (reltg.id==lyr)return;
	var x = dojo.byId(lyr);
	x.style.visibility = 'hidden';	
}

function contains(a, b) {

  // Return true if node a contains node b.

  while (b.parentNode)
    if ((b = b.parentNode) == a)
      return true;
  return false;
}
function showhide2(lyr,showhide) {
	var x = dojo.byId(lyr);
	x.style.visibility = showhide;	
}
function setLyr(obj,lyr)
{
	var newX = findPosX(obj)+135;
	var newY = findPosY(obj)-0;
	//if (lyr == 'testP') newY -= 50;
	var x = dojo.byId(lyr);
	x.style.top = newY + 'px';
	x.style.left = newX + 'px';
}

function findPosX(obj)
{
	var curleft = 0;
	if (obj.offsetParent)
	{
		while (obj.offsetParent)
		{
			curleft += obj.offsetLeft
			obj = obj.offsetParent;
		}
	}
	else if (obj.x)
		curleft += obj.x;
	return curleft;
}

function findPosY(obj)
{
	var curtop = 0;
	var printstring = '';
	if (obj.offsetParent)
	{
		while (obj.offsetParent)
		{
			printstring += ' element ' + obj.tagName + ' has ' + obj.offsetTop;
			curtop += obj.offsetTop
			obj = obj.offsetParent;
		}
	}
	else if (obj.y)
		curtop += obj.y;
	window.status = printstring;
	return curtop;
}

function displayObj(name,show) {
	var x=dojo.byId(name);
	x.style.display=(show?'':'none');
}

function selectTag(field,tagText) {
	
	//trim any trailing whitespace from tags field
	var re = /((\s*\S+)*)\s*/;
	tagField = document.getElementById(field).value.replace(re, "$1");
	if (tagField=='' || tagField.substring(tagField.length-1)==',') {
		delimiter = '';	
	} else {
		delimiter = ',';
	}

	document.getElementById(field).value = tagField+delimiter+' '+tagText;
}



function getHTML(objID, url, pars,formID,overWrite,callerID,callerClass)
{
	
	if (document.getElementById(objID).innerHTML!=''  && overWrite!=true) {
		return;
	}
	
	dojo.html.setClass(callerID,callerClass+'Waiting');
	
	dojo.io.bind({
    	url: url,
    	handle: function(type, data, evt){
       
    		if(type == 'load'){
   				
    			document.getElementById(objID).innerHTML=data;
				if (!dojo.html.isShowing(objID)){
					dojo.html.toggleShowing(objID);
				}
    			dojo.html.setClass(callerID,callerClass+'Open');
    			
    			//var replacedNode = document.getElementById("forumPostBody155");
				//dojo.widget.createWidget("PostReply", {}, replacedNode);
				//dojo.widget.createWidget("forumPostBody155")
				dojo.widget.createWidget(objID);
		//dojo.widget.createWidget('quickReplyLink155'); 
    			//return true;  				
   			}else {
				document.getElementById(objID).innerHTML='There was an error - please try again';
			}
   	 	},
    	mimetype: 'text/plain',
    	formNode: dojo.byId(formID),
    	content: pars 
    });
	

}

//messaging.js

function showMessage(countText) {
	
	if (countText>messageCount) {

		
		if (document.getElementById("messageAlert")) { 
				
			var bingTags = '<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=5,0,0,0" width="20" height="20" id="bing" align="middle"> <param name="allowScriptAccess" value="sameDomain" /> <param name="wmode" value="transparent" /><param name="movie" value="'+fullUrl+'/messaging/messagebing.swf?snd=1" /> <param name="quality" value="high" /> <param name="bgcolor" value="#ffffff" /> <embed src="'+fullUrl+'/messaging/messagebing.swf?snd=1"  quality="high" bgcolor="#ffffff" width="20" height="20" name="bing" align="middle" allowScriptAccess="sameDomain" wmode="transparent" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" /></object> ';
			document.getElementById('messageAlert').innerHTML = bingTags+'<a href="javascript:open_window(\''+fullUrl+'/messaging/message_admin.php?action=read\',\'messsageAdmin\',\'features\')">'+messagetrans+'</a> (<span id="messageCount">'+countText+'</span>)';
			self.focus();
			document.getElementById("messageAlert").style["display"]='block';
		}
			
	} else {
		document.getElementById('messageCount').innerHTML = countText;
	}
	messageCount=countText;
}

function checkMessages() {
	dojo.io.bind({
    	url: fullUrl+'/messaging/check.php',
    	handle: function(type, data, evt){
    		if(type == 'load'){
    			
    			if (data>0){
					showMessage(data);
				} else {
					if (document.getElementById("messageAlert")) {
						document.getElementById("messageAlert").style["display"]='none';
					}
				}
   			} else {
				
			}
   	 	},
    	mimetype: 'text/plain'
    });	
    setMessagePollTime();
}

function setMessagePollTime() {
	setTimeout("checkMessages()", message_refresh);	
}

function sendDiscloseStatus(hmodule,hstatus) {
	pars = {'module':hmodule,'status':hstatus};	
	dojo.io.bind({
    	url: fullUrl+'/messaging/setDiscloseStatus.php',
    	mimetype: 'text/plain',   	
    	content: pars
    });
}

String.prototype.trim = function() { return this.replace(/^\s+|\s+$/g, ""); };
