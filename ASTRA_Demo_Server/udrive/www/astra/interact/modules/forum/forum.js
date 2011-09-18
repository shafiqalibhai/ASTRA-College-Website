dojo.require("dojo.widget.*");
dojo.setModulePrefix('interact_forum','../../modules/forum/js');
dojo.widget.manager.registerWidgetPackage('interact_forum.widget');
dojo.setModulePrefix('interact','interact');
dojo.widget.manager.registerWidgetPackage('interact.widget');
//dojo.require('interact_forum.widget.PostReply');
//dojo.require('interact.widget.PostThread');
dojo.require('interact_forum.widget.ForumThread');
dojo.require('interact_forum.widget.ForumPost');
dojo.require('interact_forum.widget.ForumPostReply');
dojo.require("dojo.widget.Tooltip");


function getPostBody(postKey,parent,callerID) {
/*	
var divTemplate = dojo.byId('testNode');
var newDiv = divTemplate.cloneNode(true);
newDiv.id = 'newDiv1';
newDiv.innerHTML = 'Will this new div appear!';
dojo.dom.insertBefore(newDiv, dojo.byId(callerID));
var link = document.createElement('a');
//dojo.html.toggleShowing(newDiv);
//var anim1 = dojo.lfx.fadeShow('newDiv1', 1000);
//anim1.play();
dojo.html.hide(newDiv);
dojo.html.setOpacity(newDiv, 0);
dojo.lfx.fadeShow(newDiv,1000).play();
return;
//dojo.lfx.wipeIn(newDiv,1000).play();
*/


	
	
	if (!dojo.html.isShowing('messageBody'+postKey)){
		show_it('messageBody'+postKey,'forumPostLink'+postKey,'disclosure');
		if (parent==true) {
			if (!dojo.html.isShowing('threadDiv'+postKey)){
				dojo.html.toggleShowing('threadDiv'+postKey);
			}
		}
		getHTML('messageBody'+postKey,fullUrl+'/modules/forum/getbody.php',{post_key:postKey},false,false, callerID, 'disclosure');
		
	} else {
		show_it('messageBody'+postKey,'forumPostLink'+postKey,'disclosure');
		if (parent==true) { 
			if (dojo.html.isShowing('threadDiv'+postKey)){
				dojo.html.toggleShowing('threadDiv'+postKey);
			}
		}
	}
	
}
function getQuickReply(postKey, callerID) {

	//show_it('quickReply'+postKey,'quickReplyLink'+postKey,'forumPost');
	//if(dojo.dom.isNode('quickReplyFormDiv'+postKey)) {
		if (dojo.html.isShowing('quickReply'+postKey)){
			dojo.byId('qRBodyError'+postKey).innerHTML='';
			dojo.html.setOpacity('quickReplyForm'+postKey, 100);
			dojo.html.toggleShowing('quickReplyForm'+postKey);
			dojo.html.setClass('quickReplyLink'+postKey,'disclosureOpen');
		//}
	} else {
		getHTML('quickReply'+postKey,fullUrl+'/modules/forum/quickreply.php',{post_key:postKey},false,true, callerID, 'disclosure');
		
	}
}
function submitReply(postKey,replyID) {
	
	

	formID = 'quickReplyForm'+postKey;
	if(dojo.byId('qRBody'+postKey).value=='') {
		dojo.byId('qRBodyError'+postKey).innerHTML='You need to enter something dingbat!';
		return false;
	}
	var newReply = document.createElement('div');
	newReply.id = dojo.dom.getUniqueId;
	dojo.dom.insertBefore(newReply, dojo.byId(formID));
	dojo.dom.insertAfter(newReply, dojo.byId('quickReply'+postKey));
	
	dojo.html.setOpacity(newReply, 0);
	//getHTML(newReply.id,fullUrl+'/modules/forum/quickreply.php','',formID,true,'quickReplyLink'+postKey,'disclosure')
	
	dojo.html.setClass('quickReplyLink'+postKey,'disclosureWaiting');
	url = fullUrl+'/modules/forum/quickreply.php';
	dojo.io.bind({
    	url: url,
    	handle: function(type, data, evt){
       
    		if(type == 'load'){
   				document.getElementById(newReply.id).innerHTML=data;
				if (!dojo.html.isShowing(newReply.id)){
					dojo.html.toggleShowing(newReply.id);
				}
    			hideInput = dojo.lfx.fadeHide('quickReplyForm'+postKey,1000,'easeOut');    						showReply = dojo.lfx.fadeShow(newReply,1000);
    			dojo.lfx.Chain(hideInput, showReply).play();
				dojo.byId('qRBody'+postKey).value='';
				dojo.byId('qRSub'+postKey).value='';
				dojo.html.setClass('quickReplyLink'+postKey,'disclosureClosed');	
				//show_it('quickReplyForm'+postKey,'quickReplyLink'+postKey,'disclosure');
				//dojo.html.toggleShowing('quickReplyForm'+postKey); 
    			 				
   			}else {
				document.getElementById(newReply.id).innerHTML='There was an error - please try again';
			}
   	 	},
    	mimetype: 'text/plain',
    	formNode: dojo.byId(formID)
    
    });
	//dojo.lfx.fadeHide('quickReplyForm'+postKey,2000,'easeOut',).play();

	
	
}