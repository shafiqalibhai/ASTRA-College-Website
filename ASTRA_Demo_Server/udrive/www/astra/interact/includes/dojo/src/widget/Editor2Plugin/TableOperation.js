dojo.provide("dojo.widget.Editor2Plugin.TableOperation");

dojo.require("dojo.widget.Editor2Plugin.DialogCommands");

//subscribe to dojo.widget.RichText::init, not onLoad because after onLoad
//the stylesheets for the editing areas are already applied and the prefilters
//are executed, so we have to insert our own trick before that point
dojo.event.topic.subscribe("dojo.widget.RichText::init", function(editor){
	if(dojo.render.html.ie){
		//add/remove a class to a table with border=0 to show the border when loading/saving
		editor.contentDomPreFilters.push(dojo.widget.Editor2Plugin.TableOperation.showIETableBorder);
		editor.contentDomPostFilters.push(dojo.widget.Editor2Plugin.TableOperation.removeIEFakeClass);
	}
	//create a toggletableborder command for this editor so that tables without border can be seen
	editor.getCommand("toggletableborder");
});

dojo.lang.declare("dojo.widget.Editor2Plugin.deletetableCommand", dojo.widget.Editor2Command,
{
	execute: function(e){
		var table = dojo.withGlobal(this._editor.window, "getAncestorElement", dojo.html.selection, ['table']);
		if(table){
			dojo.withGlobal(this._editor.window, "selectElement", dojo.html.selection, [table]);
			var innergunk='';
			if(!e.altKey) {
				var rows=table.rows;   // keep table content
				for(var nrow=0 ; nrow<rows.length ; nrow++) {
					var chNodes=dojo.widget.Editor2Plugin.TableOperation._getCellChildren(rows[nrow]);
					for(var i=0; i<chNodes.length; i++) innergunk+=chNodes[i].innerHTML;
				}
			}
			this._editor.execCommand("delete");  // only cross-browser undo-friendly way to remove table
			if(innergunk.length>0) this._editor.execCommand("inserthtml", innergunk);
		}
	},
	getState: function(){
		return dojo.withGlobal(this._editor.window, "hasAncestorElement", dojo.html.selection, ['table'])?
			dojo.widget.Editor2Manager.commandState.Enabled : dojo.widget.Editor2Manager.commandState.Disabled;
	},
	getText: function(){
		return 'Delete Table (alt-click to destroy content)';
	}
});

dojo.lang.declare("dojo.widget.Editor2Plugin.toggletableborderCommand", dojo.widget.Editor2Command,
	function(){
		this._showTableBorder = false;
		dojo.event.connect(this._editor, "editorOnLoad", this, 'execute');
	},
{
	execute: function(){
		if(this._showTableBorder){
			this._showTableBorder = false;
			if(dojo.render.html.moz){
				this._editor.removeStyleSheet(dojo.uri.dojoUri("src/widget/templates/Editor2/showtableborder_gecko.css"));
			}else if(dojo.render.html.ie){
				this._editor.removeStyleSheet(dojo.uri.dojoUri("src/widget/templates/Editor2/showtableborder_ie.css"));
			}
		}else{
			this._showTableBorder = true;
			if(dojo.render.html.moz){
				this._editor.addStyleSheet(dojo.uri.dojoUri("src/widget/templates/Editor2/showtableborder_gecko.css"));
			}else if(dojo.render.html.ie){
				this._editor.addStyleSheet(dojo.uri.dojoUri("src/widget/templates/Editor2/showtableborder_ie.css"));
			}
		}
	},
	getText: function(){
		return 'Toggle Table Border';
	},
	getState: function(){
		return (this._showTableBorder ? dojo.widget.Editor2Manager.commandState.Latched : dojo.widget.Editor2Manager.commandState.Enabled);
	}
});


dojo.lang.declare("dojo.widget.Editor2Plugin.dialogtextcolorpickerCommand", dojo.widget.Editor2Command, {
execute: function(color){
	this._editor.getCommand('tabledialog').dialog.contentWidget.set_color(color);
},
getText: function(){return 'Pick a new text color';}
});

dojo.lang.declare("dojo.widget.Editor2Plugin.dialogbgcolorpickerCommand", dojo.widget.Editor2Command, {
execute: function(color){
	this._editor.getCommand('tabledialog').dialog.contentWidget.set_backgroundColor(color);
},
getText: function(){return 'Pick a new background color';}
});

dojo.lang.declare("dojo.widget.Editor2Plugin.dialogbordercolorpickerCommand", dojo.widget.Editor2Command, {
execute: function(color){
	this._editor.getCommand('tabledialog').dialog.contentWidget.set_borderColor(color);
},
getText: function(){return 'Pick a new border color';}
});


dojo.lang.declare("dojo.widget.Editor2Plugin.inserttableCommand", dojo.widget.Editor2Command, {
	execute: function(){dojo.widget.Editor2Plugin.TableOperation.callTableDialog(this._editor,'inserttable');},
	getText: function(){return 'Insert Table';},
	getState: function(){
		return this._editor._inSourceMode?
			dojo.widget.Editor2Manager.commandState.Disabled:
			dojo.widget.Editor2Manager.commandState.Enabled;
	}
});

