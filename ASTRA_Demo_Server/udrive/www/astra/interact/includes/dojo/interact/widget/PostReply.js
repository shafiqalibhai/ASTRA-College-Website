dojo.provide("interact.widget.PostReply");
dojo.require("dojo.html");

dojo.widget.defineWidget(
	"interact.widget.PostReply",
	interact.widget.PostThread,
	{
		widgetType: "PostReply",
		subject: '',
		added_by: '',
		post_key: '',
		indent: '',
		date_added: '',
		toolTip: null,
		templatePath: dojo.uri.dojoUri('interact/widget/templates/PostReply.html'),
  		postMixInProperties: function() {
    		this.strings = {
      			subject: this.subject,
      			added_by: this.added_by,
      			post_key: this.post_key,
      			indent: this.indent,
      			date_added: this.date_added
    		}
  		},
		postCreate : function(){ 
 			
			//dojo.widget.createWidget('postReply'+this.post_key);
   		}, 
	}
	
);

