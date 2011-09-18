/*
	Copyright (c) 2004-2006, The Dojo Foundation
	All Rights Reserved.

	Licensed under the Academic Free License version 2.1 or above OR the
	modified BSD license. For more information on Dojo licensing, see:

		http://dojotoolkit.org/community/licensing.shtml
*/

dojo.provide("dojo.widget.Editor2Plugin.InsertHTMLDialog");

dojo.widget.defineWidget(
	"dojo.widget.Editor2InsertHTMLDialog",
	dojo.widget.Editor2DialogContent,
{
	templatePath: dojo.uri.dojoUri("src/widget/templates/Editor2/Dialog/inserthtmldialog.html"),
	resetSizeOnLoad:false,
	getState: function(){return dojo.widget.Editor2Manager.commandState.Latched;},
	
	loadContent: function(){
		var curInst = dojo.widget.Editor2Manager.getCurrentInstance();

		this.htmlChunkNode = null;
		
		var el=dojo.withGlobal(curInst.window, "getAncestorElement", dojo.html.selection, ['span']);
		while(el) {
			if(dojo.html.getClass(el)=='Editor_HTML_Chunk') {
				this.htmlChunkNode = el; break;
			} else {el=dojo.dom.getFirstAncestorByTag(el.parentNode, 'span');}
		}
		
		if(this.htmlChunkNode){
			this.html_ta.value=curInst._postFilterContent(this.htmlChunkNode.innerHTML);
			
// FIXME: enable remove button here

		} else {
			this.html_ta.value='';
			this.htmlChunkNode = dojo.withGlobal(curInst.window, "getSelectedElement", dojo.html.selection);
		}

// can't focus on IE
		if(!dojo.render.html.ie) this.html_ta.focus();

		return true;
	},

	ok: function(){
		var curInst = dojo.widget.Editor2Manager.getCurrentInstance();
		var option = 0;
	
		if(this.htmlChunkNode){
			dojo.withGlobal(curInst.window, "selectElement", dojo.html.selection, [this.htmlChunkNode]);
		}
		
		var content=this.html_ta.value;
		
		// magic border around content if it contains html
		if(content.match(/<[^>]+>/)) {
			content='<span class="Editor_HTML_Chunk">'+content+'</span> ';
		}
		
		curInst.execCommand("inserthtml", curInst._preFilterContent(content));
	
		this.cancel();
	}
});