dojo.lang.declare("dojo.widget.Editor2Plugin.tablepropertiesCommand", dojo.widget.Editor2Command, {
	execute: function(){dojo.widget.Editor2Plugin.TableOperation.callTableDialog(this._editor,'tableproperties');},
	getText: function(){return 'Table Properties';},
	getState: function(){
		var nTOstate=(!this._editor._inSourceMode && !this.wasSouceMode && dojo.withGlobal(this._editor.window, "hasAncestorElement", dojo.html.selection, ['table']));
		if(nTOstate!=this.TOstate) {
			this._editor.toolbarWidget.TObar.style.display=(nTOstate?'block':'none');
			this.TOstate=nTOstate;
		}
		//prevent blink of TO bar when leaving source mode (may only happen if editor is in a table)
		this.wasSouceMode = this._editor._inSourceMode;

		return nTOstate? dojo.widget.Editor2Manager.commandState.Enabled:dojo.widget.Editor2Manager.commandState.Disabled;
	},
	wasSourceMode:false,
	TOstate:false
});

dojo.lang.declare("dojo.widget.Editor2Plugin.trpropertiesCommand", dojo.widget.Editor2Command, {
	execute: function(){dojo.widget.Editor2Plugin.TableOperation.callTableDialog(this._editor,'trproperties');},
	getText: function(){return 'Row Properties';}
});

dojo.lang.declare("dojo.widget.Editor2Plugin.tdpropertiesCommand", dojo.widget.Editor2Command, {
	execute: function(){dojo.widget.Editor2Plugin.TableOperation.callTableDialog(this._editor,'tdproperties');},
	getText: function(){return 'Cell Properties';}
});

dojo.lang.declare("dojo.widget.Editor2Plugin.insertrowbelowCommand", dojo.widget.Editor2Command, {
	execute: function(){dojo.widget.Editor2Plugin.TableOperation._insertRow(this._editor,true)},
	getText: function(){return 'Insert Row Below';}
});

dojo.lang.declare("dojo.widget.Editor2Plugin.insertrowaboveCommand", dojo.widget.Editor2Command, {
	execute: function(){dojo.widget.Editor2Plugin.TableOperation._insertRow(this._editor,false)},
	getText: function(){return 'Insert Row Above';}
});

dojo.lang.declare("dojo.widget.Editor2Plugin.deleterowsCommand", dojo.widget.Editor2Command, {
	execute: function(){
		var selCell=dojo.withGlobal(this._editor.window, "getAncestorElement", dojo.html.selection, ['td','th']);
		if(selCell!=null) {
			var rowIndex=selCell.parentNode.rowIndex;
			var actionPoint=rowIndex;

			var table = dojo.withGlobal(this._editor.window, "getAncestorElement", dojo.html.selection, ['table']);
			if(table.rows.length<2) {alert('Cannot delete the last row!');return;};

			var tbodyHTML='';
			var colcount=dojo.widget.Editor2Plugin.TableOperation._countColumns(table);
			var savecells=new Array;
			var rows=table.rows;

			for(var nrow=0 ; nrow<rows.length ; nrow++) {
				
				if(nrow!=actionPoint) {
					tbodyHTML+='		<tr '+dojo.widget.Editor2Plugin.TableOperation._getAttributesHTML(rows[nrow])+">\n";}
				var chNodes=dojo.widget.Editor2Plugin.TableOperation._getCellChildren(rows[nrow]);

				var chNodeNum=0, ch=null;
				for(var i=0; i<colcount; i++) {
					if(savecells[i]!==undefined) {
						rs=savecells[i].rowSpan-1;
						tbodyHTML+=dojo.widget.Editor2Plugin.TableOperation._nodeHTML(savecells[i],rs,null,true);
						savecells[i]=undefined;
					}

					if(chNodeNum<chNodes.length) {
						ch=chNodes[chNodeNum];
						if(ch.cellIndex==i) {
							var rs=ch.rowSpan;
							if(nrow<actionPoint && nrow+rs>actionPoint) {rs--;}
							if(nrow==actionPoint) {
								if(rs>1) {savecells[chNodes[i].cellIndex]=chNodes[i];}
							} else {
								tbodyHTML+=dojo.widget.Editor2Plugin.TableOperation._nodeHTML(chNodes[i],rs,null,true);
							}
							chNodeNum++;
						}
					}
				}
				if(nrow!=actionPoint) {tbodyHTML+="\n		</tr>\n";}
	
			}

			dojo.widget.Editor2Plugin.TableOperation._reinsertTable(this._editor,tbodyHTML,Math.max(rowIndex-1,0),selCell.cellIndex);
		}
	},
	getText: function(){return 'Delete Row';}
});

dojo.lang.declare("dojo.widget.Editor2Plugin.insertcolafterCommand", dojo.widget.Editor2Command, {
	execute: function(){dojo.widget.Editor2Plugin.TableOperation._insertCol(this._editor,true)},
	getText: function(){return 'Insert Column After';}
});

