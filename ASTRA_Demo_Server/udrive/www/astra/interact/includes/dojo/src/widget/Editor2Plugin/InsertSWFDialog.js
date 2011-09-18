/*
	Copyright (c) 2004-2006, The Dojo Foundation
	All Rights Reserved.

	Licensed under the Academic Free License version 2.1 or above OR the
	modified BSD license. For more information on Dojo licensing, see:

		http://dojotoolkit.org/community/licensing.shtml
*/

dojo.provide("dojo.widget.Editor2Plugin.InsertSWFDialog");

dojo.widget.defineWidget(
	"dojo.widget.Editor2InsertSWFDialog",
	dojo.widget.Editor2DialogContent,
{
	templatePath: dojo.uri.dojoUri("src/widget/templates/Editor2/Dialog/insertswf.html"),

	loadContent: function(){
		var curInst = dojo.widget.Editor2Manager.getCurrentInstance();

		this.imageNode = dojo.withGlobal(curInst.window, "getSelectedElement", dojo.html.selection);

		if(!this.imageNode || this.imageNode.tagName.toUpperCase() != 'IMG'){
			this.imageNode = dojo.withGlobal(curInst.window, "getAncestorElement", dojo.html.selection, ['img']);
		}
		
		if(!this.imageNode){
			curInst.saveSelection(); //save selection (none-activeX IE)
		}

		this.extraAttribText = "";
		this.idir.src='about:blank';
		this.w=0;this.h=0;

		var isrc=null;
		if(this.imageNode){
			var attrs = this.imageNode.attributes;
			
			var srcvars=attrs.src.value;

			var src_i=srcvars.indexOf('src=');
			var src_e=srcvars.indexOf('&',src_i);
			if(src_e==-1) {
				isrc=srcvars.substring(src_i+4,srcvars.lastIndexOf("XXCUSTOMSWFUXX")); this.swf_flashvars.value='';
			} else {
				isrc=srcvars.substring(src_i+4,src_e);
				if(srcvars.indexOf('&amp;',src_i)==src_e) src_e+=4;

this.swf_flashvars.value=srcvars.substring(src_e+1,srcvars.lastIndexOf("XXCUSTOMSWFUXX"));
				
			}	
			this.setSize(attrs.width.value,attrs.height.value);
		} else {
			this.swf_flashvars.value='';
			this.setSize(0,0);
		}
		this.setURL('',1,'');
		this.premess.innerHTML='';
//		}
//		this.checkok();
//	alert('src='+isrc+' fv='+this.swf_flashvars.value);
		this.premess.innerHTML='&nbsp;';
//		dojo.debug(isrc+'--');
	
// can't focus on IE
		if(!dojo.render.html.ie) this.swf_src.focus();

		dojo.event.topic.subscribe("/idir/newfile", this, "newFile");
		dojo.event.topic.subscribe("/ipreview/mess", this, "setMessage");
		this.idir.src=dojo.uri.dojoUri('../editor/popups/')+"file_dir.php?allowed_file_types=swf,zip&maxSize=300:5000:10000&trytofindurl="+isrc+"&callfirst=1";
		return true;
	},
	setMessage: function(dur,size,w,h,setsize,valid,pc,fps,version) {
		var pm='';
//		alert(setsize);
		if(size==0 || !valid) {
			pm='<span class="espan">Invalid swf file!</span>';
		} else {
			if(pc) {
				pm+= w+"x"+h+" pixels &bull; ";
				this.w=w;this.h=h;
				if(pc!=100)	pm+="shown at "+pc+"% &bull; ";
			}
			pm+=Math.ceil(size>>10)+"KB ("+Math.ceil(size/5000)+"s@56K)";
			if(fps) pm += ' &bull; '+fps+'fps';
			if(version) pm+= ' &bull; f'+version;
		}
		this.premess.innerHTML=pm;
		if(setsize) this.setSize(w,h);
		this.checkSize();
	},
	setSize:function(w,h) {
		this.w=w;this.h=h;
		if(w===0) {
			this.image_width.value=this.image_height.value='';
		} else {
			this.image_width.value=w;
			this.image_height.value=h;
		}
	},
	setURL: function(url,nclicks,fullpath) {
		this.swf_src.value=url;
		this.swf_fullpath=fullpath;
//		dojo.debug('*'+url+'*');
		if(url=='') {this.swf_ipreview.src='about:blank';} else {
		this.swf_ipreview.src=dojo.uri.dojoUri('../editor/popups/')+'insert_media_preview.php?media_placeholder='+escape('<img src='+this.media_placeholder_url(url,'WWI*-*IWW','HHI*-*IHH'))+'"/>'+'&fullpath='+fullpath+((nclicks&4)?'':'&setsize=1');
		
		}
		this.checkok();
	},
	updateClick: function() {
		if(this.swf_src.value!='')
			this.setURL(this.swf_src.value,5,this.swf_fullpath);},

	newFile: function(url,nclicks,fullpath) {
		if (nclicks&1) this.setURL(url,nclicks,fullpath);
	},
	
	media_placeholder_url:function(url,w,h) {
	
		// fix for not-so-clever firefox abs->relative linking
		var uri=new dojo.uri.Uri(dojo.doc().location);
		var dom=uri.scheme+'://'+uri.authority;

		if(url.substr(0,dom.length)==dom) url=value.substr(dom.length);

		return dojo.uri.dojoUri('../editor/images/')+			'CUSTOMSWFU_placeholder.gif?XXCUSTOMSWFUXXfixed:'+w+':'+h+			':src='+url+(this.swf_flashvars.value!=''?'&'+this.swf_flashvars.value:'')+'XXCUSTOMSWFUXX';
	},

	checkSize: function() {
		if (this.image_width.value!=this.w) 
			document.getElementById("wtit").style.fontWeight="bold";
			else document.getElementById("wtit").style.fontWeight="";
		if (this.image_height.value!=this.h) 
			document.getElementById("htit").style.fontWeight="bold";
			else document.getElementById("htit").style.fontWeight="";
	},
	
	// Fired when the Reset Size button is clicked
	resetSize: function() {
		if (this.w>0) {
			this.image_width.value=this.w;
			this.image_height.value=this.h;
		}
		this.checkSize();
	},
	
	widthChanged:  function() {this.sizeChanged('width');},
	heightChanged: function() {this.sizeChanged('height');},
	
	// Fired when the width or height input texts change
	sizeChanged: function(axe) {
		var th=this.image_height.value, tw=this.image_width.value;
	
		// Verifies if the aspect ration has to be mantained
		if (document.getElementById("chkLockRatio").checked && (this.w>0) && (th.substr(th.length-1)!='%' && tw.substr(tw.length-1)!='%')) {
	
			if ((axe) == "width") {
				if (tw != "") {
					if (! isNaN(tw)) {
						this.image_height.value = Math.round(this.h * (tw  / this.w));
					}
				} else this.image_height.value = "";
			} else
				if (th != "") {
					if (! isNaN(th)) {
						this.image_width.value  = Math.round(this.w  * (th / this.h));
					}
				} else this.image_width.value = "";
		}
		this.checkSize();
	},
	
	checkok: function() {
		if(this.swf_src.value=='' || this.swf_src.value=="http://") {
			if(!this.swf_ok.disabled) this.swf_ok.setDisabled(true);
		} else {
			if(this.swf_ok.disabled) this.swf_ok.setDisabled(false);
		}
	},
	
	ok: function(){
		var curInst = dojo.widget.Editor2Manager.getCurrentInstance();
		curInst.restoreSelection(); //restore previous selection, required for none-activeX IE

var w=this.image_width.value;
var h=this.image_height.value;

		if(this.imageNode){
			dojo.withGlobal(curInst.window, "selectElement", dojo.html.selection, [this.imageNode]);
		} else {
			curInst.restoreSelection(); //restore previous selection, required for none-activeX IE
		}

		if(dojo.render.html.ie) curInst.execCommand("delete");//ie doesn't like inserting over img

		curInst.execCommand("inserthtml", '<img src="'+this.media_placeholder_url(this.swf_src.value,w,h)+'" width="'+w+'" height="'+h+'" alt="Custom Flash File" />');
		curInst._updateHeight();
		this.cancel();
	}
});
