dojo.provide("interact_forum.widget.PostReply");
dojo.require("dojo.html");


dojo.widget.defineWidget(
	"interact_forum.widget.PostReply",
	dojo.widget.HtmlWidget,
	function() {
		
	},
	{
		widgetType: "PostReply",
		myname : '',
		templatePath: dojo.uri.dojoUri('../../modules/forum/js/widget/templates/PostReply.html'),

		
		showReply: function(){
			alert('you are replying to '+this.myname);
		}
	}
	
	
);