dojo.lang.declare("dojo.widget.Editor2Plugin.insertcolbeforeCommand", dojo.widget.Editor2Command, {
	execute: function(){dojo.widget.Editor2Plugin.TableOperation._insertCol(this._editor,false)},
	getText: function(){return 'Insert Column Before';}
});

dojo.lang.declare("dojo.widget.Editor2Plugin.deletecolsCommand", dojo.widget.Editor2Command, {
	execute: function(){
		var selCell=dojo.withGlobal(this._editor.window, "getAncestorElement", dojo.html.selection, ['td','th']);
		if(selCell!=null) {
			var table = dojo.withGlobal(this._editor.window, "getAncestorElement", dojo.html.selection, ['table']);
			var rowIndex=selCell.parentNode.rowIndex;
			var actionPoint=dojo.widget.Editor2Plugin.TableOperation._cellColumnNumber(table,selCell);

			if(dojo.widget.Editor2Plugin.TableOperation._countColumns(table)<2) {
				alert('Cannot delete the last column!');return;
			};

			var tbodyHTML='';
			var rows=table.rows;
			var cs,rs,colpos,colpad=[];

			for(var nrow=0 ; nrow<rows.length ; nrow++) {
				tbodyHTML+='		<tr '+dojo.widget.Editor2Plugin.TableOperation._getAttributesHTML(rows[nrow])+">\n";
				var chNodes=dojo.widget.Editor2Plugin.TableOperation._getCellChildren(rows[nrow]);
				for(var i=0; i<chNodes.length; i++) {
					cs=chNodes[i].colSpan;
					rs=chNodes[i].rowSpan;

					colpos=chNodes[i].cellIndex;
					
					if(colpad[nrow]!=-1) {
						if(colpad[nrow]!=undefined){colpos+=colpad[nrow];}
	
						if(colpos<=actionPoint && colpos+cs>actionPoint) {cs--;}
	
						if(colpos<actionPoint) {
							for(var j=1;j<rs;j++) { //pad spanned rows
								if(colpad[j+nrow]==undefined) {colpad[j+nrow]=cs;} else {
									colpad[j+nrow]+=cs;
								}
							}
						}
					}					
					if(cs){
						tbodyHTML+=dojo.widget.Editor2Plugin.TableOperation._nodeHTML(chNodes[i],null,cs,true);
					} else {
						for(var j=1;j<rs;j++) {  //pop! these rows are done!
							colpad[j+nrow]=-1;
						}
					}
				}
				tbodyHTML+="\n		</tr>\n";
			}
			dojo.widget.Editor2Plugin.TableOperation._reinsertTable(this._editor,tbodyHTML,rowIndex,Math.max(selCell.cellIndex-1,0));
		}
	},
	getText: function(){return 'Delete Column';}
});

