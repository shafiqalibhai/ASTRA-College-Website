dojo.provide("interact.widget.DisclosurePane");
dojo.require("dojo.html.*");


dojo.widget.defineWidget(
	"interact.widget.DisclosurePane",
	dojo.widget.HtmlWidget,
	{
		widgetType: "DisclosurePane",
		open:true,
		containerNode:'',
		widgetId: '',
		widgetClass:'',
		templatePath: dojo.uri.dojoUri('interact/widget/templates/DisclosurePane.html'),
		label:'',
		labelClass:'',
		open:true,
		postCreate: function() {
			this.labelNode.innerHTML=this.label;
			if (this.open) {
				dojo.html.setClass(this.labelNode,this.labelClass+'Open');
			}else {
				if (dojo.html.isShowing(this.containerNode)){
					dojo.html.toggleShowing(this.containerNode);
				}
				dojo.html.setClass(this.labelNode,this.labelClass+'Closed')
			}
		},
		showContent: function(){
			
			if (this.open) {
				dojo.lfx.wipeOut(this.containerNode,250).play();
				dojo.html.setClass(this.labelNode,this.labelClass+'Closed');
				this.open=false;
				dojo.io.cookie.set(this.widgetId+'Closed', 1, 1);
				
			}else {
				dojo.lfx.wipeIn(this.containerNode,250).play();
				dojo.html.setClass(this.labelNode,this.labelClass+'Open');
				this.open=true;
				dojo.io.cookie.set(this.widgetId+'Closed', 0, 1);
			}
		}
		
	}
	
	
);