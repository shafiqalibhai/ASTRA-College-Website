dojo.provide("dojo.widget.Editor2Plugin.InsertSymbolDialog_symbol");

dojo.widget.defineWidget(
	"dojo.widget.Editor2InsertSymbolDialog_symbol",
	dojo.widget.Editor2DialogContent,
{
	templatePath: dojo.uri.dojoUri("src/widget/templates/Editor2/Dialog/insertsymbol_symbol.html"),
	loadContent: function(){
		var curInst = dojo.widget.Editor2Manager.getCurrentInstance();
		curInst.saveSelection(); //save selection (none-activeX IE)

 		var tds=this.symboltable.getElementsByTagName('td');
 		for(var i in tds) {
 			if(tds[i].innerHTML) {
				dojo.event.kwConnect({srcObj:tds[i], srcFunc:"onclick",adviceObj:this, adviceFunc:"ok", once: true});
				dojo.event.kwConnect({srcObj:tds[i], srcFunc:"onmouseover",adviceObj:this, adviceFunc:"onMouseOver", once: true});
				dojo.event.kwConnect({srcObj:tds[i], srcFunc:"onmouseout",adviceObj:this, adviceFunc:"onMouseOut", once: true});
  			}
 		}

		return true;
	},
	ok: function(e){
		e.preventDefault();
		e.stopPropagation();
		if(e.target.nodeName.toLowerCase()=='td') {
			var curInst = dojo.widget.Editor2Manager.getCurrentInstance();
			if(dojo.render.html.ie)	curInst.restoreSelection(); //restore previous selection, required for none-activeX IE

			curInst.execCommand("inserthtml", e.target.innerHTML);
			this.cancel();
		}
	},
	onMouseOver: function(e){
		e.preventDefault();
		e.stopPropagation();
		if(e.target.nodeName.toLowerCase()=='td') {
			e.target.style.backgroundColor="#FFF";
		}	
	},
	onMouseOut: function(e){
		e.preventDefault();
		e.stopPropagation();
		if(e.target.nodeName.toLowerCase()=='td') {
			e.target.style.backgroundColor="";
		}	
	}
});