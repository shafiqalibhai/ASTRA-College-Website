dojo.provide("dojo.widget.Editor2Toolbar");

dojo.require("dojo.lang.*");
dojo.require("dojo.widget.*");
dojo.require("dojo.event.*");
dojo.require("dojo.html.layout");
dojo.require("dojo.html.display");
dojo.require("dojo.widget.RichText");

dojo.lang.declare("dojo.widget.HandlerManager", null,
	function(){
		this._registeredHandlers=[];
	},
{
	// summary: internal base class for handler function management
	registerHandler: function(/*Object*/obj, /*String*/func){
		// summary: register a handler
		// obj: object which has the function to call
		// func: the function in the object
		if(arguments.length == 2){
			this._registeredHandlers.push(function(){return obj[func].apply(obj, arguments);});
		}else{
			/* obj: Function
			    func: null
			    pId: f */
			this._registeredHandlers.push(obj);
		}
	},
	removeHandler: function(func){
		// summary: remove a registered handler
		for(var i=0;i<this._registeredHandlers.length;i++){
			if(func === this._registeredHandlers[i]){
				delete this._registeredHandlers[i];
				return;
			}
		}
		dojo.debug("HandlerManager handler "+func+" is not registered, can not remove.");
	},
	destroy: function(){
		for(var i=0;i<this._registeredHandlers.length;i++){
			delete this._registeredHandlers[i];
		}
	}
});

dojo.widget.Editor2ToolbarItemManager = new dojo.widget.HandlerManager;
dojo.lang.mixin(dojo.widget.Editor2ToolbarItemManager,
{
	getToolbarItem: function(/*String*/name){
		// summary: return a toobar item with the given name
		var item;
		name = name.toLowerCase();
		for(var i=0;i<this._registeredHandlers.length;i++){
			item = this._registeredHandlers[i](name);
			if(item){
				return item;
			}
		}

		_deprecated = function(cmd, plugin){
			if(!dojo.widget.Editor2Plugin[plugin]){
				dojo.deprecated('Toolbar item '+name+" is now defined in plugin dojo.widget.Editor2Plugin."+plugin+". It shall be required explicitly", "0.6");
				dojo['require']("dojo.widget.Editor2Plugin."+plugin); //avoid loading by the build
			}
		}
		if(name == 'forecolor' || name == 'hilitecolor'){
			_deprecated(name, 'ColorPicker')
		}else if(name == 'formatblock' || name == 'fontsize' || name == 'fontname'){
			_deprecated(name, 'DropDownList')
		}

		switch(name){
			//button for builtin functions
			case 'bold':
			case 'copy':
			case 'cut':
			case 'delete':
			case 'indent':
			case 'inserthorizontalrule':
			case 'insertorderedlist':
			case 'insertunorderedlist':
			case 'italic':
			case 'justifycenter':
			case 'justifyfull':
			case 'justifyleft':
			case 'justifyright':
			case 'outdent':
			case 'paste':
			case 'removeformat':
			case 'selectall':
			case 'strikethrough':
			case 'subscript':
			case 'superscript':
			case 'underline':
			case 'unlink':
			case 'createlink':
			case 'insertimage':
			case 'inserthtmldialog':
			//extra simple buttons
			case 'htmltoggle':
			case 'insertav':
			case 'insertswf':
			case 'blockquote':
				item = new dojo.widget.Editor2ToolbarButton(name);
				break;
			case 'undo':
			case 'redo':
if(!dojo.render.html.ie) {				item = new dojo.widget.Editor2ToolbarButton(name);}
				break;

			case 'forecolor':
			case 'hilitecolor':
				item = new dojo.widget.Editor2ToolbarColorPaletteButton(name);
				break;
			case 'plainformatblock':
				item = new dojo.widget.Editor2ToolbarFormatBlockPlainSelect("formatblock");
				break;
			case 'formatblock':
				item = new dojo.widget.Editor2ToolbarFormatBlockSelect("formatblock");
				break;
			case 'fontsize':
				item = new dojo.widget.Editor2ToolbarFontSizeSelect("fontsize");
				break;
			case 'fontname':
				item = new dojo.widget.Editor2ToolbarFontNameSelect("fontname");
				break;
			//TODO:
			case 'blockdirltr':
			case 'blockdirrtl':
			case 'dirltr':
			case 'dirrtl':
			case 'inlinedirltr':
			case 'inlinedirrtl':
				dojo.debug("Not yet implemented toolbar item: "+name);
				break;
			default:
				if(name.substr(0,13)=='insertsymbol_') {
					item = new dojo.widget.Editor2ToolbarButton(name);
				} else {
					dojo.debug("dojo.widget.Editor2ToolbarItemManager.getToolbarItem: Unknown toolbar item: "+name);
				}
		}
		return item;
	}
});

dojo.addOnUnload(dojo.widget.Editor2ToolbarItemManager, "destroy");

