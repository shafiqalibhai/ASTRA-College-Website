dojo.provide("interact_forum.widget.ForumThread");
dojo.require("interact.widget.PostThread");

dojo.widget.defineWidget("interact_forum.widget.ForumThread",
	interact.widget.PostThread,
	{
		widgetType: "ForumThread",
		templatePath: dojo.uri.dojoUri('../../modules/forum/js/widget/templates/ForumThread.html')
	}
);