dojo.lang.declare("dojo.widget.Editor2Plugin.mergecellsCommand", dojo.widget.Editor2Command, {
	execute: function(){
		var table = dojo.withGlobal(this._editor.window, "getAncestorElement", dojo.html.selection, ['table']);

		var sel, mergingCell, selWidth=0, selHeight=0, rownum=-1,i,j,k,colnum=0;

		if (dojo.render.html.moz) {
			sel=this._editor.window.getSelection();
			var xx;
			i=0;
			try {
				while(range= sel.getRangeAt(i++)) {
					var cell = range.startContainer.childNodes[range.startOffset];

					if(cell.nodeName.toLowerCase().match(/^t[dh]$/)) {
						xx=dojo.widget.Editor2Plugin.TableOperation._cellColumnNumber(table,cell);
						if(rownum==-1) {rownum=cell.parentNode.rowIndex;colnum=xx;} else {
							colnum=Math.min(colnum,xx);
							rownum=Math.min(rownum,cell.parentNode.rowIndex);
						}
						selWidth=Math.max((xx+cell.colSpan)-colnum,selWidth);
						selHeight=Math.max((cell.parentNode.rowIndex+cell.rowSpan)-rownum,selHeight);
						
						if(colnum==xx && rownum==cell.parentNode.rowIndex){
							mergingCell=cell;
						}
					}
				}
			} catch(e) {//no more cells
			}
		}

		if(!mergingCell) {
			if(mergingCell=dojo.withGlobal(this._editor.window, "getAncestorElement", dojo.html.selection, ['td','th'])) {;
				rownum=mergingCell.parentNode.rowIndex;
				colnum=dojo.widget.Editor2Plugin.TableOperation._cellColumnNumber(table,mergingCell);
			} else {alert('Please select a table cell'); return;}
		}
		
		if(selWidth<=mergingCell.colSpan && selHeight<=mergingCell.rowSpan) {
			var cc=dojo.widget.Editor2Plugin.TableOperation._countColumns(table);
			selWidth=parseInt(prompt('How many columns do you want to merge?', Math.max((colnum<cc-1 ? 2:1),mergingCell.colSpan)));
			if(!(selWidth>0)) {return;}
			
			selHeight=parseInt(prompt('How many rows do you want to merge?',Math.max((mergingCell.parentNode.rowIndex < table.rows.length-1 ? 2:1),mergingCell.rowSpan)));
			if(!(selHeight>0)) {return;}
			
			if(selWidth<=mergingCell.colSpan && selHeight<=mergingCell.rowSpan) return;
			
			if(selWidth+colnum>cc || selHeight+rownum>table.rows.length) {
				alert('Not enough rows or columns in table!');return;
			}
		}
	
		var tbodyHTML=[]; tbodyHTML[1]=''; tbodyHTML[2]='';
		var mergedHTML='';

		var rows=table.rows;
		var cs,rs,colpos,inMerge,nv,chNodes;
		var xpad=[];

//pad out table grid and merge block, check for merge collisions
//build new html table

//xpad values:  1==already spanned, 2==part of merge area... 3=both.
//first, fill in merge-area in xpad
		for(var nrow=rownum ; nrow<rownum+selHeight ; nrow++) {
			xpad[nrow]=[];
			for(i=colnum;i<colnum+selWidth; i++) xpad[nrow][i]=2;
		}

		for(var nrow=0 ; nrow<rows.length ; nrow++) {
			if(xpad[nrow]===undefined) xpad[nrow]=[];
			tbodyHTML[mergedHTML?2:1]+='		<tr '+dojo.widget.Editor2Plugin.TableOperation._getAttributesHTML(rows[nrow])+">\n";

			chNodes=dojo.widget.Editor2Plugin.TableOperation._getCellChildren(rows[nrow]);
			colpos=0;

			for(i=0; i<chNodes.length; i++) {
							
				cs=chNodes[i].colSpan; rs=chNodes[i].rowSpan;

				while(xpad[nrow][colpos]&1) colpos++;		
				
				var inMerge=xpad[nrow][colpos]&2;
				
				for(j=0;j<rs;j++) {
					for(k=0;k<cs;k++) {
						if(xpad[j+nrow]===undefined) xpad[j+nrow]=[];

						if(xpad[j+nrow][k+colpos] ^ inMerge) {
							alert('Cannot merge that!');return;
						}

						if(k<cs && j<rs) {
							nv=xpad[j+nrow][k+colpos];
							if(nv===undefined) nv=0;
							xpad[j+nrow][k+colpos]=nv|1;
						}
					}
				}

				if(!inMerge) {
					tbodyHTML[mergedHTML?2:1]+=dojo.widget.Editor2Plugin.TableOperation._nodeHTML(chNodes[i],null,null,true);
				} else {
					mergedHTML+=chNodes[i].innerHTML;
				}

				colpos+=cs;
			}
			tbodyHTML[mergedHTML?2:1]+="\n		</tr>\n";
//			dojo.widget.Editor2Plugin.TableOperation.debugMap(xpad);
		}
		
		mergedHTML = tbodyHTML[1]+			dojo.widget.Editor2Plugin.TableOperation._nodeHTML(mergingCell,selHeight,selWidth,mergedHTML)+tbodyHTML[2];
		dojo.widget.Editor2Plugin.TableOperation._reinsertTable(this._editor,mergedHTML,rownum,colnum);		
	},
	getText: function(){return 'Merge Cells';}
});

dojo.lang.declare("dojo.widget.Editor2Plugin.splitcellsCommand", dojo.widget.Editor2Command, {
	execute: function(){dojo.widget.Editor2Plugin.TableOperation._splitCells(this._editor,1,1)},
	getText: function(){return 'Split Merged Cell';},
	getState: function(){
		var selCell=dojo.withGlobal(this._editor.window, "getAncestorElement", dojo.html.selection, ['td','th']);
		return(selCell && (selCell.rowSpan>1 || selCell.colSpan>1)?
			dojo.widget.Editor2Manager.commandState.Enabled:
			dojo.widget.Editor2Manager.commandState.Disabled);			
	}
});

dojo.lang.declare("dojo.widget.Editor2Plugin.splitrowsCommand", dojo.widget.Editor2Command, {
	execute: function(){dojo.widget.Editor2Plugin.TableOperation._splitCells(this._editor,1,null)},
	getText: function(){return 'Split Merged Row';},
	getState: function(){
		var selCell=dojo.withGlobal(this._editor.window, "getAncestorElement", dojo.html.selection, ['td','th']);
		return(selCell && (selCell.rowSpan>1)?
			dojo.widget.Editor2Manager.commandState.Enabled:
			dojo.widget.Editor2Manager.commandState.Disabled);			
	}
});

dojo.lang.declare("dojo.widget.Editor2Plugin.splitcolsCommand", dojo.widget.Editor2Command, {
	execute: function(){dojo.widget.Editor2Plugin.TableOperation._splitCells(this._editor,null,1)},
	getText: function(){return 'Split Merged Column';},
	getState: function(){
		var selCell=dojo.withGlobal(this._editor.window, "getAncestorElement", dojo.html.selection, ['td','th']);
		return(selCell && (selCell.colSpan>1)?
			dojo.widget.Editor2Manager.commandState.Enabled:
			dojo.widget.Editor2Manager.commandState.Disabled);			
	}
});

