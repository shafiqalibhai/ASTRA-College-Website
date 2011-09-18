dojo.provide("interact.widget.PostThread");
dojo.require("dojo.html");

dojo.widget.defineWidget(
	"interact.widget.PostThread",
	dojo.widget.HtmlWidget,
	{
		widgetType: "PostThread",
		myName : '',
		subject: '',
		added_by: '',
		replies: '',
		added_by_value: '',
		date_added_value: '',
		replies_value: '',
		last_post_value: '',
		post_key: '',
		added_by_key: '',
		thread_key: '',
		space_key: '',
		module_key: '',
		toolTip: null,
		postTemplate: '',
		tempPath:'',
		templatePath: '',
		moduleType: '',
  		postMixInProperties: function() {
    		this.strings = {
      			subject: this.subject,
      			added_by: this.added_by,
      			added_by_value: this.added_by_value,
      			date_added_value: this.date_added_value,
      			replies: this.replies,
      			replies_value: this.replies_value,
      			post_key: this.post_key,
      			added_by_key: this.added_by_key,
      			thread_key: this.thread_key,
				last_post_value: this.last_post_value
    		}
  		},
		postCreate : function(){ 
 			dojo.widget.createWidget("threadHead"+this.thread_key);
   		}, 
		showBody: function(){

			if (dojo.byId('postBody'+this.post_key)) {
				//show_it('postBody'+this.post_key);
				this.showReplies();
				show_it('postBody'+this.post_key,'postLink'+this.post_key,'disclosure');
			} else {
				this.showReplies();
				if (!dojo.html.isShowing('messageBody'+this.post_key)){
					dojo.html.setClass('postLink'+this.post_key,'disclosureWaiting');
					pars = 	{post_key:this.post_key};				
					dojo.io.bind({
      					url: fullUrl+'/modules/forum/getbody.php',
      					mimetype: "text/json",
      					content: pars,
      					load: function(type, data, evt) {
       
      						var post_body = dojo.byId('messageBody'+data.post_key);
      						dojo.widget.createWidget("ForumPost",{photo_tag: data.photo_tag,post_body:data.body,post_key:data.post_key},post_body);
        					dojo.html.setClass('postLink'+data.post_key,'disclosureOpen');
        
        				}
      				});
    
				}	
			}
		},
		showReplies: function () {
		
			if (2==1) {
				show_it('replyHead'+this.post_key,'postLink'+this.post_key,'disclosure');
				
			} else {
				
				var reply_links = dojo.byId('repliesDiv'+this.thread_key);
				var module_type = this.moduleType;
				var full_url = fullUrl+'/modules/forum/getreplies.php?post_key='+this.thread_key;
				dojo.io.bind({
      				url: full_url,
      				mimetype: "text/json",
      				load: function(type, data, evt) {
        			      				
        				dojo.lang.forEach(data, function(reply) {
          					dojo.widget.createWidget(module_type+"PostReply",{post_key: reply.post_key,added_by:reply.added_by,subject:reply.subject,indent:reply.indent,date_added:reply.date_added},reply_links, "last");
      						dojo.widget.createWidget('replyHead'+reply.post_key);
      					
        				})
      				}
    			});
    		
			}
		}
	}
);

