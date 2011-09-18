dojo.provide("dojo.widget.Editor2Plugin.CreateLinkDialog");
dojo.require("dojo.i18n.common");
dojo.requireLocalization("dojo.widget", "Editor2");

dojo.widget.defineWidget(
	"dojo.widget.Editor2CreateLinkDialog",
	dojo.widget.Editor2DialogContent,
{
	templatePath: dojo.uri.dojoUri("src/widget/templates/Editor2/Dialog/createlink.php"),

	editableAttributes: ['href', 'target', 'title', 'name'],
	loadContent: function(){
		this.__editor=dojo.widget.Editor2Manager.getCurrentInstance();
		this.imageNode=null;

//		this.__editor.saveSelection(); //save selection (none-activeX IE) -- won't work if an IMG is selected!!

		this.linkNode = dojo.withGlobal(this.__editor.window, "getAncestorElement", dojo.html.selection, ['a']);

		if(!this.linkNode) {
			this.imageNode = dojo.withGlobal(this.__editor.window, "getSelectedElement", dojo.html.selection);
			if(!this.imageNode || this.imageNode.tagName.toUpperCase() != 'IMG'){this.imageNode=null;}
		}

		if(!this.linkNode && !this.imageNode) {
			this.__editor.saveSelection(); //save selection (none-activeX IE) -- won't work if an IMG is selected!!
		}

		this.extraAttribText = "";
		this.idir.src='';

		if(this.link_post_href) this.link_post_href.value='';
		this.link_module_href.value='';
		
		this.link_target_none.checked='checked';
		this.link_target_div.style.display='none';
		this.link_popupSettings.style.display='none';

		var vnode;
		
		for(var i=0; i<this.editableAttributes.length; ++i){
			this['link_'+this.editableAttributes[i]].value='';
		}
		this.link_href.value='http://';

		if(this.linkNode){
			var attrs = this.linkNode.attributes;
			this.link_target.value='';

			for(var i=0; i<attrs.length; i++) {
				if(dojo.lang.find(this.editableAttributes, attrs[i].name.toLowerCase())>-1){
					if(attrs[i].name.toLowerCase()=='href' && attrs[i].value.substring(0,11)=="javascript:") {
					  	this.link_href.value = attrs[i].value.match(/^[^']+'([^']+).+$/)[1];
						var popup_params = attrs[i].value.match(/'[^']*'/gim);
						this.link_pwidth.value = popup_params[3].replace(/'/g,"");
						this.link_pheight.value = popup_params[2].replace(/'/g,"");
						this.link_target_popup.checked=true;
						this.link_target.value=popup_params[1].replace(/'/g,"");
					} else if(attrs[i].name.toLowerCase()=="target") {
						if(attrs[i].value=='_blank') {
					  	  	this.link_target_blank.checked=true;
						} else if(attrs[i].value!='') {
				  	  		this.link_target_other.checked=true;
	  	  					this.link_target.value=attrs[i].value;
	  	  				}
	  	  			} else {
						this["link_"+attrs[i].name.toLowerCase()].value = (attrs[i].value == undefined) ? "" : attrs[i].value ;
					}
				}else{
					//IE lists all attributes, even default ones, filter them
					if(attrs[i].specified == undefined || attrs[i].specified){
						this.extraAttribText += attrs[i].name + '="'+attrs[i].value+'" ';
					}
				}
			}
			this.showPopupSettings();
			if(this.link_remove.disabled) this.link_remove.setDisabled(false);
		}else{

			if(!this.imageNode) {
				var html = dojo.withGlobal(this.__editor.window, "getSelectedText", dojo.html.selection);

				if(html == null || html.length == 0){
					var resource = dojo.i18n.getLocalization("dojo.widget", "Editor2", this.lang);
					alert(resource.createLinkDialogSelectError);
					return false;
				}
			}
			if(!this.link_remove.disabled) this.link_remove.setDisabled(true);
		}

		if(!this.linkNode && !this.imageNode && dojo.withGlobal(this.__editor.window, "getSelectedHtml", dojo.html.selection).match(/<a\s/i)) {
			alert('Your selection contains an existing link. To edit or remove it, place the cursor within the existing link and click the Insert/Edit Link button again.');
			this.cancel();
		} else {
	
			this.checkok();
			
			// can't focus on IE
			if(!dojo.render.html.ie) this.link_href.focus();
	
			this.idir.src=dojo.uri.dojoUri('../editor/popups/')+"file_dir.php?trytofindurl="+this.link_href.value;
			dojo.event.topic.subscribe("/idir/newfile", this, "newFile");
			return true;
		}
	},
	
	removeLink: function() {
		if(dojo.render.html.ie) {
			this.__editor.execCommand('unlink');
		} else {
			dojo.withGlobal(this.__editor.window, "selectElement", dojo.html.selection, [this.linkNode]);
			this.__editor.execCommand('inserthtml', this.linkNode.innerHTML);
		}
		this.cancel();
	},
	
	setURL: function(url) {
		this.link_href.value=url;
		if(dojo.render.html.ie) {   //stop weird "can't change focus" bug if IMG is still focused.
			this.link_href.focus();
			this.link_href.select();
		}
		this.checkok();
	},
	
	setURLpost:   function() {this.setURL(this.link_post_href.value)},
	setURLmodule: function() {this.setURL(this.link_module_href.value)},
	
	newFile: function(url,nclicks) {
		if (nclicks&1) this.setURL(url);
	},
	
	checkok: function() {
		if((this.link_href.value==''||this.link_href.value=='http://') && this.link_name.value=='') {
			if(!this.link_ok.disabled) this.link_ok.setDisabled(true);
		} else {
			if(this.link_ok.disabled) this.link_ok.setDisabled(false);
		}
	},
	
	
	showPopupSettings: function() {
		if(this.link_target_popup.checked) {
			this.link_popupSettings.style.display='';
			this.link_target_div.style.display='';
		} else {
			this.link_popupSettings.style.display='none';
			if(this.link_target_other.checked) {
				this.link_target_div.style.display='';
			} else {
				this.link_target_div.style.display='none';
			}
		}
	},

	ok: function(){
// 		if(!this.link_href.value || this.link_href.value=="http://") {
// 			alert("You must enter the URL for where this link points to");
// 			return false;
// 		}

		if(!this.linkNode){
			if(this.imageNode && dojo.render.html.ie) {
				dojo.withGlobal(this.__editor.window, "selectElement", dojo.html.selection, [this.imageNode]);
				
				var html=this.imageNode.outerHTML;
				this.__editor.execCommand("delete");//ie doesn't like inserting over img
				
			} else {
				this.__editor.restoreSelection(); //restore previous selection, required for none-activeX IE
				var html = dojo.withGlobal(this.__editor.window, "getSelectedHtml", dojo.html.selection);
			}
		}else{
			var html = this.linkNode.innerHTML;
			dojo.withGlobal(this.__editor.window, "selectElement", dojo.html.selection, [this.linkNode]);
		}

		if(this.link_target_popup.checked) {
			this.link_href.value = "javascript:open_window('"+this.link_href.value+"','"+this.link_target.value+"','"+
  			this.link_pheight.value+"','"+
	  		this.link_pwidth.value+"')";
			this.link_target.value='';

		} else if(this.link_target_blank.checked) {
			this.link_target.value='_blank';
		} else if (this.link_target_none.checked) {
			this.link_target.value='';
		}

		var attstr='', href='', nname;

		// fix for not-so-clever firefox abs->relative linking
		var uri=new dojo.uri.Uri(dojo.doc().location);
		var dom=uri.scheme+'://'+uri.authority;

		for(var i=0; i<this.editableAttributes.length; ++i){
			nname = this.editableAttributes[i];
			var value = this["link_"+nname].value;
			if(value.length > 0){
				if(nname=='href') {
					if(value.substr(0,dom.length)==dom) value=value.substr(dom.length);
					href=value;
				}
				attstr += nname + '="'+value+'" ';
			}
		}


		if(this.linkNode==null && this.imageNode==null) {
// new non-image link... get browser to make a placeholder link + paste full html link over that.
			this.__editor.execCommand("createlink", "XX_createlink_href_XX");
			var i = 0, a, els = this.__editor.editNode.getElementsByTagName("a");
			while (a = els[i++]) {
				if(a.getAttribute("href")=="XX_createlink_href_XX") {
					html=a.innerHTML;
					dojo.withGlobal(this.__editor.window, "selectElement", dojo.html.selection, [a]);
					if(dojo.render.html.ie) {
						//must unlink, THEN delete, THEN insert.  I like IE.  Ha ha.
						//lets clear the placeholder href too, for good measure.
						a.href='';
						this.__editor.execCommand('unlink'); 
						this.__editor.execCommand("delete");
					}
					break;
				}
			}
		}
		this.__editor.execCommand('inserthtml', '<a '+attstr+this.extraAttribText+'>'+html+'</a>');

		this.cancel();
	}
});