dojo.declare("dojo.widget.Editor2ToolbarButton", null,
	function(name){
		this._name = name;
//		this._command = editor.getCommand(name);
	},
{
	// summary:
	//		dojo.widget.Editor2ToolbarButton is the base class for all toolbar item in Editor2Toolbar
	create: function(/*DomNode*/node, /*dojo.widget.Editor2Toolbar*/toolbar, /*Boolean*/nohover){
		// summary: create the item
		// node: the dom node which is the root of this toolbar item
		// toolbar: the Editor2Toolbar widget this toolbar item belonging to
		// nohover: whether this item in charge of highlight this item
		this._domNode = node;
		var cmd = toolbar.parent.getCommand(this._name); //FIXME: maybe an issue if different instance has different language
		if(cmd){
			this._domNode.title = cmd.getText();
		}
		//make this unselectable: different browsers
		//use different properties for this, so use
		//js do it automatically
		this.disableSelection(this._domNode);

		this._parentToolbar = toolbar;
		dojo.event.connect(this._domNode, 'onclick', this, 'onClick');
		if(!nohover){
			dojo.event.connect(this._domNode, 'onmouseover', this, 'onMouseOver');
			dojo.event.connect(this._domNode, 'onmouseout', this, 'onMouseOut');
		}
	},
	disableSelection: function(/*DomNode*/rootnode){
		// summary: disable selection on the passed node and all its children
		dojo.html.disableSelection(rootnode);
		var nodes = rootnode.all || rootnode.getElementsByTagName("*");
		for(var x=0; x<nodes.length; x++){
			dojo.html.disableSelection(nodes[x]);
		}
	},
	onMouseOver: function(){
		var curInst = dojo.widget.Editor2Manager.getCurrentInstance();
		if(curInst){
			var _command = curInst.getCommand(this._name);
			if(_command && _command.getState() != dojo.widget.Editor2Manager.commandState.Disabled){
				this.highlightToolbarItem();
			}
		}
	},
	onMouseOut: function(){
		this.unhighlightToolbarItem();
	},
	destroy: function(){
		// summary: destructor
		this._domNode = null;
//		delete this._command;
		this._parentToolbar = null;
	},
	onClick: function(e){
		if(this._domNode && !this._domNode.disabled && this._parentToolbar.checkAvailability()){
			e.preventDefault();
			e.stopPropagation();
			var curInst = dojo.widget.Editor2Manager.getCurrentInstance();
			if(curInst){
				var _command = curInst.getCommand(this._name);
				if(_command){
					_command.execute(e);
					this.refreshState();
				}
			}
		}
	},
	refreshState: function(){
		// summary: update the state of the toolbar item
		var curInst = dojo.widget.Editor2Manager.getCurrentInstance();
		var em = dojo.widget.Editor2Manager;
		if(curInst){
			var _command = curInst.getCommand(this._name);
			if(_command){
				var state = _command.getState();
				if(state != this._lastState){
					switch(state){
						case em.commandState.Latched:
							this.latchToolbarItem();
							break;
						case em.commandState.Enabled:
							this.enableToolbarItem();
							break;
						case em.commandState.Disabled:
						default:
							this.disableToolbarItem();
					}
					this._lastState = state;
				}
			}
		}
		return em.commandState.Enabled;
	},

	latchToolbarItem: function(){
		this._domNode.disabled = false;
		this.removeToolbarItemStyle(this._domNode);
		dojo.html.addClass(this._domNode, this._parentToolbar.ToolbarLatchedItemStyle);
	},

	enableToolbarItem: function(){
		this._domNode.disabled = false;
		this.removeToolbarItemStyle(this._domNode);
		dojo.html.addClass(this._domNode, this._parentToolbar.ToolbarEnabledItemStyle);
	},

	disableToolbarItem: function(){
		this._domNode.disabled = true;
		this.removeToolbarItemStyle(this._domNode);
		dojo.html.addClass(this._domNode, this._parentToolbar.ToolbarDisabledItemStyle);
	},

	highlightToolbarItem: function(){
		dojo.html.addClass(this._domNode, this._parentToolbar.ToolbarHighlightedItemStyle);
	},

	unhighlightToolbarItem: function(){
		dojo.html.removeClass(this._domNode, this._parentToolbar.ToolbarHighlightedItemStyle);
	},

	removeToolbarItemStyle: function(){
		dojo.html.removeClass(this._domNode, this._parentToolbar.ToolbarEnabledItemStyle);
		dojo.html.removeClass(this._domNode, this._parentToolbar.ToolbarLatchedItemStyle);
		dojo.html.removeClass(this._domNode, this._parentToolbar.ToolbarDisabledItemStyle);
		this.unhighlightToolbarItem();
	}
});

