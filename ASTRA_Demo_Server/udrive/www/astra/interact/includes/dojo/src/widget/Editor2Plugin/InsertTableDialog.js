dojo.provide("dojo.widget.Editor2Plugin.InsertTableDialog");

dojo.widget.defineWidget(
	"dojo.widget.Editor2InsertTableDialog",
	dojo.widget.Editor2DialogContent,
{
	templatePath: dojo.uri.dojoUri("src/widget/templates/Editor2/Dialog/inserttable.html"),

//border styles & sides are handled as a special case.
	borderSides: ['border-top','border-left','border-right','border-bottom'],

	colorButtons: ['textcolorpicker','bgcolorpicker','bordercolorpicker'],
	updateBorderStyle:function(){
		if(this.TO_borderStyle.value &&	this.TO_border.value.length==0) {this.TO_border.value=1;}
		this.resetSample();
	},

	set_color: function(color){
		if(typeof color!=='string') color='';
		this.TO_color.value=color;
		this.resetSample();
		this.textcolorpickerx.style.visibility=(color?'visible':'hidden');
	},
	changeTextColorInput: function() {this.set_color(this.TO_color.value);},
	set_backgroundColor: function(color){
		if(typeof color!=='string') color='';
		this.TO_backgroundColor.value=color;
		this.resetSample();
		this.bgcolorpickerx.style.visibility=(color?'visible':'hidden');
	},
	changeBGColorInput: function() {this.set_backgroundColor(this.TO_backgroundColor.value);},

	set_borderColor: function(color){
		if(typeof color!=='string') color='';
		if(this.TO_border.value.length==0) this.TO_border.value=1;
		if(this.TO_borderStyle.value=='') this.TO_borderStyle.value='solid';
		this.TO_borderColor.value=color;
		this.resetSample();
		this.bordercolorpickerx.style.visibility=(color?'visible':'hidden');
	},
	changeBorderColorInput: function() {this.set_borderColor(this.TO_borderColor.value);},
	changeBorderInput: function() {
		if(this.TO_border.value!=0 && this.TO_borderStyle.value=='') this.TO_borderStyle.value='solid';
		this.resetSample();
	},

	getBorderStyleCSSText: function(){
		var bwidth=this.TO_border.value;
		var bcolor=this.TO_borderColor.value;
		var bstyle=this.TO_borderStyle.value;

		var sty=''
		if(bwidth && bstyle) {
			var sides=0;
			for(var i=0;i<4;i++) if(this['TO_'+dojo.html.toCamelCase(this.borderSides[i])].checked) sides|=(1<<i);
			
			if(sides) {
				sty='border:'+bwidth+'px '+bstyle+(bcolor?' '+bcolor:'')+';';
				for(var i=0;i<4;i++) if((sides&(1<<i))==0) sty+=this.borderSides[i]+':none;';
			} else {sty='border:none;';}
		}
		if(this.mode=='tableproperties' && this.TO_borderCollapse.checked) sty+='border-collapse:collapse;';
		return sty;
	},
	getEdStyleCSS: function() {
		var css='';
		for(var i in this.editableStyles) {
			var prop=this.editableStyles[i];
			value=this['TO_'+dojo.html.toCamelCase(prop)].value;
			if(value.length>0) {
				if(prop=='background-image') value='url('+value+')';		
				css+=prop+':'+value+';';
			}
		}
		if(this.TO_whiteSpace.checked) {
			css+='white-space:nowrap;';
		} else {if(this.parentWrap=='nowrap') css+='white-space:normal;';}
		return css;
	},
	_getFullStyleCSS: function() {
//as we provide only a subset of border features, leave border stuff alone unless we've changed it
		var bString=this.getBorderStyleCSSText();
		return this.getEdStyleCSS()+
			(this.initBorderString==bString?this.borderStyles:bString)+
			this.extraStyleText;
	},
	_nodeHTMLWithStyle: function(node,attr,style,content) {
		return '<'+node.nodeName.toLowerCase()+' '+attr+
			(node.colSpan>1? ' colspan="'+node.colSpan+'"':'')+
			(node.rowSpan>1? ' rowspan="'+node.rowSpan+'"':'')+
			(style.length?' style="'+style+'"':'')+'>'+
			(content? node.innerHTML+'</'+(node.nodeName.toLowerCase())+'>':'');
	},
	getEdAttr:function() {
		var attrs='';

		for(var i=0; i<this.editableAttributes.length; ++i){
			var name = this.editableAttributes[i];
			var value = this["TO_"+name].value;
			if(value.length > 0){
				attrs += name + '="'+value+'" ';
			}
		}
		return attrs;
	},
	resetSample: function() {
		if(this.initDone) {
			this.TO_sampleholder.innerHTML='<table '+this.getEdAttr()+' style="'+this.getEdStyleCSS()+this.getBorderStyleCSSText()+'"><tr><td style="font-size:xx-small">1</td>'+
			(this.mode!='tdproperties'?
				'<td style="font-size:xx-small">2<br />two</td></tr>'+
				(this.mode=='tableproperties'?
					'<tr><td style="font-size:xx-small">3<br />three</td><td style="font-size:xx-small">4</td></tr>':
					''
				):'</tr>'
			)+'</table>';
		}
	},
	setStyleItem: function(prop,val) {
		var camelName=dojo.html.toCamelCase(prop);
		this['TO_'+camelName].value=val;
		if(this['set_'+camelName]) {this['set_'+camelName](val);}
	},
	set_rules: function() {
		var rset=(this.TO_rules.value.length&&this.TO_rules.value!='none');
		if(rset) {
			this.TO_borderCollapse.checked=true;
			this.set_borderCollapse();
		}
		this.TO_borderCollapse.disabled=rset;
		this.resetSample();
	},
	
	set_borderCollapse: function() {
		this.TO_cellspacing.disabled = (this.TO_borderCollapse.checked?true:false);
		this.resetSample();
	},

	set_display: function(nodenames,showit) {
		for(var i in nodenames) this['TO_'+nodenames[i]].style.display=showit?'':'none';
	},
	loadContent: function(){

		var curInst = dojo.widget.Editor2Manager.getCurrentInstance();

// upside-down back-to-front communication of mode...
// done backwards because (on first call) we don't exist until after tabledialog command init
		this.mode=curInst.getCommand('tabledialog').mode;

// find title text and icon
		this.parent.titleBarText.innerHTML=(curInst.getCommand(this.mode).getText());
		this.parent.titleBarIcon.src=(dojo.uri.dojoUri("src/widget/templates/buttons/TO_"+this.mode+".gif"));

		//create color buttons from dojoAttachPoint-spans
		//these are NOT in toolbar items list
		for(var i in this.colorButtons) {
			this[this.colorButtons[i]+'Button'] = new dojo.widget.Editor2ToolbarColorPaletteButton('dialog'+this.colorButtons[i]);
			this[this.colorButtons[i]+'Button'].create(this[this.colorButtons[i]],curInst.toolbarWidget);
		}

		this.tableNode = this.propertyNode = null;
		var selCell = null, selRow = null, ccount=0, rownum;
		this.ppad=[];
		
		if(this.mode!='inserttable') {
			this.tableNode = dojo.withGlobal(curInst.window, "getSelectedElement", dojo.html.selection);
			if(!this.tableNode || this.tableNode.tagName.toUpperCase() != 'TABLE'){
				this.tableNode = dojo.withGlobal(curInst.window, "getAncestorElement", dojo.html.selection, ['table']);
			}
			if(!this.tableNode) {this.mode='inserttable';} else {
			
				if (dojo.render.html.moz) {
					var sel=curInst.window.getSelection();
					var i=0,rownum;
					try {
						while(range= sel.getRangeAt(i++)) {
							var cell = range.startContainer.childNodes[range.startOffset];
							if(cell.nodeName.toLowerCase().match(/^t[dh]$/)) {
								if(!selCell) selCell=cell;
								rownum=cell.parentNode.rowIndex
								if(this.ppad[rownum]===undefined) this.ppad[rownum]=[];
								this.ppad[rownum][cell.cellIndex]=true;
								ccount++;
							}
						}
					} catch(e) {//no more cells
					}
				}
			
				if(ccount==0) {
					var selCell=dojo.withGlobal(curInst.window, "getAncestorElement", dojo.html.selection, ['td','th']);
					if(!selCell) {alert('Please select a table cell'); return;}
					rownum=selCell.parentNode.rowIndex;
					this.ppad[rownum]=[];
					this.ppad[rownum][selCell.cellIndex]=true;
				}

				this.cellIndex=selCell.cellIndex; this.rowIndex=selCell.parentNode.rowIndex;
			}
		}

		if(this.mode=='inserttable') curInst.saveSelection(); //save selection (none-activeX IE)

		switch(this.mode){
			case 'tableproperties': this.propertyNode=this.tableNode; break;
			case 'trproperties':    this.propertyNode=selCell.parentNode; break;
			case 'tdproperties':    this.propertyNode=selCell; break;
			case 'inserttable': this.mode='tableproperties'; break;
		}

		this.editableAttributes=(this.mode=='tableproperties')?
			['summary', 'height', 'cellspacing', 'cellpadding', 'border', 'align', 'rules']:
			(this.mode=='trproperties'? ['height'] : ['height', 'border']);

		this.editableStyles=['text-align', 'color', 'background-color', 'background-image'];
		if(this.mode=='tableproperties') this.editableStyles.push('clear');
		if(this.mode=='tdproperties') this.editableStyles.push('vertical-align');

		this.set_display(
			['tablealignment', 'tableprops', 'cols_label', 'cols', 'rows_label', 'rows', 'table_desc'], this.mode=='tableproperties');

		this.set_display(
			['borderTop', 'borderLeft', 'borderRight', 'borderBottom', 'borderprops', 'width_label', 'width', 'widthtype'], this.mode!='trproperties');

		this.set_display(
			['verticalAlign_label', 'verticalAlign'], this.mode=='tdproperties');

		var tableAttributes = {};
		this.extraAttribText = "";
		this.extraStyleText = "";
		this.borderStyles = "";

		var stylesMatched=0,sidesMatched=0,matches,n;
		this.initDone=false;

		if(this.tableNode){
			if(this.mode=='tableproperties') {
				this["TO_rows"].value = this.tableNode.rows.length;
				this["TO_cols"].value = dojo.widget.Editor2Plugin.TableOperation._countColumns(this.tableNode);
				this["TO_rows"].disabled = this["TO_cols"].disabled = true;
				this["TO_caption"].value = this.tableNode.caption? this.tableNode.caption.innerHTML:"";
			}

			var width = this.propertyNode.style.width || this.propertyNode.width;
			if(width){
				this["TO_width"].value = parseInt(width);
				this["TO_widthtype"].value = (width.indexOf('%') > -1)? "percent":"pixels";
			}else{
				this["TO_width"].value = '';
				this["TO_widthtype"].value = "percent";
			}
			
			this.parentWrap=dojo.html.getComputedStyle(this.propertyNode.parentNode,'white-space');
			this.TO_whiteSpace.checked=(
				this.propertyNode.style.whiteSpace=='nowrap' || 
				(!this.propertyNode.style.whiteSpace && this.parentWrap=='nowrap'));

			var attrs = this.propertyNode.attributes;
			for(var i=0; i<attrs.length; i++) {
				var aname=attrs[i].name.toLowerCase(), aval=attrs[i].value;
				if(dojo.lang.find(this.editableAttributes, aname)>-1){
					tableAttributes[aname] = aval;
				}else{
					if(aname=='style') {
						if(aval=='null'){aval=this.tableNode.style.cssText;} //stupid ie
						var styleItems=aval.split(';');
						for(var j in styleItems) {
							matches=styleItems[j].match(/^\s*([a-z\-]+)\s*:\s*([^;]+)\s*$/i);
							if(matches && matches[1]) {
								matches[1]=matches[1].toLowerCase();
								if(matches[1]!='height'&&matches[1]!='width') {

									if(matches[1].indexOf('border')==0) {
										this.borderStyles+=styleItems[j]+';';
									} else {
										n=dojo.lang.find(this.editableStyles, matches[1]);
										if(n>-1){
											stylesMatched|=1<<n;

											if(matches[1].substr(matches[1].length-5)=='color')
												if(matches[2].toLowerCase().indexOf('rgb')==0) 
													matches[2] = (new dojo.gfx.color.Color(matches[2])).toHex();

											if(matches[1]=='background-image'){
												var urlmatch=matches[2].match(/\s*url\s*\(([^\)]+)\)/);
												if(urlmatch) matches[2]=urlmatch[1];		
											}

											this.setStyleItem(matches[1],matches[2]);
										} else {
											this.extraStyleText+=matches[1]+':'+matches[2]+';';
										}
									}
								}
							}
						}
// 					} else {
// 						//IE gives unset values as 'null' - yes... the 4 char string 'null'.  Unbelievable.
// 						if(aval && aval!='null') this.extraAttribText += ' '+aname + '="'+aval+'"';
					}
				}
			}
			var excludeTags=this.editableAttributes.slice();
			excludeTags.push('width','style','colspan','rowspan');
			this.extraAttribText=dojo.widget.Editor2Plugin.TableOperation._getAttributesHTML(this.propertyNode,excludeTags);
				
//dojo.debug(this.extraAttribText);
			//sort out border style
			var n=1;
			for(var i=3;i>=0;i--) if(this.propertyNode.style[dojo.html.toCamelCase(this.borderSides[i])+'Style']!='none') {
				sidesMatched|=(1<<i);n=i+1;
			}
			if(this.propertyNode.style.borderWidth) {
				this.TO_borderStyle.value=(
					this.propertyNode.style.borderStyle.match(/^\s*([^\s]+)\s+([^\s]+)\s+([^\s]+)\s+([^\s]+)\s*$/)? 
						RegExp['$'+n]:
						this.propertyNode.style.borderStyle
				);

				tableAttributes['border']=parseInt(
					this.propertyNode.style.borderWidth.match(/^\s*([^\s]+)\s+([^\s]+)\s+([^\s]+)\s+([^\s]+)\s*$/)?
						RegExp['$'+n]:
						this.propertyNode.style.borderWidth
				);

				//this is a bit messy...  handles checks for 4 values or just 1, and uses hex or converts rgb value.
				var bcolor=(this.propertyNode.style.borderColor.match(/^\s*(rgba*\([^\)]+\))\s+(rgba*\([^\)]+\))\s+(rgba*\([^\)]+\))\s+(rgba*\([^\)]+\))\s*$/i)?
					new dojo.gfx.color.Color(RegExp['$'+n]).toHex():
					(this.propertyNode.style.borderColor.toLowerCase().indexOf('rgb')==0 ?
						new dojo.gfx.color.Color(this.propertyNode.style.borderColor).toHex():
						(this.propertyNode.style.borderColor.match(/^\s*([^\s]+)\s+([^\s]+)\s+([^\s]+)\s+([^\s]+)\s*$/)?
							RegExp['$'+n]:
							this.propertyNode.style.borderColor
						)
					)
				);
				if(bcolor=='-moz-use-text-color') bcolor='';
				this.TO_borderColor.value=(bcolor);
			}
			this.TO_borderCollapse.checked=(this.tableNode.style.borderCollapse=='collapse');
		}else{
			this["TO_rows"].value = 3;
			this["TO_rows"].disabled = false;
			this["TO_cols"].value = 2;
			this["TO_cols"].disabled = false;
			this["TO_width"].value = 100;
			this["TO_widthtype"].value = "percent";
			this["TO_caption"].value = "";
			this.TO_borderColor.value=this.TO_borderStyle.value='';
			this.TO_whiteSpace.checked=false;
			sidesMatched=15;
		}

		for(var i=0; i<this.editableAttributes.length; ++i){
			name = this.editableAttributes[i];
			this["TO_"+name].value = (tableAttributes[name] == undefined) ? "" : tableAttributes[name];
		}
		for(var i=0; i<this.editableStyles.length; i++) if((stylesMatched&(1<<i))==0)
			this.setStyleItem(this.editableStyles[i],'');

		for(var i=0;i<4;i++) this['TO_'+dojo.html.toCamelCase(this.borderSides[i])].checked=(sidesMatched&(1<<i))?true:false;

		this.set_rules();
		this.bordercolorpickerx.style.visibility=(this.TO_borderColor.value?'visible':'hidden');

		this.initBorderString=this.propertyNode?this.getBorderStyleCSSText():'';
		this.initDone=true;
		this.resetSample();
		
		return true;
	},
	ok: function(){
		var curInst = dojo.widget.Editor2Manager.getCurrentInstance();
		var args = {}, theadHTML='', tbodyHTML='';
		
		// keep width out of sample to prevent dialog messing up.
		args["tableattrs"] = (this.mode!='trproperties' && this["TO_width"].value?
			'width="'+this["TO_width"].value +
			((this["TO_widthtype"].value == "percent")?'%" ':'px" '):'')+
			this.getEdAttr()+this.extraAttribText;

		if(this.mode=='tableproperties') {
			//show the border in IE by applying a custom class
			if(dojo.render.html.ie && !this["TO_border"].value){
				args["tableattrs"] += 'class="dojoShowIETableBorders" ';
			}
			
			var theadHTML= this.tableNode?
				this._nodeHTMLWithStyle(this.tableNode,args["tableattrs"],this._getFullStyleCSS(),false):
				'<table '+args["tableattrs"]+' style="'+this._getFullStyleCSS()+"\">\n";

			if(this["TO_caption"].value){theadHTML += "	<caption>"+this["TO_caption"].value+"</caption>\n";}
			if(this.tableNode){
				var tchildren=this.propertyNode.childNodes, tch_name;
				for(var i=0;i<tchildren.length;i++) {
					tch_name=tchildren[i].nodeName.toLowerCase();
					if(tch_name!='tbody' && tch_name!='tr' && tch_name!='caption') {
						theadHTML+='	'+dojo.widget.Editor2Plugin.TableOperation._nodeHTML(tchildren[i],null,null,true);
					}
				}
			}
		} else {
			var rows=this.tableNode.rows;
			var fullStyle=this._getFullStyleCSS();
			for(var nrow=0 ; nrow<rows.length ; nrow++) {
				if(this.mode=='trproperties') {
					tbodyHTML+=(this.ppad[nrow])?
						this._nodeHTMLWithStyle(rows[nrow],args["tableattrs"],fullStyle,true)+"\n":
						dojo.widget.Editor2Plugin.TableOperation._nodeHTML(rows[nrow],null,null,true);
				} else {
					tbodyHTML+='		<tr '+dojo.widget.Editor2Plugin.TableOperation._getAttributesHTML(rows[nrow])+'>';
					var chNodes=dojo.widget.Editor2Plugin.TableOperation._getCellChildren(rows[nrow]);
					for(var i=0; i<chNodes.length; i++) {
						tbodyHTML+=(this.ppad[nrow] && this.ppad[nrow][i])?
							this._nodeHTMLWithStyle(chNodes[i],args["tableattrs"],fullStyle,true)+"\n":
							dojo.widget.Editor2Plugin.TableOperation._nodeHTML(chNodes[i],null,null,true)+"\n";
					}
					tbodyHTML+="		</tr>\n";
				}
			}
			tbodyHTML+='	</tbody>';
		}

		if(this.tableNode){
	
			// modify table, retaining content
			dojo.widget.Editor2Plugin.TableOperation._reinsertTable(curInst,tbodyHTML,this.rowIndex,this.cellIndex,this.tableNode,theadHTML);
		} else {
		// insert new table
			args['rows'] = this["TO_rows"].value;
			args['cols'] = this["TO_cols"].value;

			var outertbody = "	<tbody>";
			var cols = "		<tr>";
			for (var i = 0; i < +args.cols; i++) { cols += "<td></td>"; }
			cols += "		</tr>";
			for (var i = 0; i < args.rows; i++) { outertbody += cols; }

			curInst.restoreSelection(); //restore previous selection, required for none-activeX IE

			curInst.execCommand("inserthtml", theadHTML+outertbody+'	</tbody></table>');
			curInst._updateHeight();
		}

		this.cancel();
	}
});