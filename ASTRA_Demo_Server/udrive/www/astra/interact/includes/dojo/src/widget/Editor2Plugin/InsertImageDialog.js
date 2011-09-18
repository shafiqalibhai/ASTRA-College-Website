dojo.provide("dojo.widget.Editor2Plugin.InsertImageDialog");

dojo.widget.defineWidget(
	"dojo.widget.Editor2InsertImageDialog",
	dojo.widget.Editor2DialogContent,
{
	templatePath: dojo.uri.dojoUri("src/widget/templates/Editor2/Dialog/insertimage.html"),

	editableAttributes: ['src', 'alt', 'width', 'height', 'hspace', 'vspace', 'border', 'align'],
	loadContent: function(){
		var curInst = dojo.widget.Editor2Manager.getCurrentInstance();
		var imageAttributes = {};
		this.extraAttribText = "";

		this.imageNode = dojo.withGlobal(curInst.window, "getSelectedElement", dojo.html.selection);

		if(!this.imageNode || this.imageNode.tagName.toUpperCase() != 'IMG'){
			this.imageNode = dojo.withGlobal(curInst.window, "getAncestorElement", dojo.html.selection, ['img']);
		}

		if(this.imageNode){
			var attrs = this.imageNode.attributes;
			for(var i=0; i<attrs.length; i++) {
				if(dojo.lang.find(this.editableAttributes, attrs[i].name.toLowerCase())>-1){
					imageAttributes[attrs[i].name] = attrs[i].value;
				}else{
					if(!dojo.render.html.ie) {  //too much ie crud to parse.
						this.extraAttribText += attrs[i].name + '="'+attrs[i].value+'" ';
					}
				}
			}
		} else {
			curInst.saveSelection(); //save selection (none-activeX IE)
		}

		if(imageAttributes['src'] == undefined) {
			if(imageAttributes['border'] == undefined) imageAttributes['border']=1; // 1px border is a good default.
			if(imageAttributes['hspace'] == undefined) imageAttributes['hspace']=2; // 2px hspace is a good default.
		}

		for(var i=0; i<this.editableAttributes.length; ++i){
			name = this.editableAttributes[i];
			this["image_"+name].value = (imageAttributes[name] == undefined) ? "" : imageAttributes[name] ;
		}
		this.premess.innerHTML='&nbsp;';
		this.showExisting();

// can't focus on IE
		if(!dojo.render.html.ie) this.image_src.focus();

this.idir.src=dojo.uri.dojoUri('../editor/popups/')+"file_dir.php?allowed_file_types=jpeg,jpg,gif,png,zip&trytofindurl="+this.image_src.value;

		dojo.event.topic.subscribe("/idir/newfile", this, "newFile");
		return true;
	},
	ok: function(){
		if(this.image_src.value=='') {
			alert("You must enter the URL");
			this.image_src.focus();
		} else if (this.image_alt.value=='') {
			alert("Please enter alternate text - this helps people who can't see images");
			this.image_alt.focus();
		} else {
			var curInst = dojo.widget.Editor2Manager.getCurrentInstance();
			var option = 0;
	
			// fix for not-so-clever firefox abs->relative linking
			var uri=new dojo.uri.Uri(dojo.doc().location);
			var dom=uri.scheme+'://'+uri.authority;

			var attstr='';
			for(var i=0; i<this.editableAttributes.length; ++i){
				name = this.editableAttributes[i];
				var value = this["image_"+name].value;
				if(value.length > 0){

					if(name=='src' && value.substr(0,dom.length)==dom) value=value.substr(dom.length);

					attstr += name + '="'+value+'" ';
				}
			}
			if(this.imageNode){
				dojo.withGlobal(curInst.window, "selectElement", dojo.html.selection, [this.imageNode]);
			} else {
				curInst.restoreSelection(); //restore previous selection, required for none-activeX IE
			}

			if(dojo.render.html.ie) curInst.execCommand("delete");//ie doesn't like inserting over img

			curInst.execCommand("inserthtml", '<img '+attstr+this.extraAttribText+'/>');
			curInst._updateHeight();
			this.cancel();
		}
	},

	imgUpdate: function() {
		this.ipreview_mess.innerHTML="&nbsp;";
	
		var nwidth  = this.pre_image.width;
		var nheight = this.pre_image.height;
		var factor;
		var premess='';
		
		if (nwidth>0) {
			premess=nwidth+"x"+nheight+" pixels";
			if (this.pre_image.fileSize) premess+=" &bull; "+Math.ceil(this.pre_image.fileSize>>10)+"KB ("+Math.ceil(this.pre_image.fileSize/5000)+"s@56K)";
			if ((factor=Math.min(228/nheight,304/nwidth))<1) {
				nwidth=Math.round(nwidth*factor);
				nheight=Math.round(nheight*factor);
				premess+=" &bull; shown at "+Math.round(factor*100)+"%";
			}
		}
		
		this.premess.innerHTML=premess;
		this.ipreview.src=this.pre_image.src;
		this.ipreview.width=nwidth;this.ipreview.height=nheight;
		this.ipreview.style.visibility="";
	
		this.image_alt.focus();
	},
	
	newFile: function(url,nclicks) {
		if (nclicks&1) {
			this.resetSizeOnLoad=true;
			this.loadFile(url);
		}
	},

	showExisting: function() {
		this.resetSizeOnLoad=false;
		this.loadFile();
	},

	loadFile:  function(url) {
		this.ipreview.src=null;
		this.ipreview.style.visibility="hidden";
		
		if (url==undefined || url=='') {url=this.image_src.value;}
		
		if(url && (this.image_alt.value=='' || this.cleanFilename(this.image_src.value)==this.image_alt.value)) {this.image_alt.value=this.cleanFilename(url);}

		this.image_src.value=url;

		this.ipreview_mess.innerHTML=((url=='')? "&nbsp;" : "Loading...<br />");

		this.pre_image.src=url;
	},

	cleanFilename: function(url) {
		var slash;
		if((slash=url.lastIndexOf('/'))>0) url=url.substring(slash+1);
		if(url.lastIndexOf('.')>-1) url=url.substring(0,url.lastIndexOf('.'));
		return url.replace(/_/g,' ');
	},
	
	updateClick: function() {this.newFile('',1);},
	
	updatePreview: function() {
		this.imgUpdate();
		if(this.resetSizeOnLoad) {
			this.resetSize();
		} else {
			this.checkSize();
		}
	},
		
	checkSize: function() {
		if (this.image_width.value!=this.pre_image.width) 
			document.getElementById("wtit").style.fontWeight="bold";
			else document.getElementById("wtit").style.fontWeight="";
		if (this.image_height.value!=this.pre_image.height) 
			document.getElementById("htit").style.fontWeight="bold";
			else document.getElementById("htit").style.fontWeight="";
	},
	
	// Fired when the Reset Size button is clicked
	resetSize: function() {
		if (this.pre_image.width>0) {
			this.image_width.value=this.pre_image.width;
			this.image_height.value=this.pre_image.height;
		}
		this.checkSize();
	},
	
	widthChanged:  function() {this.sizeChanged('width');},
	heightChanged: function() {this.sizeChanged('height');},
	
	// Fired when the width or height input texts change
	sizeChanged: function(axe) {
		var th=this.image_height.value, tw=this.image_width.value;
	
		// Verifies if the aspect ration has to be mantained
		if (document.getElementById("chkLockRatio").checked && (this.pre_image.width>0) && (th.substr(th.length-1)!='%' && tw.substr(tw.length-1)!='%')) {
	
			if ((axe) == "width") {
				if (tw != "") {
					if (! isNaN(tw)) {
						this.image_height.value = Math.round(this.pre_image.height * (tw  / this.pre_image.width));
					}
				} else this.image_height.value = "";
			} else
				if (th != "") {
					if (! isNaN(th)) {
						this.image_width.value  = Math.round(this.pre_image.width  * (th / this.pre_image.height));
					}
				} else this.image_width.value = "";
		}
		this.checkSize();
	}
});