dojo.lang.declare("dojo.widget.Editor2Plugin.togglethCommand", dojo.widget.Editor2Command, {
	execute: function(){
		var xpad=[],ccount=0;
		if (dojo.render.html.moz) {
			var sel=this._editor.window.getSelection();
			var i=0,rownum;
			try {
				while(range= sel.getRangeAt(i++)) {
					var cell = range.startContainer.childNodes[range.startOffset];
					if(cell.nodeName.toLowerCase().match(/^t[dh]$/)) {
						rownum=cell.parentNode.rowIndex
						if(xpad[rownum]===undefined) xpad[rownum]=[];
						xpad[rownum][cell.cellIndex]=true;
						ccount++;
						if(!selCell) {var selCell=cell, rowIndex=rownum;}
					}
				}
			} catch(e) {//no more cells
			}
		}
		if(ccount==0) {
			var selCell=dojo.withGlobal(this._editor.window, "getAncestorElement", dojo.html.selection, ['td','th']);
			if(!selCell) {alert('Please select a table cell'); return;}
			var rowIndex=selCell.parentNode.rowIndex;
			xpad[rowIndex]=[];
			xpad[rowIndex][selCell.cellIndex]=true;
		}
		var table=dojo.withGlobal(this._editor.window, "getAncestorElement", dojo.html.selection, ['table']);
		var tbodyHTML='',forcetag;
		var rows=table.rows;
		for(var nrow=0 ; nrow<rows.length ; nrow++) {
			tbodyHTML+='		<tr '+dojo.widget.Editor2Plugin.TableOperation._getAttributesHTML(rows[nrow])+">\n";
			var chNodes=dojo.widget.Editor2Plugin.TableOperation._getCellChildren(rows[nrow]);
			for(var i=0; i<chNodes.length; i++) {
				forcetag=(xpad[nrow] && xpad[nrow][i])?
					(chNodes[i].nodeName.toLowerCase()=='td'?'th':'td'):null;
				tbodyHTML+=dojo.widget.Editor2Plugin.TableOperation._nodeHTML(chNodes[i],null,null,true,forcetag);
			}
			tbodyHTML+="\n		</tr>\n";
		}
		dojo.widget.Editor2Plugin.TableOperation._reinsertTable(this._editor,tbodyHTML,rowIndex,selCell.cellIndex);
	},
	getText: function(){return 'Toggle Heading-Cell';},
	getState: function(){
		return(dojo.withGlobal(this._editor.window, "getAncestorElement", dojo.html.selection, ['th'])?
			dojo.widget.Editor2Manager.commandState.Latched:
			dojo.widget.Editor2Manager.commandState.Enabled);			
	}
});


