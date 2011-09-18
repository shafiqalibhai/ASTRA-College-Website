dojo.provide("dojo.widget.Editor2Plugin.InsertSymbolDialog_smiley");

dojo.widget.defineWidget(
	"dojo.widget.Editor2InsertSymbolDialog_smiley",
	dojo.widget.Editor2DialogContent,
{
	templatePath: dojo.uri.dojoUri("src/widget/templates/Editor2/Dialog/insertsymbol_smiley.html"),
	loadContent: function(){
		var curInst = dojo.widget.Editor2Manager.getCurrentInstance();
		curInst.saveSelection(); //save selection (none-activeX IE)

		var smileys=[
		"angel_smile.gif",
		"angry_smile.gif",
		"broken_heart.gif",
		"cake.gif",
		"confused_smile.gif",
		"cry_smile.gif",
		"devil_smile.gif",
		"embaressed_smile.gif",
		"envelope.gif",
		"heart.gif",
		"kiss.gif",
		"lightbulb.gif",
		"omg_smile.gif",
		"regular_smile.gif",
		"sad_smile.gif",
		"shades_smile.gif",
		"teeth_smile.gif",
		"thumbs_down.gif",
		"thumbs_up.gif",
		"tounge_smile.gif",
		"whatchutalkingabout_smile.gif",
		"wink_smile.gif"];
		
		var row=0;
		var html='';
		var smileypath=dojo.uri.dojoUri('../../images/smileys/');
		for(var i=0; i<smileys.length; i++) {
			if((i&3)==0) html+='<tr>';
			html+='<td height="26" style="background-image:url('+smileypath+smileys[i]+');background-repeat:no-repeat;background-position:50% 50%;">&nbsp;</td>';
			if((i&3)==3) html+='</tr>';
		}
		while(i&3) {
			i--;
			html+='<td><br></td>';
			if((i&3)==0) html+='</tr>';
		}
		this.symboldiv.innerHTML='<table cellSpacing="1" cellPadding="1" width="100%" border="1" rules="all" style="font-size:medium;text-align:center">'+html+'</table>';
 		
 		tds=this.symboldiv.getElementsByTagName('td');
 		for(var i in tds) {
 			if(tds[i].style && tds[i].style.backgroundImage) {
	 			dojo.event.connect(tds[i], 'onclick', this, 'ok');
 				dojo.event.connect(tds[i], 'onmouseover', this, 'onMouseOver');
 				dojo.event.connect(tds[i], 'onmouseout', this, 'onMouseOut');
 			}
 		}

		return true;
	},
	ok: function(e){
		e.preventDefault();
		e.stopPropagation();
		if(e.target.nodeName.toLowerCase()=='td') {
			var curInst = dojo.widget.Editor2Manager.getCurrentInstance();
			curInst.restoreSelection(); //restore previous selection, required for none-activeX IE

			if(dojo.render.html.ie) curInst.execCommand("delete");//ie doesn't like inserting over img

			curInst.execCommand("inserthtml", '<img width="19" height="19" src="'+e.target.style.backgroundImage.match(/url\(([^\)]+)\)/)[1]+'">');
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