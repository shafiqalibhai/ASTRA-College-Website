dojo.provide("dojo.widget.Editor2Plugin.BlockQuote");

dojo.require("dojo.widget.Editor2");
dojo.widget.Editor2Plugin.BlockQuote = {
	getCommand: function(editor, name){
		if(name=='blockquote') {
			return dojo.widget.Editor2Plugin.BlockQuote.blockQuoteCommand;
		}
	},
	getToolbarItem: function(name){
		var name = name.toLowerCase();

		var item;
		if(name=='blockquote') {
			item = new dojo.widget.Editor2ToolbarButton(name);
		}

		return item;
	},
// 	getContextMenuGroup: function(name, contextmenuplugin){
// 		return new dojo.widget.Editor2Plugin.TableContextMenu(contextmenuplugin);
// 	},
	blockQuoteCommand: {
		execute: function(){
			var curInst = dojo.widget.Editor2Manager.getCurrentInstance();
			var bq = dojo.withGlobal(curInst.window, "getAncestorElement", dojo.html.selection, ['blockquote']);
			if(bq){
				if(dojo.render.html.ie) {  //select & insert doesn't work with ie
					bq.outerHTML = bq.innerHTML;
				} else {
					dojo.withGlobal(curInst.window, "selectElement", dojo.html.selection, [bq]);
					curInst.execCommand("inserthtml", bq.innerHTML);
				}
			} else {
				curInst.execCommand("formatblock", "blockquote");
			}
			
		},
		getState: function(){
			var curInst = dojo.widget.Editor2Manager.getCurrentInstance();
			if(curInst._inSourceMode){ return false;}
			var bq = dojo.withGlobal(curInst.window, "hasAncestorElement", dojo.html.selection, ['blockquote']);
			return bq ? dojo.widget.Editor2Manager.commandState.Latched : dojo.widget.Editor2Manager.commandState.Enabled;
		},
		getText: function(){
			return 'Block Quote';
		},

		destory: function(){}
	}
};

//register command
dojo.widget.Editor2Manager.registerHandler(dojo.widget.Editor2Plugin.BlockQuote.getCommand);
dojo.widget.Editor2ToolbarItemManager.registerHandler(dojo.widget.Editor2Plugin.BlockQuote.getToolbarItem);