dojo.widget.Editor2Plugin.TableOperation = {
	commandList:['inserttable'
				,'toggletableborder'
				,'deletetable'
				,'tableproperties'
				,'trproperties'
				,'tdproperties'
				,'insertrowbelow'
				,'insertrowabove'
				,'deleterows'
				,'insertcolafter'
				,'insertcolbefore'
				,'deletecols'
				,'mergecells'
				,'splitcells'
				,'splitrows'
				,'splitcols'
				,'toggleth'
				
				,'dialogbordercolorpicker'
				,'dialogtextcolorpicker'
				,'dialogbgcolorpicker'],

	getCommand: function(editor, name){
		if(name=='tabledialog') {
			return new dojo.widget.Editor2DialogCommand(editor, 'tabledialog',
				{contentFile: "dojo.widget.Editor2Plugin.InsertTableDialog",
				contentClass: "Editor2InsertTableDialog",
				title: "Edit Table", width: "660px", height: "450px"});
		}
		if(dojo.lang.find(dojo.widget.Editor2Plugin.TableOperation.commandList,name)>-1) {
			return new dojo.widget.Editor2Plugin[name+'Command'](editor, name);
		}
	},
	getToolbarItem: function(name){
		var item;
		if(dojo.lang.find(dojo.widget.Editor2Plugin.TableOperation.commandList,name)>-1) {
			item = new dojo.widget.Editor2ToolbarButton(name);
		}
		return item;
	},
	getContextMenuGroup: function(name, contextmenuplugin){
		return new dojo.widget.Editor2Plugin.TableContextMenuGroup(contextmenuplugin);
	},
	callTableDialog: function(__editor,mode){
		var di=__editor.getCommand('tabledialog');
		di.mode=mode; di.execute();
	},
	showIETableBorder: function(dom){
		var tables = dom.getElementsByTagName('table');
		dojo.lang.forEach(tables, function(t){
			dojo.html.addClass(t, "dojoShowIETableBorders");
		});
		return dom;
	},
	removeIEFakeClass: function(dom){
		var tables = dom.getElementsByTagName('table');
		dojo.lang.forEach(tables, function(t){
			dojo.html.removeClass(t, "dojoShowIETableBorders");
		});
		return dom;
	},

	_getAttributesHTML:function(dom,excludeTags) {
		var HTMLAttr='', attrs, val, aname, i;
		if(dom && (attrs=dom.attributes)) { 
			// junk attributes....
			if(!excludeTags) excludeTags=[];
			excludeTags.push('disabled','tabindex','cols','datapagesize','hidefocus','contenteditable','_moz_resizing');
			if(dojo.render.html.ie) excludeTags.push('style');
			
			for(i=0; i<attrs.length; i++) {
				val=attrs[i].value;
				aname=attrs[i].name.toLowerCase();
				if(val.length && val!='null' && (aname!='nowrap'||val!='false')) {  //stupid IE string values
					if(!excludeTags || (dojo.lang.find(excludeTags,aname)==-1)) HTMLAttr+=aname+'="'+val+'" ';
				}
			}
			if(dojo.render.html.ie) {     // handle style for stupid IE
				var sty=dom.style;
				if(sty) {
					sty=sty.cssText;
					if(sty.length) HTMLAttr+='style="'+sty+'" ';
				}
			}
	
			HTMLAttr=HTMLAttr.substr(0,HTMLAttr.length-1);
		
			return HTMLAttr;
		} else return '';
	},
	_nodeHTML:function(cell,rowspan,colspan,content,forcetag) {
		//if rowspan or colspan are null, use existing attribute value in cell.
		return (cell.nodeType==3?
			cell.nodeValue:
			'<'+(forcetag?forcetag:cell.nodeName.toLowerCase())+' '+
			((rowspan && colspan)?
				this._getAttributesHTML(cell,['colspan','rowspan']):
				rowspan?
					this._getAttributesHTML(cell,['rowspan']):
					colspan?
						this._getAttributesHTML(cell,['colspan']):
						this._getAttributesHTML(cell))+
			(rowspan>1?' rowspan="'+rowspan+'"':'')+
			(colspan>1?' colspan="'+colspan+'"':'')+
			'>'+(content===true?cell.innerHTML:
					content===false?'<br>':content)+
			'</'+(forcetag?forcetag:cell.nodeName.toLowerCase())+'>'
		);
	},
	_getCellChildren:function(row) {
		var cellArray=[];
		for(var i=0;i<row.childNodes.length;i++) {
			if(row.childNodes[i].nodeName.match(/^t[dh]$/i)) {cellArray.push(row.childNodes[i]);}
		}
		return cellArray;
	},

// debugMap:function(map) {
// 
// for (i=0;i<map.length;i++) {
// rr=i+':';
// for (j=0;j<4;j++) {
// rr+=(map[i][j]===1?
// 	'x':map[i][j]==2?'+':map[i][j]==3?'*':' ')
// }
// dojo.debug(rr);
// }dojo.debug('');
// },

	_countColumns:function(table) {
		var colcount=0;
		var chNodes=dojo.widget.Editor2Plugin.TableOperation._getCellChildren(table.rows[0]);
		for(var i=0; i<chNodes.length; i++) colcount+=chNodes[i].colSpan;
		return colcount;
	},
	_cellColumnNumber:function(table,cell) {
		var cs,rs,colpos,xpad=[],rows=table.rows;
		var rowIndex=cell.parentNode.rowIndex;
		for(var nrow=0 ; nrow<=rowIndex ; nrow++) {
			if(xpad[nrow]===undefined) xpad[nrow]=[];
			var chNodes=dojo.widget.Editor2Plugin.TableOperation._getCellChildren(rows[nrow]);

			colpos=0;
			for(var i=0; i<chNodes.length; i++) {
				cs=chNodes[i].colSpan;
				rs=chNodes[i].rowSpan;
				while(xpad[nrow][colpos]!==undefined) colpos++;

				if(nrow==rowIndex && cell.cellIndex==chNodes[i].cellIndex) return colpos;

				for(var j=0;j<rs;j++) for(var k=0;k<cs;k++) if(j||k) {
					if(xpad[j+nrow]===undefined) xpad[j+nrow]=[];
					xpad[j+nrow][k+colpos]=false;
				}
				colpos+=cs;
			}
		}
	},
	_reinsertTable:function(__editor,tbodyHTML,row,col,table,theadHTML) {
		if(!table) table = dojo.withGlobal(__editor.window, "getAncestorElement", dojo.html.selection, ['table']);
		if(!theadHTML) {
			theadHTML='<table '+this._getAttributesHTML(table)+">\n";
			//copy non-tbody children
			var tchildren=table.childNodes, tch_name;
			for(var i=0;i<tchildren.length;i++) {
				tch_name=tchildren[i].nodeName.toLowerCase();
				if(tchildren[i].nodeType!=3 && tch_name!='tbody' && tch_name!='tr') {
					theadHTML+=this._nodeHTML(tchildren[i],null,null,true);
				}
			}	
		}

		theadHTML+='	<tbody '+this._getAttributesHTML(table.getElementsByTagName("tbody")[0])+">\n";
		if(!tbodyHTML) {
			tbodyHTML='';
			var rows=table.rows;
			for(var nrow=0 ; nrow<rows.length ; nrow++) {
				tbodyHTML+='		<tr '+dojo.widget.Editor2Plugin.TableOperation._getAttributesHTML(rows[nrow])+'>'+rows[nrow].innerHTML+"\n		</tr>\n";
			}
		}		

		if(row===undefined || row===null || col===undefined || col===null) {
			var selCell=dojo.withGlobal(__editor.window, "getAncestorElement", dojo.html.selection, ['td','th']);
			if(row===undefined || row===null) row=(selCell?selCell.parentNode.cellIndex:0);
			if(col===undefined || col===null) col=(selCell?selCell.cellIndex:0);
		}
		
		dojo.withGlobal(__editor.window, "selectElement", dojo.html.selection, [table]);
		var savParent,savPrevious;
		if(table.previousSibling) {savPrevious=table.previousSibling;} else {savParent=table.parentNode;}

// IE won't insert over an existing table, but we don't use DOM either as that kills undo
// try delete command... though this does insert a scary entry (no table) into undo stack
// -- user needs to undo a 2nd time if they don't die of shock.
		if(dojo.render.html.ie) __editor.execCommand("delete");//table.parentNode.removeChild(table);

		__editor.execCommand("inserthtml", theadHTML+tbodyHTML+"	</tbody>\n</table>");
		if(savPrevious) {
			table=savPrevious.nextSibling;
		} else {
			table=savParent.firstChild;
		}
		if(table && table.nodeName.toLowerCase()=='table'){
			var chNodes=this._getCellChildren(table.rows[row]);
			for(var i=0;i<chNodes.length;i++) {
				if((chNodes[i].cellIndex+chNodes[i].colSpan)>col) {
					var selNode=chNodes[i];
					if(!dojo.render.html.ie) selNode=selNode.lastChild;
					dojo.withGlobal(__editor.window, "selectElement", dojo.html.selection,[selNode]);
					dojo.withGlobal(__editor.window, "collapse", dojo.html.selection, [true]);
					break;
				}
			}
		}
		__editor._updateHeight();
	},
	_insertRow:function(__editor,after) {
		var selCell=dojo.withGlobal(__editor.window, "getAncestorElement", dojo.html.selection, ['td','th']);
		if(selCell!=null) {
			var cellIndex=selCell.cellIndex;
			var actionPoint=selCell.parentNode.rowIndex;
			if(after) actionPoint+=selCell.rowSpan;

			var table = dojo.withGlobal(__editor.window, "getAncestorElement", dojo.html.selection, ['table']);
			var tbodyHTML='';

			var colcount=this._countColumns(table);
			var rows=table.rows;

			for(var nrow=0 ; nrow<Math.max(rows.length,actionPoint+1) ; nrow++) {
				if(nrow==actionPoint) {
					tbodyHTML+='		<tr '+this._getAttributesHTML(selCell.parentNode)+">\n";
					for(i=0;i<colcount;i++) {tbodyHTML+='<td><br></td>';}
					tbodyHTML+="\n		</tr>";
				}
				
				if(nrow<rows.length) {
					tbodyHTML+='		<tr '+this._getAttributesHTML(rows[nrow])+">\n";
					var chNodes=this._getCellChildren(rows[nrow]);
					for(var i=0; i<chNodes.length; i++) {
						var rs=chNodes[i].rowSpan;
						if(nrow<actionPoint && nrow+rs>actionPoint) {
							rs++;
							colcount-=chNodes[i].colSpan;
						}
						tbodyHTML+=this._nodeHTML(chNodes[i],rs,null,true);
					}
					tbodyHTML+="\n		</tr>\n";
				}
			}
			this._reinsertTable(__editor,tbodyHTML,actionPoint,cellIndex);
		}
	},
	_insertCol:function(__editor,after) {
		var selCell=dojo.withGlobal(__editor.window, "getAncestorElement", dojo.html.selection, ['td','th']);
		if(selCell!=null) {
			var rowIndex=selCell.parentNode.rowIndex;
			var table = dojo.withGlobal(__editor.window, "getAncestorElement", dojo.html.selection, ['table']);
			var actionPoint=this._cellColumnNumber(table,selCell);
			if(after) actionPoint+=selCell.colSpan;

			var tbodyHTML='';
			var rows=table.rows;
			var cs,rs,done,colpos,colpad=[],skip;

			for(var nrow=0 ; nrow<rows.length ; nrow++) {
				done=false;skip=false;
				tbodyHTML+='		<tr '+this._getAttributesHTML(rows[nrow])+">\n";
				var chNodes=this._getCellChildren(rows[nrow]);
				for(var i=0; i<chNodes.length; i++) {
					cs=chNodes[i].colSpan;
					rs=chNodes[i].rowSpan;

					colpos=chNodes[i].cellIndex;
					if(colpad[nrow]!=undefined){colpos+=colpad[nrow];}
					if(colpos==actionPoint) {tbodyHTML+='<td><br></td>';done=true;}
					if(colpos>actionPoint) {skip=true;}

					if(colpos<actionPoint) {
						for(var j=1;j<rs;j++) {  //pad spanned rows
							if(colpad[j+nrow]==undefined) {colpad[j+nrow]=cs;} else {
								colpad[j+nrow]+=cs;
							}
						}
					}

					if(chNodes[i].cellIndex<actionPoint && chNodes[i].cellIndex+cs>actionPoint) {cs++;skip=true;}
					tbodyHTML+=this._nodeHTML(chNodes[i],null,cs,true);
				}
				if(!done && !skip) {tbodyHTML+='<td><br></td>';}

				tbodyHTML+="\n		</tr>\n";
			}
			this._reinsertTable(__editor,tbodyHTML,rowIndex,selCell.cellIndex);
		}
	},
	_splitCells:function(__editor,splitrows,splitcols) {
		var selCell=dojo.withGlobal(__editor.window, "getAncestorElement", dojo.html.selection, ['td','th']);
		if(selCell!=null) {
			var selcellIndex=selCell.cellIndex;
			var rowIndex=selCell.parentNode.rowIndex;

			var table = dojo.withGlobal(__editor.window, "getAncestorElement", dojo.html.selection, ['table']);
			var actionPoint=this._cellColumnNumber(table,selCell);

			var tbodyHTML='';
			var rows=table.rows;
			var cs,rs,colpos,done,colpad=[];
			for(var nrow=0 ; nrow<rows.length ; nrow++) {
				tbodyHTML+='		<tr '+this._getAttributesHTML(rows[nrow])+">\n";
				var chNodes=this._getCellChildren(rows[nrow]);
				done=false;colpos=0;
				for(var i=0; i<chNodes.length; i++) {
					cs=chNodes[i].colSpan;
					rs=chNodes[i].rowSpan;

					colpos=chNodes[i].cellIndex;
					if(colpad[nrow]!=undefined){colpos+=colpad[nrow];}
					if(colpos==actionPoint && (nrow==rowIndex || (splitrows && nrow>=rowIndex && nrow<rowIndex+selCell.rowSpan))) {
						for(var j=0;j<(splitcols?selCell.colSpan:1);j++) {
							tbodyHTML+=this._nodeHTML(selCell,splitrows,splitcols,(j==0 && nrow==rowIndex));
						}
						done=true;
					}
					if(colpos!=actionPoint || nrow!=rowIndex) {
						tbodyHTML+=this._nodeHTML(chNodes[i],null,cs,true);
						if(colpos<actionPoint) {
							for(var j=1;j<rs;j++) {
								colpad[j+nrow] = colpad[j+nrow]? colpad[j+nrow]+cs : cs;
							}
						}
					}
				}
				if(!done && (nrow==rowIndex || (splitrows && nrow>=rowIndex && nrow<rowIndex+selCell.rowSpan))) {
					for(var j=0;j<(splitcols?selCell.colSpan:1);j++) {
						tbodyHTML+=this._nodeHTML(selCell,splitrows,splitcols,(j==0 && nrow==rowIndex));
					}
				}
				tbodyHTML+="\n		</tr>\n";
			}
			this._reinsertTable(__editor,tbodyHTML,rowIndex,actionPoint);
		}
	}
}


