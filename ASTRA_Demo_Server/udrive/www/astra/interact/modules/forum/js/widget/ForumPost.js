dojo.provide("interact_forum.widget.ForumPost");
dojo.require("interact.widget.Post");

dojo.widget.defineWidget(
	"interact_forum.widget.ForumPost",
	interact.widget.Post,
	{
		widgetType: "ForumPost",
		templatePath: dojo.uri.dojoUri('../../modules/forum/js/widget/templates/ForumPost.html')
 
	}
	
);

