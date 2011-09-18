/*
	Copyright (c) 2004-2006, The Dojo Foundation
	All Rights Reserved.

	Licensed under the Academic Free License version 2.1 or above OR the
	modified BSD license. For more information on Dojo licensing, see:

		http://dojotoolkit.org/community/licensing.shtml
*/

/*
	This is a compiled version of Dojo, built for deployment and not for
	development. To get an editable version, please visit:

		http://dojotoolkit.org

	for documentation and information on getting the source.
*/

if(typeof dojo=="undefined"){
var dj_global=this;
var dj_currentContext=this;
function dj_undef(_1,_2){
return (typeof (_2||dj_currentContext)[_1]=="undefined");
}
if(dj_undef("djConfig",this)){
var djConfig={};
}
if(dj_undef("dojo",this)){
var dojo={};
}
dojo.global=function(){
return dj_currentContext;
};
dojo.locale=djConfig.locale;
dojo.version={major:0,minor:0,patch:0,flag:"dev",revision:Number("$Rev: 6425 $".match(/[0-9]+/)[0]),toString:function(){
with(dojo.version){
return major+"."+minor+"."+patch+flag+" ("+revision+")";
}
}};
dojo.evalProp=function(_3,_4,_5){
if((!_4)||(!_3)){
return undefined;
}
if(!dj_undef(_3,_4)){
return _4[_3];
}
return (_5?(_4[_3]={}):undefined);
};
dojo.parseObjPath=function(_6,_7,_8){
var _9=(_7||dojo.global());
var _a=_6.split(".");
var _b=_a.pop();
for(var i=0,l=_a.length;i<l&&_9;i++){
_9=dojo.evalProp(_a[i],_9,_8);
}
return {obj:_9,prop:_b};
};
dojo.evalObjPath=function(_e,_f){
if(typeof _e!="string"){
return dojo.global();
}
if(_e.indexOf(".")==-1){
return dojo.evalProp(_e,dojo.global(),_f);
}
var ref=dojo.parseObjPath(_e,dojo.global(),_f);
if(ref){
return dojo.evalProp(ref.prop,ref.obj,_f);
}
return null;
};
dojo.errorToString=function(_11){
if(!dj_undef("message",_11)){
return _11.message;
}else{
if(!dj_undef("description",_11)){
return _11.description;
}else{
return _11;
}
}
};
dojo.raise=function(_12,_13){
if(_13){
_12=_12+": "+dojo.errorToString(_13);
}else{
_12=dojo.errorToString(_12);
}
try{
if(djConfig.isDebug){
dojo.hostenv.println("FATAL exception raised: "+_12);
}
}
catch(e){
}
throw _13||Error(_12);
};
dojo.debug=function(){
};
dojo.debugShallow=function(obj){
};
dojo.profile={start:function(){
},end:function(){
},stop:function(){
},dump:function(){
}};
function dj_eval(_15){
return dj_global.eval?dj_global.eval(_15):eval(_15);
}
dojo.unimplemented=function(_16,_17){
var _18="'"+_16+"' not implemented";
if(_17!=null){
_18+=" "+_17;
}
dojo.raise(_18);
};
dojo.deprecated=function(_19,_1a,_1b){
var _1c="DEPRECATED: "+_19;
if(_1a){
_1c+=" "+_1a;
}
if(_1b){
_1c+=" -- will be removed in version: "+_1b;
}
dojo.debug(_1c);
};
dojo.render=(function(){
function vscaffold(_1d,_1e){
var tmp={capable:false,support:{builtin:false,plugin:false},prefixes:_1d};
for(var i=0;i<_1e.length;i++){
tmp[_1e[i]]=false;
}
return tmp;
}
return {name:"",ver:dojo.version,os:{win:false,linux:false,osx:false},html:vscaffold(["html"],["ie","opera","khtml","safari","moz"]),svg:vscaffold(["svg"],["corel","adobe","batik"]),vml:vscaffold(["vml"],["ie"]),swf:vscaffold(["Swf","Flash","Mm"],["mm"]),swt:vscaffold(["Swt"],["ibm"])};
})();
dojo.hostenv=(function(){
var _21={isDebug:false,allowQueryConfig:false,baseScriptUri:"",baseRelativePath:"",libraryScriptUri:"",iePreventClobber:false,ieClobberMinimal:true,preventBackButtonFix:true,delayMozLoadingFix:false,searchIds:[],parseWidgets:true};
if(typeof djConfig=="undefined"){
djConfig=_21;
}else{
for(var _22 in _21){
if(typeof djConfig[_22]=="undefined"){
djConfig[_22]=_21[_22];
}
}
}
return {name_:"(unset)",version_:"(unset)",getName:function(){
return this.name_;
},getVersion:function(){
return this.version_;
},getText:function(uri){
dojo.unimplemented("getText","uri="+uri);
}};
})();
dojo.hostenv.getBaseScriptUri=function(){
if(djConfig.baseScriptUri.length){
return djConfig.baseScriptUri;
}
var uri=new String(djConfig.libraryScriptUri||djConfig.baseRelativePath);
if(!uri){
dojo.raise("Nothing returned by getLibraryScriptUri(): "+uri);
}
var _25=uri.lastIndexOf("/");
djConfig.baseScriptUri=djConfig.baseRelativePath;
return djConfig.baseScriptUri;
};
(function(){
var _26={pkgFileName:"__package__",loading_modules_:{},loaded_modules_:{},addedToLoadingCount:[],removedFromLoadingCount:[],inFlightCount:0,modulePrefixes_:{dojo:{name:"dojo",value:"src"}},setModulePrefix:function(_27,_28){
this.modulePrefixes_[_27]={name:_27,value:_28};
},moduleHasPrefix:function(_29){
var mp=this.modulePrefixes_;
return Boolean(mp[_29]&&mp[_29].value);
},getModulePrefix:function(_2b){
if(this.moduleHasPrefix(_2b)){
return this.modulePrefixes_[_2b].value;
}
return _2b;
},getTextStack:[],loadUriStack:[],loadedUris:[],post_load_:false,modulesLoadedListeners:[],unloadListeners:[],loadNotifying:false};
for(var _2c in _26){
dojo.hostenv[_2c]=_26[_2c];
}
})();
dojo.hostenv.loadPath=function(_2d,_2e,cb){
var uri;
if(_2d.charAt(0)=="/"||_2d.match(/^\w+:/)){
uri=_2d;
}else{
uri=this.getBaseScriptUri()+_2d;
}
if(djConfig.cacheBust&&dojo.render.html.capable){
uri+="?"+String(djConfig.cacheBust).replace(/\W+/g,"");
}
try{
return !_2e?this.loadUri(uri,cb):this.loadUriAndCheck(uri,_2e,cb);
}
catch(e){
dojo.debug(e);
return false;
}
};
dojo.hostenv.loadUri=function(uri,cb){
if(this.loadedUris[uri]){
return true;
}
var _33=this.getText(uri,null,true);
if(!_33){
return false;
}
this.loadedUris[uri]=true;
if(cb){
_33="("+_33+")";
}
var _34=dj_eval(_33);
if(cb){
cb(_34);
}
return true;
};
dojo.hostenv.loadUriAndCheck=function(uri,_36,cb){
var ok=true;
try{
ok=this.loadUri(uri,cb);
}
catch(e){
dojo.debug("failed loading ",uri," with error: ",e);
}
return Boolean(ok&&this.findModule(_36,false));
};
dojo.loaded=function(){
};
dojo.unloaded=function(){
};
dojo.hostenv.loaded=function(){
this.loadNotifying=true;
this.post_load_=true;
var mll=this.modulesLoadedListeners;
for(var x=0;x<mll.length;x++){
mll[x]();
}
this.modulesLoadedListeners=[];
this.loadNotifying=false;
dojo.loaded();
};
dojo.hostenv.unloaded=function(){
var mll=this.unloadListeners;
while(mll.length){
(mll.pop())();
}
dojo.unloaded();
};
dojo.addOnLoad=function(obj,_3d){
var dh=dojo.hostenv;
if(arguments.length==1){
dh.modulesLoadedListeners.push(obj);
}else{
if(arguments.length>1){
dh.modulesLoadedListeners.push(function(){
obj[_3d]();
});
}
}
if(dh.post_load_&&dh.inFlightCount==0&&!dh.loadNotifying){
dh.callLoaded();
}
};
dojo.addOnUnload=function(obj,_40){
var dh=dojo.hostenv;
if(arguments.length==1){
dh.unloadListeners.push(obj);
}else{
if(arguments.length>1){
dh.unloadListeners.push(function(){
obj[_40]();
});
}
}
};
dojo.hostenv.modulesLoaded=function(){
if(this.post_load_){
return;
}
if(this.loadUriStack.length==0&&this.getTextStack.length==0){
if(this.inFlightCount>0){
dojo.debug("files still in flight!");
return;
}
dojo.hostenv.callLoaded();
}
};
dojo.hostenv.callLoaded=function(){
if(typeof setTimeout=="object"){
setTimeout("dojo.hostenv.loaded();",0);
}else{
dojo.hostenv.loaded();
}
};
dojo.hostenv.getModuleSymbols=function(_42){
var _43=_42.split(".");
for(var i=_43.length;i>0;i--){
var _45=_43.slice(0,i).join(".");
if((i==1)&&!this.moduleHasPrefix(_45)){
_43[0]="../"+_43[0];
}else{
var _46=this.getModulePrefix(_45);
if(_46!=_45){
_43.splice(0,i,_46);
break;
}
}
}
return _43;
};
dojo.hostenv._global_omit_module_check=false;
dojo.hostenv.loadModule=function(_47,_48,_49){
if(!_47){
return;
}
_49=this._global_omit_module_check||_49;
var _4a=this.findModule(_47,false);
if(_4a){
return _4a;
}
if(dj_undef(_47,this.loading_modules_)){
this.addedToLoadingCount.push(_47);
}
this.loading_modules_[_47]=1;
var _4b=_47.replace(/\./g,"/")+".js";
var _4c=_47.split(".");
var _4d=this.getModuleSymbols(_47);
var _4e=((_4d[0].charAt(0)!="/")&&!_4d[0].match(/^\w+:/));
var _4f=_4d[_4d.length-1];
var ok;
if(_4f=="*"){
_47=_4c.slice(0,-1).join(".");
while(_4d.length){
_4d.pop();
_4d.push(this.pkgFileName);
_4b=_4d.join("/")+".js";
if(_4e&&_4b.charAt(0)=="/"){
_4b=_4b.slice(1);
}
ok=this.loadPath(_4b,!_49?_47:null);
if(ok){
break;
}
_4d.pop();
}
}else{
_4b=_4d.join("/")+".js";
_47=_4c.join(".");
var _51=!_49?_47:null;
ok=this.loadPath(_4b,_51);
if(!ok&&!_48){
_4d.pop();
while(_4d.length){
_4b=_4d.join("/")+".js";
ok=this.loadPath(_4b,_51);
if(ok){
break;
}
_4d.pop();
_4b=_4d.join("/")+"/"+this.pkgFileName+".js";
if(_4e&&_4b.charAt(0)=="/"){
_4b=_4b.slice(1);
}
ok=this.loadPath(_4b,_51);
if(ok){
break;
}
}
}
if(!ok&&!_49){
dojo.raise("Could not load '"+_47+"'; last tried '"+_4b+"'");
}
}
if(!_49&&!this["isXDomain"]){
_4a=this.findModule(_47,false);
if(!_4a){
dojo.raise("symbol '"+_47+"' is not defined after loading '"+_4b+"'");
}
}
return _4a;
};
dojo.hostenv.startPackage=function(_52){
var _53=String(_52);
var _54=_53;
var _55=_52.split(/\./);
if(_55[_55.length-1]=="*"){
_55.pop();
_54=_55.join(".");
}
var _56=dojo.evalObjPath(_54,true);
this.loaded_modules_[_53]=_56;
this.loaded_modules_[_54]=_56;
return _56;
};
dojo.hostenv.findModule=function(_57,_58){
var lmn=String(_57);
if(this.loaded_modules_[lmn]){
return this.loaded_modules_[lmn];
}
if(_58){
dojo.raise("no loaded module named '"+_57+"'");
}
return null;
};
dojo.kwCompoundRequire=function(_5a){
var _5b=_5a["common"]||[];
var _5c=_5a[dojo.hostenv.name_]?_5b.concat(_5a[dojo.hostenv.name_]||[]):_5b.concat(_5a["default"]||[]);
for(var x=0;x<_5c.length;x++){
var _5e=_5c[x];
if(_5e.constructor==Array){
dojo.hostenv.loadModule.apply(dojo.hostenv,_5e);
}else{
dojo.hostenv.loadModule(_5e);
}
}
};
dojo.require=function(_5f){
dojo.hostenv.loadModule.apply(dojo.hostenv,arguments);
};
dojo.requireIf=function(_60,_61){
var _62=arguments[0];
if((_62===true)||(_62=="common")||(_62&&dojo.render[_62].capable)){
var _63=[];
for(var i=1;i<arguments.length;i++){
_63.push(arguments[i]);
}
dojo.require.apply(dojo,_63);
}
};
dojo.requireAfterIf=dojo.requireIf;
dojo.provide=function(_65){
return dojo.hostenv.startPackage.apply(dojo.hostenv,arguments);
};
dojo.registerModulePath=function(_66,_67){
return dojo.hostenv.setModulePrefix(_66,_67);
};
dojo.setModulePrefix=function(_68,_69){
dojo.deprecated("dojo.setModulePrefix(\""+_68+"\", \""+_69+"\")","replaced by dojo.registerModulePath","0.5");
return dojo.registerModulePath(_68,_69);
};
dojo.exists=function(obj,_6b){
var p=_6b.split(".");
for(var i=0;i<p.length;i++){
if(!obj[p[i]]){
return false;
}
obj=obj[p[i]];
}
return true;
};
dojo.hostenv.normalizeLocale=function(_6e){
return _6e?_6e.toLowerCase():dojo.locale;
};
dojo.hostenv.searchLocalePath=function(_6f,_70,_71){
_6f=dojo.hostenv.normalizeLocale(_6f);
var _72=_6f.split("-");
var _73=[];
for(var i=_72.length;i>0;i--){
_73.push(_72.slice(0,i).join("-"));
}
_73.push(false);
if(_70){
_73.reverse();
}
for(var j=_73.length-1;j>=0;j--){
var loc=_73[j]||"ROOT";
var _77=_71(loc);
if(_77){
break;
}
}
};
dojo.hostenv.localesGenerated=["ROOT","es-es","es","it-it","pt-br","de","de-at","fr-fr","zh-cn","pt","en-us","zh","fr","zh-tw","it","en-gb","xx","de-de","ko-kr","ja-jp","ko","en","ja"];
dojo.hostenv.registerNlsPrefix=function(){
dojo.registerModulePath("nls","nls");
};
dojo.hostenv.preloadLocalizations=function(){
if(dojo.hostenv.localesGenerated){
dojo.hostenv.registerNlsPrefix();
function preload(_78){
_78=dojo.hostenv.normalizeLocale(_78);
dojo.hostenv.searchLocalePath(_78,true,function(loc){
for(var i=0;i<dojo.hostenv.localesGenerated.length;i++){
if(dojo.hostenv.localesGenerated[i]==loc){
dojo["require"]("nls.dojo_"+loc);
return true;
}
}
return false;
});
}
preload();
var _7b=djConfig.extraLocale||[];
for(var i=0;i<_7b.length;i++){
preload(_7b[i]);
}
}
dojo.hostenv.preloadLocalizations=function(){
};
};
dojo.requireLocalization=function(_7d,_7e,_7f){
dojo.hostenv.preloadLocalizations();
var _80=[_7d,"nls",_7e].join(".");
var _81=dojo.hostenv.findModule(_80);
if(_81){
if(djConfig.localizationComplete&&_81._built){
return;
}
var _82=dojo.hostenv.normalizeLocale(_7f).replace("-","_");
var _83=_80+"."+_82;
if(dojo.hostenv.findModule(_83)){
return;
}
}
_81=dojo.hostenv.startPackage(_80);
var _84=dojo.hostenv.getModuleSymbols(_7d);
var _85=_84.concat("nls").join("/");
var _86;
dojo.hostenv.searchLocalePath(_7f,false,function(loc){
var _88=loc.replace("-","_");
var _89=_80+"."+_88;
var _8a=false;
if(!dojo.hostenv.findModule(_89)){
dojo.hostenv.startPackage(_89);
var _8b=[_85];
if(loc!="ROOT"){
_8b.push(loc);
}
_8b.push(_7e);
var _8c=_8b.join("/")+".js";
_8a=dojo.hostenv.loadPath(_8c,null,function(_8d){
var _8e=function(){
};
_8e.prototype=_86;
_81[_88]=new _8e();
for(var j in _8d){
_81[_88][j]=_8d[j];
}
});
}else{
_8a=true;
}
if(_8a&&_81[_88]){
_86=_81[_88];
}else{
_81[_88]=_86;
}
});
};
(function(){
var _90=djConfig.extraLocale;
if(_90){
if(!_90 instanceof Array){
_90=[_90];
}
var req=dojo.requireLocalization;
dojo.requireLocalization=function(m,b,_94){
req(m,b,_94);
if(_94){
return;
}
for(var i=0;i<_90.length;i++){
req(m,b,_90[i]);
}
};
}
})();
}
if(typeof window!="undefined"){
(function(){
if(djConfig.allowQueryConfig){
var _96=document.location.toString();
var _97=_96.split("?",2);
if(_97.length>1){
var _98=_97[1];
var _99=_98.split("&");
for(var x in _99){
var sp=_99[x].split("=");
if((sp[0].length>9)&&(sp[0].substr(0,9)=="djConfig.")){
var opt=sp[0].substr(9);
try{
djConfig[opt]=eval(sp[1]);
}
catch(e){
djConfig[opt]=sp[1];
}
}
}
}
}
if(((djConfig["baseScriptUri"]=="")||(djConfig["baseRelativePath"]==""))&&(document&&document.getElementsByTagName)){
var _9d=document.getElementsByTagName("script");
var _9e=/(__package__|dojo|bootstrap1)\.js([\?\.]|$)/i;
for(var i=0;i<_9d.length;i++){
var src=_9d[i].getAttribute("src");
if(!src){
continue;
}
var m=src.match(_9e);
if(m){
var _a2=src.substring(0,m.index);
if(src.indexOf("bootstrap1")>-1){
_a2+="../";
}
if(!this["djConfig"]){
djConfig={};
}
if(djConfig["baseScriptUri"]==""){
djConfig["baseScriptUri"]=_a2;
}
if(djConfig["baseRelativePath"]==""){
djConfig["baseRelativePath"]=_a2;
}
break;
}
}
}
var dr=dojo.render;
var drh=dojo.render.html;
var drs=dojo.render.svg;
var dua=(drh.UA=navigator.userAgent);
var dav=(drh.AV=navigator.appVersion);
var t=true;
var f=false;
drh.capable=t;
drh.support.builtin=t;
dr.ver=parseFloat(drh.AV);
dr.os.mac=dav.indexOf("Macintosh")>=0;
dr.os.win=dav.indexOf("Windows")>=0;
dr.os.linux=dav.indexOf("X11")>=0;
drh.opera=dua.indexOf("Opera")>=0;
drh.khtml=(dav.indexOf("Konqueror")>=0)||(dav.indexOf("Safari")>=0);
drh.safari=dav.indexOf("Safari")>=0;
var _aa=dua.indexOf("Gecko");
drh.mozilla=drh.moz=(_aa>=0)&&(!drh.khtml);
if(drh.mozilla){
drh.geckoVersion=dua.substring(_aa+6,_aa+14);
}
drh.ie=(document.all)&&(!drh.opera);
drh.ie50=drh.ie&&dav.indexOf("MSIE 5.0")>=0;
drh.ie55=drh.ie&&dav.indexOf("MSIE 5.5")>=0;
drh.ie60=drh.ie&&dav.indexOf("MSIE 6.0")>=0;
drh.ie70=drh.ie&&dav.indexOf("MSIE 7.0")>=0;
var cm=document["compatMode"];
drh.quirks=(cm=="BackCompat")||(cm=="QuirksMode")||drh.ie55||drh.ie50;
dojo.locale=dojo.locale||(drh.ie?navigator.userLanguage:navigator.language).toLowerCase();
dr.vml.capable=drh.ie;
drs.capable=f;
drs.support.plugin=f;
drs.support.builtin=f;
var _ac=window["document"];
var tdi=_ac["implementation"];
if((tdi)&&(tdi["hasFeature"])&&(tdi.hasFeature("org.w3c.dom.svg","1.0"))){
drs.capable=t;
drs.support.builtin=t;
drs.support.plugin=f;
}
if(drh.safari){
var tmp=dua.split("AppleWebKit/")[1];
var ver=parseFloat(tmp.split(" ")[0]);
if(ver>=420){
drs.capable=t;
drs.support.builtin=t;
drs.support.plugin=f;
}
}else{
}
})();
dojo.hostenv.startPackage("dojo.hostenv");
dojo.render.name=dojo.hostenv.name_="browser";
dojo.hostenv.searchIds=[];
dojo.hostenv._XMLHTTP_PROGIDS=["Msxml2.XMLHTTP","Microsoft.XMLHTTP","Msxml2.XMLHTTP.4.0"];
dojo.hostenv.getXmlhttpObject=function(){
var _b0=null;
var _b1=null;
try{
_b0=new XMLHttpRequest();
}
catch(e){
}
if(!_b0){
for(var i=0;i<3;++i){
var _b3=dojo.hostenv._XMLHTTP_PROGIDS[i];
try{
_b0=new ActiveXObject(_b3);
}
catch(e){
_b1=e;
}
if(_b0){
dojo.hostenv._XMLHTTP_PROGIDS=[_b3];
break;
}
}
}
if(!_b0){
return dojo.raise("XMLHTTP not available",_b1);
}
return _b0;
};
dojo.hostenv._blockAsync=false;
dojo.hostenv.getText=function(uri,_b5,_b6){
if(!_b5){
this._blockAsync=true;
}
var _b7=this.getXmlhttpObject();
function isDocumentOk(_b8){
var _b9=_b8["status"];
return Boolean((!_b9)||((200<=_b9)&&(300>_b9))||(_b9==304));
}
if(_b5){
var _ba=this,_bb=null,gbl=dojo.global();
var xhr=dojo.evalObjPath("dojo.io.XMLHTTPTransport");
_b7.onreadystatechange=function(){
if(_bb){
gbl.clearTimeout(_bb);
_bb=null;
}
if(_ba._blockAsync||(xhr&&xhr._blockAsync)){
_bb=gbl.setTimeout(function(){
_b7.onreadystatechange.apply(this);
},10);
}else{
if(4==_b7.readyState){
if(isDocumentOk(_b7)){
_b5(_b7.responseText);
}
}
}
};
}
_b7.open("GET",uri,_b5?true:false);
try{
_b7.send(null);
if(_b5){
return null;
}
if(!isDocumentOk(_b7)){
var err=Error("Unable to load "+uri+" status:"+_b7.status);
err.status=_b7.status;
err.responseText=_b7.responseText;
throw err;
}
}
catch(e){
this._blockAsync=false;
if((_b6)&&(!_b5)){
return null;
}else{
throw e;
}
}
this._blockAsync=false;
return _b7.responseText;
};
dojo.hostenv.defaultDebugContainerId="dojoDebug";
dojo.hostenv._println_buffer=[];
dojo.hostenv._println_safe=false;
dojo.hostenv.println=function(_bf){
if(!dojo.hostenv._println_safe){
dojo.hostenv._println_buffer.push(_bf);
}else{
try{
var _c0=document.getElementById(djConfig.debugContainerId?djConfig.debugContainerId:dojo.hostenv.defaultDebugContainerId);
if(!_c0){
_c0=dojo.body();
}
var div=document.createElement("div");
div.appendChild(document.createTextNode(_bf));
_c0.appendChild(div);
}
catch(e){
try{
document.write("<div>"+_bf+"</div>");
}
catch(e2){
window.status=_bf;
}
}
}
};
dojo.addOnLoad(function(){
dojo.hostenv._println_safe=true;
while(dojo.hostenv._println_buffer.length>0){
dojo.hostenv.println(dojo.hostenv._println_buffer.shift());
}
});
function dj_addNodeEvtHdlr(_c2,_c3,fp){
var _c5=_c2["on"+_c3]||function(){
};
_c2["on"+_c3]=function(){
fp.apply(_c2,arguments);
_c5.apply(_c2,arguments);
};
return true;
}
function dj_load_init(e){
var _c7=(e&&e.type)?e.type.toLowerCase():"load";
if(arguments.callee.initialized||(_c7!="domcontentloaded"&&_c7!="load")){
return;
}
arguments.callee.initialized=true;
if(typeof (_timer)!="undefined"){
clearInterval(_timer);
delete _timer;
}
var _c8=function(){
if(dojo.render.html.ie){
dojo.hostenv.makeWidgets();
}
};
if(dojo.hostenv.inFlightCount==0){
_c8();
dojo.hostenv.modulesLoaded();
}else{
dojo.hostenv.modulesLoadedListeners.unshift(_c8);
}
}
if(document.addEventListener){
if(dojo.render.html.opera||(dojo.render.html.moz&&!djConfig.delayMozLoadingFix)){
document.addEventListener("DOMContentLoaded",dj_load_init,null);
}
window.addEventListener("load",dj_load_init,null);
}
if(dojo.render.html.ie&&dojo.render.os.win){
document.write("<scr"+"ipt defer src=\"//:\" "+"onreadystatechange=\"if(this.readyState=='complete'){dj_load_init();}\">"+"</scr"+"ipt>");
}
if(/(WebKit|khtml)/i.test(navigator.userAgent)){
var _timer=setInterval(function(){
if(/loaded|complete/.test(document.readyState)){
dj_load_init();
}
},10);
}
if(dojo.render.html.ie){
dj_addNodeEvtHdlr(window,"beforeunload",function(){
dojo.hostenv._unloading=true;
window.setTimeout(function(){
dojo.hostenv._unloading=false;
},0);
});
}
dj_addNodeEvtHdlr(window,"unload",function(){
dojo.hostenv.unloaded();
if((!dojo.render.html.ie)||(dojo.render.html.ie&&dojo.hostenv._unloading)){
dojo.hostenv.unloaded();
}
});
dojo.hostenv.makeWidgets=function(){
var _c9=[];
if(djConfig.searchIds&&djConfig.searchIds.length>0){
_c9=_c9.concat(djConfig.searchIds);
}
if(dojo.hostenv.searchIds&&dojo.hostenv.searchIds.length>0){
_c9=_c9.concat(dojo.hostenv.searchIds);
}
if((djConfig.parseWidgets)||(_c9.length>0)){
if(dojo.evalObjPath("dojo.widget.Parse")){
var _ca=new dojo.xml.Parse();
if(_c9.length>0){
for(var x=0;x<_c9.length;x++){
var _cc=document.getElementById(_c9[x]);
if(!_cc){
continue;
}
var _cd=_ca.parseElement(_cc,null,true);
dojo.widget.getParser().createComponents(_cd);
}
}else{
if(djConfig.parseWidgets){
var _cd=_ca.parseElement(dojo.body(),null,true);
dojo.widget.getParser().createComponents(_cd);
}
}
}
}
};
dojo.addOnLoad(function(){
if(!dojo.render.html.ie){
dojo.hostenv.makeWidgets();
}
});
try{
if(dojo.render.html.ie){
document.namespaces.add("v","urn:schemas-microsoft-com:vml");
document.createStyleSheet().addRule("v\\:*","behavior:url(#default#VML)");
}
}
catch(e){
}
dojo.hostenv.writeIncludes=function(){
};
if(!dj_undef("document",this)){
dj_currentDocument=this.document;
}
dojo.doc=function(){
return dj_currentDocument;
};
dojo.body=function(){
return dojo.doc().body||dojo.doc().getElementsByTagName("body")[0];
};
dojo.byId=function(id,doc){
if((id)&&((typeof id=="string")||(id instanceof String))){
if(!doc){
doc=dj_currentDocument;
}
var ele=doc.getElementById(id);
if(ele&&(ele.id!=id)&&doc.all){
ele=null;
eles=doc.all[id];
if(eles){
if(eles.length){
for(var i=0;i<eles.length;i++){
if(eles[i].id==id){
ele=eles[i];
break;
}
}
}else{
ele=eles;
}
}
}
return ele;
}
return id;
};
dojo.setContext=function(_d2,_d3){
dj_currentContext=_d2;
dj_currentDocument=_d3;
};
dojo._fireCallback=function(_d4,_d5,_d6){
if((_d5)&&((typeof _d4=="string")||(_d4 instanceof String))){
_d4=_d5[_d4];
}
return (_d5?_d4.apply(_d5,_d6||[]):_d4());
};
dojo.withGlobal=function(_d7,_d8,_d9,_da){
var _db;
var _dc=dj_currentContext;
var _dd=dj_currentDocument;
try{
dojo.setContext(_d7,_d7.document);
_db=dojo._fireCallback(_d8,_d9,_da);
}
finally{
dojo.setContext(_dc,_dd);
}
return _db;
};
dojo.withDoc=function(_de,_df,_e0,_e1){
var _e2;
var _e3=dj_currentDocument;
try{
dj_currentDocument=_de;
_e2=dojo._fireCallback(_df,_e0,_e1);
}
finally{
dj_currentDocument=_e3;
}
return _e2;
};
}
(function(){
if(typeof dj_usingBootstrap!="undefined"){
return;
}
var _e4=false;
var _e5=false;
var _e6=false;
if((typeof this["load"]=="function")&&((typeof this["Packages"]=="function")||(typeof this["Packages"]=="object"))){
_e4=true;
}else{
if(typeof this["load"]=="function"){
_e5=true;
}else{
if(window.widget){
_e6=true;
}
}
}
var _e7=[];
if((this["djConfig"])&&((djConfig["isDebug"])||(djConfig["debugAtAllCosts"]))){
_e7.push("debug.js");
}
if((this["djConfig"])&&(djConfig["debugAtAllCosts"])&&(!_e4)&&(!_e6)){
_e7.push("browser_debug.js");
}
var _e8=djConfig["baseScriptUri"];
if((this["djConfig"])&&(djConfig["baseLoaderUri"])){
_e8=djConfig["baseLoaderUri"];
}
for(var x=0;x<_e7.length;x++){
var _ea=_e8+"src/"+_e7[x];
if(_e4||_e5){
load(_ea);
}else{
try{
document.write("<scr"+"ipt type='text/javascript' src='"+_ea+"'></scr"+"ipt>");
}
catch(e){
var _eb=document.createElement("script");
_eb.src=_ea;
document.getElementsByTagName("head")[0].appendChild(_eb);
}
}
}
})();
dojo.provide("dojo.string.common");
dojo.string.trim=function(str,wh){
if(!str.replace){
return str;
}
if(!str.length){
return str;
}
var re=(wh>0)?(/^\s+/):(wh<0)?(/\s+$/):(/^\s+|\s+$/g);
return str.replace(re,"");
};
dojo.string.trimStart=function(str){
return dojo.string.trim(str,1);
};
dojo.string.trimEnd=function(str){
return dojo.string.trim(str,-1);
};
dojo.string.repeat=function(str,_f2,_f3){
var out="";
for(var i=0;i<_f2;i++){
out+=str;
if(_f3&&i<_f2-1){
out+=_f3;
}
}
return out;
};
dojo.string.pad=function(str,len,c,dir){
var out=String(str);
if(!c){
c="0";
}
if(!dir){
dir=1;
}
while(out.length<len){
if(dir>0){
out=c+out;
}else{
out+=c;
}
}
return out;
};
dojo.string.padLeft=function(str,len,c){
return dojo.string.pad(str,len,c,1);
};
dojo.string.padRight=function(str,len,c){
return dojo.string.pad(str,len,c,-1);
};
dojo.provide("dojo.string");
dojo.provide("dojo.lang.common");
dojo.lang.inherits=function(_101,_102){
if(typeof _102!="function"){
dojo.raise("dojo.inherits: superclass argument ["+_102+"] must be a function (subclass: ["+_101+"']");
}
_101.prototype=new _102();
_101.prototype.constructor=_101;
_101.superclass=_102.prototype;
_101["super"]=_102.prototype;
};
dojo.lang._mixin=function(obj,_104){
var tobj={};
for(var x in _104){
if((typeof tobj[x]=="undefined")||(tobj[x]!=_104[x])){
obj[x]=_104[x];
}
}
if(dojo.render.html.ie&&(typeof (_104["toString"])=="function")&&(_104["toString"]!=obj["toString"])&&(_104["toString"]!=tobj["toString"])){
obj.toString=_104.toString;
}
return obj;
};
dojo.lang.mixin=function(obj,_108){
for(var i=1,l=arguments.length;i<l;i++){
dojo.lang._mixin(obj,arguments[i]);
}
return obj;
};
dojo.lang.extend=function(_10b,_10c){
for(var i=1,l=arguments.length;i<l;i++){
dojo.lang._mixin(_10b.prototype,arguments[i]);
}
return _10b;
};
dojo.lang._delegate=function(obj){
function TMP(){
}
TMP.prototype=obj;
return new TMP();
};
dojo.inherits=dojo.lang.inherits;
dojo.mixin=dojo.lang.mixin;
dojo.extend=dojo.lang.extend;
dojo.lang.find=function(_110,_111,_112,_113){
if(!dojo.lang.isArrayLike(_110)&&dojo.lang.isArrayLike(_111)){
dojo.deprecated("dojo.lang.find(value, array)","use dojo.lang.find(array, value) instead","0.5");
var temp=_110;
_110=_111;
_111=temp;
}
var _115=dojo.lang.isString(_110);
if(_115){
_110=_110.split("");
}
if(_113){
var step=-1;
var i=_110.length-1;
var end=-1;
}else{
var step=1;
var i=0;
var end=_110.length;
}
if(_112){
while(i!=end){
if(_110[i]===_111){
return i;
}
i+=step;
}
}else{
while(i!=end){
if(_110[i]==_111){
return i;
}
i+=step;
}
}
return -1;
};
dojo.lang.indexOf=dojo.lang.find;
dojo.lang.findLast=function(_119,_11a,_11b){
return dojo.lang.find(_119,_11a,_11b,true);
};
dojo.lang.lastIndexOf=dojo.lang.findLast;
dojo.lang.inArray=function(_11c,_11d){
return dojo.lang.find(_11c,_11d)>-1;
};
dojo.lang.isObject=function(it){
if(typeof it=="undefined"){
return false;
}
return (typeof it=="object"||it===null||dojo.lang.isArray(it)||dojo.lang.isFunction(it));
};
dojo.lang.isArray=function(it){
return (it&&it instanceof Array||typeof it=="array");
};
dojo.lang.isArrayLike=function(it){
if((!it)||(dojo.lang.isUndefined(it))){
return false;
}
if(dojo.lang.isString(it)){
return false;
}
if(dojo.lang.isFunction(it)){
return false;
}
if(dojo.lang.isArray(it)){
return true;
}
if((it.tagName)&&(it.tagName.toLowerCase()=="form")){
return false;
}
if(dojo.lang.isNumber(it.length)&&isFinite(it.length)){
return true;
}
return false;
};
dojo.lang.isFunction=function(it){
return (it instanceof Function||typeof it=="function");
};
(function(){
if((dojo.render.html.capable)&&(dojo.render.html["safari"])){
dojo.lang.isFunction=function(it){
if((typeof (it)=="function")&&(it=="[object NodeList]")){
return false;
}
return (it instanceof Function||typeof it=="function");
};
}
})();
dojo.lang.isString=function(it){
return (typeof it=="string"||it instanceof String);
};
dojo.lang.isAlien=function(it){
if(!it){
return false;
}
return !dojo.lang.isFunction(it)&&/\{\s*\[native code\]\s*\}/.test(String(it));
};
dojo.lang.isBoolean=function(it){
return (it instanceof Boolean||typeof it=="boolean");
};
dojo.lang.isNumber=function(it){
return (it instanceof Number||typeof it=="number");
};
dojo.lang.isUndefined=function(it){
return ((typeof (it)=="undefined")&&(it==undefined));
};
dojo.provide("dojo.lang.extras");
dojo.lang.setTimeout=function(func,_129){
var _12a=window,_12b=2;
if(!dojo.lang.isFunction(func)){
_12a=func;
func=_129;
_129=arguments[2];
_12b++;
}
if(dojo.lang.isString(func)){
func=_12a[func];
}
var args=[];
for(var i=_12b;i<arguments.length;i++){
args.push(arguments[i]);
}
return dojo.global().setTimeout(function(){
func.apply(_12a,args);
},_129);
};
dojo.lang.clearTimeout=function(_12e){
dojo.global().clearTimeout(_12e);
};
dojo.lang.getNameInObj=function(ns,item){
if(!ns){
ns=dj_global;
}
for(var x in ns){
if(ns[x]===item){
return new String(x);
}
}
return null;
};
dojo.lang.shallowCopy=function(obj,deep){
var i,ret;
if(obj===null){
return null;
}
if(dojo.lang.isObject(obj)){
ret=new obj.constructor();
for(i in obj){
if(dojo.lang.isUndefined(ret[i])){
ret[i]=deep?dojo.lang.shallowCopy(obj[i],deep):obj[i];
}
}
}else{
if(dojo.lang.isArray(obj)){
ret=[];
for(i=0;i<obj.length;i++){
ret[i]=deep?dojo.lang.shallowCopy(obj[i],deep):obj[i];
}
}else{
ret=obj;
}
}
return ret;
};
dojo.lang.firstValued=function(){
for(var i=0;i<arguments.length;i++){
if(typeof arguments[i]!="undefined"){
return arguments[i];
}
}
return undefined;
};
dojo.lang.getObjPathValue=function(_137,_138,_139){
with(dojo.parseObjPath(_137,_138,_139)){
return dojo.evalProp(prop,obj,_139);
}
};
dojo.lang.setObjPathValue=function(_13a,_13b,_13c,_13d){
if(arguments.length<4){
_13d=true;
}
with(dojo.parseObjPath(_13a,_13c,_13d)){
if(obj&&(_13d||(prop in obj))){
obj[prop]=_13b;
}
}
};
dojo.provide("dojo.io.common");
dojo.io.transports=[];
dojo.io.hdlrFuncNames=["load","error","timeout"];
dojo.io.Request=function(url,_13f,_140,_141){
if((arguments.length==1)&&(arguments[0].constructor==Object)){
this.fromKwArgs(arguments[0]);
}else{
this.url=url;
if(_13f){
this.mimetype=_13f;
}
if(_140){
this.transport=_140;
}
if(arguments.length>=4){
this.changeUrl=_141;
}
}
};
dojo.lang.extend(dojo.io.Request,{url:"",mimetype:"text/plain",method:"GET",content:undefined,transport:undefined,changeUrl:undefined,formNode:undefined,sync:false,bindSuccess:false,useCache:false,preventCache:false,load:function(type,data,_144,_145){
},error:function(type,_147,_148,_149){
},timeout:function(type,_14b,_14c,_14d){
},handle:function(type,data,_150,_151){
},timeoutSeconds:0,abort:function(){
},fromKwArgs:function(_152){
if(_152["url"]){
_152.url=_152.url.toString();
}
if(_152["formNode"]){
_152.formNode=dojo.byId(_152.formNode);
}
if(!_152["method"]&&_152["formNode"]&&_152["formNode"].method){
_152.method=_152["formNode"].method;
}
if(!_152["handle"]&&_152["handler"]){
_152.handle=_152.handler;
}
if(!_152["load"]&&_152["loaded"]){
_152.load=_152.loaded;
}
if(!_152["changeUrl"]&&_152["changeURL"]){
_152.changeUrl=_152.changeURL;
}
_152.encoding=dojo.lang.firstValued(_152["encoding"],djConfig["bindEncoding"],"");
_152.sendTransport=dojo.lang.firstValued(_152["sendTransport"],djConfig["ioSendTransport"],false);
var _153=dojo.lang.isFunction;
for(var x=0;x<dojo.io.hdlrFuncNames.length;x++){
var fn=dojo.io.hdlrFuncNames[x];
if(_152[fn]&&_153(_152[fn])){
continue;
}
if(_152["handle"]&&_153(_152["handle"])){
_152[fn]=_152.handle;
}
}
dojo.lang.mixin(this,_152);
}});
dojo.io.Error=function(msg,type,num){
this.message=msg;
this.type=type||"unknown";
this.number=num||0;
};
dojo.io.transports.addTransport=function(name){
this.push(name);
this[name]=dojo.io[name];
};
dojo.io.bind=function(_15a){
if(!(_15a instanceof dojo.io.Request)){
try{
_15a=new dojo.io.Request(_15a);
}
catch(e){
dojo.debug(e);
}
}
var _15b="";
if(_15a["transport"]){
_15b=_15a["transport"];
if(!this[_15b]){
dojo.io.sendBindError(_15a,"No dojo.io.bind() transport with name '"+_15a["transport"]+"'.");
return _15a;
}
if(!this[_15b].canHandle(_15a)){
dojo.io.sendBindError(_15a,"dojo.io.bind() transport with name '"+_15a["transport"]+"' cannot handle this type of request.");
return _15a;
}
}else{
for(var x=0;x<dojo.io.transports.length;x++){
var tmp=dojo.io.transports[x];
if((this[tmp])&&(this[tmp].canHandle(_15a))){
_15b=tmp;
break;
}
}
if(_15b==""){
dojo.io.sendBindError(_15a,"None of the loaded transports for dojo.io.bind()"+" can handle the request.");
return _15a;
}
}
this[_15b].bind(_15a);
_15a.bindSuccess=true;
return _15a;
};
dojo.io.sendBindError=function(_15e,_15f){
if((typeof _15e.error=="function"||typeof _15e.handle=="function")&&(typeof setTimeout=="function"||typeof setTimeout=="object")){
var _160=new dojo.io.Error(_15f);
setTimeout(function(){
_15e[(typeof _15e.error=="function")?"error":"handle"]("error",_160,null,_15e);
},50);
}else{
dojo.raise(_15f);
}
};
dojo.io.queueBind=function(_161){
if(!(_161 instanceof dojo.io.Request)){
try{
_161=new dojo.io.Request(_161);
}
catch(e){
dojo.debug(e);
}
}
var _162=_161.load;
_161.load=function(){
dojo.io._queueBindInFlight=false;
var ret=_162.apply(this,arguments);
dojo.io._dispatchNextQueueBind();
return ret;
};
var _164=_161.error;
_161.error=function(){
dojo.io._queueBindInFlight=false;
var ret=_164.apply(this,arguments);
dojo.io._dispatchNextQueueBind();
return ret;
};
dojo.io._bindQueue.push(_161);
dojo.io._dispatchNextQueueBind();
return _161;
};
dojo.io._dispatchNextQueueBind=function(){
if(!dojo.io._queueBindInFlight){
dojo.io._queueBindInFlight=true;
if(dojo.io._bindQueue.length>0){
dojo.io.bind(dojo.io._bindQueue.shift());
}else{
dojo.io._queueBindInFlight=false;
}
}
};
dojo.io._bindQueue=[];
dojo.io._queueBindInFlight=false;
dojo.io.argsFromMap=function(map,_167,last){
var enc=/utf/i.test(_167||"")?encodeURIComponent:dojo.string.encodeAscii;
var _16a=[];
var _16b=new Object();
for(var name in map){
var _16d=function(elt){
var val=enc(name)+"="+enc(elt);
_16a[(last==name)?"push":"unshift"](val);
};
if(!_16b[name]){
var _170=map[name];
if(dojo.lang.isArray(_170)){
dojo.lang.forEach(_170,_16d);
}else{
_16d(_170);
}
}
}
return _16a.join("&");
};
dojo.io.setIFrameSrc=function(_171,src,_173){
try{
var r=dojo.render.html;
if(!_173){
if(r.safari){
_171.location=src;
}else{
frames[_171.name].location=src;
}
}else{
var idoc;
if(r.ie){
idoc=_171.contentWindow.document;
}else{
if(r.safari){
idoc=_171.document;
}else{
idoc=_171.contentWindow;
}
}
if(!idoc){
_171.location=src;
return;
}else{
idoc.location.replace(src);
}
}
}
catch(e){
dojo.debug(e);
dojo.debug("setIFrameSrc: "+e);
}
};
dojo.provide("dojo.lang.array");
dojo.lang.mixin(dojo.lang,{has:function(obj,name){
try{
return typeof obj[name]!="undefined";
}
catch(e){
return false;
}
},isEmpty:function(obj){
if(dojo.lang.isObject(obj)){
var tmp={};
var _17a=0;
for(var x in obj){
if(obj[x]&&(!tmp[x])){
_17a++;
break;
}
}
return _17a==0;
}else{
if(dojo.lang.isArrayLike(obj)||dojo.lang.isString(obj)){
return obj.length==0;
}
}
},map:function(arr,obj,_17e){
var _17f=dojo.lang.isString(arr);
if(_17f){
arr=arr.split("");
}
if(dojo.lang.isFunction(obj)&&(!_17e)){
_17e=obj;
obj=dj_global;
}else{
if(dojo.lang.isFunction(obj)&&_17e){
var _180=obj;
obj=_17e;
_17e=_180;
}
}
if(Array.map){
var _181=Array.map(arr,_17e,obj);
}else{
var _181=[];
for(var i=0;i<arr.length;++i){
_181.push(_17e.call(obj,arr[i]));
}
}
if(_17f){
return _181.join("");
}else{
return _181;
}
},reduce:function(arr,_184,obj,_186){
var _187=_184;
var ob=obj?obj:dj_global;
dojo.lang.map(arr,function(val){
_187=_186.call(ob,_187,val);
});
return _187;
},forEach:function(_18a,_18b,_18c){
if(dojo.lang.isString(_18a)){
_18a=_18a.split("");
}
if(Array.forEach){
Array.forEach(_18a,_18b,_18c);
}else{
if(!_18c){
_18c=dj_global;
}
for(var i=0,l=_18a.length;i<l;i++){
_18b.call(_18c,_18a[i],i,_18a);
}
}
},_everyOrSome:function(_18f,arr,_191,_192){
if(dojo.lang.isString(arr)){
arr=arr.split("");
}
if(Array.every){
return Array[_18f?"every":"some"](arr,_191,_192);
}else{
if(!_192){
_192=dj_global;
}
for(var i=0,l=arr.length;i<l;i++){
var _195=_191.call(_192,arr[i],i,arr);
if(_18f&&!_195){
return false;
}else{
if((!_18f)&&(_195)){
return true;
}
}
}
return Boolean(_18f);
}
},every:function(arr,_197,_198){
return this._everyOrSome(true,arr,_197,_198);
},some:function(arr,_19a,_19b){
return this._everyOrSome(false,arr,_19a,_19b);
},filter:function(arr,_19d,_19e){
var _19f=dojo.lang.isString(arr);
if(_19f){
arr=arr.split("");
}
var _1a0;
if(Array.filter){
_1a0=Array.filter(arr,_19d,_19e);
}else{
if(!_19e){
if(arguments.length>=3){
dojo.raise("thisObject doesn't exist!");
}
_19e=dj_global;
}
_1a0=[];
for(var i=0;i<arr.length;i++){
if(_19d.call(_19e,arr[i],i,arr)){
_1a0.push(arr[i]);
}
}
}
if(_19f){
return _1a0.join("");
}else{
return _1a0;
}
},unnest:function(){
var out=[];
for(var i=0;i<arguments.length;i++){
if(dojo.lang.isArrayLike(arguments[i])){
var add=dojo.lang.unnest.apply(this,arguments[i]);
out=out.concat(add);
}else{
out.push(arguments[i]);
}
}
return out;
},toArray:function(_1a5,_1a6){
var _1a7=[];
for(var i=_1a6||0;i<_1a5.length;i++){
_1a7.push(_1a5[i]);
}
return _1a7;
}});
dojo.provide("dojo.lang.func");
dojo.lang.hitch=function(_1a9,_1aa){
var fcn=(dojo.lang.isString(_1aa)?_1a9[_1aa]:_1aa)||function(){
};
return function(){
return fcn.apply(_1a9,arguments);
};
};
dojo.lang.anonCtr=0;
dojo.lang.anon={};
dojo.lang.nameAnonFunc=function(_1ac,_1ad,_1ae){
var nso=(_1ad||dojo.lang.anon);
if((_1ae)||((dj_global["djConfig"])&&(djConfig["slowAnonFuncLookups"]==true))){
for(var x in nso){
try{
if(nso[x]===_1ac){
return x;
}
}
catch(e){
}
}
}
var ret="__"+dojo.lang.anonCtr++;
while(typeof nso[ret]!="undefined"){
ret="__"+dojo.lang.anonCtr++;
}
nso[ret]=_1ac;
return ret;
};
dojo.lang.forward=function(_1b2){
return function(){
return this[_1b2].apply(this,arguments);
};
};
dojo.lang.curry=function(ns,func){
var _1b5=[];
ns=ns||dj_global;
if(dojo.lang.isString(func)){
func=ns[func];
}
for(var x=2;x<arguments.length;x++){
_1b5.push(arguments[x]);
}
var _1b7=(func["__preJoinArity"]||func.length)-_1b5.length;
function gather(_1b8,_1b9,_1ba){
var _1bb=_1ba;
var _1bc=_1b9.slice(0);
for(var x=0;x<_1b8.length;x++){
_1bc.push(_1b8[x]);
}
_1ba=_1ba-_1b8.length;
if(_1ba<=0){
var res=func.apply(ns,_1bc);
_1ba=_1bb;
return res;
}else{
return function(){
return gather(arguments,_1bc,_1ba);
};
}
}
return gather([],_1b5,_1b7);
};
dojo.lang.curryArguments=function(ns,func,args,_1c2){
var _1c3=[];
var x=_1c2||0;
for(x=_1c2;x<args.length;x++){
_1c3.push(args[x]);
}
return dojo.lang.curry.apply(dojo.lang,[ns,func].concat(_1c3));
};
dojo.lang.tryThese=function(){
for(var x=0;x<arguments.length;x++){
try{
if(typeof arguments[x]=="function"){
var ret=(arguments[x]());
if(ret){
return ret;
}
}
}
catch(e){
dojo.debug(e);
}
}
};
dojo.lang.delayThese=function(farr,cb,_1c9,_1ca){
if(!farr.length){
if(typeof _1ca=="function"){
_1ca();
}
return;
}
if((typeof _1c9=="undefined")&&(typeof cb=="number")){
_1c9=cb;
cb=function(){
};
}else{
if(!cb){
cb=function(){
};
if(!_1c9){
_1c9=0;
}
}
}
setTimeout(function(){
(farr.shift())();
cb();
dojo.lang.delayThese(farr,cb,_1c9,_1ca);
},_1c9);
};
dojo.provide("dojo.string.extras");
dojo.string.substituteParams=function(_1cb,hash){
var map=(typeof hash=="object")?hash:dojo.lang.toArray(arguments,1);
return _1cb.replace(/\%\{(\w+)\}/g,function(_1ce,key){
if(typeof (map[key])!="undefined"&&map[key]!=null){
return map[key];
}
dojo.raise("Substitution not found: "+key);
});
};
dojo.string.capitalize=function(str){
if(!dojo.lang.isString(str)){
return "";
}
if(arguments.length==0){
str=this;
}
var _1d1=str.split(" ");
for(var i=0;i<_1d1.length;i++){
_1d1[i]=_1d1[i].charAt(0).toUpperCase()+_1d1[i].substring(1);
}
return _1d1.join(" ");
};
dojo.string.isBlank=function(str){
if(!dojo.lang.isString(str)){
return true;
}
return (dojo.string.trim(str).length==0);
};
dojo.string.encodeAscii=function(str){
if(!dojo.lang.isString(str)){
return str;
}
var ret="";
var _1d6=escape(str);
var _1d7,re=/%u([0-9A-F]{4})/i;
while((_1d7=_1d6.match(re))){
var num=Number("0x"+_1d7[1]);
var _1da=escape("&#"+num+";");
ret+=_1d6.substring(0,_1d7.index)+_1da;
_1d6=_1d6.substring(_1d7.index+_1d7[0].length);
}
ret+=_1d6.replace(/\+/g,"%2B");
return ret;
};
dojo.string.escape=function(type,str){
var args=dojo.lang.toArray(arguments,1);
switch(type.toLowerCase()){
case "xml":
case "html":
case "xhtml":
return dojo.string.escapeXml.apply(this,args);
case "sql":
return dojo.string.escapeSql.apply(this,args);
case "regexp":
case "regex":
return dojo.string.escapeRegExp.apply(this,args);
case "javascript":
case "jscript":
case "js":
return dojo.string.escapeJavaScript.apply(this,args);
case "ascii":
return dojo.string.encodeAscii.apply(this,args);
default:
return str;
}
};
dojo.string.escapeXml=function(str,_1df){
str=str.replace(/&/gm,"&amp;").replace(/</gm,"&lt;").replace(/>/gm,"&gt;").replace(/"/gm,"&quot;");
if(!_1df){
str=str.replace(/'/gm,"&#39;");
}
return str;
};
dojo.string.escapeSql=function(str){
return str.replace(/'/gm,"''");
};
dojo.string.escapeRegExp=function(str){
return str.replace(/\\/gm,"\\\\").replace(/([\f\b\n\t\r[\^$|?*+(){}])/gm,"\\$1");
};
dojo.string.escapeJavaScript=function(str){
return str.replace(/(["'\f\b\n\t\r])/gm,"\\$1");
};
dojo.string.escapeString=function(str){
return ("\""+str.replace(/(["\\])/g,"\\$1")+"\"").replace(/[\f]/g,"\\f").replace(/[\b]/g,"\\b").replace(/[\n]/g,"\\n").replace(/[\t]/g,"\\t").replace(/[\r]/g,"\\r");
};
dojo.string.summary=function(str,len){
if(!len||str.length<=len){
return str;
}
return str.substring(0,len).replace(/\.+$/,"")+"...";
};
dojo.string.endsWith=function(str,end,_1e8){
if(_1e8){
str=str.toLowerCase();
end=end.toLowerCase();
}
if((str.length-end.length)<0){
return false;
}
return str.lastIndexOf(end)==str.length-end.length;
};
dojo.string.endsWithAny=function(str){
for(var i=1;i<arguments.length;i++){
if(dojo.string.endsWith(str,arguments[i])){
return true;
}
}
return false;
};
dojo.string.startsWith=function(str,_1ec,_1ed){
if(_1ed){
str=str.toLowerCase();
_1ec=_1ec.toLowerCase();
}
return str.indexOf(_1ec)==0;
};
dojo.string.startsWithAny=function(str){
for(var i=1;i<arguments.length;i++){
if(dojo.string.startsWith(str,arguments[i])){
return true;
}
}
return false;
};
dojo.string.has=function(str){
for(var i=1;i<arguments.length;i++){
if(str.indexOf(arguments[i])>-1){
return true;
}
}
return false;
};
dojo.string.normalizeNewlines=function(text,_1f3){
if(_1f3=="\n"){
text=text.replace(/\r\n/g,"\n");
text=text.replace(/\r/g,"\n");
}else{
if(_1f3=="\r"){
text=text.replace(/\r\n/g,"\r");
text=text.replace(/\n/g,"\r");
}else{
text=text.replace(/([^\r])\n/g,"$1\r\n").replace(/\r([^\n])/g,"\r\n$1");
}
}
return text;
};
dojo.string.splitEscaped=function(str,_1f5){
var _1f6=[];
for(var i=0,_1f8=0;i<str.length;i++){
if(str.charAt(i)=="\\"){
i++;
continue;
}
if(str.charAt(i)==_1f5){
_1f6.push(str.substring(_1f8,i));
_1f8=i+1;
}
}
_1f6.push(str.substr(_1f8));
return _1f6;
};
dojo.provide("dojo.dom");
dojo.dom.ELEMENT_NODE=1;
dojo.dom.ATTRIBUTE_NODE=2;
dojo.dom.TEXT_NODE=3;
dojo.dom.CDATA_SECTION_NODE=4;
dojo.dom.ENTITY_REFERENCE_NODE=5;
dojo.dom.ENTITY_NODE=6;
dojo.dom.PROCESSING_INSTRUCTION_NODE=7;
dojo.dom.COMMENT_NODE=8;
dojo.dom.DOCUMENT_NODE=9;
dojo.dom.DOCUMENT_TYPE_NODE=10;
dojo.dom.DOCUMENT_FRAGMENT_NODE=11;
dojo.dom.NOTATION_NODE=12;
dojo.dom.dojoml="http://www.dojotoolkit.org/2004/dojoml";
dojo.dom.xmlns={svg:"http://www.w3.org/2000/svg",smil:"http://www.w3.org/2001/SMIL20/",mml:"http://www.w3.org/1998/Math/MathML",cml:"http://www.xml-cml.org",xlink:"http://www.w3.org/1999/xlink",xhtml:"http://www.w3.org/1999/xhtml",xul:"http://www.mozilla.org/keymaster/gatekeeper/there.is.only.xul",xbl:"http://www.mozilla.org/xbl",fo:"http://www.w3.org/1999/XSL/Format",xsl:"http://www.w3.org/1999/XSL/Transform",xslt:"http://www.w3.org/1999/XSL/Transform",xi:"http://www.w3.org/2001/XInclude",xforms:"http://www.w3.org/2002/01/xforms",saxon:"http://icl.com/saxon",xalan:"http://xml.apache.org/xslt",xsd:"http://www.w3.org/2001/XMLSchema",dt:"http://www.w3.org/2001/XMLSchema-datatypes",xsi:"http://www.w3.org/2001/XMLSchema-instance",rdf:"http://www.w3.org/1999/02/22-rdf-syntax-ns#",rdfs:"http://www.w3.org/2000/01/rdf-schema#",dc:"http://purl.org/dc/elements/1.1/",dcq:"http://purl.org/dc/qualifiers/1.0","soap-env":"http://schemas.xmlsoap.org/soap/envelope/",wsdl:"http://schemas.xmlsoap.org/wsdl/",AdobeExtensions:"http://ns.adobe.com/AdobeSVGViewerExtensions/3.0/"};
dojo.dom.isNode=function(wh){
if(typeof Element=="function"){
try{
return wh instanceof Element;
}
catch(e){
}
}else{
return wh&&!isNaN(wh.nodeType);
}
};
dojo.dom.getUniqueId=function(){
var _1fa=dojo.doc();
do{
var id="dj_unique_"+(++arguments.callee._idIncrement);
}while(_1fa.getElementById(id));
return id;
};
dojo.dom.getUniqueId._idIncrement=0;
dojo.dom.firstElement=dojo.dom.getFirstChildElement=function(_1fc,_1fd){
var node=_1fc.firstChild;
while(node&&node.nodeType!=dojo.dom.ELEMENT_NODE){
node=node.nextSibling;
}
if(_1fd&&node&&node.tagName&&node.tagName.toLowerCase()!=_1fd.toLowerCase()){
node=dojo.dom.nextElement(node,_1fd);
}
return node;
};
dojo.dom.lastElement=dojo.dom.getLastChildElement=function(_1ff,_200){
var node=_1ff.lastChild;
while(node&&node.nodeType!=dojo.dom.ELEMENT_NODE){
node=node.previousSibling;
}
if(_200&&node&&node.tagName&&node.tagName.toLowerCase()!=_200.toLowerCase()){
node=dojo.dom.prevElement(node,_200);
}
return node;
};
dojo.dom.nextElement=dojo.dom.getNextSiblingElement=function(node,_203){
if(!node){
return null;
}
do{
node=node.nextSibling;
}while(node&&node.nodeType!=dojo.dom.ELEMENT_NODE);
if(node&&_203&&_203.toLowerCase()!=node.tagName.toLowerCase()){
return dojo.dom.nextElement(node,_203);
}
return node;
};
dojo.dom.prevElement=dojo.dom.getPreviousSiblingElement=function(node,_205){
if(!node){
return null;
}
if(_205){
_205=_205.toLowerCase();
}
do{
node=node.previousSibling;
}while(node&&node.nodeType!=dojo.dom.ELEMENT_NODE);
if(node&&_205&&_205.toLowerCase()!=node.tagName.toLowerCase()){
return dojo.dom.prevElement(node,_205);
}
return node;
};
dojo.dom.moveChildren=function(_206,_207,trim){
var _209=0;
if(trim){
while(_206.hasChildNodes()&&_206.firstChild.nodeType==dojo.dom.TEXT_NODE){
_206.removeChild(_206.firstChild);
}
while(_206.hasChildNodes()&&_206.lastChild.nodeType==dojo.dom.TEXT_NODE){
_206.removeChild(_206.lastChild);
}
}
while(_206.hasChildNodes()){
_207.appendChild(_206.firstChild);
_209++;
}
return _209;
};
dojo.dom.copyChildren=function(_20a,_20b,trim){
var _20d=_20a.cloneNode(true);
return this.moveChildren(_20d,_20b,trim);
};
dojo.dom.replaceChildren=function(node,_20f){
dojo.dom.removeChildren(node);
node.appendChild(_20f);
};
dojo.dom.removeChildren=function(node){
var _211=node.childNodes.length;
while(node.hasChildNodes()){
dojo.dom.destroyNode(node.firstChild);
}
return _211;
};
dojo.dom.replaceNode=function(node,_213){
if(dojo.render.html.ie){
node.parentNode.insertBefore(_213,node);
return dojo.dom.removeNode(node);
}else{
return node.parentNode.replaceChild(_213,node);
}
};
dojo.dom.destroyNode=function(node){
node=dojo.dom.removeNode(node);
if(dojo.render.html.ie){
node.outerHTML="";
}
};
dojo.dom.removeNode=function(node){
if(node&&node.parentNode){
return node.parentNode.removeChild(node);
}
};
dojo.dom.getAncestors=function(node,_217,_218){
var _219=[];
var _21a=(_217&&(_217 instanceof Function||typeof _217=="function"));
while(node){
if(!_21a||_217(node)){
_219.push(node);
}
if(_218&&_219.length>0){
return _219[0];
}
node=node.parentNode;
}
if(_218){
return null;
}
return _219;
};
dojo.dom.getAncestorsByTag=function(node,tag,_21d){
tag=tag.toLowerCase();
return dojo.dom.getAncestors(node,function(el){
return ((el.tagName)&&(el.tagName.toLowerCase()==tag));
},_21d);
};
dojo.dom.getFirstAncestorByTag=function(node,tag){
return dojo.dom.getAncestorsByTag(node,tag,true);
};
dojo.dom.isDescendantOf=function(node,_222,_223){
if(_223&&node){
node=node.parentNode;
}
while(node){
if(node==_222){
return true;
}
node=node.parentNode;
}
return false;
};
dojo.dom.innerXML=function(node){
if(node.innerXML){
return node.innerXML;
}else{
if(node.xml){
return node.xml;
}else{
if(typeof XMLSerializer!="undefined"){
return (new XMLSerializer()).serializeToString(node);
}
}
}
};
dojo.dom.createDocument=function(){
var doc=null;
var _226=dojo.doc();
if(!dj_undef("ActiveXObject")){
var _227=["MSXML2","Microsoft","MSXML","MSXML3"];
for(var i=0;i<_227.length;i++){
try{
doc=new ActiveXObject(_227[i]+".XMLDOM");
}
catch(e){
}
if(doc){
break;
}
}
}else{
if((_226.implementation)&&(_226.implementation.createDocument)){
doc=_226.implementation.createDocument("","",null);
}
}
return doc;
};
dojo.dom.createDocumentFromText=function(str,_22a){
if(!_22a){
_22a="text/xml";
}
if(!dj_undef("DOMParser")){
var _22b=new DOMParser();
return _22b.parseFromString(str,_22a);
}else{
if(!dj_undef("ActiveXObject")){
var _22c=dojo.dom.createDocument();
if(_22c){
_22c.async=false;
_22c.loadXML(str);
return _22c;
}else{
dojo.debug("toXml didn't work?");
}
}else{
var _22d=dojo.doc();
if(_22d.createElement){
var tmp=_22d.createElement("xml");
tmp.innerHTML=str;
if(_22d.implementation&&_22d.implementation.createDocument){
var _22f=_22d.implementation.createDocument("foo","",null);
for(var i=0;i<tmp.childNodes.length;i++){
_22f.importNode(tmp.childNodes.item(i),true);
}
return _22f;
}
return ((tmp.document)&&(tmp.document.firstChild?tmp.document.firstChild:tmp));
}
}
}
return null;
};
dojo.dom.prependChild=function(node,_232){
if(_232.firstChild){
_232.insertBefore(node,_232.firstChild);
}else{
_232.appendChild(node);
}
return true;
};
dojo.dom.insertBefore=function(node,ref,_235){
if((_235!=true)&&(node===ref||node.nextSibling===ref)){
return false;
}
var _236=ref.parentNode;
_236.insertBefore(node,ref);
return true;
};
dojo.dom.insertAfter=function(node,ref,_239){
var pn=ref.parentNode;
if(ref==pn.lastChild){
if((_239!=true)&&(node===ref)){
return false;
}
pn.appendChild(node);
}else{
return this.insertBefore(node,ref.nextSibling,_239);
}
return true;
};
dojo.dom.insertAtPosition=function(node,ref,_23d){
if((!node)||(!ref)||(!_23d)){
return false;
}
switch(_23d.toLowerCase()){
case "before":
return dojo.dom.insertBefore(node,ref);
case "after":
return dojo.dom.insertAfter(node,ref);
case "first":
if(ref.firstChild){
return dojo.dom.insertBefore(node,ref.firstChild);
}else{
ref.appendChild(node);
return true;
}
break;
default:
ref.appendChild(node);
return true;
}
};
dojo.dom.insertAtIndex=function(node,_23f,_240){
var _241=_23f.childNodes;
if(!_241.length||_241.length==_240){
_23f.appendChild(node);
return true;
}
if(_240==0){
return dojo.dom.prependChild(node,_23f);
}
return dojo.dom.insertAfter(node,_241[_240-1]);
};
dojo.dom.textContent=function(node,text){
if(arguments.length>1){
var _244=dojo.doc();
dojo.dom.replaceChildren(node,_244.createTextNode(text));
return text;
}else{
if(node.textContent!=undefined){
return node.textContent;
}
var _245="";
if(node==null){
return _245;
}
for(var i=0;i<node.childNodes.length;i++){
switch(node.childNodes[i].nodeType){
case 1:
case 5:
_245+=dojo.dom.textContent(node.childNodes[i]);
break;
case 3:
case 2:
case 4:
_245+=node.childNodes[i].nodeValue;
break;
default:
break;
}
}
return _245;
}
};
dojo.dom.hasParent=function(node){
return Boolean(node&&node.parentNode&&dojo.dom.isNode(node.parentNode));
};
dojo.dom.isTag=function(node){
if(node&&node.tagName){
for(var i=1;i<arguments.length;i++){
if(node.tagName==String(arguments[i])){
return String(arguments[i]);
}
}
}
return "";
};
dojo.dom.setAttributeNS=function(elem,_24b,_24c,_24d){
if(elem==null||((elem==undefined)&&(typeof elem=="undefined"))){
dojo.raise("No element given to dojo.dom.setAttributeNS");
}
if(!((elem.setAttributeNS==undefined)&&(typeof elem.setAttributeNS=="undefined"))){
elem.setAttributeNS(_24b,_24c,_24d);
}else{
var _24e=elem.ownerDocument;
var _24f=_24e.createNode(2,_24c,_24b);
_24f.nodeValue=_24d;
elem.setAttributeNode(_24f);
}
};
dojo.provide("dojo.undo.browser");
try{
if((!djConfig["preventBackButtonFix"])&&(!dojo.hostenv.post_load_)){
document.write("<iframe style='border: 0px; width: 1px; height: 1px; position: absolute; bottom: 0px; right: 0px; visibility: visible;' name='djhistory' id='djhistory' src='"+(dojo.hostenv.getBaseScriptUri()+"iframe_history.html")+"'></iframe>");
}
}
catch(e){
}
if(dojo.render.html.opera){
dojo.debug("Opera is not supported with dojo.undo.browser, so back/forward detection will not work.");
}
dojo.undo.browser={initialHref:(!dj_undef("window"))?window.location.href:"",initialHash:(!dj_undef("window"))?window.location.hash:"",moveForward:false,historyStack:[],forwardStack:[],historyIframe:null,bookmarkAnchor:null,locationTimer:null,setInitialState:function(args){
this.initialState=this._createState(this.initialHref,args,this.initialHash);
},addToHistory:function(args){
this.forwardStack=[];
var hash=null;
var url=null;
if(!this.historyIframe){
this.historyIframe=window.frames["djhistory"];
}
if(!this.bookmarkAnchor){
this.bookmarkAnchor=document.createElement("a");
dojo.body().appendChild(this.bookmarkAnchor);
this.bookmarkAnchor.style.display="none";
}
if(args["changeUrl"]){
hash="#"+((args["changeUrl"]!==true)?args["changeUrl"]:(new Date()).getTime());
if(this.historyStack.length==0&&this.initialState.urlHash==hash){
this.initialState=this._createState(url,args,hash);
return;
}else{
if(this.historyStack.length>0&&this.historyStack[this.historyStack.length-1].urlHash==hash){
this.historyStack[this.historyStack.length-1]=this._createState(url,args,hash);
return;
}
}
this.changingUrl=true;
setTimeout("window.location.href = '"+hash+"'; dojo.undo.browser.changingUrl = false;",1);
this.bookmarkAnchor.href=hash;
if(dojo.render.html.ie){
url=this._loadIframeHistory();
var _254=args["back"]||args["backButton"]||args["handle"];
var tcb=function(_256){
if(window.location.hash!=""){
setTimeout("window.location.href = '"+hash+"';",1);
}
_254.apply(this,[_256]);
};
if(args["back"]){
args.back=tcb;
}else{
if(args["backButton"]){
args.backButton=tcb;
}else{
if(args["handle"]){
args.handle=tcb;
}
}
}
var _257=args["forward"]||args["forwardButton"]||args["handle"];
var tfw=function(_259){
if(window.location.hash!=""){
window.location.href=hash;
}
if(_257){
_257.apply(this,[_259]);
}
};
if(args["forward"]){
args.forward=tfw;
}else{
if(args["forwardButton"]){
args.forwardButton=tfw;
}else{
if(args["handle"]){
args.handle=tfw;
}
}
}
}else{
if(dojo.render.html.moz){
if(!this.locationTimer){
this.locationTimer=setInterval("dojo.undo.browser.checkLocation();",200);
}
}
}
}else{
url=this._loadIframeHistory();
}
this.historyStack.push(this._createState(url,args,hash));
},checkLocation:function(){
if(!this.changingUrl){
var hsl=this.historyStack.length;
if((window.location.hash==this.initialHash||window.location.href==this.initialHref)&&(hsl==1)){
this.handleBackButton();
return;
}
if(this.forwardStack.length>0){
if(this.forwardStack[this.forwardStack.length-1].urlHash==window.location.hash){
this.handleForwardButton();
return;
}
}
if((hsl>=2)&&(this.historyStack[hsl-2])){
if(this.historyStack[hsl-2].urlHash==window.location.hash){
this.handleBackButton();
return;
}
}
}
},iframeLoaded:function(evt,_25c){
if(!dojo.render.html.opera){
var _25d=this._getUrlQuery(_25c.href);
if(_25d==null){
if(this.historyStack.length==1){
this.handleBackButton();
}
return;
}
if(this.moveForward){
this.moveForward=false;
return;
}
if(this.historyStack.length>=2&&_25d==this._getUrlQuery(this.historyStack[this.historyStack.length-2].url)){
this.handleBackButton();
}else{
if(this.forwardStack.length>0&&_25d==this._getUrlQuery(this.forwardStack[this.forwardStack.length-1].url)){
this.handleForwardButton();
}
}
}
},handleBackButton:function(){
var _25e=this.historyStack.pop();
if(!_25e){
return;
}
var last=this.historyStack[this.historyStack.length-1];
if(!last&&this.historyStack.length==0){
last=this.initialState;
}
if(last){
if(last.kwArgs["back"]){
last.kwArgs["back"]();
}else{
if(last.kwArgs["backButton"]){
last.kwArgs["backButton"]();
}else{
if(last.kwArgs["handle"]){
last.kwArgs.handle("back");
}
}
}
}
this.forwardStack.push(_25e);
},handleForwardButton:function(){
var last=this.forwardStack.pop();
if(!last){
return;
}
if(last.kwArgs["forward"]){
last.kwArgs.forward();
}else{
if(last.kwArgs["forwardButton"]){
last.kwArgs.forwardButton();
}else{
if(last.kwArgs["handle"]){
last.kwArgs.handle("forward");
}
}
}
this.historyStack.push(last);
},_createState:function(url,args,hash){
return {"url":url,"kwArgs":args,"urlHash":hash};
},_getUrlQuery:function(url){
var _265=url.split("?");
if(_265.length<2){
return null;
}else{
return _265[1];
}
},_loadIframeHistory:function(){
var url=dojo.hostenv.getBaseScriptUri()+"iframe_history.html?"+(new Date()).getTime();
this.moveForward=true;
dojo.io.setIFrameSrc(this.historyIframe,url,false);
return url;
}};
dojo.provide("dojo.io.BrowserIO");
if(!dj_undef("window")){
dojo.io.checkChildrenForFile=function(node){
var _268=false;
var _269=node.getElementsByTagName("input");
dojo.lang.forEach(_269,function(_26a){
if(_268){
return;
}
if(_26a.getAttribute("type")=="file"){
_268=true;
}
});
return _268;
};
dojo.io.formHasFile=function(_26b){
return dojo.io.checkChildrenForFile(_26b);
};
dojo.io.updateNode=function(node,_26d){
node=dojo.byId(node);
var args=_26d;
if(dojo.lang.isString(_26d)){
args={url:_26d};
}
args.mimetype="text/html";
args.load=function(t,d,e){
while(node.firstChild){
if(dojo["event"]){
try{
dojo.event.browser.clean(node.firstChild);
}
catch(e){
}
}
node.removeChild(node.firstChild);
}
node.innerHTML=d;
};
dojo.io.bind(args);
};
dojo.io.formFilter=function(node){
var type=(node.type||"").toLowerCase();
return !node.disabled&&node.name&&!dojo.lang.inArray(["file","submit","image","reset","button"],type);
};
dojo.io.encodeForm=function(_274,_275,_276){
if((!_274)||(!_274.tagName)||(!_274.tagName.toLowerCase()=="form")){
dojo.raise("Attempted to encode a non-form element.");
}
if(!_276){
_276=dojo.io.formFilter;
}
var enc=/utf/i.test(_275||"")?encodeURIComponent:dojo.string.encodeAscii;
var _278=[];
for(var i=0;i<_274.elements.length;i++){
var elm=_274.elements[i];
if(!elm||elm.tagName.toLowerCase()=="fieldset"||!_276(elm)){
continue;
}
var name=enc(elm.name);
var type=elm.type.toLowerCase();
if(type=="select-multiple"){
for(var j=0;j<elm.options.length;j++){
if(elm.options[j].selected){
_278.push(name+"="+enc(elm.options[j].value));
}
}
}else{
if(dojo.lang.inArray(["radio","checkbox"],type)){
if(elm.checked){
_278.push(name+"="+enc(elm.value));
}
}else{
_278.push(name+"="+enc(elm.value));
}
}
}
var _27e=_274.getElementsByTagName("input");
for(var i=0;i<_27e.length;i++){
var _27f=_27e[i];
if(_27f.type.toLowerCase()=="image"&&_27f.form==_274&&_276(_27f)){
var name=enc(_27f.name);
_278.push(name+"="+enc(_27f.value));
_278.push(name+".x=0");
_278.push(name+".y=0");
}
}
return _278.join("&")+"&";
};
dojo.io.FormBind=function(args){
this.bindArgs={};
if(args&&args.formNode){
this.init(args);
}else{
if(args){
this.init({formNode:args});
}
}
};
dojo.lang.extend(dojo.io.FormBind,{form:null,bindArgs:null,clickedButton:null,init:function(args){
var form=dojo.byId(args.formNode);
if(!form||!form.tagName||form.tagName.toLowerCase()!="form"){
throw new Error("FormBind: Couldn't apply, invalid form");
}else{
if(this.form==form){
return;
}else{
if(this.form){
throw new Error("FormBind: Already applied to a form");
}
}
}
dojo.lang.mixin(this.bindArgs,args);
this.form=form;
this.connect(form,"onsubmit","submit");
for(var i=0;i<form.elements.length;i++){
var node=form.elements[i];
if(node&&node.type&&dojo.lang.inArray(["submit","button"],node.type.toLowerCase())){
this.connect(node,"onclick","click");
}
}
var _285=form.getElementsByTagName("input");
for(var i=0;i<_285.length;i++){
var _286=_285[i];
if(_286.type.toLowerCase()=="image"&&_286.form==form){
this.connect(_286,"onclick","click");
}
}
},onSubmit:function(form){
return true;
},submit:function(e){
e.preventDefault();
if(this.onSubmit(this.form)){
dojo.io.bind(dojo.lang.mixin(this.bindArgs,{formFilter:dojo.lang.hitch(this,"formFilter")}));
}
},click:function(e){
var node=e.currentTarget;
if(node.disabled){
return;
}
this.clickedButton=node;
},formFilter:function(node){
var type=(node.type||"").toLowerCase();
var _28d=false;
if(node.disabled||!node.name){
_28d=false;
}else{
if(dojo.lang.inArray(["submit","button","image"],type)){
if(!this.clickedButton){
this.clickedButton=node;
}
_28d=node==this.clickedButton;
}else{
_28d=!dojo.lang.inArray(["file","submit","reset","button"],type);
}
}
return _28d;
},connect:function(_28e,_28f,_290){
if(dojo.evalObjPath("dojo.event.connect")){
dojo.event.connect(_28e,_28f,this,_290);
}else{
var fcn=dojo.lang.hitch(this,_290);
_28e[_28f]=function(e){
if(!e){
e=window.event;
}
if(!e.currentTarget){
e.currentTarget=e.srcElement;
}
if(!e.preventDefault){
e.preventDefault=function(){
window.event.returnValue=false;
};
}
fcn(e);
};
}
}});
dojo.io.XMLHTTPTransport=new function(){
var _293=this;
var _294={};
this.useCache=false;
this.preventCache=false;
function getCacheKey(url,_296,_297){
return url+"|"+_296+"|"+_297.toLowerCase();
}
function addToCache(url,_299,_29a,http){
_294[getCacheKey(url,_299,_29a)]=http;
}
function getFromCache(url,_29d,_29e){
return _294[getCacheKey(url,_29d,_29e)];
}
this.clearCache=function(){
_294={};
};
function doLoad(_29f,http,url,_2a2,_2a3){
if(((http.status>=200)&&(http.status<300))||(http.status==304)||(location.protocol=="file:"&&(http.status==0||http.status==undefined))||(location.protocol=="chrome:"&&(http.status==0||http.status==undefined))){
var ret;
if(_29f.method.toLowerCase()=="head"){
var _2a5=http.getAllResponseHeaders();
ret={};
ret.toString=function(){
return _2a5;
};
var _2a6=_2a5.split(/[\r\n]+/g);
for(var i=0;i<_2a6.length;i++){
var pair=_2a6[i].match(/^([^:]+)\s*:\s*(.+)$/i);
if(pair){
ret[pair[1]]=pair[2];
}
}
}else{
if(_29f.mimetype=="text/javascript"){
try{
ret=dj_eval(http.responseText);
}
catch(e){
dojo.debug(e);
dojo.debug(http.responseText);
ret=null;
}
}else{
if(_29f.mimetype=="text/json"||_29f.mimetype=="application/json"){
try{
ret=dj_eval("("+http.responseText+")");
}
catch(e){
dojo.debug(e);
dojo.debug(http.responseText);
ret=false;
}
}else{
if((_29f.mimetype=="application/xml")||(_29f.mimetype=="text/xml")){
ret=http.responseXML;
if(!ret||typeof ret=="string"||!http.getResponseHeader("Content-Type")){
ret=dojo.dom.createDocumentFromText(http.responseText);
}
}else{
ret=http.responseText;
}
}
}
}
if(_2a3){
addToCache(url,_2a2,_29f.method,http);
}
_29f[(typeof _29f.load=="function")?"load":"handle"]("load",ret,http,_29f);
}else{
var _2a9=new dojo.io.Error("XMLHttpTransport Error: "+http.status+" "+http.statusText);
_29f[(typeof _29f.error=="function")?"error":"handle"]("error",_2a9,http,_29f);
}
}
function setHeaders(http,_2ab){
if(_2ab["headers"]){
for(var _2ac in _2ab["headers"]){
if(_2ac.toLowerCase()=="content-type"&&!_2ab["contentType"]){
_2ab["contentType"]=_2ab["headers"][_2ac];
}else{
http.setRequestHeader(_2ac,_2ab["headers"][_2ac]);
}
}
}
}
this.inFlight=[];
this.inFlightTimer=null;
this.startWatchingInFlight=function(){
if(!this.inFlightTimer){
this.inFlightTimer=setTimeout("dojo.io.XMLHTTPTransport.watchInFlight();",10);
}
};
this.watchInFlight=function(){
var now=null;
if(!dojo.hostenv._blockAsync&&!_293._blockAsync){
for(var x=this.inFlight.length-1;x>=0;x--){
try{
var tif=this.inFlight[x];
if(!tif||tif.http._aborted||!tif.http.readyState){
this.inFlight.splice(x,1);
continue;
}
if(4==tif.http.readyState){
this.inFlight.splice(x,1);
doLoad(tif.req,tif.http,tif.url,tif.query,tif.useCache);
}else{
if(tif.startTime){
if(!now){
now=(new Date()).getTime();
}
if(tif.startTime+(tif.req.timeoutSeconds*1000)<now){
if(typeof tif.http.abort=="function"){
tif.http.abort();
}
this.inFlight.splice(x,1);
tif.req[(typeof tif.req.timeout=="function")?"timeout":"handle"]("timeout",null,tif.http,tif.req);
}
}
}
}
catch(e){
try{
var _2b0=new dojo.io.Error("XMLHttpTransport.watchInFlight Error: "+e);
tif.req[(typeof tif.req.error=="function")?"error":"handle"]("error",_2b0,tif.http,tif.req);
}
catch(e2){
dojo.debug("XMLHttpTransport error callback failed: "+e2);
}
}
}
}
clearTimeout(this.inFlightTimer);
if(this.inFlight.length==0){
this.inFlightTimer=null;
return;
}
this.inFlightTimer=setTimeout("dojo.io.XMLHTTPTransport.watchInFlight();",10);
};
var _2b1=dojo.hostenv.getXmlhttpObject()?true:false;
this.canHandle=function(_2b2){
return _2b1&&dojo.lang.inArray(["text/plain","text/html","application/xml","text/xml","text/javascript","text/json","application/json"],(_2b2["mimetype"].toLowerCase()||""))&&!(_2b2["formNode"]&&dojo.io.formHasFile(_2b2["formNode"]));
};
this.multipartBoundary="45309FFF-BD65-4d50-99C9-36986896A96F";
this.bind=function(_2b3){
if(!_2b3["url"]){
if(!_2b3["formNode"]&&(_2b3["backButton"]||_2b3["back"]||_2b3["changeUrl"]||_2b3["watchForURL"])&&(!djConfig.preventBackButtonFix)){
dojo.deprecated("Using dojo.io.XMLHTTPTransport.bind() to add to browser history without doing an IO request","Use dojo.undo.browser.addToHistory() instead.","0.4");
dojo.undo.browser.addToHistory(_2b3);
return true;
}
}
var url=_2b3.url;
var _2b5="";
if(_2b3["formNode"]){
var ta=_2b3.formNode.getAttribute("action");
if((ta)&&(!_2b3["url"])){
url=ta;
}
var tp=_2b3.formNode.getAttribute("method");
if((tp)&&(!_2b3["method"])){
_2b3.method=tp;
}
_2b5+=dojo.io.encodeForm(_2b3.formNode,_2b3.encoding,_2b3["formFilter"]);
}
if(url.indexOf("#")>-1){
dojo.debug("Warning: dojo.io.bind: stripping hash values from url:",url);
url=url.split("#")[0];
}
if(_2b3["file"]){
_2b3.method="post";
}
if(!_2b3["method"]){
_2b3.method="get";
}
if(_2b3.method.toLowerCase()=="get"){
_2b3.multipart=false;
}else{
if(_2b3["file"]){
_2b3.multipart=true;
}else{
if(!_2b3["multipart"]){
_2b3.multipart=false;
}
}
}
if(_2b3["backButton"]||_2b3["back"]||_2b3["changeUrl"]){
dojo.undo.browser.addToHistory(_2b3);
}
var _2b8=_2b3["content"]||{};
if(_2b3.sendTransport){
_2b8["dojo.transport"]="xmlhttp";
}
do{
if(_2b3.postContent){
_2b5=_2b3.postContent;
break;
}
if(_2b8){
_2b5+=dojo.io.argsFromMap(_2b8,_2b3.encoding);
}
if(_2b3.method.toLowerCase()=="get"||!_2b3.multipart){
break;
}
var t=[];
if(_2b5.length){
var q=_2b5.split("&");
for(var i=0;i<q.length;++i){
if(q[i].length){
var p=q[i].split("=");
t.push("--"+this.multipartBoundary,"Content-Disposition: form-data; name=\""+p[0]+"\"","",p[1]);
}
}
}
if(_2b3.file){
if(dojo.lang.isArray(_2b3.file)){
for(var i=0;i<_2b3.file.length;++i){
var o=_2b3.file[i];
t.push("--"+this.multipartBoundary,"Content-Disposition: form-data; name=\""+o.name+"\"; filename=\""+("fileName" in o?o.fileName:o.name)+"\"","Content-Type: "+("contentType" in o?o.contentType:"application/octet-stream"),"",o.content);
}
}else{
var o=_2b3.file;
t.push("--"+this.multipartBoundary,"Content-Disposition: form-data; name=\""+o.name+"\"; filename=\""+("fileName" in o?o.fileName:o.name)+"\"","Content-Type: "+("contentType" in o?o.contentType:"application/octet-stream"),"",o.content);
}
}
if(t.length){
t.push("--"+this.multipartBoundary+"--","");
_2b5=t.join("\r\n");
}
}while(false);
var _2be=_2b3["sync"]?false:true;
var _2bf=_2b3["preventCache"]||(this.preventCache==true&&_2b3["preventCache"]!=false);
var _2c0=_2b3["useCache"]==true||(this.useCache==true&&_2b3["useCache"]!=false);
if(!_2bf&&_2c0){
var _2c1=getFromCache(url,_2b5,_2b3.method);
if(_2c1){
doLoad(_2b3,_2c1,url,_2b5,false);
return;
}
}
var http=dojo.hostenv.getXmlhttpObject(_2b3);
var _2c3=false;
if(_2be){
var _2c4=this.inFlight.push({"req":_2b3,"http":http,"url":url,"query":_2b5,"useCache":_2c0,"startTime":_2b3.timeoutSeconds?(new Date()).getTime():0});
this.startWatchingInFlight();
}else{
_293._blockAsync=true;
}
if(_2b3.method.toLowerCase()=="post"){
if(!_2b3.user){
http.open("POST",url,_2be);
}else{
http.open("POST",url,_2be,_2b3.user,_2b3.password);
}
setHeaders(http,_2b3);
http.setRequestHeader("Content-Type",_2b3.multipart?("multipart/form-data; boundary="+this.multipartBoundary):(_2b3.contentType||"application/x-www-form-urlencoded"));
try{
http.send(_2b5);
}
catch(e){
if(typeof http.abort=="function"){
http.abort();
}
doLoad(_2b3,{status:404},url,_2b5,_2c0);
}
}else{
var _2c5=url;
if(_2b5!=""){
_2c5+=(_2c5.indexOf("?")>-1?"&":"?")+_2b5;
}
if(_2bf){
_2c5+=(dojo.string.endsWithAny(_2c5,"?","&")?"":(_2c5.indexOf("?")>-1?"&":"?"))+"dojo.preventCache="+new Date().valueOf();
}
if(!_2b3.user){
http.open(_2b3.method.toUpperCase(),_2c5,_2be);
}else{
http.open(_2b3.method.toUpperCase(),_2c5,_2be,_2b3.user,_2b3.password);
}
setHeaders(http,_2b3);
try{
http.send(null);
}
catch(e){
if(typeof http.abort=="function"){
http.abort();
}
doLoad(_2b3,{status:404},url,_2b5,_2c0);
}
}
if(!_2be){
doLoad(_2b3,http,url,_2b5,_2c0);
_293._blockAsync=false;
}
_2b3.abort=function(){
try{
http._aborted=true;
}
catch(e){
}
return http.abort();
};
return;
};
dojo.io.transports.addTransport("XMLHTTPTransport");
};
}
dojo.provide("dojo.io.cookie");
dojo.io.cookie.setCookie=function(name,_2c7,days,path,_2ca,_2cb){
var _2cc=-1;
if(typeof days=="number"&&days>=0){
var d=new Date();
d.setTime(d.getTime()+(days*24*60*60*1000));
_2cc=d.toGMTString();
}
_2c7=escape(_2c7);
document.cookie=name+"="+_2c7+";"+(_2cc!=-1?" expires="+_2cc+";":"")+(path?"path="+path:"")+(_2ca?"; domain="+_2ca:"")+(_2cb?"; secure":"");
};
dojo.io.cookie.set=dojo.io.cookie.setCookie;
dojo.io.cookie.getCookie=function(name){
var idx=document.cookie.lastIndexOf(name+"=");
if(idx==-1){
return null;
}
var _2d0=document.cookie.substring(idx+name.length+1);
var end=_2d0.indexOf(";");
if(end==-1){
end=_2d0.length;
}
_2d0=_2d0.substring(0,end);
_2d0=unescape(_2d0);
return _2d0;
};
dojo.io.cookie.get=dojo.io.cookie.getCookie;
dojo.io.cookie.deleteCookie=function(name){
dojo.io.cookie.setCookie(name,"-",0);
};
dojo.io.cookie.setObjectCookie=function(name,obj,days,path,_2d7,_2d8,_2d9){
if(arguments.length==5){
_2d9=_2d7;
_2d7=null;
_2d8=null;
}
var _2da=[],_2db,_2dc="";
if(!_2d9){
_2db=dojo.io.cookie.getObjectCookie(name);
}
if(days>=0){
if(!_2db){
_2db={};
}
for(var prop in obj){
if(prop==null){
delete _2db[prop];
}else{
if(typeof obj[prop]=="string"||typeof obj[prop]=="number"){
_2db[prop]=obj[prop];
}
}
}
prop=null;
for(var prop in _2db){
_2da.push(escape(prop)+"="+escape(_2db[prop]));
}
_2dc=_2da.join("&");
}
dojo.io.cookie.setCookie(name,_2dc,days,path,_2d7,_2d8);
};
dojo.io.cookie.getObjectCookie=function(name){
var _2df=null,_2e0=dojo.io.cookie.getCookie(name);
if(_2e0){
_2df={};
var _2e1=_2e0.split("&");
for(var i=0;i<_2e1.length;i++){
var pair=_2e1[i].split("=");
var _2e4=pair[1];
if(isNaN(_2e4)){
_2e4=unescape(pair[1]);
}
_2df[unescape(pair[0])]=_2e4;
}
}
return _2df;
};
dojo.io.cookie.isSupported=function(){
if(typeof navigator.cookieEnabled!="boolean"){
dojo.io.cookie.setCookie("__TestingYourBrowserForCookieSupport__","CookiesAllowed",90,null);
var _2e5=dojo.io.cookie.getCookie("__TestingYourBrowserForCookieSupport__");
navigator.cookieEnabled=(_2e5=="CookiesAllowed");
if(navigator.cookieEnabled){
this.deleteCookie("__TestingYourBrowserForCookieSupport__");
}
}
return navigator.cookieEnabled;
};
if(!dojo.io.cookies){
dojo.io.cookies=dojo.io.cookie;
}
dojo.provide("dojo.html.common");
dojo.lang.mixin(dojo.html,dojo.dom);
dojo.html.getEventTarget=function(evt){
if(!evt){
evt=dojo.global().event||{};
}
var t=(evt.srcElement?evt.srcElement:(evt.target?evt.target:null));
while((t)&&(t.nodeType!=1)){
t=t.parentNode;
}
return t;
};
dojo.html.getViewport=function(){
var _2e8=dojo.global();
var _2e9=dojo.doc();
var w=0;
var h=0;
if(dojo.render.html.mozilla){
w=_2e9.documentElement.clientWidth;
h=_2e8.innerHeight;
}else{
if(!dojo.render.html.opera&&_2e8.innerWidth){
w=_2e8.innerWidth;
h=_2e8.innerHeight;
}else{
if(!dojo.render.html.opera&&dojo.exists(_2e9,"documentElement.clientWidth")){
var w2=_2e9.documentElement.clientWidth;
if(!w||w2&&w2<w){
w=w2;
}
h=_2e9.documentElement.clientHeight;
}else{
if(dojo.body().clientWidth){
w=dojo.body().clientWidth;
h=dojo.body().clientHeight;
}
}
}
}
return {width:w,height:h};
};
dojo.html.getScroll=function(){
var _2ed=dojo.global();
var _2ee=dojo.doc();
var top=_2ed.pageYOffset||_2ee.documentElement.scrollTop||dojo.body().scrollTop||0;
var left=_2ed.pageXOffset||_2ee.documentElement.scrollLeft||dojo.body().scrollLeft||0;
return {top:top,left:left,offset:{x:left,y:top}};
};
dojo.html.getParentByType=function(node,type){
var _2f3=dojo.doc();
var _2f4=dojo.byId(node);
type=type.toLowerCase();
while((_2f4)&&(_2f4.nodeName.toLowerCase()!=type)){
if(_2f4==(_2f3["body"]||_2f3["documentElement"])){
return null;
}
_2f4=_2f4.parentNode;
}
return _2f4;
};
dojo.html.getAttribute=function(node,attr){
node=dojo.byId(node);
if((!node)||(!node.getAttribute)){
return null;
}
var ta=typeof attr=="string"?attr:new String(attr);
var v=node.getAttribute(ta.toUpperCase());
if((v)&&(typeof v=="string")&&(v!="")){
return v;
}
if(v&&v.value){
return v.value;
}
if((node.getAttributeNode)&&(node.getAttributeNode(ta))){
return (node.getAttributeNode(ta)).value;
}else{
if(node.getAttribute(ta)){
return node.getAttribute(ta);
}else{
if(node.getAttribute(ta.toLowerCase())){
return node.getAttribute(ta.toLowerCase());
}
}
}
return null;
};
dojo.html.hasAttribute=function(node,attr){
return dojo.html.getAttribute(dojo.byId(node),attr)?true:false;
};
dojo.html.getCursorPosition=function(e){
e=e||dojo.global().event;
var _2fc={x:0,y:0};
if(e.pageX||e.pageY){
_2fc.x=e.pageX;
_2fc.y=e.pageY;
}else{
var de=dojo.doc().documentElement;
var db=dojo.body();
_2fc.x=e.clientX+((de||db)["scrollLeft"])-((de||db)["clientLeft"]);
_2fc.y=e.clientY+((de||db)["scrollTop"])-((de||db)["clientTop"]);
}
return _2fc;
};
dojo.html.isTag=function(node){
node=dojo.byId(node);
if(node&&node.tagName){
for(var i=1;i<arguments.length;i++){
if(node.tagName.toLowerCase()==String(arguments[i]).toLowerCase()){
return String(arguments[i]).toLowerCase();
}
}
}
return "";
};
if(dojo.render.html.ie&&!dojo.render.html.ie70){
if(window.location.href.substr(0,6).toLowerCase()!="https:"){
(function(){
var _301=dojo.doc().createElement("script");
_301.src="javascript:'dojo.html.createExternalElement=function(doc, tag){ return doc.createElement(tag); }'";
dojo.doc().getElementsByTagName("head")[0].appendChild(_301);
})();
}
}else{
dojo.html.createExternalElement=function(doc,tag){
return doc.createElement(tag);
};
}
dojo.provide("dojo.uri.Uri");
dojo.uri=new function(){
this.dojoUri=function(uri){
return new dojo.uri.Uri(dojo.hostenv.getBaseScriptUri(),uri);
};
this.moduleUri=function(_305,uri){
var loc=dojo.hostenv.getModuleSymbols(_305).join("/");
if(!loc){
return null;
}
if(loc.lastIndexOf("/")!=loc.length-1){
loc+="/";
}
return new dojo.uri.Uri(dojo.hostenv.getBaseScriptUri()+loc,uri);
};
this.Uri=function(){
var uri=arguments[0];
for(var i=1;i<arguments.length;i++){
if(!arguments[i]){
continue;
}
var _30a=new dojo.uri.Uri(arguments[i].toString());
var _30b=new dojo.uri.Uri(uri.toString());
if((_30a.path=="")&&(_30a.scheme==null)&&(_30a.authority==null)&&(_30a.query==null)){
if(_30a.fragment!=null){
_30b.fragment=_30a.fragment;
}
_30a=_30b;
}else{
if(_30a.scheme==null){
_30a.scheme=_30b.scheme;
if(_30a.authority==null){
_30a.authority=_30b.authority;
if(_30a.path.charAt(0)!="/"){
var path=_30b.path.substring(0,_30b.path.lastIndexOf("/")+1)+_30a.path;
var segs=path.split("/");
for(var j=0;j<segs.length;j++){
if(segs[j]=="."){
if(j==segs.length-1){
segs[j]="";
}else{
segs.splice(j,1);
j--;
}
}else{
if(j>0&&!(j==1&&segs[0]=="")&&segs[j]==".."&&segs[j-1]!=".."){
if(j==segs.length-1){
segs.splice(j,1);
segs[j-1]="";
}else{
segs.splice(j-1,2);
j-=2;
}
}
}
}
_30a.path=segs.join("/");
}
}
}
}
uri="";
if(_30a.scheme!=null){
uri+=_30a.scheme+":";
}
if(_30a.authority!=null){
uri+="//"+_30a.authority;
}
uri+=_30a.path;
if(_30a.query!=null){
uri+="?"+_30a.query;
}
if(_30a.fragment!=null){
uri+="#"+_30a.fragment;
}
}
this.uri=uri.toString();
var _30f="^(([^:/?#]+):)?(//([^/?#]*))?([^?#]*)(\\?([^#]*))?(#(.*))?$";
var r=this.uri.match(new RegExp(_30f));
this.scheme=r[2]||(r[1]?"":null);
this.authority=r[4]||(r[3]?"":null);
this.path=r[5];
this.query=r[7]||(r[6]?"":null);
this.fragment=r[9]||(r[8]?"":null);
if(this.authority!=null){
_30f="^((([^:]+:)?([^@]+))@)?([^:]*)(:([0-9]+))?$";
r=this.authority.match(new RegExp(_30f));
this.user=r[3]||null;
this.password=r[4]||null;
this.host=r[5];
this.port=r[7]||null;
}
this.toString=function(){
return this.uri;
};
};
};
dojo.provide("dojo.html.style");
dojo.html.getClass=function(node){
node=dojo.byId(node);
if(!node){
return "";
}
var cs="";
if(node.className){
cs=node.className;
}else{
if(dojo.html.hasAttribute(node,"class")){
cs=dojo.html.getAttribute(node,"class");
}
}
return cs.replace(/^\s+|\s+$/g,"");
};
dojo.html.getClasses=function(node){
var c=dojo.html.getClass(node);
return (c=="")?[]:c.split(/\s+/g);
};
dojo.html.hasClass=function(node,_316){
return (new RegExp("(^|\\s+)"+_316+"(\\s+|$)")).test(dojo.html.getClass(node));
};
dojo.html.prependClass=function(node,_318){
_318+=" "+dojo.html.getClass(node);
return dojo.html.setClass(node,_318);
};
dojo.html.addClass=function(node,_31a){
if(dojo.html.hasClass(node,_31a)){
return false;
}
_31a=(dojo.html.getClass(node)+" "+_31a).replace(/^\s+|\s+$/g,"");
return dojo.html.setClass(node,_31a);
};
dojo.html.setClass=function(node,_31c){
node=dojo.byId(node);
var cs=new String(_31c);
try{
if(typeof node.className=="string"){
node.className=cs;
}else{
if(node.setAttribute){
node.setAttribute("class",_31c);
node.className=cs;
}else{
return false;
}
}
}
catch(e){
dojo.debug("dojo.html.setClass() failed",e);
}
return true;
};
dojo.html.removeClass=function(node,_31f,_320){
try{
if(!_320){
var _321=dojo.html.getClass(node).replace(new RegExp("(^|\\s+)"+_31f+"(\\s+|$)"),"$1$2");
}else{
var _321=dojo.html.getClass(node).replace(_31f,"");
}
dojo.html.setClass(node,_321);
}
catch(e){
dojo.debug("dojo.html.removeClass() failed",e);
}
return true;
};
dojo.html.replaceClass=function(node,_323,_324){
dojo.html.removeClass(node,_324);
dojo.html.addClass(node,_323);
};
dojo.html.classMatchType={ContainsAll:0,ContainsAny:1,IsOnly:2};
dojo.html.getElementsByClass=function(_325,_326,_327,_328,_329){
_329=false;
var _32a=dojo.doc();
_326=dojo.byId(_326)||_32a;
var _32b=_325.split(/\s+/g);
var _32c=[];
if(_328!=1&&_328!=2){
_328=0;
}
var _32d=new RegExp("(\\s|^)(("+_32b.join(")|(")+"))(\\s|$)");
var _32e=_32b.join(" ").length;
var _32f=[];
if(!_329&&_32a.evaluate){
var _330=".//"+(_327||"*")+"[contains(";
if(_328!=dojo.html.classMatchType.ContainsAny){
_330+="concat(' ',@class,' '), ' "+_32b.join(" ') and contains(concat(' ',@class,' '), ' ")+" ')";
if(_328==2){
_330+=" and string-length(@class)="+_32e+"]";
}else{
_330+="]";
}
}else{
_330+="concat(' ',@class,' '), ' "+_32b.join(" ') or contains(concat(' ',@class,' '), ' ")+" ')]";
}
var _331=_32a.evaluate(_330,_326,null,XPathResult.ANY_TYPE,null);
var _332=_331.iterateNext();
while(_332){
try{
_32f.push(_332);
_332=_331.iterateNext();
}
catch(e){
break;
}
}
return _32f;
}else{
if(!_327){
_327="*";
}
_32f=_326.getElementsByTagName(_327);
var node,i=0;
outer:
while(node=_32f[i++]){
var _335=dojo.html.getClasses(node);
if(_335.length==0){
continue outer;
}
var _336=0;
for(var j=0;j<_335.length;j++){
if(_32d.test(_335[j])){
if(_328==dojo.html.classMatchType.ContainsAny){
_32c.push(node);
continue outer;
}else{
_336++;
}
}else{
if(_328==dojo.html.classMatchType.IsOnly){
continue outer;
}
}
}
if(_336==_32b.length){
if((_328==dojo.html.classMatchType.IsOnly)&&(_336==_335.length)){
_32c.push(node);
}else{
if(_328==dojo.html.classMatchType.ContainsAll){
_32c.push(node);
}
}
}
}
return _32c;
}
};
dojo.html.getElementsByClassName=dojo.html.getElementsByClass;
dojo.html.toCamelCase=function(_338){
var arr=_338.split("-"),cc=arr[0];
for(var i=1;i<arr.length;i++){
cc+=arr[i].charAt(0).toUpperCase()+arr[i].substring(1);
}
return cc;
};
dojo.html.toSelectorCase=function(_33c){
return _33c.replace(/([A-Z])/g,"-$1").toLowerCase();
};
dojo.html.getComputedStyle=function(node,_33e,_33f){
node=dojo.byId(node);
var _33e=dojo.html.toSelectorCase(_33e);
var _340=dojo.html.toCamelCase(_33e);
if(!node||!node.style){
return _33f;
}else{
if(document.defaultView&&dojo.html.isDescendantOf(node,node.ownerDocument)){
try{
var cs=document.defaultView.getComputedStyle(node,"");
if(cs){
return cs.getPropertyValue(_33e);
}
}
catch(e){
if(node.style.getPropertyValue){
return node.style.getPropertyValue(_33e);
}else{
return _33f;
}
}
}else{
if(node.currentStyle){
return node.currentStyle[_340];
}
}
}
if(node.style.getPropertyValue){
return node.style.getPropertyValue(_33e);
}else{
return _33f;
}
};
dojo.html.getStyleProperty=function(node,_343){
node=dojo.byId(node);
return (node&&node.style?node.style[dojo.html.toCamelCase(_343)]:undefined);
};
dojo.html.getStyle=function(node,_345){
var _346=dojo.html.getStyleProperty(node,_345);
return (_346?_346:dojo.html.getComputedStyle(node,_345));
};
dojo.html.setStyle=function(node,_348,_349){
node=dojo.byId(node);
if(node&&node.style){
var _34a=dojo.html.toCamelCase(_348);
node.style[_34a]=_349;
}
};
dojo.html.setStyleText=function(_34b,text){
try{
_34b.style.cssText=text;
}
catch(e){
_34b.setAttribute("style",text);
}
};
dojo.html.copyStyle=function(_34d,_34e){
if(!_34e.style.cssText){
_34d.setAttribute("style",_34e.getAttribute("style"));
}else{
_34d.style.cssText=_34e.style.cssText;
}
dojo.html.addClass(_34d,dojo.html.getClass(_34e));
};
dojo.html.getUnitValue=function(node,_350,_351){
var s=dojo.html.getComputedStyle(node,_350);
if((!s)||((s=="auto")&&(_351))){
return {value:0,units:"px"};
}
var _353=s.match(/(\-?[\d.]+)([a-z%]*)/i);
if(!_353){
return dojo.html.getUnitValue.bad;
}
return {value:Number(_353[1]),units:_353[2].toLowerCase()};
};
dojo.html.getUnitValue.bad={value:NaN,units:""};
dojo.html.getPixelValue=function(node,_355,_356){
var _357=dojo.html.getUnitValue(node,_355,_356);
if(isNaN(_357.value)){
return 0;
}
if((_357.value)&&(_357.units!="px")){
return NaN;
}
return _357.value;
};
dojo.html.setPositivePixelValue=function(node,_359,_35a){
if(isNaN(_35a)){
return false;
}
node.style[_359]=Math.max(0,_35a)+"px";
return true;
};
dojo.html.styleSheet=null;
dojo.html.insertCssRule=function(_35b,_35c,_35d){
if(!dojo.html.styleSheet){
if(document.createStyleSheet){
dojo.html.styleSheet=document.createStyleSheet();
}else{
if(document.styleSheets[0]){
dojo.html.styleSheet=document.styleSheets[0];
}else{
return null;
}
}
}
if(arguments.length<3){
if(dojo.html.styleSheet.cssRules){
_35d=dojo.html.styleSheet.cssRules.length;
}else{
if(dojo.html.styleSheet.rules){
_35d=dojo.html.styleSheet.rules.length;
}else{
return null;
}
}
}
if(dojo.html.styleSheet.insertRule){
var rule=_35b+" { "+_35c+" }";
return dojo.html.styleSheet.insertRule(rule,_35d);
}else{
if(dojo.html.styleSheet.addRule){
return dojo.html.styleSheet.addRule(_35b,_35c,_35d);
}else{
return null;
}
}
};
dojo.html.removeCssRule=function(_35f){
if(!dojo.html.styleSheet){
dojo.debug("no stylesheet defined for removing rules");
return false;
}
if(dojo.render.html.ie){
if(!_35f){
_35f=dojo.html.styleSheet.rules.length;
dojo.html.styleSheet.removeRule(_35f);
}
}else{
if(document.styleSheets[0]){
if(!_35f){
_35f=dojo.html.styleSheet.cssRules.length;
}
dojo.html.styleSheet.deleteRule(_35f);
}
}
return true;
};
dojo.html._insertedCssFiles=[];
dojo.html.insertCssFile=function(URI,doc,_362,_363){
if(!URI){
return;
}
if(!doc){
doc=document;
}
var _364=dojo.hostenv.getText(URI,false,_363);
if(_364===null){
return;
}
_364=dojo.html.fixPathsInCssText(_364,URI);
if(_362){
var idx=-1,node,ent=dojo.html._insertedCssFiles;
for(var i=0;i<ent.length;i++){
if((ent[i].doc==doc)&&(ent[i].cssText==_364)){
idx=i;
node=ent[i].nodeRef;
break;
}
}
if(node){
var _369=doc.getElementsByTagName("style");
for(var i=0;i<_369.length;i++){
if(_369[i]==node){
return;
}
}
dojo.html._insertedCssFiles.shift(idx,1);
}
}
var _36a=dojo.html.insertCssText(_364,doc);
dojo.html._insertedCssFiles.push({"doc":doc,"cssText":_364,"nodeRef":_36a});
if(_36a&&djConfig.isDebug){
_36a.setAttribute("dbgHref",URI);
}
return _36a;
};
dojo.html.insertCssText=function(_36b,doc,URI){
if(!_36b){
return;
}
if(!doc){
doc=document;
}
if(URI){
_36b=dojo.html.fixPathsInCssText(_36b,URI);
}
var _36e=doc.createElement("style");
_36e.setAttribute("type","text/css");
var head=doc.getElementsByTagName("head")[0];
if(!head){
dojo.debug("No head tag in document, aborting styles");
return;
}else{
head.appendChild(_36e);
}
if(_36e.styleSheet){
var _370=function(){
try{
_36e.styleSheet.cssText=_36b;
}
catch(e){
dojo.debug(e);
}
};
if(_36e.styleSheet.disabled){
setTimeout(_370,10);
}else{
_370();
}
}else{
var _371=doc.createTextNode(_36b);
_36e.appendChild(_371);
}
return _36e;
};
dojo.html.fixPathsInCssText=function(_372,URI){
if(!_372||!URI){
return;
}
var _374,str="",url="",_377="[\\t\\s\\w\\(\\)\\/\\.\\\\'\"-:#=&?~]+";
var _378=new RegExp("url\\(\\s*("+_377+")\\s*\\)");
var _379=/(file|https?|ftps?):\/\//;
regexTrim=new RegExp("^[\\s]*(['\"]?)("+_377+")\\1[\\s]*?$");
if(dojo.render.html.ie55||dojo.render.html.ie60){
var _37a=new RegExp("AlphaImageLoader\\((.*)src=['\"]("+_377+")['\"]");
while(_374=_37a.exec(_372)){
url=_374[2].replace(regexTrim,"$2");
if(!_379.exec(url)){
url=(new dojo.uri.Uri(URI,url).toString());
}
str+=_372.substring(0,_374.index)+"AlphaImageLoader("+_374[1]+"src='"+url+"'";
_372=_372.substr(_374.index+_374[0].length);
}
_372=str+_372;
str="";
}
while(_374=_378.exec(_372)){
url=_374[1].replace(regexTrim,"$2");
if(!_379.exec(url)){
url=(new dojo.uri.Uri(URI,url).toString());
}
str+=_372.substring(0,_374.index)+"url("+url+")";
_372=_372.substr(_374.index+_374[0].length);
}
return str+_372;
};
dojo.html.setActiveStyleSheet=function(_37b){
var i=0,a,els=dojo.doc().getElementsByTagName("link");
while(a=els[i++]){
if(a.getAttribute("rel").indexOf("style")!=-1&&a.getAttribute("title")){
a.disabled=true;
if(a.getAttribute("title")==_37b){
a.disabled=false;
}
}
}
};
dojo.html.getActiveStyleSheet=function(){
var i=0,a,els=dojo.doc().getElementsByTagName("link");
while(a=els[i++]){
if(a.getAttribute("rel").indexOf("style")!=-1&&a.getAttribute("title")&&!a.disabled){
return a.getAttribute("title");
}
}
return null;
};
dojo.html.getPreferredStyleSheet=function(){
var i=0,a,els=dojo.doc().getElementsByTagName("link");
while(a=els[i++]){
if(a.getAttribute("rel").indexOf("style")!=-1&&a.getAttribute("rel").indexOf("alt")==-1&&a.getAttribute("title")){
return a.getAttribute("title");
}
}
return null;
};
dojo.html.applyBrowserClass=function(node){
var drh=dojo.render.html;
var _387={dj_ie:drh.ie,dj_ie55:drh.ie55,dj_ie6:drh.ie60,dj_ie7:drh.ie70,dj_iequirks:drh.ie&&drh.quirks,dj_opera:drh.opera,dj_opera8:drh.opera&&(Math.floor(dojo.render.version)==8),dj_opera9:drh.opera&&(Math.floor(dojo.render.version)==9),dj_khtml:drh.khtml,dj_safari:drh.safari,dj_gecko:drh.mozilla};
for(var p in _387){
if(_387[p]){
dojo.html.addClass(node,p);
}
}
};
dojo.provide("dojo.html.*");
dojo.provide("dojo.xml.Parse");
dojo.xml.Parse=function(){
var isIE=((dojo.render.html.capable)&&(dojo.render.html.ie));
function getTagName(node){
try{
return node.tagName.toLowerCase();
}
catch(e){
return "";
}
}
function getDojoTagName(node){
var _38c=getTagName(node);
if(!_38c){
return "";
}
if((dojo.widget)&&(dojo.widget.tags[_38c])){
return _38c;
}
var p=_38c.indexOf(":");
if(p>=0){
return _38c;
}
if(_38c.substr(0,5)=="dojo:"){
return _38c;
}
if(dojo.render.html.capable&&dojo.render.html.ie&&node.scopeName!="HTML"){
return node.scopeName.toLowerCase()+":"+_38c;
}
if(_38c.substr(0,4)=="dojo"){
return "dojo:"+_38c.substring(4);
}
var djt=node.getAttribute("dojoType")||node.getAttribute("dojotype");
if(djt){
if(djt.indexOf(":")<0){
djt="dojo:"+djt;
}
return djt.toLowerCase();
}
djt=node.getAttributeNS&&node.getAttributeNS(dojo.dom.dojoml,"type");
if(djt){
return "dojo:"+djt.toLowerCase();
}
try{
djt=node.getAttribute("dojo:type");
}
catch(e){
}
if(djt){
return "dojo:"+djt.toLowerCase();
}
if((dj_global["djConfig"])&&(!djConfig["ignoreClassNames"])){
var _38f=node.className||node.getAttribute("class");
if((_38f)&&(_38f.indexOf)&&(_38f.indexOf("dojo-")!=-1)){
var _390=_38f.split(" ");
for(var x=0,c=_390.length;x<c;x++){
if(_390[x].slice(0,5)=="dojo-"){
return "dojo:"+_390[x].substr(5).toLowerCase();
}
}
}
}
return "";
}
this.parseElement=function(node,_394,_395,_396){
var _397=getTagName(node);
if(isIE&&_397.indexOf("/")==0){
return null;
}
try{
if(node.getAttribute("parseWidgets").toLowerCase()=="false"){
return {};
}
}
catch(e){
}
var _398=true;
if(_395){
var _399=getDojoTagName(node);
_397=_399||_397;
_398=Boolean(_399);
}
var _39a={};
_39a[_397]=[];
var pos=_397.indexOf(":");
if(pos>0){
var ns=_397.substring(0,pos);
_39a["ns"]=ns;
if((dojo.ns)&&(!dojo.ns.allow(ns))){
_398=false;
}
}
if(_398){
var _39d=this.parseAttributes(node);
for(var attr in _39d){
if((!_39a[_397][attr])||(typeof _39a[_397][attr]!="array")){
_39a[_397][attr]=[];
}
_39a[_397][attr].push(_39d[attr]);
}
_39a[_397].nodeRef=node;
_39a.tagName=_397;
_39a.index=_396||0;
}
var _39f=0;
for(var i=0;i<node.childNodes.length;i++){
var tcn=node.childNodes.item(i);
switch(tcn.nodeType){
case dojo.dom.ELEMENT_NODE:
_39f++;
var ctn=getDojoTagName(tcn)||getTagName(tcn);
if(!_39a[ctn]){
_39a[ctn]=[];
}
_39a[ctn].push(this.parseElement(tcn,true,_395,_39f));
if((tcn.childNodes.length==1)&&(tcn.childNodes.item(0).nodeType==dojo.dom.TEXT_NODE)){
_39a[ctn][_39a[ctn].length-1].value=tcn.childNodes.item(0).nodeValue;
}
break;
case dojo.dom.TEXT_NODE:
if(node.childNodes.length==1){
_39a[_397].push({value:node.childNodes.item(0).nodeValue});
}
break;
default:
break;
}
}
return _39a;
};
this.parseAttributes=function(node){
var _3a4={};
var atts=node.attributes;
var _3a6,i=0;
while((_3a6=atts[i++])){
if(isIE){
if(!_3a6){
continue;
}
if((typeof _3a6=="object")&&(typeof _3a6.nodeValue=="undefined")||(_3a6.nodeValue==null)||(_3a6.nodeValue=="")){
continue;
}
}
var nn=_3a6.nodeName.split(":");
nn=(nn.length==2)?nn[1]:_3a6.nodeName;
_3a4[nn]={value:_3a6.nodeValue};
}
return _3a4;
};
};
dojo.provide("dojo.lang.declare");
dojo.lang.declare=function(_3a9,_3aa,init,_3ac){
if((dojo.lang.isFunction(_3ac))||((!_3ac)&&(!dojo.lang.isFunction(init)))){
var temp=_3ac;
_3ac=init;
init=temp;
}
var _3ae=[];
if(dojo.lang.isArray(_3aa)){
_3ae=_3aa;
_3aa=_3ae.shift();
}
if(!init){
init=dojo.evalObjPath(_3a9,false);
if((init)&&(!dojo.lang.isFunction(init))){
init=null;
}
}
var ctor=dojo.lang.declare._makeConstructor();
var scp=(_3aa?_3aa.prototype:null);
if(scp){
scp.prototyping=true;
ctor.prototype=new _3aa();
scp.prototyping=false;
}
ctor.superclass=scp;
ctor.mixins=_3ae;
for(var i=0,l=_3ae.length;i<l;i++){
dojo.lang.extend(ctor,_3ae[i].prototype);
}
ctor.prototype.initializer=null;
ctor.prototype.declaredClass=_3a9;
if(dojo.lang.isArray(_3ac)){
dojo.lang.extend.apply(dojo.lang,[ctor].concat(_3ac));
}else{
dojo.lang.extend(ctor,(_3ac)||{});
}
dojo.lang.extend(ctor,dojo.lang.declare._common);
ctor.prototype.constructor=ctor;
ctor.prototype.initializer=(ctor.prototype.initializer)||(init)||(function(){
});
dojo.lang.setObjPathValue(_3a9,ctor,null,true);
return ctor;
};
dojo.lang.declare._makeConstructor=function(){
return function(){
var self=this._getPropContext();
var s=self.constructor.superclass;
if((s)&&(s.constructor)){
if(s.constructor==arguments.callee){
this._inherited("constructor",arguments);
}else{
this._contextMethod(s,"constructor",arguments);
}
}
var ms=(self.constructor.mixins)||([]);
for(var i=0,m;(m=ms[i]);i++){
(((m.prototype)&&(m.prototype.initializer))||(m)).apply(this,arguments);
}
if((!this.prototyping)&&(self.initializer)){
self.initializer.apply(this,arguments);
}
};
};
dojo.lang.declare._common={_getPropContext:function(){
return (this.___proto||this);
},_contextMethod:function(_3b8,_3b9,args){
var _3bb,_3bc=this.___proto;
this.___proto=_3b8;
try{
_3bb=_3b8[_3b9].apply(this,(args||[]));
}
catch(e){
throw e;
}
finally{
this.___proto=_3bc;
}
return _3bb;
},_inherited:function(prop,args){
var p=this._getPropContext();
do{
if((!p.constructor)||(!p.constructor.superclass)){
return;
}
p=p.constructor.superclass;
}while(!(prop in p));
return (dojo.lang.isFunction(p[prop])?this._contextMethod(p,prop,args):p[prop]);
},inherited:function(prop,args){
dojo.deprecated("'inherited' method is dangerous, do not up-call! 'inherited' is slated for removal in 0.5; name your super class (or use superclass property) instead.","0.5");
this._inherited(prop,args);
}};
dojo.declare=dojo.lang.declare;
dojo.provide("dojo.ns");
dojo.ns={namespaces:{},failed:{},loading:{},loaded:{},register:function(name,_3c3,_3c4,_3c5){
if(!_3c5||!this.namespaces[name]){
this.namespaces[name]=new dojo.ns.Ns(name,_3c3,_3c4);
}
},allow:function(name){
if(this.failed[name]){
return false;
}
if((djConfig.excludeNamespace)&&(dojo.lang.inArray(djConfig.excludeNamespace,name))){
return false;
}
return ((name==this.dojo)||(!djConfig.includeNamespace)||(dojo.lang.inArray(djConfig.includeNamespace,name)));
},get:function(name){
return this.namespaces[name];
},require:function(name){
var ns=this.namespaces[name];
if((ns)&&(this.loaded[name])){
return ns;
}
if(!this.allow(name)){
return false;
}
if(this.loading[name]){
dojo.debug("dojo.namespace.require: re-entrant request to load namespace \""+name+"\" must fail.");
return false;
}
var req=dojo.require;
this.loading[name]=true;
try{
if(name=="dojo"){
req("dojo.namespaces.dojo");
}else{
if(!dojo.hostenv.moduleHasPrefix(name)){
dojo.registerModulePath(name,"../"+name);
}
req([name,"manifest"].join("."),false,true);
}
if(!this.namespaces[name]){
this.failed[name]=true;
}
}
finally{
this.loading[name]=false;
}
return this.namespaces[name];
}};
dojo.ns.Ns=function(name,_3cc,_3cd){
this.name=name;
this.module=_3cc;
this.resolver=_3cd;
this._loaded=[];
this._failed=[];
};
dojo.ns.Ns.prototype.resolve=function(name,_3cf,_3d0){
if(!this.resolver||djConfig["skipAutoRequire"]){
return false;
}
var _3d1=this.resolver(name,_3cf);
if((_3d1)&&(!this._loaded[_3d1])&&(!this._failed[_3d1])){
var req=dojo.require;
req(_3d1,false,true);
if(dojo.hostenv.findModule(_3d1,false)){
this._loaded[_3d1]=true;
}else{
if(!_3d0){
dojo.raise("dojo.ns.Ns.resolve: module '"+_3d1+"' not found after loading via namespace '"+this.name+"'");
}
this._failed[_3d1]=true;
}
}
return Boolean(this._loaded[_3d1]);
};
dojo.registerNamespace=function(name,_3d4,_3d5){
dojo.ns.register.apply(dojo.ns,arguments);
};
dojo.registerNamespaceResolver=function(name,_3d7){
var n=dojo.ns.namespaces[name];
if(n){
n.resolver=_3d7;
}
};
dojo.registerNamespaceManifest=function(_3d9,path,name,_3dc,_3dd){
dojo.registerModulePath(name,path);
dojo.registerNamespace(name,_3dc,_3dd);
};
dojo.registerNamespace("dojo","dojo.widget");
dojo.provide("dojo.event.common");
dojo.event=new function(){
this._canTimeout=dojo.lang.isFunction(dj_global["setTimeout"])||dojo.lang.isAlien(dj_global["setTimeout"]);
function interpolateArgs(args,_3df){
var dl=dojo.lang;
var ao={srcObj:dj_global,srcFunc:null,adviceObj:dj_global,adviceFunc:null,aroundObj:null,aroundFunc:null,adviceType:(args.length>2)?args[0]:"after",precedence:"last",once:false,delay:null,rate:0,adviceMsg:false,maxCalls:-1};
switch(args.length){
case 0:
return;
case 1:
return;
case 2:
ao.srcFunc=args[0];
ao.adviceFunc=args[1];
break;
case 3:
if((dl.isObject(args[0]))&&(dl.isString(args[1]))&&(dl.isString(args[2]))){
ao.adviceType="after";
ao.srcObj=args[0];
ao.srcFunc=args[1];
ao.adviceFunc=args[2];
}else{
if((dl.isString(args[1]))&&(dl.isString(args[2]))){
ao.srcFunc=args[1];
ao.adviceFunc=args[2];
}else{
if((dl.isObject(args[0]))&&(dl.isString(args[1]))&&(dl.isFunction(args[2]))){
ao.adviceType="after";
ao.srcObj=args[0];
ao.srcFunc=args[1];
var _3e2=dl.nameAnonFunc(args[2],ao.adviceObj,_3df);
ao.adviceFunc=_3e2;
}else{
if((dl.isFunction(args[0]))&&(dl.isObject(args[1]))&&(dl.isString(args[2]))){
ao.adviceType="after";
ao.srcObj=dj_global;
var _3e2=dl.nameAnonFunc(args[0],ao.srcObj,_3df);
ao.srcFunc=_3e2;
ao.adviceObj=args[1];
ao.adviceFunc=args[2];
}
}
}
}
break;
case 4:
if((dl.isObject(args[0]))&&(dl.isObject(args[2]))){
ao.adviceType="after";
ao.srcObj=args[0];
ao.srcFunc=args[1];
ao.adviceObj=args[2];
ao.adviceFunc=args[3];
}else{
if((dl.isString(args[0]))&&(dl.isString(args[1]))&&(dl.isObject(args[2]))){
ao.adviceType=args[0];
ao.srcObj=dj_global;
ao.srcFunc=args[1];
ao.adviceObj=args[2];
ao.adviceFunc=args[3];
}else{
if((dl.isString(args[0]))&&(dl.isFunction(args[1]))&&(dl.isObject(args[2]))){
ao.adviceType=args[0];
ao.srcObj=dj_global;
var _3e2=dl.nameAnonFunc(args[1],dj_global,_3df);
ao.srcFunc=_3e2;
ao.adviceObj=args[2];
ao.adviceFunc=args[3];
}else{
if((dl.isString(args[0]))&&(dl.isObject(args[1]))&&(dl.isString(args[2]))&&(dl.isFunction(args[3]))){
ao.srcObj=args[1];
ao.srcFunc=args[2];
var _3e2=dl.nameAnonFunc(args[3],dj_global,_3df);
ao.adviceObj=dj_global;
ao.adviceFunc=_3e2;
}else{
if(dl.isObject(args[1])){
ao.srcObj=args[1];
ao.srcFunc=args[2];
ao.adviceObj=dj_global;
ao.adviceFunc=args[3];
}else{
if(dl.isObject(args[2])){
ao.srcObj=dj_global;
ao.srcFunc=args[1];
ao.adviceObj=args[2];
ao.adviceFunc=args[3];
}else{
ao.srcObj=ao.adviceObj=ao.aroundObj=dj_global;
ao.srcFunc=args[1];
ao.adviceFunc=args[2];
ao.aroundFunc=args[3];
}
}
}
}
}
}
break;
case 6:
ao.srcObj=args[1];
ao.srcFunc=args[2];
ao.adviceObj=args[3];
ao.adviceFunc=args[4];
ao.aroundFunc=args[5];
ao.aroundObj=dj_global;
break;
default:
ao.srcObj=args[1];
ao.srcFunc=args[2];
ao.adviceObj=args[3];
ao.adviceFunc=args[4];
ao.aroundObj=args[5];
ao.aroundFunc=args[6];
ao.once=args[7];
ao.delay=args[8];
ao.rate=args[9];
ao.adviceMsg=args[10];
ao.maxCalls=(!isNaN(parseInt(args[11])))?args[11]:-1;
break;
}
if(dl.isFunction(ao.aroundFunc)){
var _3e2=dl.nameAnonFunc(ao.aroundFunc,ao.aroundObj,_3df);
ao.aroundFunc=_3e2;
}
if(dl.isFunction(ao.srcFunc)){
ao.srcFunc=dl.getNameInObj(ao.srcObj,ao.srcFunc);
}
if(dl.isFunction(ao.adviceFunc)){
ao.adviceFunc=dl.getNameInObj(ao.adviceObj,ao.adviceFunc);
}
if((ao.aroundObj)&&(dl.isFunction(ao.aroundFunc))){
ao.aroundFunc=dl.getNameInObj(ao.aroundObj,ao.aroundFunc);
}
if(!ao.srcObj){
dojo.raise("bad srcObj for srcFunc: "+ao.srcFunc);
}
if(!ao.adviceObj){
dojo.raise("bad adviceObj for adviceFunc: "+ao.adviceFunc);
}
if(!ao.adviceFunc){
dojo.debug("bad adviceFunc for srcFunc: "+ao.srcFunc);
dojo.debugShallow(ao);
}
return ao;
}
this.connect=function(){
if(arguments.length==1){
var ao=arguments[0];
}else{
var ao=interpolateArgs(arguments,true);
}
if(dojo.lang.isString(ao.srcFunc)&&(ao.srcFunc.toLowerCase()=="onkey")){
if(dojo.render.html.ie){
ao.srcFunc="onkeydown";
this.connect(ao);
}
ao.srcFunc="onkeypress";
}
if(dojo.lang.isArray(ao.srcObj)&&ao.srcObj!=""){
var _3e4={};
for(var x in ao){
_3e4[x]=ao[x];
}
var mjps=[];
dojo.lang.forEach(ao.srcObj,function(src){
if((dojo.render.html.capable)&&(dojo.lang.isString(src))){
src=dojo.byId(src);
}
_3e4.srcObj=src;
mjps.push(dojo.event.connect.call(dojo.event,_3e4));
});
return mjps;
}
var mjp=dojo.event.MethodJoinPoint.getForMethod(ao.srcObj,ao.srcFunc);
if(ao.adviceFunc){
var mjp2=dojo.event.MethodJoinPoint.getForMethod(ao.adviceObj,ao.adviceFunc);
}
mjp.kwAddAdvice(ao);
return mjp;
};
this.log=function(a1,a2){
var _3ec;
if((arguments.length==1)&&(typeof a1=="object")){
_3ec=a1;
}else{
_3ec={srcObj:a1,srcFunc:a2};
}
_3ec.adviceFunc=function(){
var _3ed=[];
for(var x=0;x<arguments.length;x++){
_3ed.push(arguments[x]);
}
dojo.debug("("+_3ec.srcObj+")."+_3ec.srcFunc,":",_3ed.join(", "));
};
this.kwConnect(_3ec);
};
this.connectBefore=function(){
var args=["before"];
for(var i=0;i<arguments.length;i++){
args.push(arguments[i]);
}
return this.connect.apply(this,args);
};
this.connectAround=function(){
var args=["around"];
for(var i=0;i<arguments.length;i++){
args.push(arguments[i]);
}
return this.connect.apply(this,args);
};
this.connectOnce=function(){
var ao=interpolateArgs(arguments,true);
ao.once=true;
return this.connect(ao);
};
this.connectRunOnce=function(){
var ao=interpolateArgs(arguments,true);
ao.maxCalls=1;
return this.connect(ao);
};
this._kwConnectImpl=function(_3f5,_3f6){
var fn=(_3f6)?"disconnect":"connect";
if(typeof _3f5["srcFunc"]=="function"){
_3f5.srcObj=_3f5["srcObj"]||dj_global;
var _3f8=dojo.lang.nameAnonFunc(_3f5.srcFunc,_3f5.srcObj,true);
_3f5.srcFunc=_3f8;
}
if(typeof _3f5["adviceFunc"]=="function"){
_3f5.adviceObj=_3f5["adviceObj"]||dj_global;
var _3f8=dojo.lang.nameAnonFunc(_3f5.adviceFunc,_3f5.adviceObj,true);
_3f5.adviceFunc=_3f8;
}
_3f5.srcObj=_3f5["srcObj"]||dj_global;
_3f5.adviceObj=_3f5["adviceObj"]||_3f5["targetObj"]||dj_global;
_3f5.adviceFunc=_3f5["adviceFunc"]||_3f5["targetFunc"];
return dojo.event[fn](_3f5);
};
this.kwConnect=function(_3f9){
return this._kwConnectImpl(_3f9,false);
};
this.disconnect=function(){
if(arguments.length==1){
var ao=arguments[0];
}else{
var ao=interpolateArgs(arguments,true);
}
if(!ao.adviceFunc){
return;
}
if(dojo.lang.isString(ao.srcFunc)&&(ao.srcFunc.toLowerCase()=="onkey")){
if(dojo.render.html.ie){
ao.srcFunc="onkeydown";
this.disconnect(ao);
}
ao.srcFunc="onkeypress";
}
if(!ao.srcObj[ao.srcFunc]){
return null;
}
var mjp=dojo.event.MethodJoinPoint.getForMethod(ao.srcObj,ao.srcFunc,true);
mjp.removeAdvice(ao.adviceObj,ao.adviceFunc,ao.adviceType,ao.once);
return mjp;
};
this.kwDisconnect=function(_3fc){
return this._kwConnectImpl(_3fc,true);
};
};
dojo.event.MethodInvocation=function(_3fd,obj,args){
this.jp_=_3fd;
this.object=obj;
this.args=[];
for(var x=0;x<args.length;x++){
this.args[x]=args[x];
}
this.around_index=-1;
};
dojo.event.MethodInvocation.prototype.proceed=function(){
this.around_index++;
if(this.around_index>=this.jp_.around.length){
return this.jp_.object[this.jp_.methodname].apply(this.jp_.object,this.args);
}else{
var ti=this.jp_.around[this.around_index];
var mobj=ti[0]||dj_global;
var meth=ti[1];
return mobj[meth].call(mobj,this);
}
};
dojo.event.MethodJoinPoint=function(obj,_405){
this.object=obj||dj_global;
this.methodname=_405;
this.methodfunc=this.object[_405];
};
dojo.event.MethodJoinPoint.getForMethod=function(obj,_407){
if(!obj){
obj=dj_global;
}
var ofn=obj[_407];
if(!ofn){
ofn=obj[_407]=function(){
};
if(!obj[_407]){
dojo.raise("Cannot set do-nothing method on that object "+_407);
}
}else{
if((typeof ofn!="function")&&(!dojo.lang.isFunction(ofn))&&(!dojo.lang.isAlien(ofn))){
return null;
}
}
var _409=_407+"$joinpoint";
var _40a=_407+"$joinpoint$method";
var _40b=obj[_409];
if(!_40b){
var _40c=false;
if(dojo.event["browser"]){
if((obj["attachEvent"])||(obj["nodeType"])||(obj["addEventListener"])){
_40c=true;
dojo.event.browser.addClobberNodeAttrs(obj,[_409,_40a,_407]);
}
}
var _40d=ofn.length;
obj[_40a]=ofn;
_40b=obj[_409]=new dojo.event.MethodJoinPoint(obj,_40a);
if(!_40c){
obj[_407]=function(){
return _40b.run.apply(_40b,arguments);
};
}else{
obj[_407]=function(){
var args=[];
if(!arguments.length){
var evt=null;
try{
if(obj.ownerDocument){
evt=obj.ownerDocument.parentWindow.event;
}else{
if(obj.documentElement){
evt=obj.documentElement.ownerDocument.parentWindow.event;
}else{
if(obj.event){
evt=obj.event;
}else{
evt=window.event;
}
}
}
}
catch(e){
evt=window.event;
}
if(evt){
args.push(dojo.event.browser.fixEvent(evt,this));
}
}else{
for(var x=0;x<arguments.length;x++){
if((x==0)&&(dojo.event.browser.isEvent(arguments[x]))){
args.push(dojo.event.browser.fixEvent(arguments[x],this));
}else{
args.push(arguments[x]);
}
}
}
return _40b.run.apply(_40b,args);
};
}
obj[_407].__preJoinArity=_40d;
}
return _40b;
};
dojo.lang.extend(dojo.event.MethodJoinPoint,{squelch:false,unintercept:function(){
this.object[this.methodname]=this.methodfunc;
this.before=[];
this.after=[];
this.around=[];
},disconnect:dojo.lang.forward("unintercept"),run:function(){
var obj=this.object||dj_global;
var args=arguments;
var _413=[];
for(var x=0;x<args.length;x++){
_413[x]=args[x];
}
var _415=function(marr){
if(!marr){
dojo.debug("Null argument to unrollAdvice()");
return;
}
var _417=marr[0]||dj_global;
var _418=marr[1];
if(!_417[_418]){
dojo.raise("function \""+_418+"\" does not exist on \""+_417+"\"");
}
var _419=marr[2]||dj_global;
var _41a=marr[3];
var msg=marr[6];
var _41c=marr[7];
if(_41c>-1){
if(_41c==0){
return;
}
marr[7]--;
}
var _41d;
var to={args:[],jp_:this,object:obj,proceed:function(){
return _417[_418].apply(_417,to.args);
}};
to.args=_413;
var _41f=parseInt(marr[4]);
var _420=((!isNaN(_41f))&&(marr[4]!==null)&&(typeof marr[4]!="undefined"));
if(marr[5]){
var rate=parseInt(marr[5]);
var cur=new Date();
var _423=false;
if((marr["last"])&&((cur-marr.last)<=rate)){
if(dojo.event._canTimeout){
if(marr["delayTimer"]){
clearTimeout(marr.delayTimer);
}
var tod=parseInt(rate*2);
var mcpy=dojo.lang.shallowCopy(marr);
marr.delayTimer=setTimeout(function(){
mcpy[5]=0;
_415(mcpy);
},tod);
}
return;
}else{
marr.last=cur;
}
}
if(_41a){
_419[_41a].call(_419,to);
}else{
if((_420)&&((dojo.render.html)||(dojo.render.svg))){
dj_global["setTimeout"](function(){
if(msg){
_417[_418].call(_417,to);
}else{
_417[_418].apply(_417,args);
}
},_41f);
}else{
if(msg){
_417[_418].call(_417,to);
}else{
_417[_418].apply(_417,args);
}
}
}
};
var _426=function(){
if(this.squelch){
try{
return _415.apply(this,arguments);
}
catch(e){
dojo.debug(e);
}
}else{
return _415.apply(this,arguments);
}
};
if((this["before"])&&(this.before.length>0)){
dojo.lang.forEach(this.before.concat(new Array()),_426);
}
var _427;
try{
if((this["around"])&&(this.around.length>0)){
var mi=new dojo.event.MethodInvocation(this,obj,args);
_427=mi.proceed();
}else{
if(this.methodfunc){
_427=this.object[this.methodname].apply(this.object,args);
}
}
}
catch(e){
if(!this.squelch){
dojo.debug(e,"when calling",this.methodname,"on",this.object,"with arguments",args);
dojo.raise(e);
}
}
if((this["after"])&&(this.after.length>0)){
dojo.lang.forEach(this.after.concat(new Array()),_426);
}
return (this.methodfunc)?_427:null;
},getArr:function(kind){
var type="after";
if((typeof kind=="string")&&(kind.indexOf("before")!=-1)){
type="before";
}else{
if(kind=="around"){
type="around";
}
}
if(!this[type]){
this[type]=[];
}
return this[type];
},kwAddAdvice:function(args){
this.addAdvice(args["adviceObj"],args["adviceFunc"],args["aroundObj"],args["aroundFunc"],args["adviceType"],args["precedence"],args["once"],args["delay"],args["rate"],args["adviceMsg"],args["maxCalls"]);
},addAdvice:function(_42c,_42d,_42e,_42f,_430,_431,once,_433,rate,_435,_436){
var arr=this.getArr(_430);
if(!arr){
dojo.raise("bad this: "+this);
}
var ao=[_42c,_42d,_42e,_42f,_433,rate,_435,_436];
if(once){
if(this.hasAdvice(_42c,_42d,_430,arr)>=0){
return;
}
}
if(_431=="first"){
arr.unshift(ao);
}else{
arr.push(ao);
}
},hasAdvice:function(_439,_43a,_43b,arr){
if(!arr){
arr=this.getArr(_43b);
}
var ind=-1;
for(var x=0;x<arr.length;x++){
var aao=(typeof _43a=="object")?(new String(_43a)).toString():_43a;
var a1o=(typeof arr[x][1]=="object")?(new String(arr[x][1])).toString():arr[x][1];
if((arr[x][0]==_439)&&(a1o==aao)){
ind=x;
}
}
return ind;
},removeAdvice:function(_441,_442,_443,once){
var arr=this.getArr(_443);
var ind=this.hasAdvice(_441,_442,_443,arr);
if(ind==-1){
return false;
}
while(ind!=-1){
arr.splice(ind,1);
if(once){
break;
}
ind=this.hasAdvice(_441,_442,_443,arr);
}
return true;
}});
dojo.provide("dojo.event.topic");
dojo.event.topic=new function(){
this.topics={};
this.getTopic=function(_447){
if(!this.topics[_447]){
this.topics[_447]=new this.TopicImpl(_447);
}
return this.topics[_447];
};
this.registerPublisher=function(_448,obj,_44a){
var _448=this.getTopic(_448);
_448.registerPublisher(obj,_44a);
};
this.subscribe=function(_44b,obj,_44d){
var _44b=this.getTopic(_44b);
_44b.subscribe(obj,_44d);
};
this.unsubscribe=function(_44e,obj,_450){
var _44e=this.getTopic(_44e);
_44e.unsubscribe(obj,_450);
};
this.destroy=function(_451){
this.getTopic(_451).destroy();
delete this.topics[_451];
};
this.publishApply=function(_452,args){
var _452=this.getTopic(_452);
_452.sendMessage.apply(_452,args);
};
this.publish=function(_454,_455){
var _454=this.getTopic(_454);
var args=[];
for(var x=1;x<arguments.length;x++){
args.push(arguments[x]);
}
_454.sendMessage.apply(_454,args);
};
};
dojo.event.topic.TopicImpl=function(_458){
this.topicName=_458;
this.subscribe=function(_459,_45a){
var tf=_45a||_459;
var to=(!_45a)?dj_global:_459;
return dojo.event.kwConnect({srcObj:this,srcFunc:"sendMessage",adviceObj:to,adviceFunc:tf});
};
this.unsubscribe=function(_45d,_45e){
var tf=(!_45e)?_45d:_45e;
var to=(!_45e)?null:_45d;
return dojo.event.kwDisconnect({srcObj:this,srcFunc:"sendMessage",adviceObj:to,adviceFunc:tf});
};
this._getJoinPoint=function(){
return dojo.event.MethodJoinPoint.getForMethod(this,"sendMessage");
};
this.setSquelch=function(_461){
this._getJoinPoint().squelch=_461;
};
this.destroy=function(){
this._getJoinPoint().disconnect();
};
this.registerPublisher=function(_462,_463){
dojo.event.connect(_462,_463,this,"sendMessage");
};
this.sendMessage=function(_464){
};
};
dojo.provide("dojo.event.browser");
dojo._ie_clobber=new function(){
this.clobberNodes=[];
function nukeProp(node,prop){
try{
node[prop]=null;
}
catch(e){
}
try{
delete node[prop];
}
catch(e){
}
try{
node.removeAttribute(prop);
}
catch(e){
}
}
this.clobber=function(_467){
var na;
var tna;
if(_467){
tna=_467.all||_467.getElementsByTagName("*");
na=[_467];
for(var x=0;x<tna.length;x++){
if(tna[x]["__doClobber__"]){
na.push(tna[x]);
}
}
}else{
try{
window.onload=null;
}
catch(e){
}
na=(this.clobberNodes.length)?this.clobberNodes:document.all;
}
tna=null;
var _46b={};
for(var i=na.length-1;i>=0;i=i-1){
var el=na[i];
try{
if(el&&el["__clobberAttrs__"]){
for(var j=0;j<el.__clobberAttrs__.length;j++){
nukeProp(el,el.__clobberAttrs__[j]);
}
nukeProp(el,"__clobberAttrs__");
nukeProp(el,"__doClobber__");
}
}
catch(e){
}
}
na=null;
};
};
if(dojo.render.html.ie){
dojo.addOnUnload(function(){
dojo._ie_clobber.clobber();
try{
if((dojo["widget"])&&(dojo.widget["manager"])){
dojo.widget.manager.destroyAll();
}
}
catch(e){
}
if(dojo.widget){
for(var name in dojo.widget._templateCache){
if(dojo.widget._templateCache[name].node){
dojo.dom.destroyNode(dojo.widget._templateCache[name].node);
dojo.widget._templateCache[name].node=null;
delete dojo.widget._templateCache[name].node;
}
}
}
try{
window.onload=null;
}
catch(e){
}
try{
window.onunload=null;
}
catch(e){
}
dojo._ie_clobber.clobberNodes=[];
});
}
dojo.event.browser=new function(){
var _470=0;
this.normalizedEventName=function(_471){
switch(_471){
case "CheckboxStateChange":
case "DOMAttrModified":
case "DOMMenuItemActive":
case "DOMMenuItemInactive":
case "DOMMouseScroll":
case "DOMNodeInserted":
case "DOMNodeRemoved":
case "RadioStateChange":
return _471;
break;
default:
return _471.toLowerCase();
break;
}
};
this.clean=function(node){
if(dojo.render.html.ie){
dojo._ie_clobber.clobber(node);
}
};
this.addClobberNode=function(node){
if(!dojo.render.html.ie){
return;
}
if(!node["__doClobber__"]){
node.__doClobber__=true;
dojo._ie_clobber.clobberNodes.push(node);
node.__clobberAttrs__=[];
}
};
this.addClobberNodeAttrs=function(node,_475){
if(!dojo.render.html.ie){
return;
}
this.addClobberNode(node);
for(var x=0;x<_475.length;x++){
node.__clobberAttrs__.push(_475[x]);
}
};
this.removeListener=function(node,_478,fp,_47a){
if(!_47a){
var _47a=false;
}
_478=dojo.event.browser.normalizedEventName(_478);
if((_478=="onkey")||(_478=="key")){
if(dojo.render.html.ie){
this.removeListener(node,"onkeydown",fp,_47a);
}
_478="onkeypress";
}
if(_478.substr(0,2)=="on"){
_478=_478.substr(2);
}
if(node.removeEventListener){
node.removeEventListener(_478,fp,_47a);
}
};
this.addListener=function(node,_47c,fp,_47e,_47f){
if(!node){
return;
}
if(!_47e){
var _47e=false;
}
_47c=dojo.event.browser.normalizedEventName(_47c);
if((_47c=="onkey")||(_47c=="key")){
if(dojo.render.html.ie){
this.addListener(node,"onkeydown",fp,_47e,_47f);
}
_47c="onkeypress";
}
if(_47c.substr(0,2)!="on"){
_47c="on"+_47c;
}
if(!_47f){
var _480=function(evt){
if(!evt){
evt=window.event;
}
var ret=fp(dojo.event.browser.fixEvent(evt,this));
if(_47e){
dojo.event.browser.stopEvent(evt);
}
return ret;
};
}else{
_480=fp;
}
if(node.addEventListener){
node.addEventListener(_47c.substr(2),_480,_47e);
return _480;
}else{
if(typeof node[_47c]=="function"){
var _483=node[_47c];
node[_47c]=function(e){
_483(e);
return _480(e);
};
}else{
node[_47c]=_480;
}
if(dojo.render.html.ie){
this.addClobberNodeAttrs(node,[_47c]);
}
return _480;
}
};
this.isEvent=function(obj){
return (typeof obj!="undefined")&&(typeof Event!="undefined")&&(obj.eventPhase);
};
this.currentEvent=null;
this.callListener=function(_486,_487){
if(typeof _486!="function"){
dojo.raise("listener not a function: "+_486);
}
dojo.event.browser.currentEvent.currentTarget=_487;
return _486.call(_487,dojo.event.browser.currentEvent);
};
this._stopPropagation=function(){
dojo.event.browser.currentEvent.cancelBubble=true;
};
this._preventDefault=function(){
dojo.event.browser.currentEvent.returnValue=false;
};
this.keys={KEY_BACKSPACE:8,KEY_TAB:9,KEY_CLEAR:12,KEY_ENTER:13,KEY_SHIFT:16,KEY_CTRL:17,KEY_ALT:18,KEY_PAUSE:19,KEY_CAPS_LOCK:20,KEY_ESCAPE:27,KEY_SPACE:32,KEY_PAGE_UP:33,KEY_PAGE_DOWN:34,KEY_END:35,KEY_HOME:36,KEY_LEFT_ARROW:37,KEY_UP_ARROW:38,KEY_RIGHT_ARROW:39,KEY_DOWN_ARROW:40,KEY_INSERT:45,KEY_DELETE:46,KEY_HELP:47,KEY_LEFT_WINDOW:91,KEY_RIGHT_WINDOW:92,KEY_SELECT:93,KEY_NUMPAD_0:96,KEY_NUMPAD_1:97,KEY_NUMPAD_2:98,KEY_NUMPAD_3:99,KEY_NUMPAD_4:100,KEY_NUMPAD_5:101,KEY_NUMPAD_6:102,KEY_NUMPAD_7:103,KEY_NUMPAD_8:104,KEY_NUMPAD_9:105,KEY_NUMPAD_MULTIPLY:106,KEY_NUMPAD_PLUS:107,KEY_NUMPAD_ENTER:108,KEY_NUMPAD_MINUS:109,KEY_NUMPAD_PERIOD:110,KEY_NUMPAD_DIVIDE:111,KEY_F1:112,KEY_F2:113,KEY_F3:114,KEY_F4:115,KEY_F5:116,KEY_F6:117,KEY_F7:118,KEY_F8:119,KEY_F9:120,KEY_F10:121,KEY_F11:122,KEY_F12:123,KEY_F13:124,KEY_F14:125,KEY_F15:126,KEY_NUM_LOCK:144,KEY_SCROLL_LOCK:145};
this.revKeys=[];
for(var key in this.keys){
this.revKeys[this.keys[key]]=key;
}
this.fixEvent=function(evt,_48a){
if(!evt){
if(window["event"]){
evt=window.event;
}
}
if((evt["type"])&&(evt["type"].indexOf("key")==0)){
evt.keys=this.revKeys;
for(var key in this.keys){
evt[key]=this.keys[key];
}
if(evt["type"]=="keydown"&&dojo.render.html.ie){
switch(evt.keyCode){
case evt.KEY_SHIFT:
case evt.KEY_CTRL:
case evt.KEY_ALT:
case evt.KEY_CAPS_LOCK:
case evt.KEY_LEFT_WINDOW:
case evt.KEY_RIGHT_WINDOW:
case evt.KEY_SELECT:
case evt.KEY_NUM_LOCK:
case evt.KEY_SCROLL_LOCK:
case evt.KEY_NUMPAD_0:
case evt.KEY_NUMPAD_1:
case evt.KEY_NUMPAD_2:
case evt.KEY_NUMPAD_3:
case evt.KEY_NUMPAD_4:
case evt.KEY_NUMPAD_5:
case evt.KEY_NUMPAD_6:
case evt.KEY_NUMPAD_7:
case evt.KEY_NUMPAD_8:
case evt.KEY_NUMPAD_9:
case evt.KEY_NUMPAD_PERIOD:
break;
case evt.KEY_NUMPAD_MULTIPLY:
case evt.KEY_NUMPAD_PLUS:
case evt.KEY_NUMPAD_ENTER:
case evt.KEY_NUMPAD_MINUS:
case evt.KEY_NUMPAD_DIVIDE:
break;
case evt.KEY_PAUSE:
case evt.KEY_TAB:
case evt.KEY_BACKSPACE:
case evt.KEY_ENTER:
case evt.KEY_ESCAPE:
case evt.KEY_PAGE_UP:
case evt.KEY_PAGE_DOWN:
case evt.KEY_END:
case evt.KEY_HOME:
case evt.KEY_LEFT_ARROW:
case evt.KEY_UP_ARROW:
case evt.KEY_RIGHT_ARROW:
case evt.KEY_DOWN_ARROW:
case evt.KEY_INSERT:
case evt.KEY_DELETE:
case evt.KEY_F1:
case evt.KEY_F2:
case evt.KEY_F3:
case evt.KEY_F4:
case evt.KEY_F5:
case evt.KEY_F6:
case evt.KEY_F7:
case evt.KEY_F8:
case evt.KEY_F9:
case evt.KEY_F10:
case evt.KEY_F11:
case evt.KEY_F12:
case evt.KEY_F12:
case evt.KEY_F13:
case evt.KEY_F14:
case evt.KEY_F15:
case evt.KEY_CLEAR:
case evt.KEY_HELP:
evt.key=evt.keyCode;
break;
default:
if(evt.ctrlKey||evt.altKey){
var _48c=evt.keyCode;
if(_48c>=65&&_48c<=90&&evt.shiftKey==false){
_48c+=32;
}
if(_48c>=1&&_48c<=26&&evt.ctrlKey){
_48c+=96;
}
evt.key=String.fromCharCode(_48c);
}
}
}else{
if(evt["type"]=="keypress"){
if(dojo.render.html.opera){
if(evt.which==0){
evt.key=evt.keyCode;
}else{
if(evt.which>0){
switch(evt.which){
case evt.KEY_SHIFT:
case evt.KEY_CTRL:
case evt.KEY_ALT:
case evt.KEY_CAPS_LOCK:
case evt.KEY_NUM_LOCK:
case evt.KEY_SCROLL_LOCK:
break;
case evt.KEY_PAUSE:
case evt.KEY_TAB:
case evt.KEY_BACKSPACE:
case evt.KEY_ENTER:
case evt.KEY_ESCAPE:
evt.key=evt.which;
break;
default:
var _48c=evt.which;
if((evt.ctrlKey||evt.altKey||evt.metaKey)&&(evt.which>=65&&evt.which<=90&&evt.shiftKey==false)){
_48c+=32;
}
evt.key=String.fromCharCode(_48c);
}
}
}
}else{
if(dojo.render.html.ie){
if(!evt.ctrlKey&&!evt.altKey&&evt.keyCode>=evt.KEY_SPACE){
evt.key=String.fromCharCode(evt.keyCode);
}
}else{
if(dojo.render.html.safari){
switch(evt.keyCode){
case 25:
evt.key=evt.KEY_TAB;
evt.shift=true;
break;
case 63232:
evt.key=evt.KEY_UP_ARROW;
break;
case 63233:
evt.key=evt.KEY_DOWN_ARROW;
break;
case 63234:
evt.key=evt.KEY_LEFT_ARROW;
break;
case 63235:
evt.key=evt.KEY_RIGHT_ARROW;
break;
case 63236:
evt.key=evt.KEY_F1;
break;
case 63237:
evt.key=evt.KEY_F2;
break;
case 63238:
evt.key=evt.KEY_F3;
break;
case 63239:
evt.key=evt.KEY_F4;
break;
case 63240:
evt.key=evt.KEY_F5;
break;
case 63241:
evt.key=evt.KEY_F6;
break;
case 63242:
evt.key=evt.KEY_F7;
break;
case 63243:
evt.key=evt.KEY_F8;
break;
case 63244:
evt.key=evt.KEY_F9;
break;
case 63245:
evt.key=evt.KEY_F10;
break;
case 63246:
evt.key=evt.KEY_F11;
break;
case 63247:
evt.key=evt.KEY_F12;
break;
case 63250:
evt.key=evt.KEY_PAUSE;
break;
case 63272:
evt.key=evt.KEY_DELETE;
break;
case 63273:
evt.key=evt.KEY_HOME;
break;
case 63275:
evt.key=evt.KEY_END;
break;
case 63276:
evt.key=evt.KEY_PAGE_UP;
break;
case 63277:
evt.key=evt.KEY_PAGE_DOWN;
break;
case 63302:
evt.key=evt.KEY_INSERT;
break;
case 63248:
case 63249:
case 63289:
break;
default:
evt.key=evt.charCode>=evt.KEY_SPACE?String.fromCharCode(evt.charCode):evt.keyCode;
}
}else{
evt.key=evt.charCode>0?String.fromCharCode(evt.charCode):evt.keyCode;
}
}
}
}
}
}
if(dojo.render.html.ie){
if(!evt.target){
evt.target=evt.srcElement;
}
if(!evt.currentTarget){
evt.currentTarget=(_48a?_48a:evt.srcElement);
}
if(!evt.layerX){
evt.layerX=evt.offsetX;
}
if(!evt.layerY){
evt.layerY=evt.offsetY;
}
var doc=(evt.srcElement&&evt.srcElement.ownerDocument)?evt.srcElement.ownerDocument:document;
var _48e=((dojo.render.html.ie55)||(doc["compatMode"]=="BackCompat"))?doc.body:doc.documentElement;
if(!evt.pageX){
evt.pageX=evt.clientX+(_48e.scrollLeft||0);
}
if(!evt.pageY){
evt.pageY=evt.clientY+(_48e.scrollTop||0);
}
if(evt.type=="mouseover"){
evt.relatedTarget=evt.fromElement;
}
if(evt.type=="mouseout"){
evt.relatedTarget=evt.toElement;
}
this.currentEvent=evt;
evt.callListener=this.callListener;
evt.stopPropagation=this._stopPropagation;
evt.preventDefault=this._preventDefault;
}
return evt;
};
this.stopEvent=function(evt){
if(window.event){
evt.cancelBubble=true;
evt.returnValue=false;
}else{
evt.preventDefault();
evt.stopPropagation();
}
};
};
dojo.provide("dojo.event.*");
dojo.provide("dojo.widget.Manager");
dojo.widget.manager=new function(){
this.widgets=[];
this.widgetIds=[];
this.topWidgets={};
var _490={};
var _491=[];
this.getUniqueId=function(_492){
var _493;
do{
_493=_492+"_"+(_490[_492]!=undefined?++_490[_492]:_490[_492]=0);
}while(this.getWidgetById(_493));
return _493;
};
this.add=function(_494){
this.widgets.push(_494);
if(!_494.extraArgs["id"]){
_494.extraArgs["id"]=_494.extraArgs["ID"];
}
if(_494.widgetId==""){
if(_494["id"]){
_494.widgetId=_494["id"];
}else{
if(_494.extraArgs["id"]){
_494.widgetId=_494.extraArgs["id"];
}else{
_494.widgetId=this.getUniqueId(_494.ns+"_"+_494.widgetType);
}
}
}
if(this.widgetIds[_494.widgetId]){
dojo.debug("widget ID collision on ID: "+_494.widgetId);
}
this.widgetIds[_494.widgetId]=_494;
};
this.destroyAll=function(){
for(var x=this.widgets.length-1;x>=0;x--){
try{
this.widgets[x].destroy(true);
delete this.widgets[x];
}
catch(e){
}
}
};
this.remove=function(_496){
if(dojo.lang.isNumber(_496)){
var tw=this.widgets[_496].widgetId;
delete this.widgetIds[tw];
this.widgets.splice(_496,1);
}else{
this.removeById(_496);
}
};
this.removeById=function(id){
if(!dojo.lang.isString(id)){
id=id["widgetId"];
if(!id){
dojo.debug("invalid widget or id passed to removeById");
return;
}
}
for(var i=0;i<this.widgets.length;i++){
if(this.widgets[i].widgetId==id){
this.remove(i);
break;
}
}
};
this.getWidgetById=function(id){
if(dojo.lang.isString(id)){
return this.widgetIds[id];
}
return id;
};
this.getWidgetsByType=function(type){
var lt=type.toLowerCase();
var _49d=(type.indexOf(":")<0?function(x){
return x.widgetType.toLowerCase();
}:function(x){
return x.getNamespacedType();
});
var ret=[];
dojo.lang.forEach(this.widgets,function(x){
if(_49d(x)==lt){
ret.push(x);
}
});
return ret;
};
this.getWidgetsByFilter=function(_4a2,_4a3){
var ret=[];
dojo.lang.every(this.widgets,function(x){
if(_4a2(x)){
ret.push(x);
if(_4a3){
return false;
}
}
return true;
});
return (_4a3?ret[0]:ret);
};
this.getAllWidgets=function(){
return this.widgets.concat();
};
this.getWidgetByNode=function(node){
var w=this.getAllWidgets();
node=dojo.byId(node);
for(var i=0;i<w.length;i++){
if(w[i].domNode==node){
return w[i];
}
}
return null;
};
this.byId=this.getWidgetById;
this.byType=this.getWidgetsByType;
this.byFilter=this.getWidgetsByFilter;
this.byNode=this.getWidgetByNode;
var _4a9={};
var _4aa=["dojo.widget"];
for(var i=0;i<_4aa.length;i++){
_4aa[_4aa[i]]=true;
}
this.registerWidgetPackage=function(_4ac){
if(!_4aa[_4ac]){
_4aa[_4ac]=true;
_4aa.push(_4ac);
}
};
this.getWidgetPackageList=function(){
return dojo.lang.map(_4aa,function(elt){
return (elt!==true?elt:undefined);
});
};
this.getImplementation=function(_4ae,_4af,_4b0,ns){
var impl=this.getImplementationName(_4ae,ns);
if(impl){
var ret=_4af?new impl(_4af):new impl();
return ret;
}
};
function buildPrefixCache(){
for(var _4b4 in dojo.render){
if(dojo.render[_4b4]["capable"]===true){
var _4b5=dojo.render[_4b4].prefixes;
for(var i=0;i<_4b5.length;i++){
_491.push(_4b5[i].toLowerCase());
}
}
}
}
var _4b7=function(_4b8,_4b9){
if(!_4b9){
return null;
}
for(var i=0,l=_491.length,_4bc;i<=l;i++){
_4bc=(i<l?_4b9[_491[i]]:_4b9);
if(!_4bc){
continue;
}
for(var name in _4bc){
if(name.toLowerCase()==_4b8){
return _4bc[name];
}
}
}
return null;
};
var _4be=function(_4bf,_4c0){
var _4c1=dojo.evalObjPath(_4c0,false);
return (_4c1?_4b7(_4bf,_4c1):null);
};
this.getImplementationName=function(_4c2,ns){
var _4c4=_4c2.toLowerCase();
ns=ns||"dojo";
var imps=_4a9[ns]||(_4a9[ns]={});
var impl=imps[_4c4];
if(impl){
return impl;
}
if(!_491.length){
buildPrefixCache();
}
var _4c7=dojo.ns.get(ns);
if(!_4c7){
dojo.ns.register(ns,ns+".widget");
_4c7=dojo.ns.get(ns);
}
if(_4c7){
_4c7.resolve(_4c2);
}
impl=_4be(_4c4,_4c7.module);
if(impl){
return (imps[_4c4]=impl);
}
_4c7=dojo.ns.require(ns);
if((_4c7)&&(_4c7.resolver)){
_4c7.resolve(_4c2);
impl=_4be(_4c4,_4c7.module);
if(impl){
return (imps[_4c4]=impl);
}
}
dojo.deprecated("dojo.widget.Manager.getImplementationName","Could not locate widget implementation for \""+_4c2+"\" in \""+_4c7.module+"\" registered to namespace \""+_4c7.name+"\". "+"Developers must specify correct namespaces for all non-Dojo widgets","0.5");
for(var i=0;i<_4aa.length;i++){
impl=_4be(_4c4,_4aa[i]);
if(impl){
return (imps[_4c4]=impl);
}
}
throw new Error("Could not locate widget implementation for \""+_4c2+"\" in \""+_4c7.module+"\" registered to namespace \""+_4c7.name+"\"");
};
this.resizing=false;
this.onWindowResized=function(){
if(this.resizing){
return;
}
try{
this.resizing=true;
for(var id in this.topWidgets){
var _4ca=this.topWidgets[id];
if(_4ca.checkSize){
_4ca.checkSize();
}
}
}
catch(e){
}
finally{
this.resizing=false;
}
};
if(typeof window!="undefined"){
dojo.addOnLoad(this,"onWindowResized");
dojo.event.connect(window,"onresize",this,"onWindowResized");
}
};
(function(){
var dw=dojo.widget;
var dwm=dw.manager;
var h=dojo.lang.curry(dojo.lang,"hitch",dwm);
var g=function(_4cf,_4d0){
dw[(_4d0||_4cf)]=h(_4cf);
};
g("add","addWidget");
g("destroyAll","destroyAllWidgets");
g("remove","removeWidget");
g("removeById","removeWidgetById");
g("getWidgetById");
g("getWidgetById","byId");
g("getWidgetsByType");
g("getWidgetsByFilter");
g("getWidgetsByType","byType");
g("getWidgetsByFilter","byFilter");
g("getWidgetByNode","byNode");
dw.all=function(n){
var _4d2=dwm.getAllWidgets.apply(dwm,arguments);
if(arguments.length>0){
return _4d2[n];
}
return _4d2;
};
g("registerWidgetPackage");
g("getImplementation","getWidgetImplementation");
g("getImplementationName","getWidgetImplementationName");
dw.widgets=dwm.widgets;
dw.widgetIds=dwm.widgetIds;
dw.root=dwm.root;
})();
dojo.provide("dojo.uri.*");
dojo.provide("dojo.a11y");
dojo.a11y={imgPath:dojo.uri.dojoUri("src/widget/templates/images"),doAccessibleCheck:true,accessible:null,checkAccessible:function(){
if(this.accessible===null){
this.accessible=false;
if(this.doAccessibleCheck==true){
this.accessible=this.testAccessible();
}
}
return this.accessible;
},testAccessible:function(){
this.accessible=false;
if(dojo.render.html.ie||dojo.render.html.mozilla){
var div=document.createElement("div");
div.style.backgroundImage="url(\""+this.imgPath+"/tab_close.gif\")";
dojo.body().appendChild(div);
var _4d4=null;
if(window.getComputedStyle){
var _4d5=getComputedStyle(div,"");
_4d4=_4d5.getPropertyValue("background-image");
}else{
_4d4=div.currentStyle.backgroundImage;
}
var _4d6=false;
if(_4d4!=null&&(_4d4=="none"||_4d4=="url(invalid-url:)")){
this.accessible=true;
}
dojo.body().removeChild(div);
}
return this.accessible;
},setCheckAccessible:function(_4d7){
this.doAccessibleCheck=_4d7;
},setAccessibleMode:function(){
if(this.accessible===null){
if(this.checkAccessible()){
dojo.render.html.prefixes.unshift("a11y");
}
}
return this.accessible;
}};
dojo.provide("dojo.widget.Widget");
dojo.declare("dojo.widget.Widget",null,function(){
this.children=[];
this.extraArgs={};
},{parent:null,isTopLevel:false,disabled:false,isContainer:false,widgetId:"",widgetType:"Widget",ns:"dojo",getNamespacedType:function(){
return (this.ns?this.ns+":"+this.widgetType:this.widgetType).toLowerCase();
},toString:function(){
return "[Widget "+this.getNamespacedType()+", "+(this.widgetId||"NO ID")+"]";
},repr:function(){
return this.toString();
},enable:function(){
this.disabled=false;
},disable:function(){
this.disabled=true;
},onResized:function(){
this.notifyChildrenOfResize();
},notifyChildrenOfResize:function(){
for(var i=0;i<this.children.length;i++){
var _4d9=this.children[i];
if(_4d9.onResized){
_4d9.onResized();
}
}
},create:function(args,_4db,_4dc,ns){
if(ns){
this.ns=ns;
}
this.satisfyPropertySets(args,_4db,_4dc);
this.mixInProperties(args,_4db,_4dc);
this.postMixInProperties(args,_4db,_4dc);
dojo.widget.manager.add(this);
this.buildRendering(args,_4db,_4dc);
this.initialize(args,_4db,_4dc);
this.postInitialize(args,_4db,_4dc);
this.postCreate(args,_4db,_4dc);
return this;
},destroy:function(_4de){
if(this.parent){
this.parent.removeChild(this);
}
this.destroyChildren();
this.uninitialize();
this.destroyRendering(_4de);
dojo.widget.manager.removeById(this.widgetId);
},destroyChildren:function(){
var _4df;
var i=0;
while(this.children.length>i){
_4df=this.children[i];
if(_4df instanceof dojo.widget.Widget){
this.removeChild(_4df);
_4df.destroy();
continue;
}
i++;
}
},getChildrenOfType:function(type,_4e2){
var ret=[];
var _4e4=dojo.lang.isFunction(type);
if(!_4e4){
type=type.toLowerCase();
}
for(var x=0;x<this.children.length;x++){
if(_4e4){
if(this.children[x] instanceof type){
ret.push(this.children[x]);
}
}else{
if(this.children[x].widgetType.toLowerCase()==type){
ret.push(this.children[x]);
}
}
if(_4e2){
ret=ret.concat(this.children[x].getChildrenOfType(type,_4e2));
}
}
return ret;
},getDescendants:function(){
var _4e6=[];
var _4e7=[this];
var elem;
while((elem=_4e7.pop())){
_4e6.push(elem);
if(elem.children){
dojo.lang.forEach(elem.children,function(elem){
_4e7.push(elem);
});
}
}
return _4e6;
},isFirstChild:function(){
return this===this.parent.children[0];
},isLastChild:function(){
return this===this.parent.children[this.parent.children.length-1];
},satisfyPropertySets:function(args){
return args;
},mixInProperties:function(args,frag){
if((args["fastMixIn"])||(frag["fastMixIn"])){
for(var x in args){
this[x]=args[x];
}
return;
}
var _4ee;
var _4ef=dojo.widget.lcArgsCache[this.widgetType];
if(_4ef==null){
_4ef={};
for(var y in this){
_4ef[((new String(y)).toLowerCase())]=y;
}
dojo.widget.lcArgsCache[this.widgetType]=_4ef;
}
var _4f1={};
for(var x in args){
if(!this[x]){
var y=_4ef[(new String(x)).toLowerCase()];
if(y){
args[y]=args[x];
x=y;
}
}
if(_4f1[x]){
continue;
}
_4f1[x]=true;
if((typeof this[x])!=(typeof _4ee)){
if(typeof args[x]!="string"){
this[x]=args[x];
}else{
if(dojo.lang.isString(this[x])){
this[x]=args[x];
}else{
if(dojo.lang.isNumber(this[x])){
this[x]=new Number(args[x]);
}else{
if(dojo.lang.isBoolean(this[x])){
this[x]=(args[x].toLowerCase()=="false")?false:true;
}else{
if(dojo.lang.isFunction(this[x])){
if(args[x].search(/[^\w\.]+/i)==-1){
this[x]=dojo.evalObjPath(args[x],false);
}else{
var tn=dojo.lang.nameAnonFunc(new Function(args[x]),this);
dojo.event.kwConnect({srcObj:this,srcFunc:x,adviceObj:this,adviceFunc:tn});
}
}else{
if(dojo.lang.isArray(this[x])){
this[x]=args[x].split(";");
}else{
if(this[x] instanceof Date){
this[x]=new Date(Number(args[x]));
}else{
if(typeof this[x]=="object"){
if(this[x] instanceof dojo.uri.Uri){
this[x]=dojo.uri.dojoUri(args[x]);
}else{
var _4f3=args[x].split(";");
for(var y=0;y<_4f3.length;y++){
var si=_4f3[y].indexOf(":");
if((si!=-1)&&(_4f3[y].length>si)){
this[x][_4f3[y].substr(0,si).replace(/^\s+|\s+$/g,"")]=_4f3[y].substr(si+1);
}
}
}
}else{
this[x]=args[x];
}
}
}
}
}
}
}
}
}else{
this.extraArgs[x.toLowerCase()]=args[x];
}
}
},postMixInProperties:function(args,frag,_4f7){
},initialize:function(args,frag,_4fa){
return false;
},postInitialize:function(args,frag,_4fd){
return false;
},postCreate:function(args,frag,_500){
return false;
},uninitialize:function(){
return false;
},buildRendering:function(args,frag,_503){
dojo.unimplemented("dojo.widget.Widget.buildRendering, on "+this.toString()+", ");
return false;
},destroyRendering:function(){
dojo.unimplemented("dojo.widget.Widget.destroyRendering");
return false;
},addedTo:function(_504){
},addChild:function(_505){
dojo.unimplemented("dojo.widget.Widget.addChild");
return false;
},removeChild:function(_506){
for(var x=0;x<this.children.length;x++){
if(this.children[x]===_506){
this.children.splice(x,1);
_506.parent=null;
break;
}
}
return _506;
},getPreviousSibling:function(){
var idx=this.getParentIndex();
if(idx<=0){
return null;
}
return this.parent.children[idx-1];
},getSiblings:function(){
return this.parent.children;
},getParentIndex:function(){
return dojo.lang.indexOf(this.parent.children,this,true);
},getNextSibling:function(){
var idx=this.getParentIndex();
if(idx==this.parent.children.length-1){
return null;
}
if(idx<0){
return null;
}
return this.parent.children[idx+1];
}});
dojo.widget.lcArgsCache={};
dojo.widget.tags={};
dojo.widget.tags.addParseTreeHandler=function(type){
dojo.deprecated("addParseTreeHandler",". ParseTreeHandlers are now reserved for components. Any unfiltered DojoML tag without a ParseTreeHandler is assumed to be a widget","0.5");
};
dojo.widget.tags["dojo:propertyset"]=function(_50b,_50c,_50d){
var _50e=_50c.parseProperties(_50b["dojo:propertyset"]);
};
dojo.widget.tags["dojo:connect"]=function(_50f,_510,_511){
var _512=_510.parseProperties(_50f["dojo:connect"]);
};
dojo.widget.buildWidgetFromParseTree=function(type,frag,_515,_516,_517,_518){
dojo.a11y.setAccessibleMode();
var _519=type.split(":");
_519=(_519.length==2)?_519[1]:type;
var _51a=_518||_515.parseProperties(frag[frag["ns"]+":"+_519]);
var _51b=dojo.widget.manager.getImplementation(_519,null,null,frag["ns"]);
if(!_51b){
throw new Error("cannot find \""+type+"\" widget");
}else{
if(!_51b.create){
throw new Error("\""+type+"\" widget object has no \"create\" method and does not appear to implement *Widget");
}
}
_51a["dojoinsertionindex"]=_517;
var ret=_51b.create(_51a,frag,_516,frag["ns"]);
return ret;
};
dojo.widget.defineWidget=function(_51d,_51e,_51f,init,_521){
if(dojo.lang.isString(arguments[3])){
dojo.widget._defineWidget(arguments[0],arguments[3],arguments[1],arguments[4],arguments[2]);
}else{
var args=[arguments[0]],p=3;
if(dojo.lang.isString(arguments[1])){
args.push(arguments[1],arguments[2]);
}else{
args.push("",arguments[1]);
p=2;
}
if(dojo.lang.isFunction(arguments[p])){
args.push(arguments[p],arguments[p+1]);
}else{
args.push(null,arguments[p]);
}
dojo.widget._defineWidget.apply(this,args);
}
};
dojo.widget.defineWidget.renderers="html|svg|vml";
dojo.widget._defineWidget=function(_524,_525,_526,init,_528){
var _529=_524.split(".");
var type=_529.pop();
var regx="\\.("+(_525?_525+"|":"")+dojo.widget.defineWidget.renderers+")\\.";
var r=_524.search(new RegExp(regx));
_529=(r<0?_529.join("."):_524.substr(0,r));
dojo.widget.manager.registerWidgetPackage(_529);
var pos=_529.indexOf(".");
var _52e=(pos>-1)?_529.substring(0,pos):_529;
_528=(_528)||{};
_528.widgetType=type;
if((!init)&&(_528["classConstructor"])){
init=_528.classConstructor;
delete _528.classConstructor;
}
dojo.declare(_524,_526,init,_528);
};
dojo.provide("dojo.widget.Parse");
dojo.widget.Parse=function(_52f){
this.propertySetsList=[];
this.fragment=_52f;
this.createComponents=function(frag,_531){
var _532=[];
var _533=false;
try{
if(frag&&frag.tagName&&(frag!=frag.nodeRef)){
var _534=dojo.widget.tags;
var tna=String(frag.tagName).split(";");
for(var x=0;x<tna.length;x++){
var ltn=tna[x].replace(/^\s+|\s+$/g,"").toLowerCase();
frag.tagName=ltn;
var ret;
if(_534[ltn]){
_533=true;
ret=_534[ltn](frag,this,_531,frag.index);
_532.push(ret);
}else{
if(ltn.indexOf(":")==-1){
ltn="dojo:"+ltn;
}
ret=dojo.widget.buildWidgetFromParseTree(ltn,frag,this,_531,frag.index);
if(ret){
_533=true;
_532.push(ret);
}
}
}
}
}
catch(e){
dojo.debug("dojo.widget.Parse: error:",e);
}
if(!_533){
_532=_532.concat(this.createSubComponents(frag,_531));
}
return _532;
};
this.createSubComponents=function(_539,_53a){
var frag,_53c=[];
for(var item in _539){
frag=_539[item];
if(frag&&typeof frag=="object"&&(frag!=_539.nodeRef)&&(frag!=_539.tagName)&&(!dojo.dom.isNode(frag))){
_53c=_53c.concat(this.createComponents(frag,_53a));
}
}
return _53c;
};
this.parsePropertySets=function(_53e){
return [];
};
this.parseProperties=function(_53f){
var _540={};
for(var item in _53f){
if((_53f[item]==_53f.tagName)||(_53f[item]==_53f.nodeRef)){
}else{
var frag=_53f[item];
if(frag.tagName&&dojo.widget.tags[frag.tagName.toLowerCase()]){
}else{
if(frag[0]&&frag[0].value!=""&&frag[0].value!=null){
try{
if(item.toLowerCase()=="dataprovider"){
var _543=this;
this.getDataProvider(_543,frag[0].value);
_540.dataProvider=this.dataProvider;
}
_540[item]=frag[0].value;
var _544=this.parseProperties(frag);
for(var _545 in _544){
_540[_545]=_544[_545];
}
}
catch(e){
dojo.debug(e);
}
}
}
switch(item.toLowerCase()){
case "checked":
case "disabled":
if(typeof _540[item]!="boolean"){
_540[item]=true;
}
break;
}
}
}
return _540;
};
this.getDataProvider=function(_546,_547){
dojo.io.bind({url:_547,load:function(type,_549){
if(type=="load"){
_546.dataProvider=_549;
}
},mimetype:"text/javascript",sync:true});
};
this.getPropertySetById=function(_54a){
for(var x=0;x<this.propertySetsList.length;x++){
if(_54a==this.propertySetsList[x]["id"][0].value){
return this.propertySetsList[x];
}
}
return "";
};
this.getPropertySetsByType=function(_54c){
var _54d=[];
for(var x=0;x<this.propertySetsList.length;x++){
var cpl=this.propertySetsList[x];
var cpcc=cpl.componentClass||cpl.componentType||null;
var _551=this.propertySetsList[x]["id"][0].value;
if(cpcc&&(_551==cpcc[0].value)){
_54d.push(cpl);
}
}
return _54d;
};
this.getPropertySets=function(_552){
var ppl="dojo:propertyproviderlist";
var _554=[];
var _555=_552.tagName;
if(_552[ppl]){
var _556=_552[ppl].value.split(" ");
for(var _557 in _556){
if((_557.indexOf("..")==-1)&&(_557.indexOf("://")==-1)){
var _558=this.getPropertySetById(_557);
if(_558!=""){
_554.push(_558);
}
}else{
}
}
}
return this.getPropertySetsByType(_555).concat(_554);
};
this.createComponentFromScript=function(_559,_55a,_55b,ns){
_55b.fastMixIn=true;
var ltn=(ns||"dojo")+":"+_55a.toLowerCase();
if(dojo.widget.tags[ltn]){
return [dojo.widget.tags[ltn](_55b,this,null,null,_55b)];
}
return [dojo.widget.buildWidgetFromParseTree(ltn,_55b,this,null,null,_55b)];
};
};
dojo.widget._parser_collection={"dojo":new dojo.widget.Parse()};
dojo.widget.getParser=function(name){
if(!name){
name="dojo";
}
if(!this._parser_collection[name]){
this._parser_collection[name]=new dojo.widget.Parse();
}
return this._parser_collection[name];
};
dojo.widget.createWidget=function(name,_560,_561,_562){
var _563=false;
var _564=(typeof name=="string");
if(_564){
var pos=name.indexOf(":");
var ns=(pos>-1)?name.substring(0,pos):"dojo";
if(pos>-1){
name=name.substring(pos+1);
}
var _567=name.toLowerCase();
var _568=ns+":"+_567;
_563=(dojo.byId(name)&&!dojo.widget.tags[_568]);
}
if((arguments.length==1)&&(_563||!_564)){
var xp=new dojo.xml.Parse();
var tn=_563?dojo.byId(name):name;
return dojo.widget.getParser().createComponents(xp.parseElement(tn,null,true))[0];
}
function fromScript(_56b,name,_56d,ns){
_56d[_568]={dojotype:[{value:_567}],nodeRef:_56b,fastMixIn:true};
_56d.ns=ns;
return dojo.widget.getParser().createComponentFromScript(_56b,name,_56d,ns);
}
_560=_560||{};
var _56f=false;
var tn=null;
var h=dojo.render.html.capable;
if(h){
tn=document.createElement("span");
}
if(!_561){
_56f=true;
_561=tn;
if(h){
dojo.body().appendChild(_561);
}
}else{
if(_562){
dojo.dom.insertAtPosition(tn,_561,_562);
}else{
tn=_561;
}
}
var _571=fromScript(tn,name.toLowerCase(),_560,ns);
if((!_571)||(!_571[0])||(typeof _571[0].widgetType=="undefined")){
throw new Error("createWidget: Creation of \""+name+"\" widget failed.");
}
try{
if(_56f&&_571[0].domNode.parentNode){
_571[0].domNode.parentNode.removeChild(_571[0].domNode);
}
}
catch(e){
dojo.debug(e);
}
return _571[0];
};
dojo.provide("dojo.widget.DomWidget");
dojo.widget._cssFiles={};
dojo.widget._cssStrings={};
dojo.widget._templateCache={};
dojo.widget.defaultStrings={dojoRoot:dojo.hostenv.getBaseScriptUri(),baseScriptUri:dojo.hostenv.getBaseScriptUri()};
dojo.widget.fillFromTemplateCache=function(obj,_573,_574,_575){
var _576=_573||obj.templatePath;
var _577=dojo.widget._templateCache;
if(!_576&&!obj["widgetType"]){
do{
var _578="__dummyTemplate__"+dojo.widget._templateCache.dummyCount++;
}while(_577[_578]);
obj.widgetType=_578;
}
var wt=_576?_576.toString():obj.widgetType;
var ts=_577[wt];
if(!ts){
_577[wt]={"string":null,"node":null};
if(_575){
ts={};
}else{
ts=_577[wt];
}
}
if((!obj.templateString)&&(!_575)){
obj.templateString=_574||ts["string"];
}
if((!obj.templateNode)&&(!_575)){
obj.templateNode=ts["node"];
}
if((!obj.templateNode)&&(!obj.templateString)&&(_576)){
var _57b=dojo.hostenv.getText(_576);
if(_57b){
_57b=_57b.replace(/^\s*<\?xml(\s)+version=[\'\"](\d)*.(\d)*[\'\"](\s)*\?>/im,"");
var _57c=_57b.match(/<body[^>]*>\s*([\s\S]+)\s*<\/body>/im);
if(_57c){
_57b=_57c[1];
}
}else{
_57b="";
}
obj.templateString=_57b;
if(!_575){
_577[wt]["string"]=_57b;
}
}
if((!ts["string"])&&(!_575)){
ts.string=obj.templateString;
}
};
dojo.widget._templateCache.dummyCount=0;
dojo.widget.attachProperties=["dojoAttachPoint","id"];
dojo.widget.eventAttachProperty="dojoAttachEvent";
dojo.widget.onBuildProperty="dojoOnBuild";
dojo.widget.waiNames=["waiRole","waiState"];
dojo.widget.wai={waiRole:{name:"waiRole","namespace":"http://www.w3.org/TR/xhtml2",alias:"x2",prefix:"wairole:"},waiState:{name:"waiState","namespace":"http://www.w3.org/2005/07/aaa",alias:"aaa",prefix:""},setAttr:function(node,ns,attr,_580){
if(dojo.render.html.ie){
node.setAttribute(this[ns].alias+":"+attr,this[ns].prefix+_580);
}else{
node.setAttributeNS(this[ns]["namespace"],attr,this[ns].prefix+_580);
}
},getAttr:function(node,ns,attr){
if(dojo.render.html.ie){
return node.getAttribute(this[ns].alias+":"+attr);
}else{
return node.getAttributeNS(this[ns]["namespace"],attr);
}
},removeAttr:function(node,ns,attr){
var _587=true;
if(dojo.render.html.ie){
_587=node.removeAttribute(this[ns].alias+":"+attr);
}else{
node.removeAttributeNS(this[ns]["namespace"],attr);
}
return _587;
}};
dojo.widget.attachTemplateNodes=function(_588,_589,_58a){
var _58b=dojo.dom.ELEMENT_NODE;
function trim(str){
return str.replace(/^\s+|\s+$/g,"");
}
if(!_588){
_588=_589.domNode;
}
if(_588.nodeType!=_58b){
return;
}
var _58d=_588.all||_588.getElementsByTagName("*");
var _58e=_589;
for(var x=-1;x<_58d.length;x++){
var _590=(x==-1)?_588:_58d[x];
var _591=[];
if(!_589.widgetsInTemplate||!_590.getAttribute("dojoType")){
for(var y=0;y<this.attachProperties.length;y++){
var _593=_590.getAttribute(this.attachProperties[y]);
if(_593){
_591=_593.split(";");
for(var z=0;z<_591.length;z++){
if(dojo.lang.isArray(_589[_591[z]])){
_589[_591[z]].push(_590);
}else{
_589[_591[z]]=_590;
}
}
break;
}
}
var _595=_590.getAttribute(this.eventAttachProperty);
if(_595){
var evts=_595.split(";");
for(var y=0;y<evts.length;y++){
if((!evts[y])||(!evts[y].length)){
continue;
}
var _597=null;
var tevt=trim(evts[y]);
if(evts[y].indexOf(":")>=0){
var _599=tevt.split(":");
tevt=trim(_599[0]);
_597=trim(_599[1]);
}
if(!_597){
_597=tevt;
}
var tf=function(){
var ntf=new String(_597);
return function(evt){
if(_58e[ntf]){
_58e[ntf](dojo.event.browser.fixEvent(evt,this));
}
};
}();
dojo.event.browser.addListener(_590,tevt,tf,false,true);
}
}
for(var y=0;y<_58a.length;y++){
var _59d=_590.getAttribute(_58a[y]);
if((_59d)&&(_59d.length)){
var _597=null;
var _59e=_58a[y].substr(4);
_597=trim(_59d);
var _59f=[_597];
if(_597.indexOf(";")>=0){
_59f=dojo.lang.map(_597.split(";"),trim);
}
for(var z=0;z<_59f.length;z++){
if(!_59f[z].length){
continue;
}
var tf=function(){
var ntf=new String(_59f[z]);
return function(evt){
if(_58e[ntf]){
_58e[ntf](dojo.event.browser.fixEvent(evt,this));
}
};
}();
dojo.event.browser.addListener(_590,_59e,tf,false,true);
}
}
}
}
var _5a2=_590.getAttribute(this.templateProperty);
if(_5a2){
_589[_5a2]=_590;
}
dojo.lang.forEach(dojo.widget.waiNames,function(name){
var wai=dojo.widget.wai[name];
var val=_590.getAttribute(wai.name);
if(val){
if(val.indexOf("-")==-1){
dojo.widget.wai.setAttr(_590,wai.name,"role",val);
}else{
var _5a6=val.split("-");
dojo.widget.wai.setAttr(_590,wai.name,_5a6[0],_5a6[1]);
}
}
},this);
var _5a7=_590.getAttribute(this.onBuildProperty);
if(_5a7){
eval("var node = baseNode; var widget = targetObj; "+_5a7);
}
}
};
dojo.widget.getDojoEventsFromStr=function(str){
var re=/(dojoOn([a-z]+)(\s?))=/gi;
var evts=str?str.match(re)||[]:[];
var ret=[];
var lem={};
for(var x=0;x<evts.length;x++){
if(evts[x].length<1){
continue;
}
var cm=evts[x].replace(/\s/,"");
cm=(cm.slice(0,cm.length-1));
if(!lem[cm]){
lem[cm]=true;
ret.push(cm);
}
}
return ret;
};
dojo.declare("dojo.widget.DomWidget",dojo.widget.Widget,function(){
if((arguments.length>0)&&(typeof arguments[0]=="object")){
this.create(arguments[0]);
}
},{templateNode:null,templateString:null,templateCssString:null,preventClobber:false,domNode:null,containerNode:null,widgetsInTemplate:false,addChild:function(_5af,_5b0,pos,ref,_5b3){
if(!this.isContainer){
dojo.debug("dojo.widget.DomWidget.addChild() attempted on non-container widget");
return null;
}else{
if(_5b3==undefined){
_5b3=this.children.length;
}
this.addWidgetAsDirectChild(_5af,_5b0,pos,ref,_5b3);
this.registerChild(_5af,_5b3);
}
return _5af;
},addWidgetAsDirectChild:function(_5b4,_5b5,pos,ref,_5b8){
if((!this.containerNode)&&(!_5b5)){
this.containerNode=this.domNode;
}
var cn=(_5b5)?_5b5:this.containerNode;
if(!pos){
pos="after";
}
if(!ref){
if(!cn){
cn=dojo.body();
}
ref=cn.lastChild;
}
if(!_5b8){
_5b8=0;
}
_5b4.domNode.setAttribute("dojoinsertionindex",_5b8);
if(!ref){
cn.appendChild(_5b4.domNode);
}else{
if(pos=="insertAtIndex"){
dojo.dom.insertAtIndex(_5b4.domNode,ref.parentNode,_5b8);
}else{
if((pos=="after")&&(ref===cn.lastChild)){
cn.appendChild(_5b4.domNode);
}else{
dojo.dom.insertAtPosition(_5b4.domNode,cn,pos);
}
}
}
},registerChild:function(_5ba,_5bb){
_5ba.dojoInsertionIndex=_5bb;
var idx=-1;
for(var i=0;i<this.children.length;i++){
if(this.children[i].dojoInsertionIndex<=_5bb){
idx=i;
}
}
this.children.splice(idx+1,0,_5ba);
_5ba.parent=this;
_5ba.addedTo(this,idx+1);
delete dojo.widget.manager.topWidgets[_5ba.widgetId];
},removeChild:function(_5be){
dojo.dom.destroyNode(_5be.domNode);
return dojo.widget.DomWidget.superclass.removeChild.call(this,_5be);
},getFragNodeRef:function(frag){
if(!frag){
return null;
}
if(!frag[this.getNamespacedType()]){
dojo.raise("Error: no frag for widget type "+this.getNamespacedType()+", id "+this.widgetId+" (maybe a widget has set it's type incorrectly)");
}
return frag[this.getNamespacedType()]["nodeRef"];
},postInitialize:function(args,frag,_5c2){
var _5c3=this.getFragNodeRef(frag);
if(_5c2&&(_5c2.snarfChildDomOutput||!_5c3)){
_5c2.addWidgetAsDirectChild(this,"","insertAtIndex","",args["dojoinsertionindex"],_5c3);
}else{
if(_5c3){
if(this.domNode&&(this.domNode!==_5c3)){
dojo.dom.replaceNode(_5c3,this.domNode);
}
}
}
if(_5c2){
_5c2.registerChild(this,args.dojoinsertionindex);
}else{
dojo.widget.manager.topWidgets[this.widgetId]=this;
}
if(this.widgetsInTemplate){
var _5c4=new dojo.xml.Parse();
var _5c5;
var _5c6=this.domNode.getElementsByTagName("*");
for(var i=0;i<_5c6.length;i++){
if(_5c6[i].getAttribute("dojoAttachPoint")=="subContainerWidget"){
_5c5=_5c6[i];
}
if(_5c6[i].getAttribute("dojoType")){
_5c6[i].setAttribute("_isSubWidget",true);
}
}
if(this.isContainer&&!this.containerNode){
if(_5c5){
var src=this.getFragNodeRef(frag);
if(src){
dojo.dom.moveChildren(src,_5c5);
frag["dojoDontFollow"]=true;
}
}else{
dojo.debug("No subContainerWidget node can be found in template file for widget "+this);
}
}
var _5c9=_5c4.parseElement(this.domNode,null,true);
dojo.widget.getParser().createSubComponents(_5c9,this);
var _5ca=[];
var _5cb=[this];
var w;
while((w=_5cb.pop())){
for(var i=0;i<w.children.length;i++){
var _5cd=w.children[i];
if(_5cd._processedSubWidgets||!_5cd.extraArgs["_issubwidget"]){
continue;
}
_5ca.push(_5cd);
if(_5cd.isContainer){
_5cb.push(_5cd);
}
}
}
for(var i=0;i<_5ca.length;i++){
var _5ce=_5ca[i];
if(_5ce._processedSubWidgets){
dojo.debug("This should not happen: widget._processedSubWidgets is already true!");
return;
}
_5ce._processedSubWidgets=true;
if(_5ce.extraArgs["dojoattachevent"]){
var evts=_5ce.extraArgs["dojoattachevent"].split(";");
for(var j=0;j<evts.length;j++){
var _5d1=null;
var tevt=dojo.string.trim(evts[j]);
if(tevt.indexOf(":")>=0){
var _5d3=tevt.split(":");
tevt=dojo.string.trim(_5d3[0]);
_5d1=dojo.string.trim(_5d3[1]);
}
if(!_5d1){
_5d1=tevt;
}
if(dojo.lang.isFunction(_5ce[tevt])){
dojo.event.kwConnect({srcObj:_5ce,srcFunc:tevt,targetObj:this,targetFunc:_5d1});
}else{
alert(tevt+" is not a function in widget "+_5ce);
}
}
}
if(_5ce.extraArgs["dojoattachpoint"]){
this[_5ce.extraArgs["dojoattachpoint"]]=_5ce;
}
}
}
if(this.isContainer&&!frag["dojoDontFollow"]){
dojo.widget.getParser().createSubComponents(frag,this);
}
},buildRendering:function(args,frag){
var ts=dojo.widget._templateCache[this.widgetType];
if(args["templatecsspath"]){
args["templateCssPath"]=args["templatecsspath"];
}
var _5d7=args["templateCssPath"]||this.templateCssPath;
if(_5d7&&!dojo.widget._cssFiles[_5d7.toString()]){
if((!this.templateCssString)&&(_5d7)){
this.templateCssString=dojo.hostenv.getText(_5d7);
this.templateCssPath=null;
}
dojo.widget._cssFiles[_5d7.toString()]=true;
}
if((this["templateCssString"])&&(!this.templateCssString["loaded"])){
dojo.html.insertCssText(this.templateCssString,null,_5d7);
if(!this.templateCssString){
this.templateCssString="";
}
this.templateCssString.loaded=true;
}
if((!this.preventClobber)&&((this.templatePath)||(this.templateNode)||((this["templateString"])&&(this.templateString.length))||((typeof ts!="undefined")&&((ts["string"])||(ts["node"]))))){
this.buildFromTemplate(args,frag);
}else{
this.domNode=this.getFragNodeRef(frag);
}
this.fillInTemplate(args,frag);
},buildFromTemplate:function(args,frag){
var _5da=false;
if(args["templatepath"]){
args["templatePath"]=args["templatepath"];
}
dojo.widget.fillFromTemplateCache(this,args["templatePath"],null,_5da);
var ts=dojo.widget._templateCache[this.templatePath?this.templatePath.toString():this.widgetType];
if((ts)&&(!_5da)){
if(!this.templateString.length){
this.templateString=ts["string"];
}
if(!this.templateNode){
this.templateNode=ts["node"];
}
}
var _5dc=false;
var node=null;
var tstr=this.templateString;
if((!this.templateNode)&&(this.templateString)){
_5dc=this.templateString.match(/\$\{([^\}]+)\}/g);
if(_5dc){
var hash=this.strings||{};
for(var key in dojo.widget.defaultStrings){
if(dojo.lang.isUndefined(hash[key])){
hash[key]=dojo.widget.defaultStrings[key];
}
}
for(var i=0;i<_5dc.length;i++){
var key=_5dc[i];
key=key.substring(2,key.length-1);
var kval=(key.substring(0,5)=="this.")?dojo.lang.getObjPathValue(key.substring(5),this):hash[key];
var _5e3;
if((kval)||(dojo.lang.isString(kval))){
_5e3=new String((dojo.lang.isFunction(kval))?kval.call(this,key,this.templateString):kval);
while(_5e3.indexOf("\"")>-1){
_5e3=_5e3.replace("\"","&quot;");
}
tstr=tstr.replace(_5dc[i],_5e3);
}
}
}else{
this.templateNode=this.createNodesFromText(this.templateString,true)[0];
if(!_5da){
ts.node=this.templateNode;
}
}
}
if((!this.templateNode)&&(!_5dc)){
dojo.debug("DomWidget.buildFromTemplate: could not create template");
return false;
}else{
if(!_5dc){
node=this.templateNode.cloneNode(true);
if(!node){
return false;
}
}else{
node=this.createNodesFromText(tstr,true)[0];
}
}
this.domNode=node;
this.attachTemplateNodes();
if(this.isContainer&&this.containerNode){
var src=this.getFragNodeRef(frag);
if(src){
dojo.dom.moveChildren(src,this.containerNode);
}
}
},attachTemplateNodes:function(_5e5,_5e6){
if(!_5e5){
_5e5=this.domNode;
}
if(!_5e6){
_5e6=this;
}
return dojo.widget.attachTemplateNodes(_5e5,_5e6,dojo.widget.getDojoEventsFromStr(this.templateString));
},fillInTemplate:function(){
},destroyRendering:function(){
try{
delete this.domNode;
}
catch(e){
}
},createNodesFromText:function(){
dojo.unimplemented("dojo.widget.DomWidget.createNodesFromText");
}});
dojo.provide("dojo.html.display");
dojo.html._toggle=function(node,_5e8,_5e9){
node=dojo.byId(node);
_5e9(node,!_5e8(node));
return _5e8(node);
};
dojo.html.show=function(node){
node=dojo.byId(node);
if(dojo.html.getStyleProperty(node,"display")=="none"){
dojo.html.setStyle(node,"display",(node.dojoDisplayCache||""));
node.dojoDisplayCache=undefined;
}
};
dojo.html.hide=function(node){
node=dojo.byId(node);
if(typeof node["dojoDisplayCache"]=="undefined"){
var d=dojo.html.getStyleProperty(node,"display");
if(d!="none"){
node.dojoDisplayCache=d;
}
}
dojo.html.setStyle(node,"display","none");
};
dojo.html.setShowing=function(node,_5ee){
dojo.html[(_5ee?"show":"hide")](node);
};
dojo.html.isShowing=function(node){
return (dojo.html.getStyleProperty(node,"display")!="none");
};
dojo.html.toggleShowing=function(node){
return dojo.html._toggle(node,dojo.html.isShowing,dojo.html.setShowing);
};
dojo.html.displayMap={tr:"",td:"",th:"",img:"inline",span:"inline",input:"inline",button:"inline"};
dojo.html.suggestDisplayByTagName=function(node){
node=dojo.byId(node);
if(node&&node.tagName){
var tag=node.tagName.toLowerCase();
return (tag in dojo.html.displayMap?dojo.html.displayMap[tag]:"block");
}
};
dojo.html.setDisplay=function(node,_5f4){
dojo.html.setStyle(node,"display",((_5f4 instanceof String||typeof _5f4=="string")?_5f4:(_5f4?dojo.html.suggestDisplayByTagName(node):"none")));
};
dojo.html.isDisplayed=function(node){
return (dojo.html.getComputedStyle(node,"display")!="none");
};
dojo.html.toggleDisplay=function(node){
return dojo.html._toggle(node,dojo.html.isDisplayed,dojo.html.setDisplay);
};
dojo.html.setVisibility=function(node,_5f8){
dojo.html.setStyle(node,"visibility",((_5f8 instanceof String||typeof _5f8=="string")?_5f8:(_5f8?"visible":"hidden")));
};
dojo.html.isVisible=function(node){
return (dojo.html.getComputedStyle(node,"visibility")!="hidden");
};
dojo.html.toggleVisibility=function(node){
return dojo.html._toggle(node,dojo.html.isVisible,dojo.html.setVisibility);
};
dojo.html.setOpacity=function(node,_5fc,_5fd){
node=dojo.byId(node);
var h=dojo.render.html;
if(!_5fd){
if(_5fc>=1){
if(h.ie){
dojo.html.clearOpacity(node);
return;
}else{
_5fc=0.999999;
}
}else{
if(_5fc<0){
_5fc=0;
}
}
}
if(h.ie){
if(node.nodeName.toLowerCase()=="tr"){
var tds=node.getElementsByTagName("td");
for(var x=0;x<tds.length;x++){
tds[x].style.filter="Alpha(Opacity="+_5fc*100+")";
}
}
node.style.filter="Alpha(Opacity="+_5fc*100+")";
}else{
if(h.moz){
node.style.opacity=_5fc;
node.style.MozOpacity=_5fc;
}else{
if(h.safari){
node.style.opacity=_5fc;
node.style.KhtmlOpacity=_5fc;
}else{
node.style.opacity=_5fc;
}
}
}
};
dojo.html.clearOpacity=function(node){
node=dojo.byId(node);
var ns=node.style;
var h=dojo.render.html;
if(h.ie){
try{
if(node.filters&&node.filters.alpha){
ns.filter="";
}
}
catch(e){
}
}else{
if(h.moz){
ns.opacity=1;
ns.MozOpacity=1;
}else{
if(h.safari){
ns.opacity=1;
ns.KhtmlOpacity=1;
}else{
ns.opacity=1;
}
}
}
};
dojo.html.getOpacity=function(node){
node=dojo.byId(node);
var h=dojo.render.html;
if(h.ie){
var opac=(node.filters&&node.filters.alpha&&typeof node.filters.alpha.opacity=="number"?node.filters.alpha.opacity:100)/100;
}else{
var opac=node.style.opacity||node.style.MozOpacity||node.style.KhtmlOpacity||1;
}
return opac>=0.999999?1:Number(opac);
};
dojo.provide("dojo.html.layout");
dojo.html.sumAncestorProperties=function(node,prop){
node=dojo.byId(node);
if(!node){
return 0;
}
var _609=0;
while(node){
if(dojo.html.getComputedStyle(node,"position")=="fixed"){
return 0;
}
var val=node[prop];
if(val){
_609+=val-0;
if(node==dojo.body()){
break;
}
}
node=node.parentNode;
}
return _609;
};
dojo.html.setStyleAttributes=function(node,_60c){
node=dojo.byId(node);
var _60d=_60c.replace(/(;)?\s*$/,"").split(";");
for(var i=0;i<_60d.length;i++){
var _60f=_60d[i].split(":");
var name=_60f[0].replace(/\s*$/,"").replace(/^\s*/,"").toLowerCase();
var _611=_60f[1].replace(/\s*$/,"").replace(/^\s*/,"");
switch(name){
case "opacity":
dojo.html.setOpacity(node,_611);
break;
case "content-height":
dojo.html.setContentBox(node,{height:_611});
break;
case "content-width":
dojo.html.setContentBox(node,{width:_611});
break;
case "outer-height":
dojo.html.setMarginBox(node,{height:_611});
break;
case "outer-width":
dojo.html.setMarginBox(node,{width:_611});
break;
default:
node.style[dojo.html.toCamelCase(name)]=_611;
}
}
};
dojo.html.boxSizing={MARGIN_BOX:"margin-box",BORDER_BOX:"border-box",PADDING_BOX:"padding-box",CONTENT_BOX:"content-box"};
dojo.html.getAbsolutePosition=dojo.html.abs=function(node,_613,_614){
node=dojo.byId(node);
var _615=dojo.doc();
var ret={x:0,y:0};
var bs=dojo.html.boxSizing;
if(!_614){
_614=bs.CONTENT_BOX;
}
var _618=2;
var _619;
switch(_614){
case bs.MARGIN_BOX:
_619=3;
break;
case bs.BORDER_BOX:
_619=2;
break;
case bs.PADDING_BOX:
default:
_619=1;
break;
case bs.CONTENT_BOX:
_619=0;
break;
}
var h=dojo.render.html;
var db=_615["body"]||_615["documentElement"];
if(h.ie){
with(node.getBoundingClientRect()){
ret.x=left-2;
ret.y=top-2;
}
}else{
if(_615["getBoxObjectFor"]){
_618=1;
try{
var bo=_615.getBoxObjectFor(node);
ret.x=bo.x-dojo.html.sumAncestorProperties(node,"scrollLeft");
ret.y=bo.y-dojo.html.sumAncestorProperties(node,"scrollTop");
}
catch(e){
}
}else{
if(node["offsetParent"]){
var _61d;
if((h.safari)&&(node.style.getPropertyValue("position")=="absolute")&&(node.parentNode==db)){
_61d=db;
}else{
_61d=db.parentNode;
}
if(node.parentNode!=db){
var nd=node;
if(dojo.render.html.opera){
nd=db;
}
ret.x-=dojo.html.sumAncestorProperties(nd,"scrollLeft");
ret.y-=dojo.html.sumAncestorProperties(nd,"scrollTop");
}
var _61f=node;
do{
var n=_61f["offsetLeft"];
if(!h.opera||n>0){
ret.x+=isNaN(n)?0:n;
}
var m=_61f["offsetTop"];
ret.y+=isNaN(m)?0:m;
_61f=_61f.offsetParent;
}while((_61f!=_61d)&&(_61f!=null));
}else{
if(node["x"]&&node["y"]){
ret.x+=isNaN(node.x)?0:node.x;
ret.y+=isNaN(node.y)?0:node.y;
}
}
}
}
if(_613){
var _622=dojo.html.getScroll();
ret.y+=_622.top;
ret.x+=_622.left;
}
var _623=[dojo.html.getPaddingExtent,dojo.html.getBorderExtent,dojo.html.getMarginExtent];
if(_618>_619){
for(var i=_619;i<_618;++i){
ret.y+=_623[i](node,"top");
ret.x+=_623[i](node,"left");
}
}else{
if(_618<_619){
for(var i=_619;i>_618;--i){
ret.y-=_623[i-1](node,"top");
ret.x-=_623[i-1](node,"left");
}
}
}
ret.top=ret.y;
ret.left=ret.x;
return ret;
};
dojo.html.isPositionAbsolute=function(node){
return (dojo.html.getComputedStyle(node,"position")=="absolute");
};
dojo.html._sumPixelValues=function(node,_627,_628){
var _629=0;
for(var x=0;x<_627.length;x++){
_629+=dojo.html.getPixelValue(node,_627[x],_628);
}
return _629;
};
dojo.html.getMargin=function(node){
return {width:dojo.html._sumPixelValues(node,["margin-left","margin-right"],(dojo.html.getComputedStyle(node,"position")=="absolute")),height:dojo.html._sumPixelValues(node,["margin-top","margin-bottom"],(dojo.html.getComputedStyle(node,"position")=="absolute"))};
};
dojo.html.getBorder=function(node){
return {width:dojo.html.getBorderExtent(node,"left")+dojo.html.getBorderExtent(node,"right"),height:dojo.html.getBorderExtent(node,"top")+dojo.html.getBorderExtent(node,"bottom")};
};
dojo.html.getBorderExtent=function(node,side){
return (dojo.html.getStyle(node,"border-"+side+"-style")=="none"?0:dojo.html.getPixelValue(node,"border-"+side+"-width"));
};
dojo.html.getMarginExtent=function(node,side){
return dojo.html._sumPixelValues(node,["margin-"+side],dojo.html.isPositionAbsolute(node));
};
dojo.html.getPaddingExtent=function(node,side){
return dojo.html._sumPixelValues(node,["padding-"+side],true);
};
dojo.html.getPadding=function(node){
return {width:dojo.html._sumPixelValues(node,["padding-left","padding-right"],true),height:dojo.html._sumPixelValues(node,["padding-top","padding-bottom"],true)};
};
dojo.html.getPadBorder=function(node){
var pad=dojo.html.getPadding(node);
var _636=dojo.html.getBorder(node);
return {width:pad.width+_636.width,height:pad.height+_636.height};
};
dojo.html.getBoxSizing=function(node){
var h=dojo.render.html;
var bs=dojo.html.boxSizing;
if((h.ie)||(h.opera)){
var cm=document["compatMode"];
if((cm=="BackCompat")||(cm=="QuirksMode")){
return bs.BORDER_BOX;
}else{
return bs.CONTENT_BOX;
}
}else{
if(arguments.length==0){
node=document.documentElement;
}
var _63b=dojo.html.getStyle(node,"-moz-box-sizing");
if(!_63b){
_63b=dojo.html.getStyle(node,"box-sizing");
}
return (_63b?_63b:bs.CONTENT_BOX);
}
};
dojo.html.isBorderBox=function(node){
return (dojo.html.getBoxSizing(node)==dojo.html.boxSizing.BORDER_BOX);
};
dojo.html.getBorderBox=function(node){
node=dojo.byId(node);
return {width:node.offsetWidth,height:node.offsetHeight};
};
dojo.html.getPaddingBox=function(node){
var box=dojo.html.getBorderBox(node);
var _640=dojo.html.getBorder(node);
return {width:box.width-_640.width,height:box.height-_640.height};
};
dojo.html.getContentBox=function(node){
node=dojo.byId(node);
var _642=dojo.html.getPadBorder(node);
return {width:node.offsetWidth-_642.width,height:node.offsetHeight-_642.height};
};
dojo.html.setContentBox=function(node,args){
node=dojo.byId(node);
var _645=0;
var _646=0;
var isbb=dojo.html.isBorderBox(node);
var _648=(isbb?dojo.html.getPadBorder(node):{width:0,height:0});
var ret={};
if(typeof args.width!="undefined"){
_645=args.width+_648.width;
ret.width=dojo.html.setPositivePixelValue(node,"width",_645);
}
if(typeof args.height!="undefined"){
_646=args.height+_648.height;
ret.height=dojo.html.setPositivePixelValue(node,"height",_646);
}
return ret;
};
dojo.html.getMarginBox=function(node){
var _64b=dojo.html.getBorderBox(node);
var _64c=dojo.html.getMargin(node);
return {width:_64b.width+_64c.width,height:_64b.height+_64c.height};
};
dojo.html.setMarginBox=function(node,args){
node=dojo.byId(node);
var _64f=0;
var _650=0;
var isbb=dojo.html.isBorderBox(node);
var _652=(!isbb?dojo.html.getPadBorder(node):{width:0,height:0});
var _653=dojo.html.getMargin(node);
var ret={};
if(typeof args.width!="undefined"){
_64f=args.width-_652.width;
_64f-=_653.width;
ret.width=dojo.html.setPositivePixelValue(node,"width",_64f);
}
if(typeof args.height!="undefined"){
_650=args.height-_652.height;
_650-=_653.height;
ret.height=dojo.html.setPositivePixelValue(node,"height",_650);
}
return ret;
};
dojo.html.getElementBox=function(node,type){
var bs=dojo.html.boxSizing;
switch(type){
case bs.MARGIN_BOX:
return dojo.html.getMarginBox(node);
case bs.BORDER_BOX:
return dojo.html.getBorderBox(node);
case bs.PADDING_BOX:
return dojo.html.getPaddingBox(node);
case bs.CONTENT_BOX:
default:
return dojo.html.getContentBox(node);
}
};
dojo.html.toCoordinateObject=dojo.html.toCoordinateArray=function(_658,_659,_65a){
if(!_658.nodeType&&!(_658 instanceof String||typeof _658=="string")&&("width" in _658||"height" in _658||"left" in _658||"x" in _658||"top" in _658||"y" in _658)){
var ret={left:_658.left||_658.x||0,top:_658.top||_658.y||0,width:_658.width||0,height:_658.height||0};
}else{
var node=dojo.byId(_658);
var pos=dojo.html.abs(node,_659,_65a);
var _65e=dojo.html.getMarginBox(node);
var ret={left:pos.left,top:pos.top,width:_65e.width,height:_65e.height};
}
ret.x=ret.left;
ret.y=ret.top;
return ret;
};
dojo.html.setMarginBoxWidth=dojo.html.setOuterWidth=function(node,_660){
return dojo.html._callDeprecated("setMarginBoxWidth","setMarginBox",arguments,"width");
};
dojo.html.setMarginBoxHeight=dojo.html.setOuterHeight=function(){
return dojo.html._callDeprecated("setMarginBoxHeight","setMarginBox",arguments,"height");
};
dojo.html.getMarginBoxWidth=dojo.html.getOuterWidth=function(){
return dojo.html._callDeprecated("getMarginBoxWidth","getMarginBox",arguments,null,"width");
};
dojo.html.getMarginBoxHeight=dojo.html.getOuterHeight=function(){
return dojo.html._callDeprecated("getMarginBoxHeight","getMarginBox",arguments,null,"height");
};
dojo.html.getTotalOffset=function(node,type,_663){
return dojo.html._callDeprecated("getTotalOffset","getAbsolutePosition",arguments,null,type);
};
dojo.html.getAbsoluteX=function(node,_665){
return dojo.html._callDeprecated("getAbsoluteX","getAbsolutePosition",arguments,null,"x");
};
dojo.html.getAbsoluteY=function(node,_667){
return dojo.html._callDeprecated("getAbsoluteY","getAbsolutePosition",arguments,null,"y");
};
dojo.html.totalOffsetLeft=function(node,_669){
return dojo.html._callDeprecated("totalOffsetLeft","getAbsolutePosition",arguments,null,"left");
};
dojo.html.totalOffsetTop=function(node,_66b){
return dojo.html._callDeprecated("totalOffsetTop","getAbsolutePosition",arguments,null,"top");
};
dojo.html.getMarginWidth=function(node){
return dojo.html._callDeprecated("getMarginWidth","getMargin",arguments,null,"width");
};
dojo.html.getMarginHeight=function(node){
return dojo.html._callDeprecated("getMarginHeight","getMargin",arguments,null,"height");
};
dojo.html.getBorderWidth=function(node){
return dojo.html._callDeprecated("getBorderWidth","getBorder",arguments,null,"width");
};
dojo.html.getBorderHeight=function(node){
return dojo.html._callDeprecated("getBorderHeight","getBorder",arguments,null,"height");
};
dojo.html.getPaddingWidth=function(node){
return dojo.html._callDeprecated("getPaddingWidth","getPadding",arguments,null,"width");
};
dojo.html.getPaddingHeight=function(node){
return dojo.html._callDeprecated("getPaddingHeight","getPadding",arguments,null,"height");
};
dojo.html.getPadBorderWidth=function(node){
return dojo.html._callDeprecated("getPadBorderWidth","getPadBorder",arguments,null,"width");
};
dojo.html.getPadBorderHeight=function(node){
return dojo.html._callDeprecated("getPadBorderHeight","getPadBorder",arguments,null,"height");
};
dojo.html.getBorderBoxWidth=dojo.html.getInnerWidth=function(){
return dojo.html._callDeprecated("getBorderBoxWidth","getBorderBox",arguments,null,"width");
};
dojo.html.getBorderBoxHeight=dojo.html.getInnerHeight=function(){
return dojo.html._callDeprecated("getBorderBoxHeight","getBorderBox",arguments,null,"height");
};
dojo.html.getContentBoxWidth=dojo.html.getContentWidth=function(){
return dojo.html._callDeprecated("getContentBoxWidth","getContentBox",arguments,null,"width");
};
dojo.html.getContentBoxHeight=dojo.html.getContentHeight=function(){
return dojo.html._callDeprecated("getContentBoxHeight","getContentBox",arguments,null,"height");
};
dojo.html.setContentBoxWidth=dojo.html.setContentWidth=function(node,_675){
return dojo.html._callDeprecated("setContentBoxWidth","setContentBox",arguments,"width");
};
dojo.html.setContentBoxHeight=dojo.html.setContentHeight=function(node,_677){
return dojo.html._callDeprecated("setContentBoxHeight","setContentBox",arguments,"height");
};
