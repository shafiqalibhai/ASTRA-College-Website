/*
	Copyright (c) 2004-2006, The Dojo Foundation
	All Rights Reserved.

	Licensed under the Academic Free License version 2.1 or above OR the
	modified BSD license. For more information on Dojo licensing, see:

		http://dojotoolkit.org/community/licensing.shtml
*/
//117
dojo.provide("dojo.widget.Editor2Plugin.InsertAVDialog");

dojo.widget.defineWidget(
	"dojo.widget.Editor2InsertAVDialog",
	dojo.widget.Editor2DialogContent,
{
	templatePath: dojo.uri.dojoUri("src/widget/templates/Editor2/Dialog/insertav.html"),

	getExtension:function(name) {
		var li=name.lastIndexOf('.');
		if(li>-1) {return name.substr(li+1);}
		return null;
	},

	loadContent: function(){
		var curInst = dojo.widget.Editor2Manager.getCurrentInstance();
		
		this.imageNode = dojo.withGlobal(curInst.window, "getSelectedElement", dojo.html.selection);

		if(!this.imageNode || this.imageNode.tagName.toUpperCase() != 'IMG'){
			this.imageNode = dojo.withGlobal(curInst.window, "getAncestorElement", dojo.html.selection, ['img']);
		}
		
		if(!this.imageNode){
			curInst.saveSelection(); //save selection (none-activeX IE)
		}

		this.dur=this.size=0
		this.w=0;this.h=0;
		this.extraAttribText="";
		this.idir.src='';
		this.av_showvolume.checked=true;
		this.av_showprogress.checked=true;
		this.av_showplay.checked=true;

		this.av_autoplay.checked=false;

		this.filechecked=false;
		if(this.imageNode){
			var src = this.imageNode.attributes.src.value;

			m=src.match(/(size=[0-9]+:)?(dur=[0-9]+:)?([0-9,]+:)?(DL=[a-zA-Z0-9,]+:)?fixed:([^:]*):([^:]*):([^\"]*)XX[A-Z0-9]+XX/);

			if(m[4]) this.av_DL.value=m[4].substring(3,m[4].length-1);
			
			this.setMessage(m[2]?m[2].substring(4,m[2].length-1):0,
							m[1]?m[1].substring(5,m[1].length-1):0,
							m[5],m[6]);
			
			//non-default settings for checkboxes...
				if(m[7].match(/showvolume=0/)) this.av_showvolume.checked=false;
				this.av_showprogress.checked=m[7].match(/showprogress=1/)?true:false;
				if(m[7].match(/showplay=0/)) this.av_showplay.checked=false;
			
				if(m[7].match(/autoplay=1/)) this.av_autoplay.checked=true;
			
			m=m[7].match(/(snd|media)name=([^&]+)/);
			this.av_src.value=m[2];
			//dur,DL,w,h,   medianame,showvolume,showprogress,autoplay,  ---showplay for vid
			
	
			this.updateClick();

		} else {
			this.setURL('',1);
			this.premess.innerHTML='';
		}
		
// can't focus on IE
		if(!dojo.render.html.ie) this.av_src.focus();

		this.idir.src=dojo.uri.dojoUri('../editor/popups/')+"file_dir.php?allowed_file_types=mp3:m4a:wma,flv:mov:mp4:wmv:avi:jpg,swf,zip&maxSize=10000:20000:30000&trytofindurl="+this.av_src.value;
		dojo.event.topic.subscribe("/idir/newfile", this, "newFile");
		dojo.event.topic.subscribe("/ipreview/mess", this, "setMessage");

		return true;
	},
	setMessage: function(dur,size,w,h) {
		var pm='';
		if(size) {
			this.size=size;
			pm+=size>(1<<20)?
				Math.round(size/(1<<20))+'MB &bull; ':
				Math.round(size/(1<<10))+'KB &bull; ';
		}
			
		if(dur && dur>-1) {
			dur=Math.round(dur);
			this.dur=dur;
			var s=dur % 60;
			if(s<10) s='0'+s;
			pm=Math.floor(dur/60)+':'+s+' &bull; '+pm;
			
			pm+=Math.round(size/dur/(1<<10)<<3)+'Kb/s &bull; ';
		}
		if(w && w>-1) {this.w=w; pm+=w}
		if(h && h>-1) {this.h=h; pm+='x'+h+' pixels';}
		
		this.premess.innerHTML=pm;

		this.filechecked=true;
	},

// showobj:function(obj) {
// obj.style.visibility='';
// },
	setURL: function(url,nclicks) {
		if(url=='') {
			this.av_DL.value='';
		} else {
			var extn=this.getExtension(url);
			var ismp3=(extn.substr(0,3).toLowerCase()=='mp3');

			var firstc=extn.indexOf(':');
			if(firstc>-1) {
				url=url.substr(0,url.length-(extn.length-firstc));
			}
			
			extn_split=extn.split(':');
			extn='';
			for(ee in extn_split) {
				ev=extn_split[ee]
				if(ev && ev!='flv' && ev!='jpg') {extn+=ev+',';}
			}
			// the above leaves a trailing ',' -- which is good because we always add 'embed' option on the end
			extn+='embed';

			if((nclicks&4)==0) this.av_DL.value=extn;
		}
	
		this.av_src.value=url;

		if(url=='') {this.av_ipreview.src='about:blank';} else {
			if((nclicks&4)==0) {
				this.dur=this.size=this.w=this.h=0;
				this.premess.innerHTML='* Perusing File * <img src="'+dojo.uri.dojoUri('../editor/popups/')+'insert_interactive_progress.gif" width="48" height="10"/>';this.filechecked=false;
			}

			if(ismp3) {
//				if((nclicks&4)==0) this.av_DL.value='mp3';

				this.av_showplay.checked=true;
				this.av_showplay_span.style.display='none';
			} else {
				this.av_showplay_span.style.display='';
			}

//alert(('<img src='+this.media_placeholder_url()+'"/>')+((nclicks&4)?'':'&setsize=1'));
			this.av_ipreview.src=dojo.uri.dojoUri('../editor/popups/')+'insert_media_preview.php?media_placeholder='+escape('<img src='+this.media_placeholder_url()+'"/>')+((nclicks&4)?'':'&setsize=1');
		}
		this.checkok();
	},
	updateClick: function() {
		if(this.av_src.value!='')
			this.setURL(this.av_src.value,5);},

	newFile: function(url,nclicks) {
		if (nclicks&1) this.setURL(url,nclicks);
	},

// 	media_size: function(calc) {
// 		var w=this.w, h=this.h;
// 		var url=this.av_src.value;
// 		var extn=url.substr(url.length-4);
// 
// 		if(!w  || !h || calc) {
// 			if(extn=='.mp3') {
// 				w=(this.av_showplay.checked?40:0)+ (this.av_showvolume.checked?40:0)+(this.av_showprogress.checked?320:0);
// 				h=40;
// 			} else {w=320; h=240;}
// 		}
// 		return new Array(w,h);
// 	},

	media_placeholder_url: function() {
		var url,w,h;
		if(url=this.av_src.value) {

		var extn=url.substr(url.length-4);
		
		
		if(extn=='.mp3') {
			w=(this.av_showplay.checked?40:0)+ (this.av_showvolume.checked?40:0)+(this.av_showprogress.checked?320:0);
			h=40;
			var ph='audioPlaceholder'+(w<60?'40':(w>100?'400':''))+'.gif';
		} else {
			if(this.w && this.h) {
				w=this.w; h=this.h;
			} else {
				w=320; h=240;
			}
			var ph='videoPlaceholder.gif';
		}

		// fix for not-so-clever firefox abs->relative linking
		var uri=new dojo.uri.Uri(dojo.doc().location);
		var dom=uri.scheme+'://'+uri.authority;

		if(url.substr(0,dom.length)==dom) url=value.substr(dom.length);

		var extn=url.substr(url.length-4);

		return '<img src="'+dojo.uri.dojoUri('../editor/images/')+ph+'?XXAVPLAYERXX'+
		(this.size?'size='+this.size+':':'')+
		(this.dur?'dur='+this.dur+':':'')+
		(this.av_DL.value?'DL='+this.av_DL.value+':':'')+
		'fixed:'+w+':'+h+':'+
		'showvolume='+(this.av_showvolume.checked?'1':'0')+'&'+
		'showprogress='+(this.av_showprogress.checked?'1':'0')+'&'+
		'showplay='+(this.av_showplay.checked?'1':'0')+'&'+
		'autoplay='+(this.av_autoplay.checked?'1':'0')+'&'+
		'medianame='+url+
		'XXAVPLAYERXX'+'" width="'+w+'" height="'+h+'" alt="Flash AV Player" />';
		} else return '';
		
	},
	
	checkok: function() {
		if(this.av_src.value=='' || this.av_src.value=="http://") {
			if(!this.av_ok.disabled) this.av_ok.setDisabled(true);
		} else {
			if(this.av_ok.disabled) this.av_ok.setDisabled(false);
		}
	},
	
	ok: function(){
		var url=this.av_src.value;
		var extn=url.substr(url.length-4);

		if(this.filechecked) {
			var curInst = dojo.widget.Editor2Manager.getCurrentInstance();
			curInst.restoreSelection(); //restore previous selection, required for non-activeX IE
	
			if(this.imageNode){
				dojo.withGlobal(curInst.window, "selectElement", dojo.html.selection, [this.imageNode]);
			} else {
				curInst.restoreSelection(); //restore previous selection, required for none-activeX IE
			}

			if(dojo.render.html.ie) curInst.execCommand("delete");//ie doesn't like inserting over img	
	
			curInst.execCommand("inserthtml", this.media_placeholder_url());
			curInst._updateHeight();
			this.cancel();
		}
	}
});