//register commands:
dojo.widget.Editor2Manager.registerHandler(dojo.widget.Editor2Plugin.TableOperation.getCommand);

//register toolbar items:
dojo.widget.Editor2ToolbarItemManager.registerHandler(dojo.widget.Editor2Plugin.TableOperation.getToolbarItem);

//add context menu support if dojo.widget.Editor2Plugin.ContextMenu is included before this plugin
if(dojo.widget.Editor2Plugin.ContextMenuManager){
	dojo.widget.Editor2Plugin.ContextMenuManager.registerGroup('Table', dojo.widget.Editor2Plugin.TableOperation.getContextMenuGroup);

	dojo.declare("dojo.widget.Editor2Plugin.TableContextMenuGroup",
		dojo.widget.Editor2Plugin.SimpleContextMenuGroup,
	{
		createItems: function(){
			this.items.push(dojo.widget.createWidget("Editor2ContextMenuItem", {caption: "Delete Table", command: 'deletetable'}));
			this.items.push(dojo.widget.createWidget("Editor2ContextMenuItem", {caption: "Table Property", command: 'inserttable', iconClass: "TB_Button_Icon TB_Button_Table"}));
		},
		checkVisibility: function(){
			var curInst = dojo.widget.Editor2Manager.getCurrentInstance();
			var table = dojo.withGlobal(curInst.window, "hasAncestorElement", dojo.html.selection, ['table']);

			if(dojo.withGlobal(curInst.window, "hasAncestorElement", dojo.html.selection, ['table'])){
				this.items[0].show();
				this.items[1].show();
				return true;
			}else{
				this.items[0].hide();
				this.items[1].hide();
				return false;
			}
		}
	});
}