dojo.declare("dojo.widget.Editor2ToolbarFormatBlockPlainSelect", dojo.widget.Editor2ToolbarButton, {
	// summary: dojo.widget.Editor2ToolbarFormatBlockPlainSelect provides a simple select for setting block format

	create: function(node, toolbar){
//		dojo.widget.Editor2ToolbarFormatBlockPlainSelect.superclass.create.apply(this, arguments);
		this._domNode = node;
		this._parentToolbar = toolbar;
		//TODO: check node is a select
		this._domNode = node;
		this.disableSelection(this._domNode);
		dojo.event.connect(this._domNode, 'onchange', this, 'onChange');
	},

	destroy: function(){
		this._domNode = null;
	},

	onChange: function(){
		if(this._parentToolbar.checkAvailability()){
			var sv = this._domNode.value.toLowerCase();
			var curInst = dojo.widget.Editor2Manager.getCurrentInstance();
			if(curInst){
				var _command = curInst.getCommand(this._name);
				if(_command){
					_command.execute(sv);
				}
			}
		}
	},

	refreshState: function(){
		if(this._domNode){
			dojo.widget.Editor2ToolbarFormatBlockPlainSelect.superclass.refreshState.call(this);
			var curInst = dojo.widget.Editor2Manager.getCurrentInstance();
			if(curInst){
				var _command = curInst.getCommand(this._name);
				if(_command){
					var format = _command.getValue();
					if(!format){ format = ""; }
					dojo.lang.forEach(this._domNode.options, function(item){
						if(item.value.toLowerCase() == format.toLowerCase()){
							item.selected = true;
						}
					});
				}
			}
		}
	}
});

dojo.widget.defineWidget(
	"dojo.widget.Editor2Toolbar",
	dojo.widget.HtmlWidget,
	function(){
		dojo.event.connect(this, "fillInTemplate", dojo.lang.hitch(this, function(){
			if(dojo.render.html.ie){
				this.domNode.style.zoom = 1.0;
			}
		}));
	},
	{
		// summary:
		//		dojo.widget.Editor2Toolbar is the main widget for the toolbar associated with an Editor2

		templatePath: dojo.uri.dojoUri("src/widget/templates/EditorToolbar.html"),
		templateCssPath: dojo.uri.dojoUri("src/widget/templates/EditorToolbar.css"),

		// ToolbarLatchedItemStyle: String: class name for latched toolbar button items
		ToolbarLatchedItemStyle: "ToolbarButtonLatched",

		// ToolbarEnabledItemStyle: String: class name for enabled toolbar button items
		ToolbarEnabledItemStyle: "ToolbarButtonEnabled",

		// ToolbarDisabledItemStyle: String: class name for disabled toolbar button items
		ToolbarDisabledItemStyle: "ToolbarButtonDisabled",

		// ToolbarHighlightedItemStyle: String: class name for highlighted toolbar button items
		ToolbarHighlightedItemStyle: "ToolbarButtonHighlighted",

		// ToolbarHighlightedSelectStyle: String: class name for highlighted toolbar select items
		ToolbarHighlightedSelectStyle: "ToolbarSelectHighlighted",

		// ToolbarHighlightedSelectItemStyle: String: class name for highlighted toolbar select dropdown items
		ToolbarHighlightedSelectItemStyle: "ToolbarSelectHighlightedItem",

//		itemNodeType: 'span', //all the items (with attribute dojoETItemName set) defined in the toolbar should be a of this type

		postCreate: function(){
			var nodes = dojo.html.getElementsByClass("dojoEditorToolbarItem", this.domNode/*, this.itemNodeType*/);

			this.items = {};
			for(var x=0; x<nodes.length; x++){
				var node = nodes[x];
				var itemname = node.getAttribute("dojoETItemName");
				if(itemname){
					var item = dojo.widget.Editor2ToolbarItemManager.getToolbarItem(itemname);
					if(item){
						item.create(node, this);
						this.items[itemname.toLowerCase()] = item;
					}else{
						//hide unsupported toolbar items
						node.style.display = "none";
					}
				}
			}
			
			var td= dojo.dom.getFirstAncestorByTag(this._domNode, 'td');
			if(td){
				dojo.debug(td.name+' '+td.width+' '+td.style.width);
				td.width='1000px';
			}else {dojo.debug('foo'+this._domNode);}

var fat=document.createElement("img");
fat.src=dojo.uri.dojoUri("src/widget/templates/images/blank.gif");
fat.width=dojo.html.getMarginBox(this.domNode).width;
fat.height=1;
dojo.dom.insertBefore(fat,this.domNode);

		},

		update: function(){
			// summary: update all the toolbar items
			for(var cmd in this.items){
				this.items[cmd].refreshState();
			}
		},

		shareGroup: '',
		checkAvailability: function(){
			// summary: returns whether items in this toolbar can be executed
			// description: 
			//		For unshared toolbar, when clicking on a toolbar, the corresponding
			//		editor will be focused, and this function always return true. For shared
			//		toolbar, if the current focued editor is not one of the instances sharing
			//		this toolbar, this function return false, otherwise true.
			if(!this.shareGroup){
				this.parent.focus();
				return true;
			}
			var curInst = dojo.widget.Editor2Manager.getCurrentInstance();
			if(this.shareGroup == curInst.toolbarGroup){
				return true;
			}
			return false;
		},
		destroy: function(){
			for(var it in this.items){
				this.items[it].destroy();
				delete this.items[it];
			}
			dojo.widget.Editor2Toolbar.superclass.destroy.call(this);
		}
	}
);
