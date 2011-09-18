dojo.provide("interact.widget.Post");
dojo.require("dojo.html");

dojo.widget.defineWidget(
	"interact.widget.Post",
	dojo.widget.HtmlWidget,
	{
		widgetType: "Post",
		photo_tag: '',
		post_body: '',
		post_key: '',
		toolTip: null,
		templatePath: dojo.uri.dojoUri('interact/widget/templates/Post.html'),
  		postMixInProperties: function() {
    		this.strings = {
      			photo_tag: this.photo_tag,
      			post_body: this.post_body,
      			post_key: this.post_key
      			
    		}
  		},
		postCreate : function(){ 
 			dojo.widget.createWidget('postBody'+this.post_key);
   		}, 
	}
	
);

