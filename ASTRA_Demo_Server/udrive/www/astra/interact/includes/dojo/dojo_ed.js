dojo.provide("dojo.html.util");
dojo.html.getElementWindow=function(_678){
return dojo.html.getDocumentWindow(_678.ownerDocument);
};
dojo.html.getDocumentWindow=function(doc){
if(dojo.render.html.safari&&!doc._parentWindow){
var fix=function(win){
win.document._parentWindow=win;
for(var i=0;i<win.frames.length;i++){
fix(win.frames[i]);
}
};
fix(window.top);
}
if(dojo.render.html.ie&&window!==document.parentWindow&&!doc._parentWindow){
doc.parentWindow.execScript("document._parentWindow = window;","Javascript");
var win=doc._parentWindow;
doc._parentWindow=null;
return win;
}
return doc._parentWindow||doc.parentWindow||doc.defaultView;
};
dojo.html.getAbsolutePositionExt=function(node,_67f,_680,_681){
var _682=dojo.html.getElementWindow(node);
var ret=dojo.withGlobal(_682,"getAbsolutePosition",dojo.html,arguments);
var win=dojo.html.getElementWindow(node);
if(_681!=win&&win.frameElement){
var ext=dojo.html.getAbsolutePositionExt(win.frameElement,_67f,_680,_681);
ret.x+=ext.x;
ret.y+=ext.y;
}
ret.top=ret.y;
ret.left=ret.x;
return ret;
};
dojo.html.gravity=function(node,e){
node=dojo.byId(node);
var _688=dojo.html.getCursorPosition(e);
with(dojo.html){
var _689=getAbsolutePosition(node,true);
var bb=getBorderBox(node);
var _68b=_689.x+(bb.width/2);
var _68c=_689.y+(bb.height/2);
}
with(dojo.html.gravity){
return ((_688.x<_68b?WEST:EAST)|(_688.y<_68c?NORTH:SOUTH));
}
};
dojo.html.gravity.NORTH=1;
dojo.html.gravity.SOUTH=1<<1;
dojo.html.gravity.EAST=1<<2;
dojo.html.gravity.WEST=1<<3;
dojo.html.overElement=function(_68d,e){
_68d=dojo.byId(_68d);
var _68f=dojo.html.getCursorPosition(e);
var bb=dojo.html.getBorderBox(_68d);
var _691=dojo.html.getAbsolutePosition(_68d,true,dojo.html.boxSizing.BORDER_BOX);
var top=_691.y;
var _693=top+bb.height;
var left=_691.x;
var _695=left+bb.width;
return (_68f.x>=left&&_68f.x<=_695&&_68f.y>=top&&_68f.y<=_693);
};
dojo.html.renderedTextContent=function(node){
node=dojo.byId(node);
var _697="";
if(node==null){
return _697;
}
for(var i=0;i<node.childNodes.length;i++){
switch(node.childNodes[i].nodeType){
case 1:
case 5:
var _699="unknown";
try{
_699=dojo.html.getStyle(node.childNodes[i],"display");
}
catch(E){
}
switch(_699){
case "block":
case "list-item":
case "run-in":
case "table":
case "table-row-group":
case "table-header-group":
case "table-footer-group":
case "table-row":
case "table-column-group":
case "table-column":
case "table-cell":
case "table-caption":
_697+="\n";
_697+=dojo.html.renderedTextContent(node.childNodes[i]);
_697+="\n";
break;
case "none":
break;
default:
if(node.childNodes[i].tagName&&node.childNodes[i].tagName.toLowerCase()=="br"){
_697+="\n";
}else{
_697+=dojo.html.renderedTextContent(node.childNodes[i]);
}
break;
}
break;
case 3:
case 2:
case 4:
var text=node.childNodes[i].nodeValue;
var _69b="unknown";
try{
_69b=dojo.html.getStyle(node,"text-transform");
}
catch(E){
}
switch(_69b){
case "capitalize":
var _69c=text.split(" ");
for(var i=0;i<_69c.length;i++){
_69c[i]=_69c[i].charAt(0).toUpperCase()+_69c[i].substring(1);
}
text=_69c.join(" ");
break;
case "uppercase":
text=text.toUpperCase();
break;
case "lowercase":
text=text.toLowerCase();
break;
default:
break;
}
switch(_69b){
case "nowrap":
break;
case "pre-wrap":
break;
case "pre-line":
break;
case "pre":
break;
default:
text=text.replace(/\s+/," ");
if(/\s$/.test(_697)){
text.replace(/^\s/,"");
}
break;
}
_697+=text;
break;
default:
break;
}
}
return _697;
};
dojo.html.createNodesFromText=function(txt,trim){
if(trim){
txt=txt.replace(/^\s+|\s+$/g,"");
}
var tn=dojo.doc().createElement("div");
tn.style.visibility="hidden";
dojo.body().appendChild(tn);
var _6a0="none";
if((/^<t[dh][\s\r\n>]/i).test(txt.replace(/^\s+/))){
txt="<table><tbody><tr>"+txt+"</tr></tbody></table>";
_6a0="cell";
}else{
if((/^<tr[\s\r\n>]/i).test(txt.replace(/^\s+/))){
txt="<table><tbody>"+txt+"</tbody></table>";
_6a0="row";
}else{
if((/^<(thead|tbody|tfoot)[\s\r\n>]/i).test(txt.replace(/^\s+/))){
txt="<table>"+txt+"</table>";
_6a0="section";
}
}
}
tn.innerHTML=txt;
if(tn["normalize"]){
tn.normalize();
}
var _6a1=null;
switch(_6a0){
case "cell":
_6a1=tn.getElementsByTagName("tr")[0];
break;
case "row":
_6a1=tn.getElementsByTagName("tbody")[0];
break;
case "section":
_6a1=tn.getElementsByTagName("table")[0];
break;
default:
_6a1=tn;
break;
}
var _6a2=[];
for(var x=0;x<_6a1.childNodes.length;x++){
_6a2.push(_6a1.childNodes[x].cloneNode(true));
}
tn.style.display="none";
dojo.html.destroyNode(tn);
return _6a2;
};
dojo.html.placeOnScreen=function(node,_6a5,_6a6,_6a7,_6a8,_6a9,_6aa){
if(_6a5 instanceof Array||typeof _6a5=="array"){
_6aa=_6a9;
_6a9=_6a8;
_6a8=_6a7;
_6a7=_6a6;
_6a6=_6a5[1];
_6a5=_6a5[0];
}
if(_6a9 instanceof String||typeof _6a9=="string"){
_6a9=_6a9.split(",");
}
if(!isNaN(_6a7)){
_6a7=[Number(_6a7),Number(_6a7)];
}else{
if(!(_6a7 instanceof Array||typeof _6a7=="array")){
_6a7=[0,0];
}
}
var _6ab=dojo.html.getScroll().offset;
var view=dojo.html.getViewport();
node=dojo.byId(node);
var _6ad=node.style.display;
node.style.display="";
var bb=dojo.html.getBorderBox(node);
var w=bb.width;
var h=bb.height;
node.style.display=_6ad;
if(!(_6a9 instanceof Array||typeof _6a9=="array")){
_6a9=["TL"];
}
var _6b1,_6b2,_6b3=Infinity,_6b4;
for(var _6b5=0;_6b5<_6a9.length;++_6b5){
var _6b6=_6a9[_6b5];
var _6b7=true;
var tryX=_6a5-(_6b6.charAt(1)=="L"?0:w)+_6a7[0]*(_6b6.charAt(1)=="L"?1:-1);
var tryY=_6a6-(_6b6.charAt(0)=="T"?0:h)+_6a7[1]*(_6b6.charAt(0)=="T"?1:-1);
if(_6a8){
tryX-=_6ab.x;
tryY-=_6ab.y;
}
if(tryX<0){
tryX=0;
_6b7=false;
}
if(tryY<0){
tryY=0;
_6b7=false;
}
var x=tryX+w;
if(x>view.width){
x=view.width-w;
_6b7=false;
}else{
x=tryX;
}
x=Math.max(_6a7[0],x)+_6ab.x;
var y=tryY+h;
if(y>view.height){
y=view.height-h;
_6b7=false;
}else{
y=tryY;
}
y=Math.max(_6a7[1],y)+_6ab.y;
if(_6b7){
_6b1=x;
_6b2=y;
_6b3=0;
_6b4=_6b6;
break;
}else{
var dist=Math.pow(x-tryX-_6ab.x,2)+Math.pow(y-tryY-_6ab.y,2);
if(_6b3>dist){
_6b3=dist;
_6b1=x;
_6b2=y;
_6b4=_6b6;
}
}
}
if(!_6aa){
node.style.left=_6b1+"px";
node.style.top=_6b2+"px";
}
return {left:_6b1,top:_6b2,x:_6b1,y:_6b2,dist:_6b3,corner:_6b4};
};
dojo.html.placeOnScreenAroundElement=function(node,_6be,_6bf,_6c0,_6c1,_6c2){
var best,_6c4=Infinity;
_6be=dojo.byId(_6be);
var _6c5=_6be.style.display;
_6be.style.display="";
var mb=dojo.html.getElementBox(_6be,_6c0);
var _6c7=mb.width;
var _6c8=mb.height;
var _6c9=dojo.html.getAbsolutePosition(_6be,true,_6c0);
_6be.style.display=_6c5;
for(var _6ca in _6c1){
var pos,_6cc,_6cd;
var _6ce=_6c1[_6ca];
_6cc=_6c9.x+(_6ca.charAt(1)=="L"?0:_6c7);
_6cd=_6c9.y+(_6ca.charAt(0)=="T"?0:_6c8);
pos=dojo.html.placeOnScreen(node,_6cc,_6cd,_6bf,true,_6ce,true);
if(pos.dist==0){
best=pos;
break;
}else{
if(_6c4>pos.dist){
_6c4=pos.dist;
best=pos;
}
}
}
if(!_6c2){
node.style.left=best.left+"px";
node.style.top=best.top+"px";
}
return best;
};
dojo.html.scrollIntoView=function(node){
if(!node){
return;
}
if(dojo.render.html.ie){
if(dojo.html.getBorderBox(node.parentNode).height<=node.parentNode.scrollHeight){
node.scrollIntoView(false);
}
}else{
if(dojo.render.html.mozilla){
node.scrollIntoView(false);
}else{
var _6d0=node.parentNode;
var _6d1=_6d0.scrollTop+dojo.html.getBorderBox(_6d0).height;
var _6d2=node.offsetTop+dojo.html.getMarginBox(node).height;
if(_6d1<_6d2){
_6d0.scrollTop+=(_6d2-_6d1);
}else{
if(_6d0.scrollTop>node.offsetTop){
_6d0.scrollTop-=(_6d0.scrollTop-node.offsetTop);
}
}
}
}
};
dojo.provide("dojo.gfx.color");
dojo.gfx.color.Color=function(r,g,b,a){
if(dojo.lang.isArray(r)){
this.r=r[0];
this.g=r[1];
this.b=r[2];
this.a=r[3]||1;
}else{
if(dojo.lang.isString(r)){
var rgb=dojo.gfx.color.extractRGB(r);
this.r=rgb[0];
this.g=rgb[1];
this.b=rgb[2];
this.a=g||1;
}else{
if(r instanceof dojo.gfx.color.Color){
this.r=r.r;
this.b=r.b;
this.g=r.g;
this.a=r.a;
}else{
this.r=r;
this.g=g;
this.b=b;
this.a=a;
}
}
}
};
dojo.gfx.color.Color.fromArray=function(arr){
return new dojo.gfx.color.Color(arr[0],arr[1],arr[2],arr[3]);
};
dojo.extend(dojo.gfx.color.Color,{toRgb:function(_6d9){
if(_6d9){
return this.toRgba();
}else{
return [this.r,this.g,this.b];
}
},toRgba:function(){
return [this.r,this.g,this.b,this.a];
},toHex:function(){
return dojo.gfx.color.rgb2hex(this.toRgb());
},toCss:function(){
return "rgb("+this.toRgb().join()+")";
},toString:function(){
return this.toHex();
},blend:function(_6da,_6db){
var rgb=null;
if(dojo.lang.isArray(_6da)){
rgb=_6da;
}else{
if(_6da instanceof dojo.gfx.color.Color){
rgb=_6da.toRgb();
}else{
rgb=new dojo.gfx.color.Color(_6da).toRgb();
}
}
return dojo.gfx.color.blend(this.toRgb(),rgb,_6db);
}});
dojo.gfx.color.named={white:[255,255,255],black:[0,0,0],red:[255,0,0],green:[0,255,0],lime:[0,255,0],blue:[0,0,255],navy:[0,0,128],gray:[128,128,128],silver:[192,192,192]};
dojo.gfx.color.blend=function(a,b,_6df){
if(typeof a=="string"){
return dojo.gfx.color.blendHex(a,b,_6df);
}
if(!_6df){
_6df=0;
}
_6df=Math.min(Math.max(-1,_6df),1);
_6df=((_6df+1)/2);
var c=[];
for(var x=0;x<3;x++){
c[x]=parseInt(b[x]+((a[x]-b[x])*_6df));
}
return c;
};
dojo.gfx.color.blendHex=function(a,b,_6e4){
return dojo.gfx.color.rgb2hex(dojo.gfx.color.blend(dojo.gfx.color.hex2rgb(a),dojo.gfx.color.hex2rgb(b),_6e4));
};
dojo.gfx.color.extractRGB=function(_6e5){
var hex="0123456789abcdef";
_6e5=_6e5.toLowerCase();
if(_6e5.indexOf("rgb")==0){
var _6e7=_6e5.match(/rgba*\((\d+), *(\d+), *(\d+)/i);
var ret=_6e7.splice(1,3);
return ret;
}else{
var _6e9=dojo.gfx.color.hex2rgb(_6e5);
if(_6e9){
return _6e9;
}else{
return dojo.gfx.color.named[_6e5]||[255,255,255];
}
}
};
dojo.gfx.color.hex2rgb=function(hex){
var _6eb="0123456789ABCDEF";
var rgb=new Array(3);
if(hex.indexOf("#")==0){
hex=hex.substring(1);
}
hex=hex.toUpperCase();
if(hex.replace(new RegExp("["+_6eb+"]","g"),"")!=""){
return null;
}
if(hex.length==3){
rgb[0]=hex.charAt(0)+hex.charAt(0);
rgb[1]=hex.charAt(1)+hex.charAt(1);
rgb[2]=hex.charAt(2)+hex.charAt(2);
}else{
rgb[0]=hex.substring(0,2);
rgb[1]=hex.substring(2,4);
rgb[2]=hex.substring(4);
}
for(var i=0;i<rgb.length;i++){
rgb[i]=_6eb.indexOf(rgb[i].charAt(0))*16+_6eb.indexOf(rgb[i].charAt(1));
}
return rgb;
};
dojo.gfx.color.rgb2hex=function(r,g,b){
if(dojo.lang.isArray(r)){
g=r[1]||0;
b=r[2]||0;
r=r[0]||0;
}
var ret=dojo.lang.map([r,g,b],function(x){
x=new Number(x);
var s=x.toString(16);
while(s.length<2){
s="0"+s;
}
return s;
});
ret.unshift("#");
return ret.join("");
};
dojo.provide("dojo.lfx.Animation");
dojo.lfx.Line=function(_6f4,end){
this.start=_6f4;
this.end=end;
if(dojo.lang.isArray(_6f4)){
var diff=[];
dojo.lang.forEach(this.start,function(s,i){
diff[i]=this.end[i]-s;
},this);
this.getValue=function(n){
var res=[];
dojo.lang.forEach(this.start,function(s,i){
res[i]=(diff[i]*n)+s;
},this);
return res;
};
}else{
var diff=end-_6f4;
this.getValue=function(n){
return (diff*n)+this.start;
};
}
};
dojo.lfx.easeDefault=function(n){
if(dojo.render.html.khtml){
return (parseFloat("0.5")+((Math.sin((n+parseFloat("1.5"))*Math.PI))/2));
}else{
return (0.5+((Math.sin((n+1.5)*Math.PI))/2));
}
};
dojo.lfx.easeIn=function(n){
return Math.pow(n,3);
};
dojo.lfx.easeOut=function(n){
return (1-Math.pow(1-n,3));
};
dojo.lfx.easeInOut=function(n){
return ((3*Math.pow(n,2))-(2*Math.pow(n,3)));
};
dojo.lfx.IAnimation=function(){
};
dojo.lang.extend(dojo.lfx.IAnimation,{curve:null,duration:1000,easing:null,repeatCount:0,rate:25,handler:null,beforeBegin:null,onBegin:null,onAnimate:null,onEnd:null,onPlay:null,onPause:null,onStop:null,play:null,pause:null,stop:null,connect:function(evt,_703,_704){
if(!_704){
_704=_703;
_703=this;
}
_704=dojo.lang.hitch(_703,_704);
var _705=this[evt]||function(){
};
this[evt]=function(){
var ret=_705.apply(this,arguments);
_704.apply(this,arguments);
return ret;
};
return this;
},fire:function(evt,args){
if(this[evt]){
this[evt].apply(this,(args||[]));
}
return this;
},repeat:function(_709){
this.repeatCount=_709;
return this;
},_active:false,_paused:false});
dojo.lfx.Animation=function(_70a,_70b,_70c,_70d,_70e,rate){
dojo.lfx.IAnimation.call(this);
if(dojo.lang.isNumber(_70a)||(!_70a&&_70b.getValue)){
rate=_70e;
_70e=_70d;
_70d=_70c;
_70c=_70b;
_70b=_70a;
_70a=null;
}else{
if(_70a.getValue||dojo.lang.isArray(_70a)){
rate=_70d;
_70e=_70c;
_70d=_70b;
_70c=_70a;
_70b=null;
_70a=null;
}
}
if(dojo.lang.isArray(_70c)){
this.curve=new dojo.lfx.Line(_70c[0],_70c[1]);
}else{
this.curve=_70c;
}
if(_70b!=null&&_70b>0){
this.duration=_70b;
}
if(_70e){
this.repeatCount=_70e;
}
if(rate){
this.rate=rate;
}
if(_70a){
dojo.lang.forEach(["handler","beforeBegin","onBegin","onEnd","onPlay","onStop","onAnimate"],function(item){
if(_70a[item]){
this.connect(item,_70a[item]);
}
},this);
}
if(_70d&&dojo.lang.isFunction(_70d)){
this.easing=_70d;
}
};
dojo.inherits(dojo.lfx.Animation,dojo.lfx.IAnimation);
dojo.lang.extend(dojo.lfx.Animation,{_startTime:null,_endTime:null,_timer:null,_percent:0,_startRepeatCount:0,play:function(_711,_712){
if(_712){
clearTimeout(this._timer);
this._active=false;
this._paused=false;
this._percent=0;
}else{
if(this._active&&!this._paused){
return this;
}
}
this.fire("handler",["beforeBegin"]);
this.fire("beforeBegin");
if(_711>0){
setTimeout(dojo.lang.hitch(this,function(){
this.play(null,_712);
}),_711);
return this;
}
this._startTime=new Date().valueOf();
if(this._paused){
this._startTime-=(this.duration*this._percent/100);
}
this._endTime=this._startTime+this.duration;
this._active=true;
this._paused=false;
var step=this._percent/100;
var _714=this.curve.getValue(step);
if(this._percent==0){
if(!this._startRepeatCount){
this._startRepeatCount=this.repeatCount;
}
this.fire("handler",["begin",_714]);
this.fire("onBegin",[_714]);
}
this.fire("handler",["play",_714]);
this.fire("onPlay",[_714]);
this._cycle();
return this;
},pause:function(){
clearTimeout(this._timer);
if(!this._active){
return this;
}
this._paused=true;
var _715=this.curve.getValue(this._percent/100);
this.fire("handler",["pause",_715]);
this.fire("onPause",[_715]);
return this;
},gotoPercent:function(pct,_717){
clearTimeout(this._timer);
this._active=true;
this._paused=true;
this._percent=pct;
if(_717){
this.play();
}
return this;
},stop:function(_718){
clearTimeout(this._timer);
var step=this._percent/100;
if(_718){
step=1;
}
var _71a=this.curve.getValue(step);
this.fire("handler",["stop",_71a]);
this.fire("onStop",[_71a]);
this._active=false;
this._paused=false;
return this;
},status:function(){
if(this._active){
return this._paused?"paused":"playing";
}else{
return "stopped";
}
return this;
},_cycle:function(){
clearTimeout(this._timer);
if(this._active){
var curr=new Date().valueOf();
var step=(curr-this._startTime)/(this._endTime-this._startTime);
if(step>=1){
step=1;
this._percent=100;
}else{
this._percent=step*100;
}
if((this.easing)&&(dojo.lang.isFunction(this.easing))){
step=this.easing(step);
}
var _71d=this.curve.getValue(step);
this.fire("handler",["animate",_71d]);
this.fire("onAnimate",[_71d]);
if(step<1){
this._timer=setTimeout(dojo.lang.hitch(this,"_cycle"),this.rate);
}else{
this._active=false;
this.fire("handler",["end"]);
this.fire("onEnd");
if(this.repeatCount>0){
this.repeatCount--;
this.play(null,true);
}else{
if(this.repeatCount==-1){
this.play(null,true);
}else{
if(this._startRepeatCount){
this.repeatCount=this._startRepeatCount;
this._startRepeatCount=0;
}
}
}
}
}
return this;
}});
dojo.lfx.Combine=function(_71e){
dojo.lfx.IAnimation.call(this);
this._anims=[];
this._animsEnded=0;
var _71f=arguments;
if(_71f.length==1&&(dojo.lang.isArray(_71f[0])||dojo.lang.isArrayLike(_71f[0]))){
_71f=_71f[0];
}
dojo.lang.forEach(_71f,function(anim){
this._anims.push(anim);
anim.connect("onEnd",dojo.lang.hitch(this,"_onAnimsEnded"));
},this);
};
dojo.inherits(dojo.lfx.Combine,dojo.lfx.IAnimation);
dojo.lang.extend(dojo.lfx.Combine,{_animsEnded:0,play:function(_721,_722){
if(!this._anims.length){
return this;
}
this.fire("beforeBegin");
if(_721>0){
setTimeout(dojo.lang.hitch(this,function(){
this.play(null,_722);
}),_721);
return this;
}
if(_722||this._anims[0].percent==0){
this.fire("onBegin");
}
this.fire("onPlay");
this._animsCall("play",null,_722);
return this;
},pause:function(){
this.fire("onPause");
this._animsCall("pause");
return this;
},stop:function(_723){
this.fire("onStop");
this._animsCall("stop",_723);
return this;
},_onAnimsEnded:function(){
this._animsEnded++;
if(this._animsEnded>=this._anims.length){
this.fire("onEnd");
}
return this;
},_animsCall:function(_724){
var args=[];
if(arguments.length>1){
for(var i=1;i<arguments.length;i++){
args.push(arguments[i]);
}
}
var _727=this;
dojo.lang.forEach(this._anims,function(anim){
anim[_724](args);
},_727);
return this;
}});
dojo.lfx.Chain=function(_729){
dojo.lfx.IAnimation.call(this);
this._anims=[];
this._currAnim=-1;
var _72a=arguments;
if(_72a.length==1&&(dojo.lang.isArray(_72a[0])||dojo.lang.isArrayLike(_72a[0]))){
_72a=_72a[0];
}
var _72b=this;
dojo.lang.forEach(_72a,function(anim,i,_72e){
this._anims.push(anim);
if(i<_72e.length-1){
anim.connect("onEnd",dojo.lang.hitch(this,"_playNext"));
}else{
anim.connect("onEnd",dojo.lang.hitch(this,function(){
this.fire("onEnd");
}));
}
},this);
};
dojo.inherits(dojo.lfx.Chain,dojo.lfx.IAnimation);
dojo.lang.extend(dojo.lfx.Chain,{_currAnim:-1,play:function(_72f,_730){
if(!this._anims.length){
return this;
}
if(_730||!this._anims[this._currAnim]){
this._currAnim=0;
}
var _731=this._anims[this._currAnim];
this.fire("beforeBegin");
if(_72f>0){
setTimeout(dojo.lang.hitch(this,function(){
this.play(null,_730);
}),_72f);
return this;
}
if(_731){
if(this._currAnim==0){
this.fire("handler",["begin",this._currAnim]);
this.fire("onBegin",[this._currAnim]);
}
this.fire("onPlay",[this._currAnim]);
_731.play(null,_730);
}
return this;
},pause:function(){
if(this._anims[this._currAnim]){
this._anims[this._currAnim].pause();
this.fire("onPause",[this._currAnim]);
}
return this;
},playPause:function(){
if(this._anims.length==0){
return this;
}
if(this._currAnim==-1){
this._currAnim=0;
}
var _732=this._anims[this._currAnim];
if(_732){
if(!_732._active||_732._paused){
this.play();
}else{
this.pause();
}
}
return this;
},stop:function(){
var _733=this._anims[this._currAnim];
if(_733){
_733.stop();
this.fire("onStop",[this._currAnim]);
}
return _733;
},_playNext:function(){
if(this._currAnim==-1||this._anims.length==0){
return this;
}
this._currAnim++;
if(this._anims[this._currAnim]){
this._anims[this._currAnim].play(null,true);
}
return this;
}});
dojo.lfx.combine=function(_734){
var _735=arguments;
if(dojo.lang.isArray(arguments[0])){
_735=arguments[0];
}
if(_735.length==1){
return _735[0];
}
return new dojo.lfx.Combine(_735);
};
dojo.lfx.chain=function(_736){
var _737=arguments;
if(dojo.lang.isArray(arguments[0])){
_737=arguments[0];
}
if(_737.length==1){
return _737[0];
}
return new dojo.lfx.Chain(_737);
};
dojo.provide("dojo.html.color");
dojo.html.getBackgroundColor=function(node){
node=dojo.byId(node);
var _739;
do{
_739=dojo.html.getStyle(node,"background-color");
if(_739.toLowerCase()=="rgba(0, 0, 0, 0)"){
_739="transparent";
}
if(node==document.getElementsByTagName("body")[0]){
node=null;
break;
}
node=node.parentNode;
}while(node&&dojo.lang.inArray(["transparent",""],_739));
if(_739=="transparent"){
_739=[255,255,255,0];
}else{
_739=dojo.gfx.color.extractRGB(_739);
}
return _739;
};
dojo.provide("dojo.lfx.html");
dojo.lfx.html._byId=function(_73a){
if(!_73a){
return [];
}
if(dojo.lang.isArrayLike(_73a)){
if(!_73a.alreadyChecked){
var n=[];
dojo.lang.forEach(_73a,function(node){
n.push(dojo.byId(node));
});
n.alreadyChecked=true;
return n;
}else{
return _73a;
}
}else{
var n=[];
n.push(dojo.byId(_73a));
n.alreadyChecked=true;
return n;
}
};
dojo.lfx.html.propertyAnimation=function(_73d,_73e,_73f,_740,_741){
_73d=dojo.lfx.html._byId(_73d);
var _742={"propertyMap":_73e,"nodes":_73d,"duration":_73f,"easing":_740||dojo.lfx.easeDefault};
var _743=function(args){
if(args.nodes.length==1){
var pm=args.propertyMap;
if(!dojo.lang.isArray(args.propertyMap)){
var parr=[];
for(var _747 in pm){
pm[_747].property=_747;
parr.push(pm[_747]);
}
pm=args.propertyMap=parr;
}
dojo.lang.forEach(pm,function(prop){
if(dj_undef("start",prop)){
if(prop.property!="opacity"){
prop.start=parseInt(dojo.html.getComputedStyle(args.nodes[0],prop.property));
}else{
prop.start=dojo.html.getOpacity(args.nodes[0]);
}
}
});
}
};
var _749=function(_74a){
var _74b=[];
dojo.lang.forEach(_74a,function(c){
_74b.push(Math.round(c));
});
return _74b;
};
var _74d=function(n,_74f){
n=dojo.byId(n);
if(!n||!n.style){
return;
}
for(var s in _74f){
try{
if(s=="opacity"){
dojo.html.setOpacity(n,_74f[s]);
}else{
n.style[s]=_74f[s];
}
}
catch(e){
dojo.debug(e);
}
}
};
var _751=function(_752){
this._properties=_752;
this.diffs=new Array(_752.length);
dojo.lang.forEach(_752,function(prop,i){
if(dojo.lang.isFunction(prop.start)){
prop.start=prop.start(prop,i);
}
if(dojo.lang.isFunction(prop.end)){
prop.end=prop.end(prop,i);
}
if(dojo.lang.isArray(prop.start)){
this.diffs[i]=null;
}else{
if(prop.start instanceof dojo.gfx.color.Color){
prop.startRgb=prop.start.toRgb();
prop.endRgb=prop.end.toRgb();
}else{
this.diffs[i]=prop.end-prop.start;
}
}
},this);
this.getValue=function(n){
var ret={};
dojo.lang.forEach(this._properties,function(prop,i){
var _759=null;
if(dojo.lang.isArray(prop.start)){
}else{
if(prop.start instanceof dojo.gfx.color.Color){
_759=(prop.units||"rgb")+"(";
for(var j=0;j<prop.startRgb.length;j++){
_759+=Math.round(((prop.endRgb[j]-prop.startRgb[j])*n)+prop.startRgb[j])+(j<prop.startRgb.length-1?",":"");
}
_759+=")";
}else{
_759=((this.diffs[i])*n)+prop.start+(prop.property!="opacity"?prop.units||"px":"");
}
}
ret[dojo.html.toCamelCase(prop.property)]=_759;
},this);
return ret;
};
};
var anim=new dojo.lfx.Animation({beforeBegin:function(){
_743(_742);
anim.curve=new _751(_742.propertyMap);
},onAnimate:function(_75c){
dojo.lang.forEach(_742.nodes,function(node){
_74d(node,_75c);
});
}},_742.duration,null,_742.easing);
if(_741){
for(var x in _741){
if(dojo.lang.isFunction(_741[x])){
anim.connect(x,anim,_741[x]);
}
}
}
return anim;
};
dojo.lfx.html._makeFadeable=function(_75f){
var _760=function(node){
if(dojo.render.html.ie){
if((node.style.zoom.length==0)&&(dojo.html.getStyle(node,"zoom")=="normal")){
node.style.zoom="1";
}
if((node.style.width.length==0)&&(dojo.html.getStyle(node,"width")=="auto")){
node.style.width="auto";
}
}
};
if(dojo.lang.isArrayLike(_75f)){
dojo.lang.forEach(_75f,_760);
}else{
_760(_75f);
}
};
dojo.lfx.html.fade=function(_762,_763,_764,_765,_766){
_762=dojo.lfx.html._byId(_762);
var _767={property:"opacity"};
if(!dj_undef("start",_763)){
_767.start=_763.start;
}else{
_767.start=function(){
return dojo.html.getOpacity(_762[0]);
};
}
if(!dj_undef("end",_763)){
_767.end=_763.end;
}else{
dojo.raise("dojo.lfx.html.fade needs an end value");
}
var anim=dojo.lfx.propertyAnimation(_762,[_767],_764,_765);
anim.connect("beforeBegin",function(){
dojo.lfx.html._makeFadeable(_762);
});
if(_766){
anim.connect("onEnd",function(){
_766(_762,anim);
});
}
return anim;
};
dojo.lfx.html.fadeIn=function(_769,_76a,_76b,_76c){
return dojo.lfx.html.fade(_769,{end:1},_76a,_76b,_76c);
};
dojo.lfx.html.fadeOut=function(_76d,_76e,_76f,_770){
return dojo.lfx.html.fade(_76d,{end:0},_76e,_76f,_770);
};
dojo.lfx.html.fadeShow=function(_771,_772,_773,_774){
_771=dojo.lfx.html._byId(_771);
dojo.lang.forEach(_771,function(node){
dojo.html.setOpacity(node,0);
});
var anim=dojo.lfx.html.fadeIn(_771,_772,_773,_774);
anim.connect("beforeBegin",function(){
if(dojo.lang.isArrayLike(_771)){
dojo.lang.forEach(_771,dojo.html.show);
}else{
dojo.html.show(_771);
}
});
return anim;
};
dojo.lfx.html.fadeHide=function(_777,_778,_779,_77a){
var anim=dojo.lfx.html.fadeOut(_777,_778,_779,function(){
if(dojo.lang.isArrayLike(_777)){
dojo.lang.forEach(_777,dojo.html.hide);
}else{
dojo.html.hide(_777);
}
if(_77a){
_77a(_777,anim);
}
});
return anim;
};
dojo.lfx.html.wipeIn=function(_77c,_77d,_77e,_77f){
_77c=dojo.lfx.html._byId(_77c);
var _780=[];
dojo.lang.forEach(_77c,function(node){
var _782={};
var _783,_784,_785;
with(node.style){
_783=top;
_784=left;
_785=position;
top="-9999px";
left="-9999px";
position="absolute";
display="";
}
var _786=dojo.html.getBorderBox(node).height;
with(node.style){
top=_783;
left=_784;
position=_785;
display="none";
}
var anim=dojo.lfx.propertyAnimation(node,{"height":{start:1,end:function(){
return _786;
}}},_77d,_77e);
anim.connect("beforeBegin",function(){
_782.overflow=node.style.overflow;
_782.height=node.style.height;
with(node.style){
overflow="hidden";
_786="1px";
}
dojo.html.show(node);
});
anim.connect("onEnd",function(){
with(node.style){
overflow=_782.overflow;
_786=_782.height;
}
if(_77f){
_77f(node,anim);
}
});
_780.push(anim);
});
return dojo.lfx.combine(_780);
};
dojo.lfx.html.wipeOut=function(_788,_789,_78a,_78b){
_788=dojo.lfx.html._byId(_788);
var _78c=[];
dojo.lang.forEach(_788,function(node){
var _78e={};
var anim=dojo.lfx.propertyAnimation(node,{"height":{start:function(){
return dojo.html.getContentBox(node).height;
},end:1}},_789,_78a,{"beforeBegin":function(){
_78e.overflow=node.style.overflow;
_78e.height=node.style.height;
with(node.style){
overflow="hidden";
}
dojo.html.show(node);
},"onEnd":function(){
dojo.html.hide(node);
with(node.style){
overflow=_78e.overflow;
height=_78e.height;
}
if(_78b){
_78b(node,anim);
}
}});
_78c.push(anim);
});
return dojo.lfx.combine(_78c);
};
dojo.lfx.html.slideTo=function(_790,_791,_792,_793,_794){
_790=dojo.lfx.html._byId(_790);
var _795=[];
var _796=dojo.html.getComputedStyle;
dojo.lang.forEach(_790,function(node){
var top=null;
var left=null;
var init=(function(){
var _79b=node;
return function(){
var pos=_796(_79b,"position");
top=(pos=="absolute"?node.offsetTop:parseInt(_796(node,"top"))||0);
left=(pos=="absolute"?node.offsetLeft:parseInt(_796(node,"left"))||0);
if(!dojo.lang.inArray(["absolute","relative"],pos)){
var ret=dojo.html.abs(_79b,true);
dojo.html.setStyleAttributes(_79b,"position:absolute;top:"+ret.y+"px;left:"+ret.x+"px;");
top=ret.y;
left=ret.x;
}
};
})();
init();
var anim=dojo.lfx.propertyAnimation(node,{"top":{start:top,end:(_791.top||0)},"left":{start:left,end:(_791.left||0)}},_792,_793,{"beforeBegin":init});
if(_794){
anim.connect("onEnd",function(){
_794(_790,anim);
});
}
_795.push(anim);
});
return dojo.lfx.combine(_795);
};
dojo.lfx.html.slideBy=function(_79f,_7a0,_7a1,_7a2,_7a3){
_79f=dojo.lfx.html._byId(_79f);
var _7a4=[];
var _7a5=dojo.html.getComputedStyle;
dojo.lang.forEach(_79f,function(node){
var top=null;
var left=null;
var init=(function(){
var _7aa=node;
return function(){
var pos=_7a5(_7aa,"position");
top=(pos=="absolute"?node.offsetTop:parseInt(_7a5(node,"top"))||0);
left=(pos=="absolute"?node.offsetLeft:parseInt(_7a5(node,"left"))||0);
if(!dojo.lang.inArray(["absolute","relative"],pos)){
var ret=dojo.html.abs(_7aa,true);
dojo.html.setStyleAttributes(_7aa,"position:absolute;top:"+ret.y+"px;left:"+ret.x+"px;");
top=ret.y;
left=ret.x;
}
};
})();
init();
var anim=dojo.lfx.propertyAnimation(node,{"top":{start:top,end:top+(_7a0.top||0)},"left":{start:left,end:left+(_7a0.left||0)}},_7a1,_7a2).connect("beforeBegin",init);
if(_7a3){
anim.connect("onEnd",function(){
_7a3(_79f,anim);
});
}
_7a4.push(anim);
});
return dojo.lfx.combine(_7a4);
};
dojo.lfx.html.explode=function(_7ae,_7af,_7b0,_7b1,_7b2){
var h=dojo.html;
_7ae=dojo.byId(_7ae);
_7af=dojo.byId(_7af);
var _7b4=h.toCoordinateObject(_7ae,true);
var _7b5=document.createElement("div");
h.copyStyle(_7b5,_7af);
if(_7af.explodeClassName){
_7b5.className=_7af.explodeClassName;
}
with(_7b5.style){
position="absolute";
display="none";
var _7b6=h.getStyle(_7ae,"background-color");
backgroundColor=_7b6?_7b6.toLowerCase():"transparent";
backgroundColor=(backgroundColor=="transparent")?"rgb(221, 221, 221)":backgroundColor;
}
dojo.body().appendChild(_7b5);
with(_7af.style){
visibility="hidden";
display="block";
}
var _7b7=h.toCoordinateObject(_7af,true);
with(_7af.style){
display="none";
visibility="visible";
}
var _7b8={opacity:{start:0.5,end:1}};
dojo.lang.forEach(["height","width","top","left"],function(type){
_7b8[type]={start:_7b4[type],end:_7b7[type]};
});
var anim=new dojo.lfx.propertyAnimation(_7b5,_7b8,_7b0,_7b1,{"beforeBegin":function(){
h.setDisplay(_7b5,"block");
},"onEnd":function(){
h.setDisplay(_7af,"block");
_7b5.parentNode.removeChild(_7b5);
}});
if(_7b2){
anim.connect("onEnd",function(){
_7b2(_7af,anim);
});
}
return anim;
};
dojo.lfx.html.implode=function(_7bb,end,_7bd,_7be,_7bf){
var h=dojo.html;
_7bb=dojo.byId(_7bb);
end=dojo.byId(end);
var _7c1=dojo.html.toCoordinateObject(_7bb,true);
var _7c2=dojo.html.toCoordinateObject(end,true);
var _7c3=document.createElement("div");
dojo.html.copyStyle(_7c3,_7bb);
if(_7bb.explodeClassName){
_7c3.className=_7bb.explodeClassName;
}
dojo.html.setOpacity(_7c3,0.3);
with(_7c3.style){
position="absolute";
display="none";
backgroundColor=h.getStyle(_7bb,"background-color").toLowerCase();
}
dojo.body().appendChild(_7c3);
var _7c4={opacity:{start:1,end:0.5}};
dojo.lang.forEach(["height","width","top","left"],function(type){
_7c4[type]={start:_7c1[type],end:_7c2[type]};
});
var anim=new dojo.lfx.propertyAnimation(_7c3,_7c4,_7bd,_7be,{"beforeBegin":function(){
dojo.html.hide(_7bb);
dojo.html.show(_7c3);
},"onEnd":function(){
_7c3.parentNode.removeChild(_7c3);
}});
if(_7bf){
anim.connect("onEnd",function(){
_7bf(_7bb,anim);
});
}
return anim;
};
dojo.lfx.html.highlight=function(_7c7,_7c8,_7c9,_7ca,_7cb){
_7c7=dojo.lfx.html._byId(_7c7);
var _7cc=[];
dojo.lang.forEach(_7c7,function(node){
var _7ce=dojo.html.getBackgroundColor(node);
var bg=dojo.html.getStyle(node,"background-color").toLowerCase();
var _7d0=dojo.html.getStyle(node,"background-image");
var _7d1=(bg=="transparent"||bg=="rgba(0, 0, 0, 0)");
while(_7ce.length>3){
_7ce.pop();
}
var rgb=new dojo.gfx.color.Color(_7c8);
var _7d3=new dojo.gfx.color.Color(_7ce);
var anim=dojo.lfx.propertyAnimation(node,{"background-color":{start:rgb,end:_7d3}},_7c9,_7ca,{"beforeBegin":function(){
if(_7d0){
node.style.backgroundImage="none";
}
node.style.backgroundColor="rgb("+rgb.toRgb().join(",")+")";
},"onEnd":function(){
if(_7d0){
node.style.backgroundImage=_7d0;
}
if(_7d1){
node.style.backgroundColor="transparent";
}
if(_7cb){
_7cb(node,anim);
}
}});
_7cc.push(anim);
});
return dojo.lfx.combine(_7cc);
};
dojo.lfx.html.unhighlight=function(_7d5,_7d6,_7d7,_7d8,_7d9){
_7d5=dojo.lfx.html._byId(_7d5);
var _7da=[];
dojo.lang.forEach(_7d5,function(node){
var _7dc=new dojo.gfx.color.Color(dojo.html.getBackgroundColor(node));
var rgb=new dojo.gfx.color.Color(_7d6);
var _7de=dojo.html.getStyle(node,"background-image");
var anim=dojo.lfx.propertyAnimation(node,{"background-color":{start:_7dc,end:rgb}},_7d7,_7d8,{"beforeBegin":function(){
if(_7de){
node.style.backgroundImage="none";
}
node.style.backgroundColor="rgb("+_7dc.toRgb().join(",")+")";
},"onEnd":function(){
if(_7d9){
_7d9(node,anim);
}
}});
_7da.push(anim);
});
return dojo.lfx.combine(_7da);
};
dojo.lang.mixin(dojo.lfx,dojo.lfx.html);
dojo.provide("dojo.lfx.*");
dojo.provide("dojo.lfx.toggle");
dojo.lfx.toggle.plain={show:function(node,_7e1,_7e2,_7e3){
dojo.html.show(node);
if(dojo.lang.isFunction(_7e3)){
_7e3();
}
},hide:function(node,_7e5,_7e6,_7e7){
dojo.html.hide(node);
if(dojo.lang.isFunction(_7e7)){
_7e7();
}
}};
dojo.lfx.toggle.fade={show:function(node,_7e9,_7ea,_7eb){
dojo.lfx.fadeShow(node,_7e9,_7ea,_7eb).play();
},hide:function(node,_7ed,_7ee,_7ef){
dojo.lfx.fadeHide(node,_7ed,_7ee,_7ef).play();
}};
dojo.lfx.toggle.wipe={show:function(node,_7f1,_7f2,_7f3){
dojo.lfx.wipeIn(node,_7f1,_7f2,_7f3).play();
},hide:function(node,_7f5,_7f6,_7f7){
dojo.lfx.wipeOut(node,_7f5,_7f6,_7f7).play();
}};
dojo.lfx.toggle.explode={show:function(node,_7f9,_7fa,_7fb,_7fc){
dojo.lfx.explode(_7fc||{x:0,y:0,width:0,height:0},node,_7f9,_7fa,_7fb).play();
},hide:function(node,_7fe,_7ff,_800,_801){
dojo.lfx.implode(node,_801||{x:0,y:0,width:0,height:0},_7fe,_7ff,_800).play();
}};
dojo.provide("dojo.widget.HtmlWidget");
dojo.declare("dojo.widget.HtmlWidget",dojo.widget.DomWidget,{templateCssPath:null,templatePath:null,lang:"",toggle:"plain",toggleDuration:150,initialize:function(args,frag){
},postMixInProperties:function(args,frag){
if(this.lang===""){
this.lang=null;
}
this.toggleObj=dojo.lfx.toggle[this.toggle.toLowerCase()]||dojo.lfx.toggle.plain;
},createNodesFromText:function(txt,wrap){
return dojo.html.createNodesFromText(txt,wrap);
},destroyRendering:function(_808){
try{
if(this.bgIframe){
this.bgIframe.remove();
delete this.bgIframe;
}
if(!_808&&this.domNode){
dojo.event.browser.clean(this.domNode);
}
dojo.html.destroyNode(this.domNode);
delete this.domNode;
}
catch(e){
}
},isShowing:function(){
return dojo.html.isShowing(this.domNode);
},toggleShowing:function(){
if(this.isShowing()){
this.hide();
}else{
this.show();
}
},show:function(){
if(this.isShowing()){
return;
}
this.animationInProgress=true;
this.toggleObj.show(this.domNode,this.toggleDuration,null,dojo.lang.hitch(this,this.onShow),this.explodeSrc);
},onShow:function(){
this.animationInProgress=false;
this.checkSize();
},hide:function(){
if(!this.isShowing()){
return;
}
this.animationInProgress=true;
this.toggleObj.hide(this.domNode,this.toggleDuration,null,dojo.lang.hitch(this,this.onHide),this.explodeSrc);
},onHide:function(){
this.animationInProgress=false;
},_isResized:function(w,h){
if(!this.isShowing()){
return false;
}
var wh=dojo.html.getMarginBox(this.domNode);
var _80c=w||wh.width;
var _80d=h||wh.height;
if(this.width==_80c&&this.height==_80d){
return false;
}
this.width=_80c;
this.height=_80d;
return true;
},checkSize:function(){
if(!this._isResized()){
return;
}
this.onResized();
},resizeTo:function(w,h){
dojo.html.setMarginBox(this.domNode,{width:w,height:h});
if(this.isShowing()){
this.onResized();
}
},resizeSoon:function(){
if(this.isShowing()){
dojo.lang.setTimeout(this,this.onResized,0);
}
},onResized:function(){
dojo.lang.forEach(this.children,function(_810){
if(_810.checkSize){
_810.checkSize();
}
});
}});
dojo.provide("dojo.widget.*");

dojo.provide("dojo.AdapterRegistry");
dojo.AdapterRegistry=function(_811){
this.pairs=[];
this.returnWrappers=_811||false;
};
dojo.lang.extend(dojo.AdapterRegistry,{register:function(name,_813,wrap,_815,_816){
var type=(_816)?"unshift":"push";
this.pairs[type]([name,_813,wrap,_815]);
},match:function(){
for(var i=0;i<this.pairs.length;i++){
var pair=this.pairs[i];
if(pair[1].apply(this,arguments)){
if((pair[3])||(this.returnWrappers)){
return pair[2];
}else{
return pair[2].apply(this,arguments);
}
}
}
throw new Error("No match found");
},unregister:function(name){
for(var i=0;i<this.pairs.length;i++){
var pair=this.pairs[i];
if(pair[0]==name){
this.pairs.splice(i,1);
return true;
}
}
return false;
}});
dojo.provide("dojo.Deferred");
dojo.Deferred=function(_81d){
this.chain=[];
this.id=this._nextId();
this.fired=-1;
this.paused=0;
this.results=[null,null];
this.canceller=_81d;
this.silentlyCancelled=false;
};
dojo.lang.extend(dojo.Deferred,{getFunctionFromArgs:function(){
var a=arguments;
if((a[0])&&(!a[1])){
if(dojo.lang.isFunction(a[0])){
return a[0];
}else{
if(dojo.lang.isString(a[0])){
return dj_global[a[0]];
}
}
}else{
if((a[0])&&(a[1])){
return dojo.lang.hitch(a[0],a[1]);
}
}
return null;
},makeCalled:function(){
var _81f=new dojo.Deferred();
_81f.callback();
return _81f;
},repr:function(){
var _820;
if(this.fired==-1){
_820="unfired";
}else{
if(this.fired==0){
_820="success";
}else{
_820="error";
}
}
return "Deferred("+this.id+", "+_820+")";
},toString:dojo.lang.forward("repr"),_nextId:(function(){
var n=1;
return function(){
return n++;
};
})(),cancel:function(){
if(this.fired==-1){
if(this.canceller){
this.canceller(this);
}else{
this.silentlyCancelled=true;
}
if(this.fired==-1){
this.errback(new Error(this.repr()));
}
}else{
if((this.fired==0)&&(this.results[0] instanceof dojo.Deferred)){
this.results[0].cancel();
}
}
},_pause:function(){
this.paused++;
},_unpause:function(){
this.paused--;
if((this.paused==0)&&(this.fired>=0)){
this._fire();
}
},_continue:function(res){
this._resback(res);
this._unpause();
},_resback:function(res){
this.fired=((res instanceof Error)?1:0);
this.results[this.fired]=res;
this._fire();
},_check:function(){
if(this.fired!=-1){
if(!this.silentlyCancelled){
dojo.raise("already called!");
}
this.silentlyCancelled=false;
return;
}
},callback:function(res){
this._check();
this._resback(res);
},errback:function(res){
this._check();
if(!(res instanceof Error)){
res=new Error(res);
}
this._resback(res);
},addBoth:function(cb,cbfn){
var _828=this.getFunctionFromArgs(cb,cbfn);
if(arguments.length>2){
_828=dojo.lang.curryArguments(null,_828,arguments,2);
}
return this.addCallbacks(_828,_828);
},addCallback:function(cb,cbfn){
var _82b=this.getFunctionFromArgs(cb,cbfn);
if(arguments.length>2){
_82b=dojo.lang.curryArguments(null,_82b,arguments,2);
}
return this.addCallbacks(_82b,null);
},addErrback:function(cb,cbfn){
var _82e=this.getFunctionFromArgs(cb,cbfn);
if(arguments.length>2){
_82e=dojo.lang.curryArguments(null,_82e,arguments,2);
}
return this.addCallbacks(null,_82e);
return this.addCallbacks(null,cbfn);
},addCallbacks:function(cb,eb){
this.chain.push([cb,eb]);
if(this.fired>=0){
this._fire();
}
return this;
},_fire:function(){
var _831=this.chain;
var _832=this.fired;
var res=this.results[_832];
var self=this;
var cb=null;
while(_831.length>0&&this.paused==0){
var pair=_831.shift();
var f=pair[_832];
if(f==null){
continue;
}
try{
res=f(res);
_832=((res instanceof Error)?1:0);
if(res instanceof dojo.Deferred){
cb=function(res){
self._continue(res);
};
this._pause();
}
}
catch(err){
_832=1;
res=err;
}
}
this.fired=_832;
this.results[_832]=res;
if((cb)&&(this.paused)){
res.addBoth(cb);
}
}});
dojo.provide("dojo.dnd.DragAndDrop");
dojo.declare("dojo.dnd.DragSource",null,{type:"",onDragEnd:function(){
},onDragStart:function(){
},onSelected:function(){
},unregister:function(){
dojo.dnd.dragManager.unregisterDragSource(this);
},reregister:function(){
dojo.dnd.dragManager.registerDragSource(this);
}});
dojo.declare("dojo.dnd.DragObject",null,{type:"",register:function(){
var dm=dojo.dnd.dragManager;
if(dm["registerDragObject"]){
dm.registerDragObject(this);
}
},onDragStart:function(){
},onDragMove:function(){
},onDragOver:function(){
},onDragOut:function(){
},onDragEnd:function(){
},onDragLeave:this.onDragOut,onDragEnter:this.onDragOver,ondragout:this.onDragOut,ondragover:this.onDragOver});
dojo.declare("dojo.dnd.DropTarget",null,{acceptsType:function(type){
if(!dojo.lang.inArray(this.acceptedTypes,"*")){
if(!dojo.lang.inArray(this.acceptedTypes,type)){
return false;
}
}
return true;
},accepts:function(_83b){
if(!dojo.lang.inArray(this.acceptedTypes,"*")){
for(var i=0;i<_83b.length;i++){
if(!dojo.lang.inArray(this.acceptedTypes,_83b[i].type)){
return false;
}
}
}
return true;
},unregister:function(){
dojo.dnd.dragManager.unregisterDropTarget(this);
},onDragOver:function(){
},onDragOut:function(){
},onDragMove:function(){
},onDropStart:function(){
},onDrop:function(){
},onDropEnd:function(){
}},function(){
this.acceptedTypes=[];
});
dojo.dnd.DragEvent=function(){
this.dragSource=null;
this.dragObject=null;
this.target=null;
this.eventStatus="success";
};
dojo.declare("dojo.dnd.DragManager",null,{selectedSources:[],dragObjects:[],dragSources:[],registerDragSource:function(){
},dropTargets:[],registerDropTarget:function(){
},lastDragTarget:null,currentDragTarget:null,onKeyDown:function(){
},onMouseOut:function(){
},onMouseMove:function(){
},onMouseUp:function(){
}});
dojo.provide("dojo.dnd.HtmlDragManager");
dojo.declare("dojo.dnd.HtmlDragManager",dojo.dnd.DragManager,{disabled:false,nestedTargets:false,mouseDownTimer:null,dsCounter:0,dsPrefix:"dojoDragSource",dropTargetDimensions:[],currentDropTarget:null,previousDropTarget:null,_dragTriggered:false,selectedSources:[],dragObjects:[],currentX:null,currentY:null,lastX:null,lastY:null,mouseDownX:null,mouseDownY:null,threshold:7,dropAcceptable:false,cancelEvent:function(e){
e.stopPropagation();
e.preventDefault();
},registerDragSource:function(ds){
if(ds["domNode"]){
var dp=this.dsPrefix;
var _840=dp+"Idx_"+(this.dsCounter++);
ds.dragSourceId=_840;
this.dragSources[_840]=ds;
ds.domNode.setAttribute(dp,_840);
if(dojo.render.html.ie){
dojo.event.browser.addListener(ds.domNode,"ondragstart",this.cancelEvent);
}
}
},unregisterDragSource:function(ds){
if(ds["domNode"]){
var dp=this.dsPrefix;
var _843=ds.dragSourceId;
delete ds.dragSourceId;
delete this.dragSources[_843];
ds.domNode.setAttribute(dp,null);
if(dojo.render.html.ie){
dojo.event.browser.removeListener(ds.domNode,"ondragstart",this.cancelEvent);
}
}
},registerDropTarget:function(dt){
this.dropTargets.push(dt);
},unregisterDropTarget:function(dt){
var _846=dojo.lang.find(this.dropTargets,dt,true);
if(_846>=0){
this.dropTargets.splice(_846,1);
}
},getDragSource:function(e){
var tn=e.target;
if(tn===dojo.body()){
return;
}
var ta=dojo.html.getAttribute(tn,this.dsPrefix);
while((!ta)&&(tn)){
tn=tn.parentNode;
if((!tn)||(tn===dojo.body())){
return;
}
ta=dojo.html.getAttribute(tn,this.dsPrefix);
}
return this.dragSources[ta];
},onKeyDown:function(e){
},onMouseDown:function(e){
if(this.disabled){
return;
}
if(dojo.render.html.ie){
if(e.button!=1){
return;
}
}else{
if(e.which!=1){
return;
}
}
var _84c=e.target.nodeType==dojo.html.TEXT_NODE?e.target.parentNode:e.target;
if(dojo.html.isTag(_84c,"button","textarea","input","select","option")){
return;
}
var ds=this.getDragSource(e);
if(!ds){
return;
}
if(!dojo.lang.inArray(this.selectedSources,ds)){
this.selectedSources.push(ds);
ds.onSelected();
}
this.mouseDownX=e.pageX;
this.mouseDownY=e.pageY;
e.preventDefault();
dojo.event.connect(document,"onmousemove",this,"onMouseMove");
},onMouseUp:function(e,_84f){
if(this.selectedSources.length==0){
return;
}
this.mouseDownX=null;
this.mouseDownY=null;
this._dragTriggered=false;
e.dragSource=this.dragSource;
if((!e.shiftKey)&&(!e.ctrlKey)){
if(this.currentDropTarget){
this.currentDropTarget.onDropStart();
}
dojo.lang.forEach(this.dragObjects,function(_850){
var ret=null;
if(!_850){
return;
}
if(this.currentDropTarget){
e.dragObject=_850;
var ce=this.currentDropTarget.domNode.childNodes;
if(ce.length>0){
e.dropTarget=ce[0];
while(e.dropTarget==_850.domNode){
e.dropTarget=e.dropTarget.nextSibling;
}
}else{
e.dropTarget=this.currentDropTarget.domNode;
}
if(this.dropAcceptable){
ret=this.currentDropTarget.onDrop(e);
}else{
this.currentDropTarget.onDragOut(e);
}
}
e.dragStatus=this.dropAcceptable&&ret?"dropSuccess":"dropFailure";
dojo.lang.delayThese([function(){
try{
_850.dragSource.onDragEnd(e);
}
catch(err){
var _853={};
for(var i in e){
if(i=="type"){
_853.type="mouseup";
continue;
}
_853[i]=e[i];
}
_850.dragSource.onDragEnd(_853);
}
},function(){
_850.onDragEnd(e);
}]);
},this);
this.selectedSources=[];
this.dragObjects=[];
this.dragSource=null;
if(this.currentDropTarget){
this.currentDropTarget.onDropEnd();
}
}else{
}
dojo.event.disconnect(document,"onmousemove",this,"onMouseMove");
this.currentDropTarget=null;
},onScroll:function(){
for(var i=0;i<this.dragObjects.length;i++){
if(this.dragObjects[i].updateDragOffset){
this.dragObjects[i].updateDragOffset();
}
}
if(this.dragObjects.length){
this.cacheTargetLocations();
}
},_dragStartDistance:function(x,y){
if((!this.mouseDownX)||(!this.mouseDownX)){
return;
}
var dx=Math.abs(x-this.mouseDownX);
var dx2=dx*dx;
var dy=Math.abs(y-this.mouseDownY);
var dy2=dy*dy;
return parseInt(Math.sqrt(dx2+dy2),10);
},cacheTargetLocations:function(){
dojo.profile.start("cacheTargetLocations");
this.dropTargetDimensions=[];
dojo.lang.forEach(this.dropTargets,function(_85c){
var tn=_85c.domNode;
if(!tn||!_85c.accepts(this.dragSource)){
return;
}
var abs=dojo.html.getAbsolutePosition(tn,true);
var bb=dojo.html.getBorderBox(tn);
this.dropTargetDimensions.push([[abs.x,abs.y],[abs.x+bb.width,abs.y+bb.height],_85c]);
},this);
dojo.profile.end("cacheTargetLocations");
},onMouseMove:function(e){
if((dojo.render.html.ie)&&(e.button!=1)){
this.currentDropTarget=null;
this.onMouseUp(e,true);
return;
}
if((this.selectedSources.length)&&(!this.dragObjects.length)){
var dx;
var dy;
if(!this._dragTriggered){
this._dragTriggered=(this._dragStartDistance(e.pageX,e.pageY)>this.threshold);
if(!this._dragTriggered){
return;
}
dx=e.pageX-this.mouseDownX;
dy=e.pageY-this.mouseDownY;
}
this.dragSource=this.selectedSources[0];
dojo.lang.forEach(this.selectedSources,function(_863){
if(!_863){
return;
}
var tdo=_863.onDragStart(e);
if(tdo){
tdo.onDragStart(e);
tdo.dragOffset.y+=dy;
tdo.dragOffset.x+=dx;
tdo.dragSource=_863;
this.dragObjects.push(tdo);
}
},this);
this.previousDropTarget=null;
this.cacheTargetLocations();
}
dojo.lang.forEach(this.dragObjects,function(_865){
if(_865){
_865.onDragMove(e);
}
});
if(this.currentDropTarget){
var c=dojo.html.toCoordinateObject(this.currentDropTarget.domNode,true);
var dtp=[[c.x,c.y],[c.x+c.width,c.y+c.height]];
}
if((!this.nestedTargets)&&(dtp)&&(this.isInsideBox(e,dtp))){
if(this.dropAcceptable){
this.currentDropTarget.onDragMove(e,this.dragObjects);
}
}else{
var _868=this.findBestTarget(e);
if(_868.target===null){
if(this.currentDropTarget){
this.currentDropTarget.onDragOut(e);
this.previousDropTarget=this.currentDropTarget;
this.currentDropTarget=null;
}
this.dropAcceptable=false;
return;
}
if(this.currentDropTarget!==_868.target){
if(this.currentDropTarget){
this.previousDropTarget=this.currentDropTarget;
this.currentDropTarget.onDragOut(e);
}
this.currentDropTarget=_868.target;
e.dragObjects=this.dragObjects;
this.dropAcceptable=this.currentDropTarget.onDragOver(e);
}else{
if(this.dropAcceptable){
this.currentDropTarget.onDragMove(e,this.dragObjects);
}
}
}
},findBestTarget:function(e){
var _86a=this;
var _86b=new Object();
_86b.target=null;
_86b.points=null;
dojo.lang.every(this.dropTargetDimensions,function(_86c){
if(!_86a.isInsideBox(e,_86c)){
return true;
}
_86b.target=_86c[2];
_86b.points=_86c;
return Boolean(_86a.nestedTargets);
});
return _86b;
},isInsideBox:function(e,_86e){
if((e.pageX>_86e[0][0])&&(e.pageX<_86e[1][0])&&(e.pageY>_86e[0][1])&&(e.pageY<_86e[1][1])){
return true;
}
return false;
},onMouseOver:function(e){
},onMouseOut:function(e){
}});
dojo.dnd.dragManager=new dojo.dnd.HtmlDragManager();
(function(){
var d=document;
var dm=dojo.dnd.dragManager;
dojo.event.connect(d,"onkeydown",dm,"onKeyDown");
dojo.event.connect(d,"onmouseover",dm,"onMouseOver");
dojo.event.connect(d,"onmouseout",dm,"onMouseOut");
dojo.event.connect(d,"onmousedown",dm,"onMouseDown");
dojo.event.connect(d,"onmouseup",dm,"onMouseUp");
dojo.event.connect(window,"onscroll",dm,"onScroll");
})();
dojo.provide("dojo.html.selection");
dojo.html.selectionType={NONE:0,TEXT:1,CONTROL:2};
dojo.html.clearSelection=function(){
var _873=dojo.global();
var _874=dojo.doc();
try{
if(_873["getSelection"]){
if(dojo.render.html.safari){
_873.getSelection().collapse();
}else{
_873.getSelection().removeAllRanges();
}
}else{
if(_874.selection){
if(_874.selection.empty){
_874.selection.empty();
}else{
if(_874.selection.clear){
_874.selection.clear();
}
}
}
}
return true;
}
catch(e){
dojo.debug(e);
return false;
}
};
dojo.html.disableSelection=function(_875){
_875=dojo.byId(_875)||dojo.body();
var h=dojo.render.html;
if(h.mozilla){
_875.style.MozUserSelect="none";
}else{
if(h.safari){
_875.style.KhtmlUserSelect="none";
}else{
if(h.ie){
_875.unselectable="on";
}else{
return false;
}
}
}
return true;
};
dojo.html.enableSelection=function(_877){
_877=dojo.byId(_877)||dojo.body();
var h=dojo.render.html;
if(h.mozilla){
_877.style.MozUserSelect="";
}else{
if(h.safari){
_877.style.KhtmlUserSelect="";
}else{
if(h.ie){
_877.unselectable="off";
}else{
return false;
}
}
}
return true;
};
dojo.html.selectInputText=function(_879){
var _87a=dojo.global();
var _87b=dojo.doc();
_879=dojo.byId(_879);
if(_87b["selection"]&&dojo.body()["createTextRange"]){
var _87c=_879.createTextRange();
_87c.moveStart("character",0);
_87c.moveEnd("character",_879.value.length);
_87c.select();
}else{
if(_87a["getSelection"]){
var _87d=_87a.getSelection();
_879.setSelectionRange(0,_879.value.length);
}
}
_879.focus();
};
dojo.lang.mixin(dojo.html.selection,{getType:function(){
if(dojo.doc()["selection"]){
return dojo.html.selectionType[dojo.doc().selection.type.toUpperCase()];
}else{
var _87e=dojo.html.selectionType.TEXT;
var oSel;
try{
oSel=dojo.global().getSelection();
}
catch(e){
}
if(oSel&&oSel.rangeCount==1){
var _880=oSel.getRangeAt(0);
if(_880.startContainer==_880.endContainer&&(_880.endOffset-_880.startOffset)==1&&_880.startContainer.nodeType!=dojo.dom.TEXT_NODE){
_87e=dojo.html.selectionType.CONTROL;
}
}
return _87e;
}
},isCollapsed:function(){
var _881=dojo.global();
var _882=dojo.doc();
if(_882["selection"]){
return _882.selection.createRange().text=="";
}else{
if(_881["getSelection"]){
var _883=_881.getSelection();
if(dojo.lang.isString(_883)){
return _883=="";
}else{
return _883.isCollapsed||_883.toString()=="";
}
}
}
},getSelectedElement:function(){
if(dojo.html.selection.getType()==dojo.html.selectionType.CONTROL){
if(dojo.doc()["selection"]){
var _884=dojo.doc().selection.createRange();
if(_884&&_884.item){
return dojo.doc().selection.createRange().item(0);
}
}else{
var _885=dojo.global().getSelection();
return _885.anchorNode.childNodes[_885.anchorOffset];
}
}
},getParentElement:function(){
if(dojo.html.selection.getType()==dojo.html.selectionType.CONTROL){
var p=dojo.html.selection.getSelectedElement();
if(p){
return p.parentNode;
}
}else{
if(dojo.doc()["selection"]){
return dojo.doc().selection.createRange().parentElement();
}else{
var _887=dojo.global().getSelection();
if(_887){
var node=_887.anchorNode;
while(node&&node.nodeType!=dojo.dom.ELEMENT_NODE){
node=node.parentNode;
}
return node;
}
}
}
},getSelectedText:function(){
if(dojo.doc()["selection"]){
if(dojo.html.selection.getType()==dojo.html.selectionType.CONTROL){
return null;
}
return dojo.doc().selection.createRange().text;
}else{
var _889=dojo.global().getSelection();
if(_889){
return _889.toString();
}
}
},getSelectedHtml:function(){
if(dojo.doc()["selection"]){
if(dojo.html.selection.getType()==dojo.html.selectionType.CONTROL){
return null;
}
return dojo.doc().selection.createRange().htmlText;
}else{
var _88a=dojo.global().getSelection();
if(_88a&&_88a.rangeCount){
var frag=_88a.getRangeAt(0).cloneContents();
var div=document.createElement("div");
div.appendChild(frag);
return div.innerHTML;
}
return null;
}
},hasAncestorElement:function(_88d){
return (dojo.html.selection.getAncestorElement.apply(this,arguments)!=null);
},getAncestorElement:function(_88e){
var node=dojo.html.selection.getSelectedElement()||dojo.html.selection.getParentElement();
while(node){
if(dojo.html.selection.isTag(node,arguments).length>0){
return node;
}
node=node.parentNode;
}
return null;
},isTag:function(node,tags){
if(node&&node.tagName){
for(var i=0;i<tags.length;i++){
if(node.tagName.toLowerCase()==String(tags[i]).toLowerCase()){
return String(tags[i]).toLowerCase();
}
}
}
return "";
},selectElement:function(_893){
var _894=dojo.global();
var _895=dojo.doc();
_893=dojo.byId(_893);
if(_895.selection&&dojo.body().createTextRange){
try{
var _896=dojo.body().createControlRange();
_896.addElement(_893);
_896.select();
}
catch(e){
dojo.html.selection.selectElementChildren(_893);
}
}else{
if(_894["getSelection"]){
var _897=_894.getSelection();
if(_897["removeAllRanges"]){
var _896=_895.createRange();
_896.selectNode(_893);
_897.removeAllRanges();
_897.addRange(_896);
}
}
}
},selectElementChildren:function(_898){
var _899=dojo.global();
var _89a=dojo.doc();
_898=dojo.byId(_898);
if(_89a.selection&&dojo.body().createTextRange){
var _89b=dojo.body().createTextRange();
_89b.moveToElementText(_898);
_89b.select();
}else{
if(_899["getSelection"]){
var _89c=_899.getSelection();
if(_89c["setBaseAndExtent"]){
_89c.setBaseAndExtent(_898,0,_898,_898.innerText.length-1);
}else{
if(_89c["selectAllChildren"]){
_89c.selectAllChildren(_898);
}
}
}
}
},getBookmark:function(){
var _89d;
var _89e=dojo.doc();
if(_89e["selection"]){
var _89f=_89e.selection.createRange();
_89d=_89f.getBookmark();
}else{
var _8a0;
try{
_8a0=dojo.global().getSelection();
}
catch(e){
}
if(_8a0){
var _89f=_8a0.getRangeAt(0);
_89d=_89f.cloneRange();
}else{
dojo.debug("No idea how to store the current selection for this browser!");
}
}
return _89d;
},moveToBookmark:function(_8a1){
var _8a2=dojo.doc();
if(_8a2["selection"]){
var _8a3=_8a2.selection.createRange();
_8a3.moveToBookmark(_8a1);
_8a3.select();
}else{
var _8a4;
try{
_8a4=dojo.global().getSelection();
}
catch(e){
}
if(_8a4&&_8a4["removeAllRanges"]){
_8a4.removeAllRanges();
_8a4.addRange(_8a1);
}else{
dojo.debug("No idea how to restore selection for this browser!");
}
}
},collapse:function(_8a5){
if(dojo.global()["getSelection"]){
var _8a6=dojo.global().getSelection();
if(_8a6.removeAllRanges){
if(_8a5){
_8a6.collapseToStart();
}else{
_8a6.collapseToEnd();
}
}else{
dojo.global().getSelection().collapse(_8a5);
}
}else{
if(dojo.doc().selection){
var _8a7=dojo.doc().selection.createRange();
_8a7.collapse(_8a5);
_8a7.select();
}
}
},remove:function(){
if(dojo.doc().selection){
var _8a8=dojo.doc().selection;
if(_8a8.type.toUpperCase()!="NONE"){
_8a8.clear();
}
return _8a8;
}else{
var _8a8=dojo.global().getSelection();
for(var i=0;i<_8a8.rangeCount;i++){
_8a8.getRangeAt(i).deleteContents();
}
return _8a8;
}
}});
dojo.provide("dojo.html.iframe");
dojo.html.iframeContentWindow=function(_8aa){
var win=dojo.html.getDocumentWindow(dojo.html.iframeContentDocument(_8aa))||dojo.html.iframeContentDocument(_8aa).__parent__||(_8aa.name&&document.frames[_8aa.name])||null;
return win;
};
dojo.html.iframeContentDocument=function(_8ac){
var doc=_8ac.contentDocument||((_8ac.contentWindow)&&(_8ac.contentWindow.document))||((_8ac.name)&&(document.frames[_8ac.name])&&(document.frames[_8ac.name].document))||null;
return doc;
};
dojo.html.BackgroundIframe=function(node){
if(dojo.render.html.ie55||dojo.render.html.ie60){
var html="<iframe src='javascript:false'"+" style='position: absolute; left: 0px; top: 0px; width: 100%; height: 100%;"+"z-index: -1; filter:Alpha(Opacity=\"0\");' "+">";
this.iframe=dojo.doc().createElement(html);
this.iframe.tabIndex=-1;
if(node){
node.appendChild(this.iframe);
this.domNode=node;
}else{
dojo.body().appendChild(this.iframe);
this.iframe.style.display="none";
}
}
};
dojo.lang.extend(dojo.html.BackgroundIframe,{iframe:null,onResized:function(){
if(this.iframe&&this.domNode&&this.domNode.parentNode){
var _8b0=dojo.html.getMarginBox(this.domNode);
if(_8b0.width==0||_8b0.height==0){
dojo.lang.setTimeout(this,this.onResized,100);
return;
}
this.iframe.style.width=_8b0.width+"px";
this.iframe.style.height=_8b0.height+"px";
}
},size:function(node){
if(!this.iframe){
return;
}
var _8b2=dojo.html.toCoordinateObject(node,true,dojo.html.boxSizing.BORDER_BOX);
with(this.iframe.style){
width=_8b2.width+"px";
height=_8b2.height+"px";
left=_8b2.left+"px";
top=_8b2.top+"px";
}
},setZIndex:function(node){
if(!this.iframe){
return;
}
if(dojo.dom.isNode(node)){
this.iframe.style.zIndex=dojo.html.getStyle(node,"z-index")-1;
}else{
if(!isNaN(node)){
this.iframe.style.zIndex=node;
}
}
},show:function(){
if(this.iframe){
this.iframe.style.display="block";
}
},hide:function(){
if(this.iframe){
this.iframe.style.display="none";
}
},remove:function(){
if(this.iframe){
dojo.html.removeNode(this.iframe,true);
delete this.iframe;
this.iframe=null;
}
}});
dojo.provide("dojo.dnd.HtmlDragAndDrop");
dojo.declare("dojo.dnd.HtmlDragSource",dojo.dnd.DragSource,{dragClass:"",onDragStart:function(){
var _8b4=new dojo.dnd.HtmlDragObject(this.dragObject,this.type);
if(this.dragClass){
_8b4.dragClass=this.dragClass;
}
if(this.constrainToContainer){
_8b4.constrainTo(this.constrainingContainer||this.domNode.parentNode);
}
return _8b4;
},setDragHandle:function(node){
node=dojo.byId(node);
dojo.dnd.dragManager.unregisterDragSource(this);
this.domNode=node;
dojo.dnd.dragManager.registerDragSource(this);
},setDragTarget:function(node){
this.dragObject=node;
},constrainTo:function(_8b7){
this.constrainToContainer=true;
if(_8b7){
this.constrainingContainer=_8b7;
}
},onSelected:function(){
for(var i=0;i<this.dragObjects.length;i++){
dojo.dnd.dragManager.selectedSources.push(new dojo.dnd.HtmlDragSource(this.dragObjects[i]));
}
},addDragObjects:function(el){
for(var i=0;i<arguments.length;i++){
this.dragObjects.push(arguments[i]);
}
}},function(node,type){
node=dojo.byId(node);
this.dragObjects=[];
this.constrainToContainer=false;
if(node){
this.domNode=node;
this.dragObject=node;
this.type=(type)||(this.domNode.nodeName.toLowerCase());
this.reregister();
}
});
dojo.declare("dojo.dnd.HtmlDragObject",dojo.dnd.DragObject,{dragClass:"",opacity:0.5,createIframe:true,disableX:false,disableY:false,createDragNode:function(){
var node=this.domNode.cloneNode(true);
if(this.dragClass){
dojo.html.addClass(node,this.dragClass);
}
if(this.opacity<1){
dojo.html.setOpacity(node,this.opacity);
}
var ltn=node.tagName.toLowerCase();
var isTr=(ltn=="tr");
if((isTr)||(ltn=="tbody")){
var doc=this.domNode.ownerDocument;
var _8c1=doc.createElement("table");
if(isTr){
var _8c2=doc.createElement("tbody");
_8c1.appendChild(_8c2);
_8c2.appendChild(node);
}else{
_8c1.appendChild(node);
}
var _8c3=((isTr)?this.domNode:this.domNode.firstChild);
var _8c4=((isTr)?node:node.firstChild);
var _8c5=tdp.childNodes;
var _8c6=_8c4.childNodes;
for(var i=0;i<_8c5.length;i++){
if((_8c6[i])&&(_8c6[i].style)){
_8c6[i].style.width=dojo.html.getContentBox(_8c5[i]).width+"px";
}
}
node=_8c1;
}
if((dojo.render.html.ie55||dojo.render.html.ie60)&&this.createIframe){
with(node.style){
top="0px";
left="0px";
}
var _8c8=document.createElement("div");
_8c8.appendChild(node);
this.bgIframe=new dojo.html.BackgroundIframe(_8c8);
_8c8.appendChild(this.bgIframe.iframe);
node=_8c8;
}
node.style.zIndex=999;
return node;
},onDragStart:function(e){
dojo.html.clearSelection();
this.scrollOffset=dojo.html.getScroll().offset;
this.dragStartPosition=dojo.html.getAbsolutePosition(this.domNode,true);
this.dragOffset={y:this.dragStartPosition.y-e.pageY,x:this.dragStartPosition.x-e.pageX};
this.dragClone=this.createDragNode();
this.containingBlockPosition=this.domNode.offsetParent?dojo.html.getAbsolutePosition(this.domNode.offsetParent,true):{x:0,y:0};
if(this.constrainToContainer){
this.constraints=this.getConstraints();
}
with(this.dragClone.style){
position="absolute";
top=this.dragOffset.y+e.pageY+"px";
left=this.dragOffset.x+e.pageX+"px";
}
dojo.body().appendChild(this.dragClone);
dojo.event.topic.publish("dragStart",{source:this});
},getConstraints:function(){
if(this.constrainingContainer.nodeName.toLowerCase()=="body"){
var _8ca=dojo.html.getViewport();
var _8cb=_8ca.width;
var _8cc=_8ca.height;
var _8cd=dojo.html.getScroll().offset;
var x=_8cd.x;
var y=_8cd.y;
}else{
var _8d0=dojo.html.getContentBox(this.constrainingContainer);
_8cb=_8d0.width;
_8cc=_8d0.height;
x=this.containingBlockPosition.x+dojo.html.getPixelValue(this.constrainingContainer,"padding-left",true)+dojo.html.getBorderExtent(this.constrainingContainer,"left");
y=this.containingBlockPosition.y+dojo.html.getPixelValue(this.constrainingContainer,"padding-top",true)+dojo.html.getBorderExtent(this.constrainingContainer,"top");
}
var mb=dojo.html.getMarginBox(this.domNode);
return {minX:x,minY:y,maxX:x+_8cb-mb.width,maxY:y+_8cc-mb.height};
},updateDragOffset:function(){
var _8d2=dojo.html.getScroll().offset;
if(_8d2.y!=this.scrollOffset.y){
var diff=_8d2.y-this.scrollOffset.y;
this.dragOffset.y+=diff;
this.scrollOffset.y=_8d2.y;
}
if(_8d2.x!=this.scrollOffset.x){
var diff=_8d2.x-this.scrollOffset.x;
this.dragOffset.x+=diff;
this.scrollOffset.x=_8d2.x;
}
},onDragMove:function(e){
this.updateDragOffset();
var x=this.dragOffset.x+e.pageX;
var y=this.dragOffset.y+e.pageY;
if(this.constrainToContainer){
if(x<this.constraints.minX){
x=this.constraints.minX;
}
if(y<this.constraints.minY){
y=this.constraints.minY;
}
if(x>this.constraints.maxX){
x=this.constraints.maxX;
}
if(y>this.constraints.maxY){
y=this.constraints.maxY;
}
}
this.setAbsolutePosition(x,y);
dojo.event.topic.publish("dragMove",{source:this});
},setAbsolutePosition:function(x,y){
if(!this.disableY){
this.dragClone.style.top=y+"px";
}
if(!this.disableX){
this.dragClone.style.left=x+"px";
}
},onDragEnd:function(e){
switch(e.dragStatus){
case "dropSuccess":
dojo.html.removeNode(this.dragClone);
this.dragClone=null;
break;
case "dropFailure":
var _8da=dojo.html.getAbsolutePosition(this.dragClone,true);
var _8db={left:this.dragStartPosition.x+1,top:this.dragStartPosition.y+1};
var anim=dojo.lfx.slideTo(this.dragClone,_8db,500,dojo.lfx.easeOut);
var _8dd=this;
dojo.event.connect(anim,"onEnd",function(e){
dojo.lang.setTimeout(function(){
dojo.html.removeNode(_8dd.dragClone);
_8dd.dragClone=null;
},200);
});
anim.play();
break;
}
dojo.event.topic.publish("dragEnd",{source:this});
},constrainTo:function(_8df){
this.constrainToContainer=true;
if(_8df){
this.constrainingContainer=_8df;
}else{
this.constrainingContainer=this.domNode.parentNode;
}
}},function(node,type){
this.domNode=dojo.byId(node);
this.type=type;
this.constrainToContainer=false;
this.dragSource=null;
this.register();
});
dojo.declare("dojo.dnd.HtmlDropTarget",dojo.dnd.DropTarget,{vertical:false,onDragOver:function(e){
if(!this.accepts(e.dragObjects)){
return false;
}
this.childBoxes=[];
for(var i=0,_8e4;i<this.domNode.childNodes.length;i++){
_8e4=this.domNode.childNodes[i];
if(_8e4.nodeType!=dojo.html.ELEMENT_NODE){
continue;
}
var pos=dojo.html.getAbsolutePosition(_8e4,true);
var _8e6=dojo.html.getBorderBox(_8e4);
this.childBoxes.push({top:pos.y,bottom:pos.y+_8e6.height,left:pos.x,right:pos.x+_8e6.width,height:_8e6.height,width:_8e6.width,node:_8e4});
}
return true;
},_getNodeUnderMouse:function(e){
for(var i=0,_8e9;i<this.childBoxes.length;i++){
with(this.childBoxes[i]){
if(e.pageX>=left&&e.pageX<=right&&e.pageY>=top&&e.pageY<=bottom){
return i;
}
}
}
return -1;
},createDropIndicator:function(){
this.dropIndicator=document.createElement("div");
with(this.dropIndicator.style){
position="absolute";
zIndex=999;
if(this.vertical){
borderLeftWidth="1px";
borderLeftColor="black";
borderLeftStyle="solid";
height=dojo.html.getBorderBox(this.domNode).height+"px";
top=dojo.html.getAbsolutePosition(this.domNode,true).y+"px";
}else{
borderTopWidth="1px";
borderTopColor="black";
borderTopStyle="solid";
width=dojo.html.getBorderBox(this.domNode).width+"px";
left=dojo.html.getAbsolutePosition(this.domNode,true).x+"px";
}
}
},onDragMove:function(e,_8eb){
var i=this._getNodeUnderMouse(e);
if(!this.dropIndicator){
this.createDropIndicator();
}
var _8ed=this.vertical?dojo.html.gravity.WEST:dojo.html.gravity.NORTH;
var hide=false;
if(i<0){
if(this.childBoxes.length){
var _8ef=(dojo.html.gravity(this.childBoxes[0].node,e)&_8ed);
if(_8ef){
hide=true;
}
}else{
var _8ef=true;
}
}else{
var _8f0=this.childBoxes[i];
var _8ef=(dojo.html.gravity(_8f0.node,e)&_8ed);
if(_8f0.node===_8eb[0].dragSource.domNode){
hide=true;
}else{
var _8f1=_8ef?(i>0?this.childBoxes[i-1]:_8f0):(i<this.childBoxes.length-1?this.childBoxes[i+1]:_8f0);
if(_8f1.node===_8eb[0].dragSource.domNode){
hide=true;
}
}
}
if(hide){
this.dropIndicator.style.display="none";
return;
}else{
this.dropIndicator.style.display="";
}
this.placeIndicator(e,_8eb,i,_8ef);
if(!dojo.html.hasParent(this.dropIndicator)){
dojo.body().appendChild(this.dropIndicator);
}
},placeIndicator:function(e,_8f3,_8f4,_8f5){
var _8f6=this.vertical?"left":"top";
var _8f7;
if(_8f4<0){
if(this.childBoxes.length){
_8f7=_8f5?this.childBoxes[0]:this.childBoxes[this.childBoxes.length-1];
}else{
this.dropIndicator.style[_8f6]=dojo.html.getAbsolutePosition(this.domNode,true)[this.vertical?"x":"y"]+"px";
}
}else{
_8f7=this.childBoxes[_8f4];
}
if(_8f7){
this.dropIndicator.style[_8f6]=(_8f5?_8f7[_8f6]:_8f7[this.vertical?"right":"bottom"])+"px";
if(this.vertical){
this.dropIndicator.style.height=_8f7.height+"px";
this.dropIndicator.style.top=_8f7.top+"px";
}else{
this.dropIndicator.style.width=_8f7.width+"px";
this.dropIndicator.style.left=_8f7.left+"px";
}
}
},onDragOut:function(e){
if(this.dropIndicator){
dojo.html.removeNode(this.dropIndicator);
delete this.dropIndicator;
}
},onDrop:function(e){
this.onDragOut(e);
var i=this._getNodeUnderMouse(e);
var _8fb=this.vertical?dojo.html.gravity.WEST:dojo.html.gravity.NORTH;
if(i<0){
if(this.childBoxes.length){
if(dojo.html.gravity(this.childBoxes[0].node,e)&_8fb){
return this.insert(e,this.childBoxes[0].node,"before");
}else{
return this.insert(e,this.childBoxes[this.childBoxes.length-1].node,"after");
}
}
return this.insert(e,this.domNode,"append");
}
var _8fc=this.childBoxes[i];
if(dojo.html.gravity(_8fc.node,e)&_8fb){
return this.insert(e,_8fc.node,"before");
}else{
return this.insert(e,_8fc.node,"after");
}
},insert:function(e,_8fe,_8ff){
var node=e.dragObject.domNode;
if(_8ff=="before"){
return dojo.html.insertBefore(node,_8fe);
}else{
if(_8ff=="after"){
return dojo.html.insertAfter(node,_8fe);
}else{
if(_8ff=="append"){
_8fe.appendChild(node);
return true;
}
}
}
return false;
}},function(node,_902){
if(arguments.length==0){
return;
}
this.domNode=dojo.byId(node);
dojo.dnd.DropTarget.call(this);
if(_902&&dojo.lang.isString(_902)){
_902=[_902];
}
this.acceptedTypes=_902||[];
dojo.dnd.dragManager.registerDropTarget(this);
});
dojo.provide("dojo.dnd.*");
dojo.provide("dojo.dnd.HtmlDragMove");
dojo.declare("dojo.dnd.HtmlDragMoveSource",dojo.dnd.HtmlDragSource,{onDragStart:function(){
var _903=new dojo.dnd.HtmlDragMoveObject(this.dragObject,this.type);
if(this.constrainToContainer){
_903.constrainTo(this.constrainingContainer);
}
return _903;
},onSelected:function(){
for(var i=0;i<this.dragObjects.length;i++){
dojo.dnd.dragManager.selectedSources.push(new dojo.dnd.HtmlDragMoveSource(this.dragObjects[i]));
}
}});
dojo.declare("dojo.dnd.HtmlDragMoveObject",dojo.dnd.HtmlDragObject,{onDragStart:function(e){
dojo.html.clearSelection();
this.dragClone=this.domNode;
if(dojo.html.getComputedStyle(this.domNode,"position")!="absolute"){
this.domNode.style.position="relative";
}
var left=parseInt(dojo.html.getComputedStyle(this.domNode,"left"));
var top=parseInt(dojo.html.getComputedStyle(this.domNode,"top"));
this.dragStartPosition={x:isNaN(left)?0:left,y:isNaN(top)?0:top};
this.scrollOffset=dojo.html.getScroll().offset;
this.dragOffset={y:this.dragStartPosition.y-e.pageY,x:this.dragStartPosition.x-e.pageX};
this.containingBlockPosition={x:0,y:0};
if(this.constrainToContainer){
this.constraints=this.getConstraints();
}
dojo.event.connect(this.domNode,"onclick",this,"_squelchOnClick");
},onDragEnd:function(e){
},setAbsolutePosition:function(x,y){
if(!this.disableY){
this.domNode.style.top=y+"px";
}
if(!this.disableX){
this.domNode.style.left=x+"px";
}
},_squelchOnClick:function(e){
dojo.event.browser.stopEvent(e);
dojo.event.disconnect(this.domNode,"onclick",this,"_squelchOnClick");
}});
dojo.provide("dojo.lang.type");
dojo.lang.whatAmI=function(_90c){
dojo.deprecated("dojo.lang.whatAmI","use dojo.lang.getType instead","0.5");
return dojo.lang.getType(_90c);
};
dojo.lang.whatAmI.custom={};
dojo.lang.getType=function(_90d){
try{
if(dojo.lang.isArray(_90d)){
return "array";
}
if(dojo.lang.isFunction(_90d)){
return "function";
}
if(dojo.lang.isString(_90d)){
return "string";
}
if(dojo.lang.isNumber(_90d)){
return "number";
}
if(dojo.lang.isBoolean(_90d)){
return "boolean";
}
if(dojo.lang.isAlien(_90d)){
return "alien";
}
if(dojo.lang.isUndefined(_90d)){
return "undefined";
}
for(var name in dojo.lang.whatAmI.custom){
if(dojo.lang.whatAmI.custom[name](_90d)){
return name;
}
}
if(dojo.lang.isObject(_90d)){
return "object";
}
}
catch(e){
}
return "unknown";
};
dojo.lang.isNumeric=function(_90f){
return (!isNaN(_90f)&&isFinite(_90f)&&(_90f!=null)&&!dojo.lang.isBoolean(_90f)&&!dojo.lang.isArray(_90f)&&!/^\s*$/.test(_90f));
};
dojo.lang.isBuiltIn=function(_910){
return (dojo.lang.isArray(_910)||dojo.lang.isFunction(_910)||dojo.lang.isString(_910)||dojo.lang.isNumber(_910)||dojo.lang.isBoolean(_910)||(_910==null)||(_910 instanceof Error)||(typeof _910=="error"));
};
dojo.lang.isPureObject=function(_911){
return ((_911!=null)&&dojo.lang.isObject(_911)&&_911.constructor==Object);
};
dojo.lang.isOfType=function(_912,type,_914){
var _915=false;
if(_914){
_915=_914["optional"];
}
if(_915&&((_912===null)||dojo.lang.isUndefined(_912))){
return true;
}
if(dojo.lang.isArray(type)){
var _916=type;
for(var i in _916){
var _918=_916[i];
if(dojo.lang.isOfType(_912,_918)){
return true;
}
}
return false;
}else{
if(dojo.lang.isString(type)){
type=type.toLowerCase();
}
switch(type){
case Array:
case "array":
return dojo.lang.isArray(_912);
case Function:
case "function":
return dojo.lang.isFunction(_912);
case String:
case "string":
return dojo.lang.isString(_912);
case Number:
case "number":
return dojo.lang.isNumber(_912);
case "numeric":
return dojo.lang.isNumeric(_912);
case Boolean:
case "boolean":
return dojo.lang.isBoolean(_912);
case Object:
case "object":
return dojo.lang.isObject(_912);
case "pureobject":
return dojo.lang.isPureObject(_912);
case "builtin":
return dojo.lang.isBuiltIn(_912);
case "alien":
return dojo.lang.isAlien(_912);
case "undefined":
return dojo.lang.isUndefined(_912);
case null:
case "null":
return (_912===null);
case "optional":
dojo.deprecated("dojo.lang.isOfType(value, [type, \"optional\"])","use dojo.lang.isOfType(value, type, {optional: true} ) instead","0.5");
return ((_912===null)||dojo.lang.isUndefined(_912));
default:
if(dojo.lang.isFunction(type)){
return (_912 instanceof type);
}else{
dojo.raise("dojo.lang.isOfType() was passed an invalid type");
}
}
}
dojo.raise("If we get here, it means a bug was introduced above.");
};
dojo.lang.getObject=function(str){
var _91a=str.split("."),i=0,obj=dj_global;
do{
obj=obj[_91a[i++]];
}while(i<_91a.length&&obj);
return (obj!=dj_global)?obj:null;
};
dojo.lang.doesObjectExist=function(str){
var _91e=str.split("."),i=0,obj=dj_global;
do{
obj=obj[_91e[i++]];
}while(i<_91e.length&&obj);
return (obj&&obj!=dj_global);
};
dojo.provide("dojo.lang.assert");
dojo.lang.assert=function(_921,_922){
if(!_921){
var _923="An assert statement failed.\n"+"The method dojo.lang.assert() was called with a 'false' value.\n";
if(_922){
_923+="Here's the assert message:\n"+_922+"\n";
}
throw new Error(_923);
}
};
dojo.lang.assertType=function(_924,type,_926){
if(dojo.lang.isString(_926)){
dojo.deprecated("dojo.lang.assertType(value, type, \"message\")","use dojo.lang.assertType(value, type) instead","0.5");
}
if(!dojo.lang.isOfType(_924,type,_926)){
if(!dojo.lang.assertType._errorMessage){
dojo.lang.assertType._errorMessage="Type mismatch: dojo.lang.assertType() failed.";
}
dojo.lang.assert(false,dojo.lang.assertType._errorMessage);
}
};
dojo.lang.assertValidKeywords=function(_927,_928,_929){
var key;
if(!_929){
if(!dojo.lang.assertValidKeywords._errorMessage){
dojo.lang.assertValidKeywords._errorMessage="In dojo.lang.assertValidKeywords(), found invalid keyword:";
}
_929=dojo.lang.assertValidKeywords._errorMessage;
}
if(dojo.lang.isArray(_928)){
for(key in _927){
if(!dojo.lang.inArray(_928,key)){
dojo.lang.assert(false,_929+" "+key);
}
}
}else{
for(key in _927){
if(!(key in _928)){
dojo.lang.assert(false,_929+" "+key);
}
}
}
};
dojo.provide("dojo.lang.repr");
dojo.lang.reprRegistry=new dojo.AdapterRegistry();
dojo.lang.registerRepr=function(name,_92c,wrap,_92e){
dojo.lang.reprRegistry.register(name,_92c,wrap,_92e);
};
dojo.lang.repr=function(obj){
if(typeof (obj)=="undefined"){
return "undefined";
}else{
if(obj===null){
return "null";
}
}
try{
if(typeof (obj["__repr__"])=="function"){
return obj["__repr__"]();
}else{
if((typeof (obj["repr"])=="function")&&(obj.repr!=arguments.callee)){
return obj["repr"]();
}
}
return dojo.lang.reprRegistry.match(obj);
}
catch(e){
if(typeof (obj.NAME)=="string"&&(obj.toString==Function.prototype.toString||obj.toString==Object.prototype.toString)){
return obj.NAME;
}
}
if(typeof (obj)=="function"){
obj=(obj+"").replace(/^\s+/,"");
var idx=obj.indexOf("{");
if(idx!=-1){
obj=obj.substr(0,idx)+"{...}";
}
}
return obj+"";
};
dojo.lang.reprArrayLike=function(arr){
try{
var na=dojo.lang.map(arr,dojo.lang.repr);
return "["+na.join(", ")+"]";
}
catch(e){
}
};
(function(){
var m=dojo.lang;
m.registerRepr("arrayLike",m.isArrayLike,m.reprArrayLike);
m.registerRepr("string",m.isString,m.reprString);
m.registerRepr("numbers",m.isNumber,m.reprNumber);
m.registerRepr("boolean",m.isBoolean,m.reprNumber);
})();
dojo.provide("dojo.lang.*");
dojo.provide("dojo.lfx.shadow");
dojo.lfx.shadow=function(node){
this.shadowPng=dojo.uri.dojoUri("src/html/images/shadow");
this.shadowThickness=8;
this.shadowOffset=15;
this.init(node);
};
dojo.extend(dojo.lfx.shadow,{init:function(node){
this.node=node;
this.pieces={};
var x1=-1*this.shadowThickness;
var y0=this.shadowOffset;
var y1=this.shadowOffset+this.shadowThickness;
this._makePiece("tl","top",y0,"left",x1);
this._makePiece("l","top",y1,"left",x1,"scale");
this._makePiece("tr","top",y0,"left",0);
this._makePiece("r","top",y1,"left",0,"scale");
this._makePiece("bl","top",0,"left",x1);
this._makePiece("b","top",0,"left",0,"crop");
this._makePiece("br","top",0,"left",0);
},_makePiece:function(name,_93a,_93b,_93c,_93d,_93e){
var img;
var url=this.shadowPng+name.toUpperCase()+".png";
if(dojo.render.html.ie55||dojo.render.html.ie60){
img=dojo.doc().createElement("div");
img.style.filter="progid:DXImageTransform.Microsoft.AlphaImageLoader(src='"+url+"'"+(_93e?", sizingMethod='"+_93e+"'":"")+")";
}else{
img=dojo.doc().createElement("img");
img.src=url;
}
img.style.position="absolute";
img.style[_93a]=_93b+"px";
img.style[_93c]=_93d+"px";
img.style.width=this.shadowThickness+"px";
img.style.height=this.shadowThickness+"px";
this.pieces[name]=img;
this.node.appendChild(img);
},size:function(_941,_942){
var _943=_942-(this.shadowOffset+this.shadowThickness+1);
if(_943<0){
_943=0;
}
if(_942<1){
_942=1;
}
if(_941<1){
_941=1;
}
with(this.pieces){
l.style.height=_943+"px";
r.style.height=_943+"px";
b.style.width=(_941-1)+"px";
bl.style.top=(_942-1)+"px";
b.style.top=(_942-1)+"px";
br.style.top=(_942-1)+"px";
tr.style.left=(_941-1)+"px";
r.style.left=(_941-1)+"px";
br.style.left=(_941-1)+"px";
}
}});
dojo.provide("dojo.io.*");
dojo.provide("dojo.widget.ContentPane");
dojo.widget.defineWidget("dojo.widget.ContentPane",dojo.widget.HtmlWidget,function(){
this._styleNodes=[];
this._onLoadStack=[];
this._onUnloadStack=[];
this._callOnUnload=false;
this._ioBindObj;
this.scriptScope;
this.bindArgs={};
},{isContainer:true,adjustPaths:true,href:"",extractContent:true,parseContent:true,cacheContent:true,preload:false,refreshOnShow:false,handler:"",executeScripts:false,scriptSeparation:true,loadingMessage:"Loading...",isLoaded:false,postCreate:function(args,frag,_946){
if(this.handler!==""){
this.setHandler(this.handler);
}
if(this.isShowing()||this.preload){
this.loadContents();
}
},show:function(){
if(this.refreshOnShow){
this.refresh();
}else{
this.loadContents();
}
dojo.widget.ContentPane.superclass.show.call(this);
},refresh:function(){
this.isLoaded=false;
this.loadContents();
},loadContents:function(){
if(this.isLoaded){
return;
}
if(dojo.lang.isFunction(this.handler)){
this._runHandler();
}else{
if(this.href!=""){
this._downloadExternalContent(this.href,this.cacheContent&&!this.refreshOnShow);
}
}
},setUrl:function(url){
this.href=url;
this.isLoaded=false;
if(this.preload||this.isShowing()){
this.loadContents();
}
},abort:function(){
var bind=this._ioBindObj;
if(!bind||!bind.abort){
return;
}
bind.abort();
delete this._ioBindObj;
},_downloadExternalContent:function(url,_94a){
this.abort();
this._handleDefaults(this.loadingMessage,"onDownloadStart");
var self=this;
this._ioBindObj=dojo.io.bind(this._cacheSetting({url:url,mimetype:"text/html",handler:function(type,data,xhr){
delete self._ioBindObj;
if(type=="load"){
self.onDownloadEnd.call(self,url,data);
}else{
var e={responseText:xhr.responseText,status:xhr.status,statusText:xhr.statusText,responseHeaders:xhr.getAllResponseHeaders(),text:"Error loading '"+url+"' ("+xhr.status+" "+xhr.statusText+")"};
self._handleDefaults.call(self,e,"onDownloadError");
self.onLoad();
}
}},_94a));
},_cacheSetting:function(_950,_951){
for(var x in this.bindArgs){
if(dojo.lang.isUndefined(_950[x])){
_950[x]=this.bindArgs[x];
}
}
if(dojo.lang.isUndefined(_950.useCache)){
_950.useCache=_951;
}
if(dojo.lang.isUndefined(_950.preventCache)){
_950.preventCache=!_951;
}
if(dojo.lang.isUndefined(_950.mimetype)){
_950.mimetype="text/html";
}
return _950;
},onLoad:function(e){
this._runStack("_onLoadStack");
this.isLoaded=true;
},onUnLoad:function(e){
dojo.deprecated(this.widgetType+".onUnLoad, use .onUnload (lowercased load)",0.5);
},onUnload:function(e){
this._runStack("_onUnloadStack");
delete this.scriptScope;
if(this.onUnLoad!==dojo.widget.ContentPane.prototype.onUnLoad){
this.onUnLoad.apply(this,arguments);
}
},_runStack:function(_956){
var st=this[_956];
var err="";
var _959=this.scriptScope||window;
for(var i=0;i<st.length;i++){
try{
st[i].call(_959);
}
catch(e){
err+="\n"+st[i]+" failed: "+e.description;
}
}
this[_956]=[];
if(err.length){
var name=(_956=="_onLoadStack")?"addOnLoad":"addOnUnLoad";
this._handleDefaults(name+" failure\n "+err,"onExecError","debug");
}
},addOnLoad:function(obj,func){
this._pushOnStack(this._onLoadStack,obj,func);
},addOnUnload:function(obj,func){
this._pushOnStack(this._onUnloadStack,obj,func);
},addOnUnLoad:function(){
dojo.deprecated(this.widgetType+".addOnUnLoad, use addOnUnload instead. (lowercased Load)",0.5);
this.addOnUnload.apply(this,arguments);
},_pushOnStack:function(_960,obj,func){
if(typeof func=="undefined"){
_960.push(obj);
}else{
_960.push(function(){
obj[func]();
});
}
},destroy:function(){
this.onUnload();
dojo.widget.ContentPane.superclass.destroy.call(this);
},onExecError:function(e){
},onContentError:function(e){
},onDownloadError:function(e){
},onDownloadStart:function(e){
},onDownloadEnd:function(url,data){
data=this.splitAndFixPaths(data,url);
this.setContent(data);
},_handleDefaults:function(e,_96a,_96b){
if(!_96a){
_96a="onContentError";
}
if(dojo.lang.isString(e)){
e={text:e};
}
if(!e.text){
e.text=e.toString();
}
e.toString=function(){
return this.text;
};
if(typeof e.returnValue!="boolean"){
e.returnValue=true;
}
if(typeof e.preventDefault!="function"){
e.preventDefault=function(){
this.returnValue=false;
};
}
this[_96a](e);
if(e.returnValue){
switch(_96b){
case true:
case "alert":
alert(e.toString());
break;
case "debug":
dojo.debug(e.toString());
break;
default:
if(this._callOnUnload){
this.onUnload();
}
this._callOnUnload=false;
if(arguments.callee._loopStop){
dojo.debug(e.toString());
}else{
arguments.callee._loopStop=true;
this._setContent(e.toString());
}
}
}
arguments.callee._loopStop=false;
},splitAndFixPaths:function(s,url){
var _96e=[],_96f=[],tmp=[];
var _971=[],_972=[],attr=[],_974=[];
var str="",path="",fix="",_978="",tag="",_97a="";
if(!url){
url="./";
}
if(s){
var _97b=/<title[^>]*>([\s\S]*?)<\/title>/i;
while(_971=_97b.exec(s)){
_96e.push(_971[1]);
s=s.substring(0,_971.index)+s.substr(_971.index+_971[0].length);
}
if(this.adjustPaths){
var _97c=/<[a-z][a-z0-9]*[^>]*\s(?:(?:src|href|style)=[^>])+[^>]*>/i;
var _97d=/\s(src|href|style)=(['"]?)([\w()\[\]\/.,\\'"-:;#=&?\s@]+?)\2/i;
var _97e=/^(?:[#]|(?:(?:https?|ftps?|file|javascript|mailto|news):))/;
while(tag=_97c.exec(s)){
str+=s.substring(0,tag.index);
s=s.substring((tag.index+tag[0].length),s.length);
tag=tag[0];
_978="";
while(attr=_97d.exec(tag)){
path="";
_97a=attr[3];
switch(attr[1].toLowerCase()){
case "src":
case "href":
if(_97e.exec(_97a)){
path=_97a;
}else{
path=(new dojo.uri.Uri(url,_97a).toString());
}
break;
case "style":
path=dojo.html.fixPathsInCssText(_97a,url);
break;
default:
path=_97a;
}
fix=" "+attr[1]+"="+attr[2]+path+attr[2];
_978+=tag.substring(0,attr.index)+fix;
tag=tag.substring((attr.index+attr[0].length),tag.length);
}
str+=_978+tag;
}
s=str+s;
}
_97b=/(?:<(style)[^>]*>([\s\S]*?)<\/style>|<link ([^>]*rel=['"]?stylesheet['"]?[^>]*)>)/i;
while(_971=_97b.exec(s)){
if(_971[1]&&_971[1].toLowerCase()=="style"){
_974.push(dojo.html.fixPathsInCssText(_971[2],url));
}else{
if(attr=_971[3].match(/href=(['"]?)([^'">]*)\1/i)){
_974.push({path:attr[2]});
}
}
s=s.substring(0,_971.index)+s.substr(_971.index+_971[0].length);
}
var _97b=/<script([^>]*)>([\s\S]*?)<\/script>/i;
var _97f=/src=(['"]?)([^"']*)\1/i;
var _980=/.*(\bdojo\b\.js(?:\.uncompressed\.js)?)$/;
var _981=/(?:var )?\bdjConfig\b(?:[\s]*=[\s]*\{[^}]+\}|\.[\w]*[\s]*=[\s]*[^;\n]*)?;?|dojo\.hostenv\.writeIncludes\(\s*\);?/g;
var _982=/dojo\.(?:(?:require(?:After)?(?:If)?)|(?:widget\.(?:manager\.)?registerWidgetPackage)|(?:(?:hostenv\.)?setModulePrefix|registerModulePath)|defineNamespace)\((['"]).*?\1\)\s*;?/;
while(_971=_97b.exec(s)){
if(this.executeScripts&&_971[1]){
if(attr=_97f.exec(_971[1])){
if(_980.exec(attr[2])){
dojo.debug("Security note! inhibit:"+attr[2]+" from  being loaded again.");
}else{
_96f.push({path:attr[2]});
}
}
}
if(_971[2]){
var sc=_971[2].replace(_981,"");
if(!sc){
continue;
}
while(tmp=_982.exec(sc)){
_972.push(tmp[0]);
sc=sc.substring(0,tmp.index)+sc.substr(tmp.index+tmp[0].length);
}
if(this.executeScripts){
_96f.push(sc);
}
}
s=s.substr(0,_971.index)+s.substr(_971.index+_971[0].length);
}
if(this.extractContent){
_971=s.match(/<body[^>]*>\s*([\s\S]+)\s*<\/body>/im);
if(_971){
s=_971[1];
}
}
if(this.executeScripts&&this.scriptSeparation){
var _97b=/(<[a-zA-Z][a-zA-Z0-9]*\s[^>]*?\S=)((['"])[^>]*scriptScope[^>]*>)/;
var _984=/([\s'";:\(])scriptScope(.*)/;
str="";
while(tag=_97b.exec(s)){
tmp=((tag[3]=="'")?"\"":"'");
fix="";
str+=s.substring(0,tag.index)+tag[1];
while(attr=_984.exec(tag[2])){
tag[2]=tag[2].substring(0,attr.index)+attr[1]+"dojo.widget.byId("+tmp+this.widgetId+tmp+").scriptScope"+attr[2];
}
str+=tag[2];
s=s.substr(tag.index+tag[0].length);
}
s=str+s;
}
}
return {"xml":s,"styles":_974,"titles":_96e,"requires":_972,"scripts":_96f,"url":url};
},_setContent:function(cont){
this.destroyChildren();
for(var i=0;i<this._styleNodes.length;i++){
if(this._styleNodes[i]&&this._styleNodes[i].parentNode){
this._styleNodes[i].parentNode.removeChild(this._styleNodes[i]);
}
}
this._styleNodes=[];
var node=this.containerNode||this.domNode;
while(node.firstChild){
try{
dojo.event.browser.clean(node.firstChild);
}
catch(e){
}
node.removeChild(node.firstChild);
}
try{
if(typeof cont!="string"){
node.innerHTML="";
node.appendChild(cont);
}else{
node.innerHTML=cont;
}
}
catch(e){
e.text="Couldn't load content:"+e.description;
this._handleDefaults(e,"onContentError");
}
},setContent:function(data){
this.abort();
if(this._callOnUnload){
this.onUnload();
}
this._callOnUnload=true;
if(!data||dojo.html.isNode(data)){
this._setContent(data);
this.onResized();
this.onLoad();
}else{
if(typeof data.xml!="string"){
this.href="";
data=this.splitAndFixPaths(data);
}
this._setContent(data.xml);
for(var i=0;i<data.styles.length;i++){
if(data.styles[i].path){
this._styleNodes.push(dojo.html.insertCssFile(data.styles[i].path,dojo.doc(),false,true));
}else{
this._styleNodes.push(dojo.html.insertCssText(data.styles[i]));
}
}
if(this.parseContent){
for(var i=0;i<data.requires.length;i++){
try{
eval(data.requires[i]);
}
catch(e){
e.text="ContentPane: error in package loading calls, "+(e.description||e);
this._handleDefaults(e,"onContentError","debug");
}
}
}
var _98a=this;
function asyncParse(){
if(_98a.executeScripts){
_98a._executeScripts(data.scripts);
}
if(_98a.parseContent){
var node=_98a.containerNode||_98a.domNode;
var _98c=new dojo.xml.Parse();
var frag=_98c.parseElement(node,null,true);
dojo.widget.getParser().createSubComponents(frag,_98a);
}
_98a.onResized();
_98a.onLoad();
}
if(dojo.hostenv.isXDomain&&data.requires.length){
dojo.addOnLoad(asyncParse);
}else{
asyncParse();
}
}
},setHandler:function(_98e){
var fcn=dojo.lang.isFunction(_98e)?_98e:window[_98e];
if(!dojo.lang.isFunction(fcn)){
this._handleDefaults("Unable to set handler, '"+_98e+"' not a function.","onExecError",true);
return;
}
this.handler=function(){
return fcn.apply(this,arguments);
};
},_runHandler:function(){
var ret=true;
if(dojo.lang.isFunction(this.handler)){
this.handler(this,this.domNode);
ret=false;
}
this.onLoad();
return ret;
},_executeScripts:function(_991){
var self=this;
var tmp="",code="";
for(var i=0;i<_991.length;i++){
if(_991[i].path){
dojo.io.bind(this._cacheSetting({"url":_991[i].path,"load":function(type,_997){
dojo.lang.hitch(self,tmp=";"+_997);
},"error":function(type,_999){
_999.text=type+" downloading remote script";
self._handleDefaults.call(self,_999,"onExecError","debug");
},"mimetype":"text/plain","sync":true},this.cacheContent));
code+=tmp;
}else{
code+=_991[i];
}
}
try{
if(this.scriptSeparation){
delete this.scriptScope;
this.scriptScope=new (new Function("_container_",code+"; return this;"))(self);
}else{
var djg=dojo.global();
if(djg.execScript){
djg.execScript(code);
}else{
var djd=dojo.doc();
var sc=djd.createElement("script");
sc.appendChild(djd.createTextNode(code));
(this.containerNode||this.domNode).appendChild(sc);
}
}
}
catch(e){
e.text="Error running scripts from content:\n"+e.description;
this._handleDefaults(e,"onExecError","debug");
}
}});
dojo.provide("dojo.widget.Dialog");
dojo.declare("dojo.widget.ModalDialogBase",null,{isContainer:true,focusElement:"",bgColor:"black",bgOpacity:0.4,followScroll:true,closeOnBackgroundClick:false,trapTabs:function(e){
if(e.target==this.tabStartOuter){
if(this._fromTrap){
this.tabStart.focus();
this._fromTrap=false;
}else{
this._fromTrap=true;
this.tabEnd.focus();
}
}else{
if(e.target==this.tabStart){
if(this._fromTrap){
this._fromTrap=false;
}else{
this._fromTrap=true;
this.tabEnd.focus();
}
}else{
if(e.target==this.tabEndOuter){
if(this._fromTrap){
this.tabEnd.focus();
this._fromTrap=false;
}else{
this._fromTrap=true;
this.tabStart.focus();
}
}else{
if(e.target==this.tabEnd){
if(this._fromTrap){
this._fromTrap=false;
}else{
this._fromTrap=true;
this.tabStart.focus();
}
}
}
}
}
},clearTrap:function(e){
var _99f=this;
setTimeout(function(){
_99f._fromTrap=false;
},100);
},postCreate:function(){
with(this.domNode.style){
position="absolute";
zIndex=999;
display="none";
overflow="visible";
}
var b=dojo.body();
b.appendChild(this.domNode);
this.bg=document.createElement("div");
this.bg.className="dialogUnderlay";
with(this.bg.style){
position="absolute";
left=top="0px";
zIndex=998;
display="none";
}
b.appendChild(this.bg);
this.setBackgroundColor(this.bgColor);
this.bgIframe=new dojo.html.BackgroundIframe();
if(this.bgIframe.iframe){
with(this.bgIframe.iframe.style){
position="absolute";
left=top="0px";
zIndex=90;
display="none";
}
}
if(this.closeOnBackgroundClick){
dojo.event.kwConnect({srcObj:this.bg,srcFunc:"onclick",adviceObj:this,adviceFunc:"onBackgroundClick",once:true});
}
},uninitialize:function(){
this.bgIframe.remove();
dojo.html.removeNode(this.bg,true);
},setBackgroundColor:function(_9a1){
if(arguments.length>=3){
_9a1=new dojo.gfx.color.Color(arguments[0],arguments[1],arguments[2]);
}else{
_9a1=new dojo.gfx.color.Color(_9a1);
}
this.bg.style.backgroundColor=_9a1.toString();
return this.bgColor=_9a1;
},setBackgroundOpacity:function(op){
if(arguments.length==0){
op=this.bgOpacity;
}
dojo.html.setOpacity(this.bg,op);
try{
this.bgOpacity=dojo.html.getOpacity(this.bg);
}
catch(e){
this.bgOpacity=op;
}
return this.bgOpacity;
},_sizeBackground:function(){
if(this.bgOpacity>0){
var _9a3=dojo.html.getViewport();
var h=_9a3.height;
var w=_9a3.width;
with(this.bg.style){
width=w+"px";
height=h+"px";
}
var _9a6=dojo.html.getScroll().offset;
this.bg.style.top=_9a6.y+"px";
this.bg.style.left=_9a6.x+"px";
var _9a3=dojo.html.getViewport();
if(_9a3.width!=w){
this.bg.style.width=_9a3.width+"px";
}
if(_9a3.height!=h){
this.bg.style.height=_9a3.height+"px";
}
}
this.bgIframe.size(this.bg);
},_showBackground:function(){
if(this.bgOpacity>0){
this.bg.style.display="block";
}
if(this.bgIframe.iframe){
this.bgIframe.iframe.style.display="block";
}
},placeModalDialog:function(){
var _9a7=dojo.html.getScroll().offset;
var _9a8=dojo.html.getViewport();
var mb;
if(this.isShowing()){
mb=dojo.html.getMarginBox(this.domNode);
}else{
dojo.html.setVisibility(this.domNode,false);
dojo.html.show(this.domNode);
mb=dojo.html.getMarginBox(this.domNode);
dojo.html.hide(this.domNode);
dojo.html.setVisibility(this.domNode,true);
}
var x=_9a7.x+(_9a8.width-mb.width)/2;
var y=_9a7.y+(_9a8.height-mb.height)/2;
with(this.domNode.style){
left=x+"px";
top=y+"px";
}
},_onKey:function(evt){
if(evt.key){
var node=evt.target;
while(node!=null){
if(node==this.domNode){
return;
}
node=node.parentNode;
}
if(evt.key!=evt.KEY_TAB){
dojo.event.browser.stopEvent(evt);
}else{
if(!dojo.render.html.opera){
try{
this.tabStart.focus();
}
catch(e){
}
}
}
}
},showModalDialog:function(){
if(this.followScroll&&!this._scrollConnected){
this._scrollConnected=true;
dojo.event.connect(window,"onscroll",this,"_onScroll");
}
dojo.event.connect(document.documentElement,"onkey",this,"_onKey");
this.placeModalDialog();
this.setBackgroundOpacity();
this._sizeBackground();
this._showBackground();
this._fromTrap=true;
setTimeout(dojo.lang.hitch(this,function(){
try{
this.tabStart.focus();
}
catch(e){
}
}),50);
},hideModalDialog:function(){
if(this.focusElement){
dojo.byId(this.focusElement).focus();
dojo.byId(this.focusElement).blur();
}
this.bg.style.display="none";
this.bg.style.width=this.bg.style.height="1px";
if(this.bgIframe.iframe){
this.bgIframe.iframe.style.display="none";
}
dojo.event.disconnect(document.documentElement,"onkey",this,"_onKey");
if(this._scrollConnected){
this._scrollConnected=false;
dojo.event.disconnect(window,"onscroll",this,"_onScroll");
}
},_onScroll:function(){
var _9ae=dojo.html.getScroll().offset;
this.bg.style.top=_9ae.y+"px";
this.bg.style.left=_9ae.x+"px";
this.placeModalDialog();
},checkSize:function(){
if(this.isShowing()){
this._sizeBackground();
this.placeModalDialog();
this.onResized();
}
},onBackgroundClick:function(){
if(this.lifetime-this.timeRemaining>=this.blockDuration){
return;
}
this.hide();
}});
dojo.widget.defineWidget("dojo.widget.Dialog",[dojo.widget.ContentPane,dojo.widget.ModalDialogBase],{templatePath:dojo.uri.dojoUri("src/widget/templates/Dialog.html"),blockDuration:0,lifetime:0,closeNode:"",postMixInProperties:function(){
dojo.widget.Dialog.superclass.postMixInProperties.apply(this,arguments);
if(this.closeNode){
this.setCloseControl(this.closeNode);
}
},postCreate:function(){
dojo.widget.Dialog.superclass.postCreate.apply(this,arguments);
dojo.widget.ModalDialogBase.prototype.postCreate.apply(this,arguments);
},show:function(){
if(this.lifetime){
this.timeRemaining=this.lifetime;
if(this.timerNode){
this.timerNode.innerHTML=Math.ceil(this.timeRemaining/1000);
}
if(this.blockDuration&&this.closeNode){
if(this.lifetime>this.blockDuration){
this.closeNode.style.visibility="hidden";
}else{
this.closeNode.style.display="none";
}
}
if(this.timer){
clearInterval(this.timer);
}
this.timer=setInterval(dojo.lang.hitch(this,"_onTick"),100);
}
this.showModalDialog();
dojo.widget.Dialog.superclass.show.call(this);
},onLoad:function(){
this.placeModalDialog();
dojo.widget.Dialog.superclass.onLoad.call(this);
},fillInTemplate:function(){
},hide:function(){
this.hideModalDialog();
dojo.widget.Dialog.superclass.hide.call(this);
if(this.timer){
clearInterval(this.timer);
}
},setTimerNode:function(node){
this.timerNode=node;
},setCloseControl:function(node){
this.closeNode=dojo.byId(node);
dojo.event.connect(this.closeNode,"onclick",this,"hide");
},setShowControl:function(node){
node=dojo.byId(node);
dojo.event.connect(node,"onclick",this,"show");
},_onTick:function(){
if(this.timer){
this.timeRemaining-=100;
if(this.lifetime-this.timeRemaining>=this.blockDuration){
if(this.closeNode){
this.closeNode.style.visibility="visible";
}
}
if(!this.timeRemaining){
clearInterval(this.timer);
this.hide();
}else{
if(this.timerNode){
this.timerNode.innerHTML=Math.ceil(this.timeRemaining/1000);
}
}
}
}});
dojo.provide("dojo.widget.RichText");
if(dojo.hostenv.post_load_){
(function(){
var _9b2=dojo.doc().createElement("textarea");
_9b2.id="dojo.widget.RichText.savedContent";
_9b2.style="display:none;position:absolute;top:-100px;left:-100px;height:3px;width:3px;overflow:hidden;";
dojo.body().appendChild(_9b2);
})();
}else{
try{
dojo.doc().write("<textarea id=\"dojo.widget.RichText.savedContent\" "+"style=\"display:none;position:absolute;top:-100px;left:-100px;height:3px;width:3px;overflow:hidden;\"></textarea>");
}
catch(e){
}
}
dojo.widget.defineWidget("dojo.widget.RichText",dojo.widget.HtmlWidget,function(){
this.contentPreFilters=[];
this.contentPostFilters=[];
this.contentDomPreFilters=[];
this.contentDomPostFilters=[];
this.editingAreaStyleSheets=[];
if(dojo.render.html.moz){
this.contentPreFilters.push(this._fixContentForMoz);
}
this._keyHandlers={};
if(dojo.Deferred){
this.onLoadDeferred=new dojo.Deferred();
}
},{inheritWidth:false,focusOnLoad:false,preFilterTextarea:false,saveName:"",styleSheets:"",_content:"",height:"",minHeight:"4em",isClosed:true,isLoaded:false,useActiveX:false,relativeImageUrls:false,_SEPARATOR:"@@**%%__RICHTEXTBOUNDRY__%%**@@",onLoadDeferred:null,fillInTemplate:function(){
dojo.event.topic.publish("dojo.widget.RichText::init",this);
this.open();
dojo.event.connect(this,"onKeyPressed",this,"afterKeyPress");
dojo.event.connect(this,"onKeyPress",this,"keyPress");
dojo.event.connect(this,"onKeyDown",this,"keyDown");
dojo.event.connect(this,"onKeyUp",this,"keyUp");
this.setupDefaultShortcuts();
},setupDefaultShortcuts:function(){
var ctrl=this.KEY_CTRL;
var exec=function(cmd,arg){
return arguments.length==1?function(){
this.execCommand(cmd);
}:function(){
this.execCommand(cmd,arg);
};
};
this.addKeyHandler("b",ctrl,exec("bold"));
this.addKeyHandler("i",ctrl,exec("italic"));
this.addKeyHandler("u",ctrl,exec("underline"));
this.addKeyHandler("a",ctrl,exec("selectall"));
this.addKeyHandler("s",ctrl,function(){
this.save(true);
});
this.addKeyHandler("1",ctrl,exec("formatblock","h1"));
this.addKeyHandler("2",ctrl,exec("formatblock","h2"));
this.addKeyHandler("3",ctrl,exec("formatblock","h3"));
this.addKeyHandler("4",ctrl,exec("formatblock","h4"));
this.addKeyHandler("\\",ctrl,exec("insertunorderedlist"));
if(!dojo.render.html.ie){
this.addKeyHandler("Z",ctrl,exec("redo"));
}
},events:["onBlur","onFocus","onKeyPress","onKeyDown","onKeyUp","onClick"],open:function(_9b7){
if(this.onLoadDeferred.fired>=0){
this.onLoadDeferred=new dojo.Deferred();
}
var h=dojo.render.html;
if(!this.isClosed){
this.close();
}
dojo.event.topic.publish("dojo.widget.RichText::open",this);
this._content="";
if((arguments.length==1)&&(_9b7["nodeName"])){
this.domNode=_9b7;
}
if((this.domNode["nodeName"])&&(this.domNode.nodeName.toLowerCase()=="textarea")){
this.textarea=this.domNode;
var html=dojo.string.trim(this.textarea.value);
if(this.preFilterTextarea){
html=this._preFilterContent(html);
}
this.domNode=dojo.doc().createElement("div");
dojo.html.copyStyle(this.domNode,this.textarea);
var _9ba=dojo.lang.hitch(this,function(){
with(this.textarea.style){
display="block";
position="absolute";
left=top="-1000px";
if(h.ie){
this.__overflow=overflow;
overflow="hidden";
}
}
});
if(h.ie){
setTimeout(_9ba,10);
}else{
_9ba();
}
if(!h.safari){
dojo.html.insertBefore(this.domNode,this.textarea);
}
if(this.textarea.form){
dojo.event.connect("before",this.textarea.form,"onsubmit",dojo.lang.hitch(this,function(){
this.textarea.value=this.getEditorContent();
}));
}
var _9bb=this;
dojo.event.connect(this,"postCreate",function(){
dojo.html.insertAfter(_9bb.textarea,_9bb.domNode);
});
}else{
var html=this._preFilterContent(dojo.string.trim(this.domNode.innerHTML));
}
if(html == ""){
if(dojo.render.html.ie) {html='<br><br>';} else {html = "&nbsp;"; }
}
var _9bc=dojo.html.getContentBox(this.domNode);
this._oldHeight=_9bc.height;
this._oldWidth=_9bc.width;
this._firstChildContributingMargin=this._getContributingMargin(this.domNode,"top");
this._lastChildContributingMargin=this._getContributingMargin(this.domNode,"bottom");
this.savedContent=this.domNode.innerHTML;
this.domNode.innerHTML="";
if((this.domNode["nodeName"])&&(this.domNode.nodeName=="LI")){
this.domNode.innerHTML=" <br>";
}
this.editingArea=dojo.doc().createElement("div");
this.domNode.appendChild(this.editingArea);
if(this.saveName!=""){
var _9bd=dojo.doc().getElementById("dojo.widget.RichText.savedContent");
if(_9bd.value!=""){
var _9be=_9bd.value.split(this._SEPARATOR);
for(var i=0;i<_9be.length;i++){
var data=_9be[i].split(":");
if(data[0]==this.saveName){
html=data[1];
_9be.splice(i,1);
break;
}
}
}
dojo.event.connect("before",window,"onunload",this,"_saveContent");
}
if(h.ie70&&this.useActiveX){
dojo.debug("activeX in ie70 is not currently supported, useActiveX is ignored for now.");
this.useActiveX=false;
}
if(this.useActiveX&&h.ie){
var self=this;
setTimeout(function(){
self._drawObject(html);
},0);
}else{
if(h.ie){
this.iframe=dojo.doc().createElement("iframe");
this.iframe.src="javascript:void(0)";
this.editorObject=this.iframe;
with(this.iframe.style){
border="0";
width="100%";
}
this.iframe.frameBorder=0;
this.editingArea.appendChild(this.iframe);
this.window=this.iframe.contentWindow;
this.document=this.window.document;
this.document.open();
this.document.write("<html><head><style>body{margin:0;padding:0;border:0;overflow:hidden;}</style></head><body><div></div></body></html>");
this.document.close();
this.editNode=this.document.body.firstChild;
this.editNode.contentEditable=true;
with(this.iframe.style){
if(h.ie70){
if(this.height){
height=this.height;
}
if(this.minHeight){
minHeight=this.minHeight;
}
}else{
height=this.height?this.height:this.minHeight;
}
}
if(!this._cacheLocalBlockFormatNames()){
var _9c2=["p","pre","address","h1","h2","h3","h4","h5","h6","ol","div","ul"];
var _9c3="";
for(var i in _9c2){
if(_9c2[i].charAt(1)!="l"){
_9c3+="<"+_9c2[i]+"><span>content</span></"+_9c2[i]+">";
}else{
_9c3+="<"+_9c2[i]+"><li>content</li></"+_9c2[i]+">";
}
}
with(this.editNode.style){
position="absolute";
left="-2000px";
top="-2000px";
}
this.editNode.innerHTML=_9c3;
var node=this.editNode.firstChild;
while(node){
dojo.withGlobal(this.window,"selectElement",dojo.html.selection,[node.firstChild]);
var _9c5=node.tagName.toLowerCase();
this._local2NativeFormatNames[_9c5]=this.queryCommandValue("formatblock");
this._native2LocalFormatNames[this._local2NativeFormatNames[_9c5]]=_9c5;
node=node.nextSibling;
}
with(this.editNode.style){
position="";
left="";
top="";
}
}
this.editNode.innerHTML=html;
if(this.height){
this.document.body.style.overflowY="scroll";
}
dojo.lang.forEach(this.events,function(e){
dojo.event.connect(this.editNode,e.toLowerCase(),this,e);
},this);
this.onLoad();
}else{
this._drawIframe(html);
this.editorObject=this.iframe;
}
}
if(this.domNode.nodeName=="LI"){
this.domNode.lastChild.style.marginTop="-1.2em";
}
dojo.html.addClass(this.domNode,"RichTextEditable");
this.isClosed=false;
},_hasCollapseableMargin:function(_9c7,side){
if(dojo.html.getPixelValue(_9c7,"border-"+side+"-width",false)){
return false;
}else{
if(dojo.html.getPixelValue(_9c7,"padding-"+side,false)){
return false;
}else{
return true;
}
}
},_getContributingMargin:function(_9c9,_9ca){
if(_9ca=="top"){
var _9cb="previousSibling";
var _9cc="nextSibling";
var _9cd="firstChild";
var _9ce="margin-top";
var _9cf="margin-bottom";
}else{
var _9cb="nextSibling";
var _9cc="previousSibling";
var _9cd="lastChild";
var _9ce="margin-bottom";
var _9cf="margin-top";
}
var _9d0=dojo.html.getPixelValue(_9c9,_9ce,false);
function isSignificantNode(_9d1){
return !(_9d1.nodeType==3&&dojo.string.isBlank(_9d1.data))&&dojo.html.getStyle(_9d1,"display")!="none"&&!dojo.html.isPositionAbsolute(_9d1);
}
var _9d2=0;
var _9d3=_9c9[_9cd];
while(_9d3){
while((!isSignificantNode(_9d3))&&_9d3[_9cc]){
_9d3=_9d3[_9cc];
}
_9d2=Math.max(_9d2,dojo.html.getPixelValue(_9d3,_9ce,false));
if(!this._hasCollapseableMargin(_9d3,_9ca)){
break;
}
_9d3=_9d3[_9cd];
}
if(!this._hasCollapseableMargin(_9c9,_9ca)){
return parseInt(_9d2);
}
var _9d4=0;
var _9d5=_9c9[_9cb];
while(_9d5){
if(isSignificantNode(_9d5)){
_9d4=dojo.html.getPixelValue(_9d5,_9cf,false);
break;
}
_9d5=_9d5[_9cb];
}
if(!_9d5){
_9d4=dojo.html.getPixelValue(_9c9.parentNode,_9ce,false);
}
if(_9d2>_9d0){
return parseInt(Math.max((_9d2-_9d0)-_9d4,0));
}else{
return 0;
}
},_drawIframe:function(html){
var _9d7=Boolean(dojo.render.html.moz&&(typeof window.XML=="undefined"));
if(!this.iframe){
var _9d8=(new dojo.uri.Uri(dojo.doc().location)).host;
this.iframe=dojo.doc().createElement("iframe");
with(this.iframe){
style.border="none";
style.lineHeight="0";
style.verticalAlign="bottom";
scrolling=this.height?"auto":"no";
}
}
this.iframe.src=dojo.uri.dojoUri("src/widget/templates/richtextframe.html")+((dojo.doc().domain!=_9d8)?("#"+dojo.doc().domain):"");
this.iframe.width=this.inheritWidth?this._oldWidth:"100%";
if(this.height){
this.iframe.style.height=this.height;
}else{
var _9d9=this._oldHeight;
if(this._hasCollapseableMargin(this.domNode,"top")){
_9d9+=this._firstChildContributingMargin;
}
if(this._hasCollapseableMargin(this.domNode,"bottom")){
_9d9+=this._lastChildContributingMargin;
}
this.iframe.height=_9d9;
}
var _9da=dojo.doc().createElement("div");
_9da.innerHTML=html;
this.editingArea.appendChild(_9da);
if(this.relativeImageUrls){
var imgs=_9da.getElementsByTagName("img");
for(var i=0;i<imgs.length;i++){
imgs[i].src=(new dojo.uri.Uri(dojo.global().location,imgs[i].src)).toString();
}
html=_9da.innerHTML;
}
var _9dd=dojo.html.firstElement(_9da);
var _9de=dojo.html.lastElement(_9da);
if(_9dd){
_9dd.style.marginTop=this._firstChildContributingMargin+"px";
}
if(_9de){
_9de.style.marginBottom=this._lastChildContributingMargin+"px";
}
this.editingArea.appendChild(this.iframe);
if(dojo.render.html.safari){
this.iframe.src=this.iframe.src;
}
var _9df=false;
var _9e0=dojo.lang.hitch(this,function(){
if(!_9df){
_9df=true;
}else{
return;
}
if(!this.editNode){
if(this.iframe.contentWindow){
this.window=this.iframe.contentWindow;
this.document=this.iframe.contentWindow.document;
}else{
if(this.iframe.contentDocument){
this.window=this.iframe.contentDocument.window;
this.document=this.iframe.contentDocument;
}
}
var _9e1=(function(_9e2){
return function(_9e3){
return dojo.html.getStyle(_9e2,_9e3);
};
})(this.domNode);
var font=_9e1("font-weight")+" "+_9e1("font-size")+" "+_9e1("font-family");
var _9e5="1.25";
var _9e6=dojo.html.getUnitValue(this.domNode,"line-height");
if(_9e6.value&&_9e6.units==""){
_9e5=_9e6.value;
}
dojo.html.insertCssText("body,html{background:transparent;padding:0;margin:0;}"+"body{top:0;left:0;right:0;"+(((this.height)||(dojo.render.html.opera))?"":"position:fixed;")+"font:"+font+";"+"min-height:"+this.minHeight+";"+"line-height:"+_9e5+"}"+"p{margin: 1em 0 !important;}"+"body > *:first-child{padding-top:0 !important;margin-top:"+this._firstChildContributingMargin+"px !important;}"+"body > *:last-child{padding-bottom:0 !important;margin-bottom:"+this._lastChildContributingMargin+"px !important;}"+"li > ul:-moz-first-node, li > ol:-moz-first-node{padding-top:1.2em;}\n"+"li{min-height:1.2em;}"+"",this.document);
dojo.html.removeNode(_9da);
this.document.body.innerHTML=html;
if(_9d7||dojo.render.html.safari){
this.document.designMode="on";
}
this.onLoad();
}else{
dojo.html.removeNode(_9da);
this.editNode.innerHTML=html;
this.onDisplayChanged();
}
});
if(this.editNode){
_9e0(); // iframe already exists, just set content
}else if(dojo.render.html.mozilla){
// mozilla needs some time to have the iframe ready
setTimeout(_9e0, (this.easyEditClicked?666:250));
}else{ // opera, safari
_9e0();
}

},_applyEditingAreaStyleSheets:function(){
var _9e7=[];
if(this.styleSheets){
_9e7=this.styleSheets.split(";");
this.styleSheets="";
}
_9e7=_9e7.concat(this.editingAreaStyleSheets);
this.editingAreaStyleSheets=[];
if(_9e7.length>0){
for(var i=0;i<_9e7.length;i++){
var url=_9e7[i];
if(url){
this.addStyleSheet(dojo.uri.dojoUri(url));
}
}
}
},addStyleSheet:function(uri){
var url=uri.toString();
if(dojo.lang.find(this.editingAreaStyleSheets,url)>-1){
dojo.debug("dojo.widget.RichText.addStyleSheet: Style sheet "+url+" is already applied to the editing area!");
return;
}
if(url.charAt(0)=="."||(url.charAt(0)!="/"&&!uri.host)){
url=(new dojo.uri.Uri(dojo.global().location,url)).toString();
}
this.editingAreaStyleSheets.push(url);
if(this.document.createStyleSheet){
this.document.createStyleSheet(url);
}else{
var head=this.document.getElementsByTagName("head")[0];
var _9ed=this.document.createElement("link");
with(_9ed){
rel="stylesheet";
type="text/css";
href=url;
}
head.appendChild(_9ed);
}
},removeStyleSheet:function(uri){
var url=uri.toString();
if(url.charAt(0)=="."||(url.charAt(0)!="/"&&!uri.host)){
url=(new dojo.uri.Uri(dojo.global().location,url)).toString();
}
var _9f0=dojo.lang.find(this.editingAreaStyleSheets,url);
if(_9f0==-1){
dojo.debug("dojo.widget.RichText.removeStyleSheet: Style sheet "+url+" is not applied to the editing area so it can not be removed!");
return;
}
delete this.editingAreaStyleSheets[_9f0];
var _9f1=this.document.getElementsByTagName("link");
for(var i=0;i<_9f1.length;i++){
if(_9f1[i].href==url){
if(dojo.render.html.ie){
_9f1[i].href="";
}
dojo.html.removeNode(_9f1[i]);
break;
}
}
},_drawObject:function(html){
this.object=dojo.html.createExternalElement(dojo.doc(),"object");
with(this.object){
classid="clsid:2D360201-FFF5-11D1-8D03-00A0C959BC0A";
width=this.inheritWidth?this._oldWidth:"100%";
style.height=this.height?this.height:(this._oldHeight+"px");
Scrollbars=this.height?true:false;
Appearance=this._activeX.appearance.flat;
}
this.editorObject=this.object;
this.editingArea.appendChild(this.object);
this.object.attachEvent("DocumentComplete",dojo.lang.hitch(this,"onLoad"));
dojo.lang.forEach(this.events,function(e){
this.object.attachEvent(e.toLowerCase(),dojo.lang.hitch(this,e));
},this);
this.object.DocumentHTML="<!doctype HTML PUBLIC \"-//W3C//DTD HTML 4.01//EN\" \"http://www.w3.org/TR/html4/strict.dtd\">"+"<html><title></title>"+"<style type=\"text/css\">"+"    body,html { padding: 0; margin: 0; }"+(this.height?"":"    body,  { overflow: hidden; }")+"</style>"+"<body><div>"+html+"<div></body></html>";
this._cacheLocalBlockFormatNames();
},_local2NativeFormatNames:{},_native2LocalFormatNames:{},_cacheLocalBlockFormatNames:function(){
if(!this._native2LocalFormatNames["p"]){
var obj=this.object;
var _9f6=false;
if(!obj){
try{
obj=dojo.html.createExternalElement(dojo.doc(),"object");
obj.classid="clsid:2D360201-FFF5-11D1-8D03-00A0C959BC0A";
dojo.body().appendChild(obj);
obj.DocumentHTML="<html><head></head><body></body></html>";
}
catch(e){
_9f6=true;
}
}
try{
var _9f7=new ActiveXObject("DEGetBlockFmtNamesParam.DEGetBlockFmtNamesParam");
obj.ExecCommand(this._activeX.command["getblockformatnames"],0,_9f7);
var _9f8=new VBArray(_9f7.Names);
var _9f9=_9f8.toArray();
var _9fa=["p","pre","address","h1","h2","h3","h4","h5","h6","ol","ul","","","","","div"];
for(var i=0;i<_9fa.length;++i){
if(_9fa[i].length>0){
this._local2NativeFormatNames[_9f9[i]]=_9fa[i];
this._native2LocalFormatNames[_9fa[i]]=_9f9[i];
}
}
}
catch(e){
_9f6=true;
}
if(obj){
dojo.body().removeChild(obj);
}
}
return !_9f6;
},_isResized:function(){
return false;
},onLoad:function(e){
this.isLoaded=true;
if(this.object){
this.document=this.object.DOM;
this.window=this.document.parentWindow;
this.editNode=this.document.body.firstChild;
this.editingArea.style.height=this.height?this.height:this.minHeight;
if(!this.height){
this.connect(this,"onDisplayChanged","_updateHeight");
}
this.window._frameElement=this.object;
}else{
if(this.iframe&&!dojo.render.html.ie){
this.editNode=this.document.body;
if(!this.height){
this.connect(this,"onDisplayChanged","_updateHeight");
}
try{
this.document.execCommand("useCSS",false,true);
this.document.execCommand("styleWithCSS",false,false);
}
catch(e2){
}
if(dojo.render.html.safari){
this.connect(this.editNode,"onblur","onBlur");
this.connect(this.editNode,"onfocus","onFocus");
this.connect(this.editNode,"onclick","onFocus");
this.interval=setInterval(dojo.lang.hitch(this,"onDisplayChanged"),750);
}else{
if(dojo.render.html.mozilla||dojo.render.html.opera){
var doc=this.document;
var _9fe=dojo.event.browser.addListener;
var self=this;
dojo.lang.forEach(this.events,function(e){
var l=_9fe(self.document,e.substr(2).toLowerCase(),dojo.lang.hitch(self,e));
if(e=="onBlur"){
var _a02={unBlur:function(e){
dojo.event.browser.removeListener(doc,"blur",l);
}};
dojo.event.connect("before",self,"close",_a02,"unBlur");
}
});
}
}
}else{
if(dojo.render.html.ie){
if(!this.height){
this.connect(this,"onDisplayChanged","_updateHeight");
}
this.editNode.style.zoom=1;
}
}
}
this._applyEditingAreaStyleSheets();
if(this.focusOnLoad){
this.focus();
}
this.onDisplayChanged(e);
if(this.onLoadDeferred){
this.onLoadDeferred.callback(true);
}
},onKeyDown:function(e){
if((!e)&&(this.object)){
e=dojo.event.browser.fixEvent(this.window.event);
}
if((dojo.render.html.ie)&&(e.keyCode==e.KEY_TAB)){
e.preventDefault();
e.stopPropagation();
this.execCommand((e.shiftKey?"outdent":"indent"));
}else{
if(dojo.render.html.ie){
if((65<=e.keyCode)&&(e.keyCode<=90)){
e.charCode=e.keyCode;
this.onKeyPress(e);
}
}
}
if(e.keyCode<41&&e.keyCode>36&&!e.shiftKey){
var _a05=dojo.withGlobal(this.window,"getSelectedText",dojo.html.selection);
this.oldrng=((_a05==null||_a05.length==0)?this._createRange():null);
}else{
this.oldrng=null;
}
},onKeyUp:function(e){
if(this.oldrng&&e.keyCode<41&&e.keyCode>36){
var _a07=this._createRange();
if((dojo.render.html.ie&&_a07.offsetLeft==this.oldrng.offsetLeft&&_a07.offsetTop==this.oldrng.offsetTop)||(!dojo.render.html.ie&&_a07.startContainer==this.oldrng.startContainer&&((e.keyCode<39&&_a07.startOffset==this.oldrng.startOffset)||(_a07.endOffset==this.oldrng.endOffset)))){
this.execCommand("selectall");
dojo.withGlobal(this.window,"collapse",dojo.html.selection,[e.keyCode<39]);
}
}
return;
},KEY_CTRL:1,onKeyPress:function(e){
if((!e)&&(this.object)){
e=dojo.event.browser.fixEvent(this.window.event);
}
var _a09=e.ctrlKey?this.KEY_CTRL:0;
if(this._keyHandlers[e.key]){
var _a0a=this._keyHandlers[e.key],i=0,_a0c;
while(_a0c=_a0a[i++]){
if(_a09==_a0c.modifiers){
e.preventDefault();
_a0c.handler.call(this);
break;
}
}
}
dojo.lang.setTimeout(this,this.onKeyPressed,1,e);
},addKeyHandler:function(key,_a0e,_a0f){
if(!(this._keyHandlers[key] instanceof Array)){
this._keyHandlers[key]=[];
}
this._keyHandlers[key].push({modifiers:_a0e||0,handler:_a0f});
},onKeyPressed:function(e){
this.onDisplayChanged();
},onClick:function(e){
this.onDisplayChanged(e);
},onBlur:function(e){
},_initialFocus:true,onFocus:function(e){
if(this._initialFocus){
this._initialFocus=false;
var tc=dojo.string.trim(this.editNode.innerHTML)
if(tc == "&nbsp;" || tc=='<BR><BR>'){
	this.placeCursorAtStart();
}

}
},blur:function(){
if(this.iframe){
this.window.blur();
}else{
if(this.object){
this.document.body.blur();
}else{
if(this.editNode){
this.editNode.blur();
}
}
}
},focus:function(){
if(this.iframe&&!dojo.render.html.ie){
this.window.focus();
}else{
if(this.object){
this.document.focus();
}else{
if(this.editNode&&this.editNode.focus){
this.editNode.focus();
}else{
dojo.debug("Have no idea how to focus into the editor!");
}
}
}
},onDisplayChanged:function(e){
},_activeX:{command:{bold:5000,italic:5023,underline:5048,justifycenter:5024,justifyleft:5025,justifyright:5026,cut:5003,copy:5002,paste:5032,"delete":5004,undo:5049,redo:5033,removeformat:5034,selectall:5035,unlink:5050,indent:5018,outdent:5031,insertorderedlist:5030,insertunorderedlist:5051,inserttable:5022,insertcell:5019,insertcol:5020,insertrow:5021,deletecells:5005,deletecols:5006,deleterows:5007,mergecells:5029,splitcell:5047,setblockformat:5043,getblockformat:5011,getblockformatnames:5012,setfontname:5044,getfontname:5013,setfontsize:5045,getfontsize:5014,setbackcolor:5042,getbackcolor:5010,setforecolor:5046,getforecolor:5015,findtext:5008,font:5009,hyperlink:5016,image:5017,lockelement:5027,makeabsolute:5028,sendbackward:5036,bringforward:5037,sendbelowtext:5038,bringabovetext:5039,sendtoback:5040,bringtofront:5041,properties:5052},ui:{"default":0,prompt:1,noprompt:2},status:{notsupported:0,disabled:1,enabled:3,latched:7,ninched:11},appearance:{flat:0,inset:1},state:{unchecked:0,checked:1,gray:2}},_normalizeCommand:function(cmd){
var drh=dojo.render.html;
var _a17=cmd.toLowerCase();
if(_a17=="formatblock"){
if(drh.safari){
_a17="heading";
}
}else{
if(this.object){
switch(_a17){
case "createlink":
_a17="hyperlink";
break;
case "insertimage":
_a17="image";
break;
}
}else{
if(_a17=="hilitecolor"&&!drh.mozilla){
_a17="backcolor";
}
}
}
return _a17;
},queryCommandAvailable:function(_a18){
var ie=1;
var _a1a=1<<1;
var _a1b=1<<2;
var _a1c=1<<3;
var _a1d=1<<4;
var _a1e=false;
if(dojo.render.html.safari){
var tmp=dojo.render.html.UA.split("AppleWebKit/")[1];
var ver=parseFloat(tmp.split(" ")[0]);
if(ver>=420){
_a1e=true;
}
}
function isSupportedBy(_a21){
return {ie:Boolean(_a21&ie),mozilla:Boolean(_a21&_a1a),safari:Boolean(_a21&_a1b),safari420:Boolean(_a21&_a1d),opera:Boolean(_a21&_a1c)};
}
var _a22=null;
switch(_a18.toLowerCase()){
case "bold":
case "italic":
case "underline":
case "subscript":
case "superscript":
case "fontname":
case "fontsize":
case "forecolor":
case "hilitecolor":
case "justifycenter":
case "justifyfull":
case "justifyleft":
case "justifyright":
case "delete":
case "selectall":
_a22=isSupportedBy(_a1a|ie|_a1b|_a1c);
break;
case "createlink":
case "unlink":
case "removeformat":
case "inserthorizontalrule":
case "insertimage":
case "insertorderedlist":
case "insertunorderedlist":
case "indent":
case "outdent":
case "formatblock":
case "inserthtml":
case "undo":
case "redo":
case "strikethrough":
case "inserttable":
_a22=isSupportedBy(_a1a|ie|_a1c|_a1d);
break;
case "blockdirltr":
case "blockdirrtl":
case "dirltr":
case "dirrtl":
case "inlinedirltr":
case "inlinedirrtl":
_a22=isSupportedBy(ie);
break;
case "cut":
case "copy":
case "paste":
_a22=isSupportedBy(ie|_a1a|_a1d);
break;
case "insertcell":
case "insertcol":
case "insertrow":
case "deletecells":
case "deletecols":
case "deleterows":
case "mergecells":
case "splitcell":
_a22=isSupportedBy(this.object?ie:0);
break;
default:
return false;
}
return (dojo.render.html.ie&&_a22.ie)||(dojo.render.html.mozilla&&_a22.mozilla)||(dojo.render.html.safari&&_a22.safari)||(_a1e&&_a22.safari420)||(dojo.render.html.opera&&_a22.opera);
},execCommand:function(_a23,_a24){
var _a25;
this.focus();
_a23=this._normalizeCommand(_a23);
if(_a24!=undefined){
if(_a23=="heading"){
throw new Error("unimplemented");
}else{
if(_a23=="formatblock"){
if(this.object){
_a24=this._native2LocalFormatNames[_a24];
}else{
if(dojo.render.html.ie){
_a24="<"+_a24+">";
}
}
}
}
}
if(this.object){
switch(_a23){
case "hilitecolor":
_a23="setbackcolor";
break;
case "forecolor":
case "backcolor":
case "fontsize":
case "fontname":
_a23="set"+_a23;
break;
case "formatblock":
_a23="setblockformat";
}
if(_a23=="strikethrough"){
_a23="inserthtml";
var _a26=this.document.selection.createRange();
if(!_a26.htmlText){
return;
}
_a24=_a26.htmlText.strike();
}else{
if(_a23=="inserthorizontalrule"){
_a23="inserthtml";
_a24="<hr>";
}
}
if(_a23=="inserttable"){
var _a27=this.constructor._tableInfo;
if(!_a27){
_a27=document.createElement("object");
_a27.classid="clsid:47B0DFC7-B7A3-11D1-ADC5-006008A5848C";
document.body.appendChild(_a27);
this.constructor._table=_a27;
}
_a27.NumRows=_a24["rows"];
_a27.NumCols=_a24["cols"];
_a27.TableAttrs=_a24["TableAttrs"];
_a27.CellAttrs=_a24["CellAttrs"];
_a27.Caption=_a24["Caption"];
}
if(_a23=="inserthtml"){
var _a26=this.document.selection.createRange();
if(this.document.selection.type.toUpperCase()=="CONTROL"){
for(var i=0;i<_a26.length;i++){
_a26.item(i).outerHTML=_a24;
}
}else{
_a26.pasteHTML(_a24);
_a26.select();
}
_a25=true;
}else{
if(arguments.length==1){
_a25=this.object.ExecCommand(this._activeX.command[_a23],this._activeX.ui.noprompt);
}else{
_a25=this.object.ExecCommand(this._activeX.command[_a23],this._activeX.ui.noprompt,_a24);
}
}
}else{
if(_a23=="inserthtml"){
if(dojo.render.html.ie){
var _a29=this.document.selection.createRange();
_a29.pasteHTML(_a24);
_a29.select();
return true;
}else{
return this.document.execCommand(_a23,false,_a24);
}
}else{
if((_a23=="unlink")&&(this.queryCommandEnabled("unlink"))&&(dojo.render.html.mozilla)){
var _a2a=this.window.getSelection();
var _a2b=_a2a.getRangeAt(0);
var _a2c=_a2b.startContainer;
var _a2d=_a2b.startOffset;
var _a2e=_a2b.endContainer;
var _a2f=_a2b.endOffset;
var a=dojo.withGlobal(this.window,"getAncestorElement",dojo.html.selection,["a"]);
dojo.withGlobal(this.window,"selectElement",dojo.html.selection,[a]);
_a25=this.document.execCommand("unlink",false,null);
var _a2b=this.document.createRange();
_a2b.setStart(_a2c,_a2d);
_a2b.setEnd(_a2e,_a2f);
_a2a.removeAllRanges();
_a2a.addRange(_a2b);
return _a25;
}else{
if((_a23=="inserttable")){
var cols="<tr>";
for(var i=0;i<_a24.cols;i++){
cols+="<td></td>";
}
cols+="</tr>";
var _a32="<table";
for(field in _a24.TableAttrs){
_a32+=" "+field+"=\""+_a24.TableAttrs[field]+"\"";
}
_a32+=">";
if(_a24.caption){
_a32+="<caption>"+_a24.caption+"</caption>";
}
_a32+="<tbody>";
for(var i=0;i<_a24.rows;i++){
_a32+=cols;
}
_a32+="</tbody></table>";
_a25=this.execCommand("inserthtml",_a32);
}else{
if((_a23=="hilitecolor")&&(dojo.render.html.mozilla)){
this.document.execCommand("useCSS",false,false);
_a25=this.document.execCommand(_a23,false,_a24);
this.document.execCommand("useCSS",false,true);
}else{
if((dojo.render.html.ie)&&((_a23=="backcolor")||(_a23=="forecolor"))){
_a24=arguments.length>1?_a24:null;
_a25=this.document.execCommand(_a23,false,_a24);
}else{
if(_a23=="indent"){
if(dojo.render.html.mozilla){
this.document.execCommand("useCSS",false,false);
return this.document.execCommand(_a23,false,_a24);
this.document.execCommand("useCSS",false,true);
return true;
}else{
if(dojo.render.html.ie){
if(!dojo.withGlobal(this.window,"getAncestorElement",dojo.html.selection,["ul","ol"])){
var node=dojo.withGlobal(this.window,"getAncestorElement",dojo.html.selection,["div","p","pre","address","h1","h2","h3","h4","h5","h6"]);
if(node&&node!=this.editNode){
var _a34=dojo.html.getStyleProperty(node,"margin-left");
dojo.html.setStyle(node,"margin-left",(_a34.match(/ ?[0-9]+ ?px/)?parseInt(_a34)+40+"px":"40px"));
}else{
this.execCommand("formatblock","div");
node=dojo.withGlobal(this.window,"getAncestorElement",dojo.html.selection,["div"]);
dojo.html.setStyle(node,"margin-left","40px");
}
return true;
}
}
}
}
if(_a23=="outdent"){
if(dojo.render.html.mozilla){
this.document.execCommand("useCSS",false,false);
return this.document.execCommand(_a23,false,_a24);
this.document.execCommand("useCSS",false,true);
return true;
}
var node=dojo.withGlobal(this.window,"getAncestorElement",dojo.html.selection,["div","p","pre","address","h1","h2","h3","h4","h5","h6"]);
if(node&&node!=this.editNode){
var _a34=dojo.html.getStyleProperty(node,"margin-left");
_a34=(_a34.match(/ ?[0-9]+ ?px/)?Math.max(parseInt(_a34)-40,0):0);
dojo.html.setStyle(node,"margin-left",(_a34?_a34+"px":""));
return true;
}
}
if(_a23=="formatblock"&&_a24.toLowerCase()=="<blockquote>"){
_a23="indent";
_a24=null;
}
_a24=arguments.length>1?_a24:null;
if(_a24||_a23!="createlink"){
_a25=this.document.execCommand(_a23,false,_a24);
}
}
}
}
}
}
}
this.onDisplayChanged();
return _a25;
},queryCommandEnabled:function(_a35){
if(_a35=="outdent"){
return true;
}
if(_a35=="superscript"||_a35=="subscript"){
return true;
}
_a35=this._normalizeCommand(_a35);
if(this.object){
switch(_a35){
case "hilitecolor":
_a35="setbackcolor";
break;
case "forecolor":
case "backcolor":
case "fontsize":
case "fontname":
_a35="set"+_a35;
break;
case "formatblock":
_a35="setblockformat";
break;
case "strikethrough":
_a35="bold";
break;
case "inserthorizontalrule":
return true;
}
if(typeof this._activeX.command[_a35]=="undefined"){
return false;
}
var _a36=this.object.QueryStatus(this._activeX.command[_a35]);
return ((_a36!=this._activeX.status.notsupported)&&(_a36!=this._activeX.status.disabled));
}else{
if(dojo.render.html.mozilla){
if(_a35=="unlink"){
return dojo.withGlobal(this.window,"hasAncestorElement",dojo.html.selection,["a"]);
}else{
if(_a35=="inserttable"){
return true;
}
}
}
var elem=(dojo.render.html.ie)?this.document.selection.createRange():this.document;
return elem.queryCommandEnabled(_a35);
}
},queryCommandState:function(_a38){
_a38=this._normalizeCommand(_a38);
if(_a38=="createlink"){
return (this.queryCommandEnabled("unlink"));
}
if(this.object){
if(_a38=="forecolor"){
_a38="setforecolor";
}else{
if(_a38=="backcolor"){
_a38="setbackcolor";
}else{
if(_a38=="strikethrough"){
return dojo.withGlobal(this.window,"hasAncestorElement",dojo.html.selection,["strike"]);
}else{
if(_a38=="inserthorizontalrule"){
return false;
}
}
}
}
if(typeof this._activeX.command[_a38]=="undefined"){
return null;
}
var _a39=this.object.QueryStatus(this._activeX.command[_a38]);
return ((_a39==this._activeX.status.latched)||(_a39==this._activeX.status.ninched));
}else{
return this.document.queryCommandState(_a38);
}
},queryCommandValue:function(_a3a){
_a3a=this._normalizeCommand(_a3a);
if(this.object){
switch(_a3a){
case "forecolor":
case "backcolor":
case "fontsize":
case "fontname":
_a3a="get"+_a3a;
return this.object.execCommand(this._activeX.command[_a3a],this._activeX.ui.noprompt);
case "formatblock":
var _a3b=this.object.execCommand(this._activeX.command["getblockformat"],this._activeX.ui.noprompt);
if(_a3b){
return this._local2NativeFormatNames[_a3b];
}
}
}else{
if(dojo.render.html.ie&&_a3a=="formatblock"){
return this._local2NativeFormatNames[this.document.queryCommandValue(_a3a)]||this.document.queryCommandValue(_a3a);
}
return this.document.queryCommandValue(_a3a);
}
},placeCursorAtStart:function(){
this.focus();
if(dojo.render.html.moz&&this.editNode.firstChild&&this.editNode.firstChild.nodeType!=dojo.dom.TEXT_NODE){
dojo.withGlobal(this.window,"selectElementChildren",dojo.html.selection,[this.editNode.firstChild]);
}else{
dojo.withGlobal(this.window,"selectElementChildren",dojo.html.selection,[this.editNode]);
}
dojo.withGlobal(this.window,"collapse",dojo.html.selection,[true]);
},placeCursorAtEnd:function(){
this.focus();
if(dojo.render.html.moz&&this.editNode.lastChild&&this.editNode.lastChild.nodeType!=dojo.dom.TEXT_NODE){
dojo.withGlobal(this.window,"selectElementChildren",dojo.html.selection,[this.editNode.lastChild]);
}else{
dojo.withGlobal(this.window,"selectElementChildren",dojo.html.selection,[this.editNode]);
}
dojo.withGlobal(this.window,"collapse",dojo.html.selection,[false]);
},replaceEditorContent:function(html){
html=this._preFilterContent(html);
if(this.isClosed){
this.domNode.innerHTML=html;
}else{
if((this.window&&this.window.getSelection&&!dojo.render.html.moz)||dojo.render.html.ie){
this.editNode.innerHTML=html;
}else{
if((this.window&&this.window.getSelection)||(this.document&&this.document.selection)){
this.execCommand("selectall");
if(dojo.render.html.moz&&!html){
html="&nbsp;";
}
this.execCommand("inserthtml",html);
}
}
}
},_preFilterContent:function(html){
var ec=html;
dojo.lang.forEach(this.contentPreFilters,function(ef){
ec=ef(ec);
});
if(this.contentDomPreFilters.length>0){
var dom=dojo.doc().createElement("div");
dom.style.display="none";
dojo.body().appendChild(dom);
dom.innerHTML=ec;
dojo.lang.forEach(this.contentDomPreFilters,function(ef){
dom=ef(dom);
});
ec=dom.innerHTML;
dojo.body().removeChild(dom);
}
return ec;
},_postFilterContent:function(html){
var ec=html;
if(this.contentDomPostFilters.length>0){
var dom=this.document.createElement("div");
dom.innerHTML=ec;
dojo.lang.forEach(this.contentDomPostFilters,function(ef){
dom=ef(dom);
});
ec=dom.innerHTML;
}
dojo.lang.forEach(this.contentPostFilters,function(ef){
ec=ef(ec);
});
return ec;
},_lastHeight:0,_updateHeight:function(){
if(!this.isLoaded){
return;
}
if(this.height){
return;
}
var _a47=dojo.html.getBorderBox(this.editNode).height;
if(!_a47){
_a47=dojo.html.getBorderBox(this.document.body).height;
}
if(_a47==0){
dojo.debug("Can not figure out the height of the editing area!");
return;
}
this._lastHeight=_a47;
this.editorObject.style.height=this._lastHeight+"px";
this.window.scrollTo(0,0);
},_saveContent:function(e){
var _a49=dojo.doc().getElementById("dojo.widget.RichText.savedContent");
_a49.value+=this._SEPARATOR+this.saveName+":"+this.getEditorContent();
},getEditorContent:function(){
var ec="";
try{
ec=(this._content.length>0)?this._content:this.editNode.innerHTML;
if(dojo.string.trim(ec)=="&nbsp;"){
ec="";
}
}
catch(e){
}
if(dojo.render.html.ie&&!this.object){
var re=new RegExp("(?:<p>&nbsp;</p>[\n\r]*)+$","i");
ec=ec.replace(re,"");
}
ec=this._postFilterContent(ec);
if(this.relativeImageUrls){
var _a4c=dojo.global().location.protocol+"//"+dojo.global().location.host;
var _a4d=dojo.global().location.pathname;
if(_a4d.match(/\/$/)){
}else{
var _a4e=_a4d.split("/");
if(_a4e.length){
_a4e.pop();
}
_a4d=_a4e.join("/")+"/";
}
var _a4f=new RegExp("(<img[^>]* src=[\"'])("+_a4c+"("+_a4d+")?)","ig");
ec=ec.replace(_a4f,"$1");
}
return ec;
},close:function(save,_a51){
if(this.isClosed){
return false;
}
if(arguments.length==0){
save=true;
}
this._content=this._postFilterContent(this.editNode.innerHTML);
var _a52=(this.savedContent!=this._content);
if(this.interval){
clearInterval(this.interval);
}
if(dojo.render.html.ie&&!this.object){
dojo.event.browser.clean(this.editNode);
}
if(this.iframe){
delete this.iframe;
}
if(this.textarea){
with(this.textarea.style){
position="";
left=top="";
if(dojo.render.html.ie){
overflow=this.__overflow;
this.__overflow=null;
}
}
if(save){
this.textarea.value=this._content;
}else{
this.textarea.value=this.savedContent;
}
dojo.html.removeNode(this.domNode);
this.domNode=this.textarea;
}else{
if(save){
if(dojo.render.html.moz){
var nc=dojo.doc().createElement("span");
this.domNode.appendChild(nc);
nc.innerHTML=this.editNode.innerHTML;
}else{
this.domNode.innerHTML=this._content;
}
}else{
this.domNode.innerHTML=this.savedContent;
}
}
dojo.html.removeClass(this.domNode,"RichTextEditable");
this.isClosed=true;
this.isLoaded=false;
delete this.editNode;
if(this.window._frameElement){
this.window._frameElement=null;
}
this.window=null;
this.document=null;
this.object=null;
this.editingArea=null;
this.editorObject=null;
return _a52;
},destroyRendering:function(){
},destroy:function(){
this.destroyRendering();
if(!this.isClosed){
this.close(false);
}
dojo.widget.RichText.superclass.destroy.call(this);
},connect:function(_a54,_a55,_a56){
dojo.event.connect(_a54,_a55,this,_a56);
},disconnect:function(_a57,_a58,_a59){
dojo.event.disconnect(_a57,_a58,this,_a59);
},disconnectAllWithRoot:function(_a5a){
dojo.deprecated("disconnectAllWithRoot","is deprecated. No need to disconnect manually","0.5");
},_fixContentForMoz:function(html){
html=html.replace(/<strong([ \>])/gi,"<b$1");
html=html.replace(/<\/strong>/gi,"</b>");
html=html.replace(/<em([ \>])/gi,"<i$1");
html=html.replace(/<\/em>/gi,"</i>");
return html;
},_createRange:function(sel){
if(typeof sel=="undefined"){
var sel=(dojo.render.html.ie?document.selection:this.window.getSelection());
}
if(dojo.render.html.ie){
return sel.createRange();
}else{
if(typeof sel!="undefined"){
try{
return sel.getRangeAt(0);
}
catch(e){
return document.createRange();
}
}else{
return document.createRange();
}
}
}});

dojo.provide("dojo.widget.Editor2Plugin.bFilters");
dojo.widget.Editor2Plugin.bFilters={tidy_tags:function(str){
return str.replace(/<(\/?)b( [^>]*)?>/g,"<$1strong$2>").replace(/<(\/?)i( [^>]*)?>/g,"<$1em$2>").replace(/<\?xml:[^>]*>/g,"").replace(/<\/?st1:[^>]*>/g,"").replace(/<\/?[a-z]\:[^>]*>/g,"").replace(/<(\/?)(h[1-6]+)[^>]*>/gi,"<$1$2>").replace(/<(b[a-qs-z][a-z]*|[ac-z][a-z]*|br[a-z]+)><\1>/gi,"<$1>").replace(/<\/(b[a-qs-z][a-z]*|[ac-z][a-z]*|br[a-z]+)><\/\1>/gi,"</$1>").replace(/  */gi," ").replace(/\x96|\x99/g,"").
replace(/<img[^>]*XXEDITOR_SCRIPTXX([^\"]*)XXEDITOR_SCRIPTXX\"[^>]*\>/gi,function(NN,scri){
return unescape(scri);
});
},untidy_tags:function(str){
return str.replace(/<\/script>/gi,String.fromCharCode(237)).replace(/<script([^\xed]*?)\xed/gi,function(NN,scri){
return "<img src=\""+dojo.uri.dojoUri("../editor/images/")+"EDITOR_SCRIPT_placeholder.gif?XXEDITOR_SCRIPTXX"+escape("<script"+scri+"</script>")+"XXEDITOR_SCRIPTXX\">";
}).		replace(/<\/object>/gi,String.fromCharCode(0xed)).
			replace(/<object([^\xed]*?)\xed/gi,function(NN,scri) {			
				return '<img src="'+dojo.uri.dojoUri('../editor/images/')+			'embedPlaceholder.gif?XXEDITOR_SCRIPTXX'+escape('<object'+scri+'</object>')+'XXEDITOR_SCRIPTXX">';
			}).
		replace(/<embed([^>]*?[^\/])\/?>(<\/embed>)?/gi,function(NN,scri) {			
			return '<img src="'+dojo.uri.dojoUri('../editor/images/')+			'embedPlaceholder.gif?XXEDITOR_SCRIPTXX'+escape('<embed'+scri+'/>')+'XXEDITOR_SCRIPTXX">';
		}). 
			replace(/<(\/?)strong( [^>]*)?>/g,"<$1b$2>").replace(/<(\/?)em( [^>]*)?>/g,"<$1i$2>");
}};

dojo.provide("dojo.widget.Editor2Toolbar");
dojo.lang.declare("dojo.widget.HandlerManager",null,function(){
this._registeredHandlers=[];
},{registerHandler:function(obj,func){
if(arguments.length==2){
this._registeredHandlers.push(function(){
return obj[func].apply(obj,arguments);
});
}else{
this._registeredHandlers.push(obj);
}
},removeHandler:function(func){
for(var i=0;i<this._registeredHandlers.length;i++){
if(func===this._registeredHandlers[i]){
delete this._registeredHandlers[i];
return;
}
}
dojo.debug("HandlerManager handler "+func+" is not registered, can not remove.");
},destroy:function(){
for(var i=0;i<this._registeredHandlers.length;i++){
delete this._registeredHandlers[i];
}
}});
dojo.widget.Editor2ToolbarItemManager=new dojo.widget.HandlerManager;
dojo.lang.mixin(dojo.widget.Editor2ToolbarItemManager,{getToolbarItem:function(name){
var item;
name=name.toLowerCase();
for(var i=0;i<this._registeredHandlers.length;i++){
item=this._registeredHandlers[i](name);
if(item){
return item;
}
}
_deprecated=function(cmd,_a88){
if(!dojo.widget.Editor2Plugin[_a88]){
dojo.deprecated("Toolbar item "+name+" is now defined in plugin dojo.widget.Editor2Plugin."+_a88+". It shall be required explicitly","0.6");
dojo["require"]("dojo.widget.Editor2Plugin."+_a88);
}
};
if(name=="forecolor"||name=="hilitecolor"){
_deprecated(name,"ColorPicker");
}else{
if(name=="formatblock"||name=="fontsize"||name=="fontname"){
_deprecated(name,"DropDownList");
}
}
switch(name){
case "bold":
case "copy":
case "cut":
case "delete":
case "indent":
case "inserthorizontalrule":
case "insertorderedlist":
case "insertunorderedlist":
case "italic":
case "justifycenter":
case "justifyfull":
case "justifyleft":
case "justifyright":
case "outdent":
case "paste":
case "removeformat":
case "selectall":
case "strikethrough":
case "subscript":
case "superscript":
case "underline":
case "unlink":
case "createlink":
case "insertimage":
case "inserthtmldialog":
case "htmltoggle":
case "insertav":
case "insertswf":
case "blockquote":
item=new dojo.widget.Editor2ToolbarButton(name);
break;
case "undo":
case "redo":
if(!dojo.render.html.ie){
item=new dojo.widget.Editor2ToolbarButton(name);
}
break;
case "forecolor":
case "hilitecolor":
item=new dojo.widget.Editor2ToolbarColorPaletteButton(name);
break;
case "plainformatblock":
item=new dojo.widget.Editor2ToolbarFormatBlockPlainSelect("formatblock");
break;
case "formatblock":
item=new dojo.widget.Editor2ToolbarFormatBlockSelect("formatblock");
break;
case "fontsize":
item=new dojo.widget.Editor2ToolbarFontSizeSelect("fontsize");
break;
case "fontname":
item=new dojo.widget.Editor2ToolbarFontNameSelect("fontname");
break;
case "blockdirltr":
case "blockdirrtl":
case "dirltr":
case "dirrtl":
case "inlinedirltr":
case "inlinedirrtl":
dojo.debug("Not yet implemented toolbar item: "+name);
break;
default:
if(name.substr(0,13)=="insertsymbol_"){
item=new dojo.widget.Editor2ToolbarButton(name);
}else{
dojo.debug("dojo.widget.Editor2ToolbarItemManager.getToolbarItem: Unknown toolbar item: "+name);
}
}
return item;
}});
dojo.addOnUnload(dojo.widget.Editor2ToolbarItemManager,"destroy");
dojo.declare("dojo.widget.Editor2ToolbarButton",null,function(name){
this._name=name;
},{create:function(node,_a8b,_a8c){
this._domNode=node;
var cmd=_a8b.parent.getCommand(this._name);
if(cmd){
this._domNode.title=cmd.getText();
}
this.disableSelection(this._domNode);
this._parentToolbar=_a8b;
dojo.event.connect(this._domNode,"onclick",this,"onClick");
if(!_a8c){
dojo.event.connect(this._domNode,"onmouseover",this,"onMouseOver");
dojo.event.connect(this._domNode,"onmouseout",this,"onMouseOut");
}
},disableSelection:function(_a8e){
dojo.html.disableSelection(_a8e);
var _a8f=_a8e.all||_a8e.getElementsByTagName("*");
for(var x=0;x<_a8f.length;x++){
dojo.html.disableSelection(_a8f[x]);
}
},onMouseOver:function(){
var _a91=dojo.widget.Editor2Manager.getCurrentInstance();
if(_a91){
var _a92=_a91.getCommand(this._name);
if(_a92&&_a92.getState()!=dojo.widget.Editor2Manager.commandState.Disabled){
this.highlightToolbarItem();
}
}
},onMouseOut:function(){
this.unhighlightToolbarItem();
},destroy:function(){
this._domNode=null;
this._parentToolbar=null;
},onClick:function(e){
if(this._domNode&&!this._domNode.disabled&&this._parentToolbar.checkAvailability()){
e.preventDefault();
e.stopPropagation();
var _a94=dojo.widget.Editor2Manager.getCurrentInstance();
if(_a94){
var _a95=_a94.getCommand(this._name);
if(_a95){
_a95.execute(e);
this.refreshState();
}
}
}
},refreshState:function(){
var _a96=dojo.widget.Editor2Manager.getCurrentInstance();
var em=dojo.widget.Editor2Manager;
if(_a96){
var _a98=_a96.getCommand(this._name);
if(_a98){
var _a99=_a98.getState();
if(_a99!=this._lastState){
switch(_a99){
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
this._lastState=_a99;
}
}
}
return em.commandState.Enabled;
},latchToolbarItem:function(){
this._domNode.disabled=false;
this.removeToolbarItemStyle(this._domNode);
dojo.html.addClass(this._domNode,this._parentToolbar.ToolbarLatchedItemStyle);
},enableToolbarItem:function(){
this._domNode.disabled=false;
this.removeToolbarItemStyle(this._domNode);
dojo.html.addClass(this._domNode,this._parentToolbar.ToolbarEnabledItemStyle);
},disableToolbarItem:function(){
this._domNode.disabled=true;
this.removeToolbarItemStyle(this._domNode);
dojo.html.addClass(this._domNode,this._parentToolbar.ToolbarDisabledItemStyle);
},highlightToolbarItem:function(){
dojo.html.addClass(this._domNode,this._parentToolbar.ToolbarHighlightedItemStyle);
},unhighlightToolbarItem:function(){
dojo.html.removeClass(this._domNode,this._parentToolbar.ToolbarHighlightedItemStyle);
},removeToolbarItemStyle:function(){
dojo.html.removeClass(this._domNode,this._parentToolbar.ToolbarEnabledItemStyle);
dojo.html.removeClass(this._domNode,this._parentToolbar.ToolbarLatchedItemStyle);
dojo.html.removeClass(this._domNode,this._parentToolbar.ToolbarDisabledItemStyle);
this.unhighlightToolbarItem();
}});
dojo.declare("dojo.widget.Editor2ToolbarFormatBlockPlainSelect",dojo.widget.Editor2ToolbarButton,{create:function(node,_a9b){
this._domNode=node;
this._parentToolbar=_a9b;
this._domNode=node;
this.disableSelection(this._domNode);
dojo.event.connect(this._domNode,"onchange",this,"onChange");
},destroy:function(){
this._domNode=null;
},onChange:function(){
if(this._parentToolbar.checkAvailability()){
var sv=this._domNode.value.toLowerCase();
var _a9d=dojo.widget.Editor2Manager.getCurrentInstance();
if(_a9d){
var _a9e=_a9d.getCommand(this._name);
if(_a9e){
_a9e.execute(sv);
}
}
}
},refreshState:function(){
if(this._domNode){
dojo.widget.Editor2ToolbarFormatBlockPlainSelect.superclass.refreshState.call(this);
var _a9f=dojo.widget.Editor2Manager.getCurrentInstance();
if(_a9f){
var _aa0=_a9f.getCommand(this._name);
if(_aa0){
var _aa1=_aa0.getValue();
if(!_aa1){
_aa1="";
}
dojo.lang.forEach(this._domNode.options,function(item){
if(item.value.toLowerCase()==_aa1.toLowerCase()){
item.selected=true;
}
});
}
}
}
}});
dojo.widget.defineWidget("dojo.widget.Editor2Toolbar",dojo.widget.HtmlWidget,function(){
dojo.event.connect(this,"fillInTemplate",dojo.lang.hitch(this,function(){
if(dojo.render.html.ie){
this.domNode.style.zoom=1;
}
}));
},{templatePath:dojo.uri.dojoUri("src/widget/templates/EditorToolbar.html"),templateCssPath:dojo.uri.dojoUri("src/widget/templates/EditorToolbar.css"),ToolbarLatchedItemStyle:"ToolbarButtonLatched",ToolbarEnabledItemStyle:"ToolbarButtonEnabled",ToolbarDisabledItemStyle:"ToolbarButtonDisabled",ToolbarHighlightedItemStyle:"ToolbarButtonHighlighted",ToolbarHighlightedSelectStyle:"ToolbarSelectHighlighted",ToolbarHighlightedSelectItemStyle:"ToolbarSelectHighlightedItem",postCreate:function(){
var _aa3=dojo.html.getElementsByClass("dojoEditorToolbarItem",this.domNode);
this.items={};
for(var x=0;x<_aa3.length;x++){
var node=_aa3[x];
var _aa6=node.getAttribute("dojoETItemName");
if(_aa6){
var item=dojo.widget.Editor2ToolbarItemManager.getToolbarItem(_aa6);
if(item){
item.create(node,this);
this.items[_aa6.toLowerCase()]=item;
}else{
node.style.display="none";
}
}
}

if(dojo.dom.getFirstAncestorByTag(this.domNode, 'td')) {
var fat=document.createElement("img");
fat.src=dojo.uri.dojoUri("src/widget/templates/images/blank.gif");
fat.width=dojo.html.getMarginBox(this.domNode).width;
fat.height=1;
dojo.dom.insertBefore(fat,this.domNode);
}

},update:function(){
for(var cmd in this.items){
this.items[cmd].refreshState();
}
},shareGroup:"",checkAvailability:function(){
if(!this.shareGroup){
this.parent.focus();
return true;
}
var _aa9=dojo.widget.Editor2Manager.getCurrentInstance();
if(this.shareGroup==_aa9.toolbarGroup){
return true;
}
return false;
},destroy:function(){
for(var it in this.items){
this.items[it].destroy();
delete this.items[it];
}
dojo.widget.Editor2Toolbar.superclass.destroy.call(this);
}});
dojo.provide("dojo.i18n.common");
dojo.i18n.getLocalization=function(_aab,_aac,_aad){
dojo.hostenv.preloadLocalizations();
_aad=dojo.hostenv.normalizeLocale(_aad);
var _aae=_aad.split("-");
var _aaf=[_aab,"nls",_aac].join(".");
var _ab0=dojo.hostenv.findModule(_aaf,true);
var _ab1;
for(var i=_aae.length;i>0;i--){
var loc=_aae.slice(0,i).join("_");
if(_ab0[loc]){
_ab1=_ab0[loc];
break;
}
}
if(!_ab1){
_ab1=_ab0.ROOT;
}
if(_ab1){
var _ab4=function(){
};
_ab4.prototype=_ab1;
return new _ab4();
}
dojo.raise("Bundle not found: "+_aac+" in "+_aab+" , locale="+_aad);
};
dojo.i18n.isLTR=function(_ab5){
var lang=dojo.hostenv.normalizeLocale(_ab5).split("-")[0];
var RTL={ar:true,fa:true,he:true,ur:true,yi:true};
return !RTL[lang];
};
dojo.provide("dojo.widget.Editor2Plugin.AlwaysShowToolbar");
dojo.event.topic.subscribe("dojo.widget.Editor2::onLoad",function(_ab8){
if(_ab8.toolbarAlwaysVisible){
var p=new dojo.widget.Editor2Plugin.AlwaysShowToolbar(_ab8);
}
});
dojo.declare("dojo.widget.Editor2Plugin.AlwaysShowToolbar",null,function(_aba){
this.editor=_aba;
this.editor.registerLoadedPlugin(this);
this.setup();
},{_scrollSetUp:false,_fixEnabled:false,_scrollThreshold:false,_handleScroll:true,setup:function(){
var tdn=this.editor.toolbarWidget;
if(!tdn.tbBgIframe){
tdn.tbBgIframe=new dojo.html.BackgroundIframe(tdn.domNode);
tdn.tbBgIframe.onResized();
}
this.scrollInterval=setInterval(dojo.lang.hitch(this,"globalOnScrollHandler"),100);
dojo.event.connect("before",this.editor.toolbarWidget,"destroy",this,"destroy");
},globalOnScrollHandler:function(){
var isIE=dojo.render.html.ie;
if(!this._handleScroll){
return;
}
var dh=dojo.html;
var tdn=this.editor.toolbarWidget.domNode;
var db=dojo.body();
if(!this._scrollSetUp){
this._scrollSetUp=true;
var _ac0=dh.getMarginBox(this.editor.domNode).width;
this._scrollThreshold=dh.abs(tdn,true).y;
if((isIE)&&(db)&&(dh.getStyle(db,"background-image")=="none")){
with(db.style){
backgroundImage="url("+dojo.uri.dojoUri("src/widget/templates/images/blank.gif")+")";
backgroundAttachment="fixed";
}
}
}
var _ac1=(window["pageYOffset"])?window["pageYOffset"]:(document["documentElement"]||document["body"]).scrollTop;
if(_ac1>this._scrollThreshold){
if(!this._fixEnabled){
var _ac2=dojo.html.getMarginBox(tdn);
this.editor.editorObject.style.marginTop=_ac2.height+"px";
if(isIE){
tdn.style.left=dojo.html.abs(tdn,dojo.html.boxSizing.MARGIN_BOX).x;
if(tdn.previousSibling){
this._IEOriginalPos=["after",tdn.previousSibling];
}else{
if(tdn.nextSibling){
this._IEOriginalPos=["before",tdn.nextSibling];
}else{
this._IEOriginalPos=["",tdn.parentNode];
}
}
dojo.body().appendChild(tdn);
dojo.html.addClass(tdn,"IEFixedToolbar");
}else{
with(tdn.style){
position="fixed";
top="0px";
}
}
tdn.style.width=_ac2.width+"px";
tdn.style.zIndex=99;
this._fixEnabled=true;
}
if(!dojo.render.html.safari){
var _ac3=(this.height)?parseInt(this.editor.height):this.editor._lastHeight;
if(_ac1>(this._scrollThreshold+_ac3)){
tdn.style.display="none";
}else{
tdn.style.display="";
}
}
}else{
if(this._fixEnabled){
(this.editor.object||this.editor.iframe).style.marginTop=null;
with(tdn.style){
position="";
top="";
zIndex="";
display="";
}
if(isIE){
tdn.style.left="";
dojo.html.removeClass(tdn,"IEFixedToolbar");
if(this._IEOriginalPos){
dojo.html.insertAtPosition(tdn,this._IEOriginalPos[1],this._IEOriginalPos[0]);
this._IEOriginalPos=null;
}else{
dojo.html.insertBefore(tdn,this.editor.object||this.editor.iframe);
}
}
tdn.style.width="";
this._fixEnabled=false;
}
}
},destroy:function(){
this._IEOriginalPos=null;
this._handleScroll=false;
clearInterval(this.scrollInterval);
this.editor.unregisterLoadedPlugin(this);
if(dojo.render.html.ie){
dojo.html.removeClass(this.editor.toolbarWidget.domNode,"IEFixedToolbar");
}
}});
dojo.provide("dojo.widget.Editor2");
dojo.widget.Editor2Manager=new dojo.widget.HandlerManager();
dojo.lang.mixin(dojo.widget.Editor2Manager,{_currentInstance:null,commandState:{Disabled:0,Latched:1,Enabled:2},getCurrentInstance:function(){
return this._currentInstance;
},setCurrentInstance:function(inst){
this._currentInstance=inst;
},getCommand:function(_ac5,name){
var _ac7;
name=name.toLowerCase();
for(var i=0;i<this._registeredHandlers.length;i++){
_ac7=this._registeredHandlers[i](_ac5,name);
if(_ac7){
return _ac7;
}
}
if(name=="createlink"||name=="insertimage"){
if(!dojo.widget.Editor2Plugin.DialogCommands){
dojo.deprecated("Command "+name+" is now defined in plugin dojo.widget.Editor2Plugin.DialogCommands. It shall be required explicitly","0.6");
dojo["require"]("dojo.widget.Editor2Plugin.DialogCommands");
}
}
var _ac9=dojo.i18n.getLocalization("dojo.widget","Editor2",this.lang);
switch(name){
case "htmltoggle":
_ac7=new dojo.widget.Editor2BrowserCommand(_ac5,name);
break;
case "anchor":
_ac7=new dojo.widget.Editor2Command(_ac5,name);
break;
case "createlink":
_ac7=new dojo.widget.Editor2DialogCommand(_ac5,name,{contentFile:"dojo.widget.Editor2Plugin.CreateLinkDialog",contentClass:"Editor2CreateLinkDialog",title:_ac9.createLinkDialogTitle,width:"660px",height:"498px",lang:this.lang});
break;
case "insertimage":
_ac7=new dojo.widget.Editor2DialogCommand(_ac5,name,{contentFile:"dojo.widget.Editor2Plugin.InsertImageDialog",contentClass:"Editor2InsertImageDialog",title:_ac9.insertImageDialogTitle,width:"610px",height:"530px",lang:this.lang});
_ac7.getState=function(){
var _aca=dojo.widget.Editor2Manager.getCurrentInstance();
if(_aca._inSourceMode){
return false;
}
var el=dojo.withGlobal(_aca.window,"getAncestorElement",dojo.html.selection,["img"]);
return (el&&!el.src.match(/XX[A-Z_0-9]+XX/))?dojo.widget.Editor2Manager.commandState.Latched:dojo.widget.Editor2Manager.commandState.Enabled;
};
break;
case "insertav":
var nw=610;
if (self.innerHeight) {nw = self.innerWidth;} else if (document.documentElement && document.documentElement.clientHeight){nw = document.documentElement.clientWidth;}
else if (document.body) {nw = document.body.clientWidth;}
nw=Math.min(nw,710);
_ac7=new dojo.widget.Editor2DialogCommand(_ac5,name,{contentFile:"dojo.widget.Editor2Plugin.InsertAVDialog",contentClass:"Editor2InsertAVDialog",title:"Insert/Edit Audio or Video",width:nw+"px",height:"512px",lang:this.lang});
_ac7.getState=function(){
var _acc=dojo.widget.Editor2Manager.getCurrentInstance();
if(_acc._inSourceMode){
return false;
}
var el=dojo.withGlobal(_acc.window,"getAncestorElement",dojo.html.selection,["img"]);
return (el&&el.src.match(/XX(MP3STREAM|AVPLAYER)XX/))?dojo.widget.Editor2Manager.commandState.Latched:dojo.widget.Editor2Manager.commandState.Enabled;
};
break;
case "insertswf":
_ac7=new dojo.widget.Editor2DialogCommand(_ac5,name,{contentFile:"dojo.widget.Editor2Plugin.InsertSWFDialog",contentClass:"Editor2InsertSWFDialog",title:"Insert/Edit SWF",width:"660px",height:"512px",lang:this.lang});
_ac7.getState=function(){
var _ace=dojo.widget.Editor2Manager.getCurrentInstance();
if(_ace._inSourceMode){
return false;
}
var el=dojo.withGlobal(_ace.window,"getAncestorElement",dojo.html.selection,["img"]);
return (el&&el.src.match(/XXCUSTOMSWFUXX/))?dojo.widget.Editor2Manager.commandState.Latched:dojo.widget.Editor2Manager.commandState.Enabled;
};
break;
case "inserthtmldialog":
_ac7=new dojo.widget.Editor2DialogCommand(_ac5,name,{contentFile:"dojo.widget.Editor2Plugin.InsertHTMLDialog",contentClass:"Editor2InsertHTMLDialog",title:"Insert/Edit HTML Chunk",width:"600px",height:"420px",lang:this.lang});
_ac7.getState=function(){
var _ad0=dojo.widget.Editor2Manager.getCurrentInstance();
if(_ad0._inSourceMode){
return false;
}
var el=dojo.withGlobal(_ad0.window,"getAncestorElement",dojo.html.selection,["span"]);
while(el){
if(dojo.html.getClass(el)=="Editor_HTML_Chunk"){
return dojo.widget.Editor2Manager.commandState.Latched;
}else{
el=dojo.dom.getFirstAncestorByTag(el.parentNode,"span");
}
}
return dojo.widget.Editor2Manager.commandState.Enabled;
};
break;
default:
if(name.substr(0,13)=="insertsymbol_"){
_ac7=new dojo.widget.Editor2DialogCommand(_ac5,name,{contentFile:"dojo.widget.Editor2Plugin.InsertSymbolDialog_"+name.substr(13),contentClass:"Editor2InsertSymbolDialog_"+name.substr(13),title:"Insert "+name.substr(13),width:"440px",height:"360px",lang:this.lang});
_ac7.getState=function(){
var _ad2=dojo.widget.Editor2Manager.getCurrentInstance();
return _ad2._inSourceMode?dojo.widget.Editor2Manager.commandState.Disabled:dojo.widget.Editor2Manager.commandState.Enabled;
};
_ac7.template=name.substr(13);
}else{
var _ad3=this.getCurrentInstance();
if((_ad3&&_ad3.queryCommandAvailable(name))||(!_ad3&&dojo.widget.Editor2.prototype.queryCommandAvailable(name))){
_ac7=new dojo.widget.Editor2BrowserCommand(_ac5,name);
}else{
dojo.debug("dojo.widget.Editor2Manager.getCommand: Unknown command "+name);
return;
}
}
}
return _ac7;
},destroy:function(){
this._currentInstance=null;
dojo.widget.HandlerManager.prototype.destroy.call(this);
}});
dojo.addOnUnload(dojo.widget.Editor2Manager,"destroy");
dojo.lang.declare("dojo.widget.Editor2Command",null,function(_ad4,name){
this._editor=_ad4;
this._updateTime=0;
this._name=name;
},{_text:"Unknown",execute:function(para){
dojo.unimplemented("dojo.widget.Editor2Command.execute");
},getText:function(){
return this._text;
},getState:function(){
return dojo.widget.Editor2Manager.commandState.Enabled;
},destroy:function(){
}});
dojo.lang.declare("dojo.widget.Editor2BrowserCommand",dojo.widget.Editor2Command,function(_ad7,name){
var _ad9=dojo.i18n.getLocalization("dojo.widget","Editor2BrowserCommand",_ad7.lang);
var text=_ad9[name.toLowerCase()];
if(text){
this._text=text;
}
},{execute:function(para){
this._editor.execCommand(this._name,para);
},getState:function(){
if(this._editor._lastStateTimestamp>this._updateTime||this._state==undefined){
this._updateTime=this._editor._lastStateTimestamp;
try{
if(this._editor.queryCommandEnabled(this._name)){
if(this._editor.queryCommandState(this._name)){
this._state=dojo.widget.Editor2Manager.commandState.Latched;
}else{
this._state=dojo.widget.Editor2Manager.commandState.Enabled;
}
}else{
this._state=dojo.widget.Editor2Manager.commandState.Disabled;
}
}
catch(e){
this._state=dojo.widget.Editor2Manager.commandState.Enabled;
}
}
return this._state;
},getValue:function(){
try{
return this._editor.queryCommandValue(this._name);
}
catch(e){
}
}});
dojo.widget.Editor2ToolbarGroups={};
dojo.widget.defineWidget("dojo.widget.Editor2",dojo.widget.RichText,function(){
this._loadedCommands={};
},{toolbarAlwaysVisible:false,toolbarWidget:null,scrollInterval:null,toolbarTemplatePath:dojo.uri.dojoUri("src/widget/templates/EditorToolbarOneline.html"),toolbarTemplateCssPath:dojo.uri.dojoUri("src/widget/templates/EditorToolbar.css"),toolbarPlaceHolder:"",_inSourceMode:false,_htmlEditNode:null,toolbarGroup:"",shareToolbar:false,contextMenuGroupSet:"",editorOnLoad:function(){
dojo.event.topic.publish("dojo.widget.Editor2::preLoadingToolbar",this);
if(this.toolbarAlwaysVisible){
dojo.require("dojo.widget.Editor2Plugin.AlwaysShowToolbar");
}
if(this.toolbarWidget){
this.toolbarWidget.show();
dojo.html.insertBefore(this.toolbarWidget.domNode,this.domNode.firstChild);
}else{
if(this.shareToolbar){
dojo.deprecated("Editor2:shareToolbar is deprecated in favor of toolbarGroup","0.5");
this.toolbarGroup="defaultDojoToolbarGroup";
}
if(this.toolbarGroup){
if(dojo.widget.Editor2ToolbarGroups[this.toolbarGroup]){
this.toolbarWidget=dojo.widget.Editor2ToolbarGroups[this.toolbarGroup];
}
}
if(!this.toolbarWidget){
var _adc={shareGroup:this.toolbarGroup,parent:this,lang:this.lang};
_adc.templatePath=this.toolbarTemplatePath;
if(this.toolbarTemplateCssPath){
_adc.templateCssPath=this.toolbarTemplateCssPath;
}
if(this.toolbarPlaceHolder){
this.toolbarWidget=dojo.widget.createWidget("Editor2Toolbar",_adc,dojo.byId(this.toolbarPlaceHolder),"after");
}else{
this.toolbarWidget=dojo.widget.createWidget("Editor2Toolbar",_adc,this.domNode.firstChild,"before");
}
if(this.toolbarGroup){
dojo.widget.Editor2ToolbarGroups[this.toolbarGroup]=this.toolbarWidget;
}
dojo.event.connect(this,"close",this.toolbarWidget,"hide");
this.toolbarLoaded();
}
}
dojo.event.topic.registerPublisher("Editor2.clobberFocus",this,"clobberFocus");
dojo.event.topic.subscribe("Editor2.clobberFocus",this,"setBlur");
dojo.event.topic.publish("dojo.widget.Editor2::onLoad",this);
},toolbarLoaded:function(){
},registerLoadedPlugin:function(obj){
if(!this.loadedPlugins){
this.loadedPlugins=[];
}
this.loadedPlugins.push(obj);
},unregisterLoadedPlugin:function(obj){
for(var i in this.loadedPlugins){
if(this.loadedPlugins[i]===obj){
delete this.loadedPlugins[i];
return;
}
}
dojo.debug("dojo.widget.Editor2.unregisterLoadedPlugin: unknown plugin object: "+obj);
},execCommand:function(_ae0,_ae1){
switch(_ae0.toLowerCase()){
case "htmltoggle":
this.toggleHtmlEditing();
break;
default:
dojo.widget.Editor2.superclass.execCommand.apply(this,arguments);
}
},queryCommandEnabled:function(_ae2,_ae3){
switch(_ae2.toLowerCase()){
case "htmltoggle":
return true;
default:
if(this._inSourceMode){
return false;
}
return dojo.widget.Editor2.superclass.queryCommandEnabled.apply(this,arguments);
}
},queryCommandState:function(_ae4,_ae5){
switch(_ae4.toLowerCase()){
case "htmltoggle":
return this._inSourceMode;
default:
return dojo.widget.Editor2.superclass.queryCommandState.apply(this,arguments);
}
},onClick:function(e){
dojo.widget.Editor2.superclass.onClick.call(this,e);
if(dojo.widget.PopupManager){
if(!e){
e=this.window.event;
}
dojo.widget.PopupManager.onClick(e);
}
},clobberFocus:function(){
},toggleHtmlEditing:function(){
if(this===dojo.widget.Editor2Manager.getCurrentInstance()){
if(!this._inSourceMode){
var html=this.getEditorContent();
this._inSourceMode=true;
if(!this._htmlEditNode){
this._htmlEditNode=dojo.doc().createElement("textarea");
dojo.html.insertAfter(this._htmlEditNode,this.editorObject);
}
this._htmlEditNode.style.display="";
this._htmlEditNode.style.width="100%";
this._htmlEditNode.style.height=Math.max(dojo.html.getBorderBox(this.editNode).height,100)+"px";
this._htmlEditNode.value=html;
with(this.editorObject.style){
position="absolute";
left="-2000px";
top="-2000px";
}
}else{
this._inSourceMode=false;
this._htmlEditNode.blur();
with(this.editorObject.style){
position="";
left="";
top="";
}
var html=this._htmlEditNode.value;
dojo.lang.setTimeout(this,"replaceEditorContent",1,html);
this._htmlEditNode.style.display="none";
this.focus();
}
this.onDisplayChanged(null,true);
}
},setFocus:function(){
if(dojo.widget.Editor2Manager.getCurrentInstance()===this){
return;
}
this.clobberFocus();
dojo.widget.Editor2Manager.setCurrentInstance(this);
},setBlur:function(){
},saveSelection:function(){
this._bookmark=null;
this._bookmark=dojo.withGlobal(this.window,dojo.html.selection.getBookmark);
},restoreSelection:function(){
if(this._bookmark){
this.focus();
dojo.withGlobal(this.window,"moveToBookmark",dojo.html.selection,[this._bookmark]);
this._bookmark=null;
}else{
dojo.debug("restoreSelection: no saved selection is found!");
}
},_updateToolbarLastRan:null,_updateToolbarTimer:null,_updateToolbarFrequency:500,updateToolbar:function(_ae8){
if((!this.isLoaded)||(!this.toolbarWidget)){
return;
}
var diff=new Date()-this._updateToolbarLastRan;
if((!_ae8)&&(this._updateToolbarLastRan)&&((diff<this._updateToolbarFrequency))){
clearTimeout(this._updateToolbarTimer);
var _aea=this;
this._updateToolbarTimer=setTimeout(function(){
_aea.updateToolbar();
},this._updateToolbarFrequency/2);
return;
}else{
this._updateToolbarLastRan=new Date();
}
if(dojo.widget.Editor2Manager.getCurrentInstance()!==this){
return;
}
this.toolbarWidget.update();
},destroy:function(_aeb){
this._htmlEditNode=null;
dojo.event.disconnect(this,"close",this.toolbarWidget,"hide");
if(!_aeb){
this.toolbarWidget.destroy();
}
dojo.widget.Editor2.superclass.destroy.call(this);
},_lastStateTimestamp:0,onDisplayChanged:function(e,_aed){
this._lastStateTimestamp=(new Date()).getTime();
dojo.widget.Editor2.superclass.onDisplayChanged.call(this,e);
this.updateToolbar(_aed);
},onLoad:function(){
try{
dojo.widget.Editor2.superclass.onLoad.call(this);
}
catch(e){
dojo.debug(e);
}
this.editorOnLoad();
},onFocus:function(){
dojo.widget.Editor2.superclass.onFocus.call(this);
this.setFocus();
},getEditorContent:function(){
if(this._inSourceMode){
return this._htmlEditNode.value;
}
return dojo.widget.Editor2.superclass.getEditorContent.call(this);
},replaceEditorContent:function(html){
if(this._inSourceMode){
this._htmlEditNode.value=html;
return;
}
dojo.widget.Editor2.superclass.replaceEditorContent.apply(this,arguments);
},getCommand:function(name){
if(this._loadedCommands[name]){
return this._loadedCommands[name];
}
var cmd=dojo.widget.Editor2Manager.getCommand(this,name);
this._loadedCommands[name]=cmd;
return cmd;
},shortcuts:[["bold"],["italic"],["underline"],["selectall","a"],["insertunorderedlist","\\"]],setupDefaultShortcuts:function(){
var exec=function(cmd){
return function(){
cmd.execute();
};
};
var self=this;
dojo.lang.forEach(this.shortcuts,function(item){
var cmd=self.getCommand(item[0]);
if(cmd){
self.addKeyHandler(item[1]?item[1]:item[0].charAt(0),item[2]==undefined?self.KEY_CTRL:item[2],exec(cmd));
}
});
}});
dojo.provide("dojo.widget.Editor2Plugin.BlockQuote");
dojo.widget.Editor2Plugin.BlockQuote={getCommand:function(_af6,name){
if(name=="blockquote"){
return dojo.widget.Editor2Plugin.BlockQuote.blockQuoteCommand;
}
},getToolbarItem:function(name){
var name=name.toLowerCase();
var item;
if(name=="blockquote"){
item=new dojo.widget.Editor2ToolbarButton(name);
}
return item;
},blockQuoteCommand:{execute:function(){
var _afa=dojo.widget.Editor2Manager.getCurrentInstance();
var bq=dojo.withGlobal(_afa.window,"getAncestorElement",dojo.html.selection,["blockquote"]);
if(bq){
if(dojo.render.html.ie){
bq.outerHTML=bq.innerHTML;
}else{
dojo.withGlobal(_afa.window,"selectElement",dojo.html.selection,[bq]);
_afa.execCommand("inserthtml",bq.innerHTML);
}
}else{
_afa.execCommand("formatblock","blockquote");
}
},getState:function(){
var _afc=dojo.widget.Editor2Manager.getCurrentInstance();
if(_afc._inSourceMode){
return false;
}
var bq=dojo.withGlobal(_afc.window,"hasAncestorElement",dojo.html.selection,["blockquote"]);
return bq?dojo.widget.Editor2Manager.commandState.Latched:dojo.widget.Editor2Manager.commandState.Enabled;
},getText:function(){
return "Block Quote";
},destory:function(){
}}};
dojo.widget.Editor2Manager.registerHandler(dojo.widget.Editor2Plugin.BlockQuote.getCommand);
dojo.widget.Editor2ToolbarItemManager.registerHandler(dojo.widget.Editor2Plugin.BlockQuote.getToolbarItem);
dojo.provide("dojo.widget.html.layout");
dojo.widget.html.layout=function(_afe,_aff,_b00){
dojo.html.addClass(_afe,"dojoLayoutContainer");
_aff=dojo.lang.filter(_aff,function(_b01,idx){
_b01.idx=idx;
return dojo.lang.inArray(["top","bottom","left","right","client","flood"],_b01.layoutAlign);
});
if(_b00&&_b00!="none"){
var rank=function(_b04){
switch(_b04.layoutAlign){
case "flood":
return 1;
case "left":
case "right":
return (_b00=="left-right")?2:3;
case "top":
case "bottom":
return (_b00=="left-right")?3:2;
default:
return 4;
}
};
_aff.sort(function(a,b){
return (rank(a)-rank(b))||(a.idx-b.idx);
});
}
var f={top:dojo.html.getPixelValue(_afe,"padding-top",true),left:dojo.html.getPixelValue(_afe,"padding-left",true)};
dojo.lang.mixin(f,dojo.html.getContentBox(_afe));
dojo.lang.forEach(_aff,function(_b08){
var elm=_b08.domNode;
var pos=_b08.layoutAlign;
with(elm.style){
left=f.left+"px";
top=f.top+"px";
bottom="auto";
right="auto";
}
dojo.html.addClass(elm,"dojoAlign"+dojo.string.capitalize(pos));
if((pos=="top")||(pos=="bottom")){
dojo.html.setMarginBox(elm,{width:f.width});
var h=dojo.html.getMarginBox(elm).height;
f.height-=h;
if(pos=="top"){
f.top+=h;
}else{
elm.style.top=f.top+f.height+"px";
}
if(_b08.onResized){
_b08.onResized();
}
}else{
if(pos=="left"||pos=="right"){
var w=dojo.html.getMarginBox(elm).width;
if(_b08.resizeTo){
_b08.resizeTo(w,f.height);
}else{
dojo.html.setMarginBox(elm,{width:w,height:f.height});
}
f.width-=w;
if(pos=="left"){
f.left+=w;
}else{
elm.style.left=f.left+f.width+"px";
}
}else{
if(pos=="flood"||pos=="client"){
if(_b08.resizeTo){
_b08.resizeTo(f.width,f.height);
}else{
dojo.html.setMarginBox(elm,{width:f.width,height:f.height});
}
}
}
}
});
};
dojo.html.insertCssText(".dojoLayoutContainer{ position: relative; display: block; overflow: hidden; }\n"+"body .dojoAlignTop, body .dojoAlignBottom, body .dojoAlignLeft, body .dojoAlignRight { position: absolute; overflow: hidden; }\n"+"body .dojoAlignClient { position: absolute }\n"+".dojoAlignClient { overflow: auto; }\n");
dojo.provide("dojo.widget.ResizeHandle");
dojo.widget.defineWidget("dojo.widget.ResizeHandle",dojo.widget.HtmlWidget,{targetElmId:"",templateCssPath:dojo.uri.dojoUri("src/widget/templates/ResizeHandle.css"),templateString:"<div class=\"dojoHtmlResizeHandle\"><div></div></div>",postCreate:function(){
dojo.event.connect(this.domNode,"onmousedown",this,"_beginSizing");
},_beginSizing:function(e){
if(this._isSizing){
return false;
}
this.targetWidget=dojo.widget.byId(this.targetElmId);
this.targetDomNode=this.targetWidget?this.targetWidget.domNode:dojo.byId(this.targetElmId);
if(!this.targetDomNode){
return;
}
this._isSizing=true;
this.startPoint={"x":e.clientX,"y":e.clientY};
var mb=dojo.html.getMarginBox(this.targetDomNode);
this.startSize={"w":mb.width,"h":mb.height};
dojo.event.kwConnect({srcObj:dojo.body(),srcFunc:"onmousemove",targetObj:this,targetFunc:"_changeSizing",rate:25});
dojo.event.connect(dojo.body(),"onmouseup",this,"_endSizing");
e.preventDefault();
},_changeSizing:function(e){
try{
if(!e.clientX||!e.clientY){
return;
}
}
catch(e){
return;
}
var dx=this.startPoint.x-e.clientX;
var dy=this.startPoint.y-e.clientY;
var newW=this.startSize.w-dx;
var newH=this.startSize.h-dy;
if(this.minSize){
var mb=dojo.html.getMarginBox(this.targetDomNode);
if(newW<this.minSize.w){
newW=mb.width;
}
if(newH<this.minSize.h){
newH=mb.height;
}
}
if(this.targetWidget){
this.targetWidget.resizeTo(newW,newH);
}else{
dojo.html.setMarginBox(this.targetDomNode,{width:newW,height:newH});
}
e.preventDefault();
},_endSizing:function(e){
dojo.event.disconnect(dojo.body(),"onmousemove",this,"_changeSizing");
dojo.event.disconnect(dojo.body(),"onmouseup",this,"_endSizing");
this._isSizing=false;
}});
dojo.provide("dojo.widget.FloatingPane");
dojo.declare("dojo.widget.FloatingPaneBase",null,{title:"",iconSrc:"",hasShadow:false,constrainToContainer:false,taskBarId:"",resizable:true,titleBarDisplay:true,windowState:"normal",displayCloseAction:false,displayMinimizeAction:false,displayMaximizeAction:false,_max_taskBarConnectAttempts:5,_taskBarConnectAttempts:0,templatePath:dojo.uri.dojoUri("src/widget/templates/FloatingPane.html"),templateCssPath:dojo.uri.dojoUri("src/widget/templates/FloatingPane.css"),fillInFloatingPaneTemplate:function(args,frag){
var _b18=this.getFragNodeRef(frag);
dojo.html.copyStyle(this.domNode,_b18);
dojo.body().appendChild(this.domNode);
if(!this.isShowing()){
this.windowState="minimized";
}
if(this.iconSrc==""){
dojo.html.removeNode(this.titleBarIcon);
}else{
this.titleBarIcon.src=this.iconSrc.toString();
}
if(this.titleBarDisplay){
this.titleBar.style.display="";
dojo.html.disableSelection(this.titleBar);
this.titleBarIcon.style.display=(this.iconSrc==""?"none":"");
this.minimizeAction.style.display=(this.displayMinimizeAction?"":"none");
this.maximizeAction.style.display=(this.displayMaximizeAction&&this.windowState!="maximized"?"":"none");
this.restoreAction.style.display=(this.displayMaximizeAction&&this.windowState=="maximized"?"":"none");
this.closeAction.style.display=(this.displayCloseAction?"":"none");
this.drag=new dojo.dnd.HtmlDragMoveSource(this.domNode);
if(this.constrainToContainer){
this.drag.constrainTo();
}
this.drag.setDragHandle(this.titleBar);
var self=this;
dojo.event.topic.subscribe("dragMove",function(info){
if(info.source.domNode==self.domNode){
dojo.event.topic.publish("floatingPaneMove",{source:self});
}
});
}
if(this.resizable){
this.resizeBar.style.display="";
this.resizeHandle=dojo.widget.createWidget("ResizeHandle",{targetElmId:this.widgetId,id:this.widgetId+"_resize"});
this.resizeBar.appendChild(this.resizeHandle.domNode);
}
if(this.hasShadow){
this.shadow=new dojo.lfx.shadow(this.domNode);
}
this.bgIframe=new dojo.html.BackgroundIframe(this.domNode);
if(this.taskBarId){
this._taskBarSetup();
}
dojo.body().removeChild(this.domNode);
},postCreate:function(){
if(dojo.hostenv.post_load_){
this._setInitialWindowState();
}else{
dojo.addOnLoad(this,"_setInitialWindowState");
}
},maximizeWindow:function(evt){
var mb=dojo.html.getMarginBox(this.domNode);
this.previous={width:mb.width||this.width,height:mb.height||this.height,left:this.domNode.style.left,top:this.domNode.style.top,bottom:this.domNode.style.bottom,right:this.domNode.style.right};
if(this.domNode.parentNode.style.overflow.toLowerCase()!="hidden"){
this.parentPrevious={overflow:this.domNode.parentNode.style.overflow};
dojo.debug(this.domNode.parentNode.style.overflow);
this.domNode.parentNode.style.overflow="hidden";
}
this.domNode.style.left=dojo.html.getPixelValue(this.domNode.parentNode,"padding-left",true)+"px";
this.domNode.style.top=dojo.html.getPixelValue(this.domNode.parentNode,"padding-top",true)+"px";
if((this.domNode.parentNode.nodeName.toLowerCase()=="body")){
var _b1d=dojo.html.getViewport();
var _b1e=dojo.html.getPadding(dojo.body());
this.resizeTo(_b1d.width-_b1e.width,_b1d.height-_b1e.height);
}else{
var _b1f=dojo.html.getContentBox(this.domNode.parentNode);
this.resizeTo(_b1f.width,_b1f.height);
}
this.maximizeAction.style.display="none";
this.restoreAction.style.display="";
if(this.resizeHandle){
this.resizeHandle.domNode.style.display="none";
}
this.drag.setDragHandle(null);
this.windowState="maximized";
},minimizeWindow:function(evt){
this.hide();
for(var attr in this.parentPrevious){
this.domNode.parentNode.style[attr]=this.parentPrevious[attr];
}
this.lastWindowState=this.windowState;
this.windowState="minimized";
},restoreWindow:function(evt){
if(this.windowState=="minimized"){
this.show();
if(this.lastWindowState=="maximized"){
this.domNode.parentNode.style.overflow="hidden";
this.windowState="maximized";
}else{
this.windowState="normal";
}
}else{
if(this.windowState=="maximized"){
for(var attr in this.previous){
this.domNode.style[attr]=this.previous[attr];
}
for(var attr in this.parentPrevious){
this.domNode.parentNode.style[attr]=this.parentPrevious[attr];
}
this.resizeTo(this.previous.width,this.previous.height);
this.previous=null;
this.parentPrevious=null;
this.restoreAction.style.display="none";
this.maximizeAction.style.display=this.displayMaximizeAction?"":"none";
if(this.resizeHandle){
this.resizeHandle.domNode.style.display="";
}
this.drag.setDragHandle(this.titleBar);
this.windowState="normal";
}else{
}
}
},toggleDisplay:function(){
if(this.windowState=="minimized"){
this.restoreWindow();
}else{
this.minimizeWindow();
}
},closeWindow:function(evt){
dojo.html.removeNode(this.domNode);
this.destroy();
},onMouseDown:function(evt){
this.bringToTop();
},bringToTop:function(){
var _b26=dojo.widget.manager.getWidgetsByType(this.widgetType);
var _b27=[];
for(var x=0;x<_b26.length;x++){
if(this.widgetId!=_b26[x].widgetId){
_b27.push(_b26[x]);
}
}
_b27.sort(function(a,b){
return a.domNode.style.zIndex-b.domNode.style.zIndex;
});
_b27.push(this);
var _b2b=100;
for(x=0;x<_b27.length;x++){
_b27[x].domNode.style.zIndex=_b2b+x*2;
}
},_setInitialWindowState:function(){
if(this.isShowing()){
this.width=-1;
var mb=dojo.html.getMarginBox(this.domNode);
this.resizeTo(mb.width,mb.height);
}
if(this.windowState=="maximized"){
this.maximizeWindow();
this.show();
return;
}
if(this.windowState=="normal"){
this.show();
return;
}
if(this.windowState=="minimized"){
this.hide();
return;
}
this.windowState="minimized";
},_taskBarSetup:function(){
var _b2d=dojo.widget.getWidgetById(this.taskBarId);
if(!_b2d){
if(this._taskBarConnectAttempts<this._max_taskBarConnectAttempts){
dojo.lang.setTimeout(this,this._taskBarSetup,50);
this._taskBarConnectAttempts++;
}else{
dojo.debug("Unable to connect to the taskBar");
}
return;
}
_b2d.addChild(this);
},showFloatingPane:function(){
this.bringToTop();
},onFloatingPaneShow:function(){
var mb=dojo.html.getMarginBox(this.domNode);
this.resizeTo(mb.width,mb.height);
},resizeTo:function(_b2f,_b30){
dojo.html.setMarginBox(this.domNode,{width:_b2f,height:_b30});
dojo.widget.html.layout(this.domNode,[{domNode:this.titleBar,layoutAlign:"top"},{domNode:this.resizeBar,layoutAlign:"bottom"},{domNode:this.containerNode,layoutAlign:"client"}]);
dojo.widget.html.layout(this.containerNode,this.children,"top-bottom");
this.bgIframe.onResized();
if(this.shadow){
this.shadow.size(_b2f,_b30);
}
this.onResized();
},checkSize:function(){
},destroyFloatingPane:function(){
if(this.resizeHandle){
this.resizeHandle.destroy();
this.resizeHandle=null;
}
}});
dojo.widget.defineWidget("dojo.widget.FloatingPane",[dojo.widget.ContentPane,dojo.widget.FloatingPaneBase],{fillInTemplate:function(args,frag){
this.fillInFloatingPaneTemplate(args,frag);
dojo.widget.FloatingPane.superclass.fillInTemplate.call(this,args,frag);
},postCreate:function(){
dojo.widget.FloatingPaneBase.prototype.postCreate.apply(this,arguments);
dojo.widget.FloatingPane.superclass.postCreate.apply(this,arguments);
},show:function(){
dojo.widget.FloatingPane.superclass.show.apply(this,arguments);
this.showFloatingPane();
},onShow:function(){
dojo.widget.FloatingPane.superclass.onShow.call(this);
this.onFloatingPaneShow();
},destroy:function(){
this.destroyFloatingPane();
dojo.widget.FloatingPane.superclass.destroy.apply(this,arguments);
}});
dojo.widget.defineWidget("dojo.widget.ModalFloatingPane",[dojo.widget.FloatingPane,dojo.widget.ModalDialogBase],{windowState:"minimized",displayCloseAction:true,postCreate:function(){
dojo.widget.ModalDialogBase.prototype.postCreate.call(this);
dojo.widget.ModalFloatingPane.superclass.postCreate.call(this);
},show:function(){
this.showModalDialog();
dojo.widget.ModalFloatingPane.superclass.show.apply(this,arguments);
this.bg.style.zIndex=this.domNode.style.zIndex-1;
},hide:function(){
this.hideModalDialog();
dojo.widget.ModalFloatingPane.superclass.hide.apply(this,arguments);
},closeWindow:function(){
this.hide();
dojo.widget.ModalFloatingPane.superclass.closeWindow.apply(this,arguments);
}});
dojo.provide("dojo.widget.Editor2Plugin.DialogCommands");
dojo.widget.defineWidget("dojo.widget.Editor2Dialog",[dojo.widget.HtmlWidget,dojo.widget.FloatingPaneBase,dojo.widget.ModalDialogBase],{templatePath:dojo.uri.dojoUri("src/widget/templates/Editor2/EditorDialog.html"),modal:true,width:"",height:"",windowState:"minimized",displayCloseAction:true,contentFile:"",contentClass:"",fillInTemplate:function(args,frag){
this.fillInFloatingPaneTemplate(args,frag);
dojo.widget.Editor2Dialog.superclass.fillInTemplate.call(this,args,frag);
},postCreate:function(){
if(this.contentFile){
dojo.require(this.contentFile);
}
if(this.modal){
dojo.widget.ModalDialogBase.prototype.postCreate.call(this);
}else{
with(this.domNode.style){
zIndex=999;
display="none";
}
}
dojo.widget.FloatingPaneBase.prototype.postCreate.apply(this,arguments);
dojo.widget.Editor2Dialog.superclass.postCreate.call(this);
if(this.width&&this.height){
with(this.domNode.style){
width=this.width;
height=this.height;
}
}
},createContent:function(){
if(!this.contentWidget&&this.contentClass){
this.contentWidget=dojo.widget.createWidget(this.contentClass);
this.addChild(this.contentWidget);
}
},show:function(){
if(!this.contentWidget){
dojo.widget.Editor2Dialog.superclass.show.apply(this,arguments);
this.createContent();
dojo.widget.Editor2Dialog.superclass.hide.call(this);
}
if(!this.contentWidget||!this.contentWidget.loadContent()){
return;
}
this.showFloatingPane();
dojo.widget.Editor2Dialog.superclass.show.apply(this,arguments);
if(this.modal){
this.showModalDialog();
}
if(this.modal){
this.bg.style.zIndex=this.domNode.style.zIndex-1;
}
},onShow:function(){
dojo.widget.Editor2Dialog.superclass.onShow.call(this);
this.onFloatingPaneShow();
},closeWindow:function(){
this.hide();
dojo.widget.Editor2Dialog.superclass.closeWindow.apply(this,arguments);
},hide:function(){
if(this.modal){
this.hideModalDialog();
}
dojo.widget.Editor2Dialog.superclass.hide.call(this);
},checkSize:function(){
if(this.isShowing()){
if(this.modal){
this._sizeBackground();
}
this.placeModalDialog();
this.onResized();
}
}});
dojo.widget.defineWidget("dojo.widget.Editor2DialogContent",dojo.widget.HtmlWidget,{widgetsInTemplate:true,postMixInProperties:function(){
dojo.widget.HtmlWidget.superclass.postMixInProperties.apply(this,arguments);
this.editorStrings=dojo.i18n.getLocalization("dojo.widget","Editor2",this.lang);
this.commonStrings=dojo.i18n.getLocalization("dojo.widget","common",this.lang);
},loadContent:function(){
return true;
},cancel:function(){
this.parent.hide();
}});
dojo.lang.declare("dojo.widget.Editor2DialogCommand",dojo.widget.Editor2BrowserCommand,function(_b35,name,_b37){
_b37.iconSrc=dojo.uri.dojoUri("src/widget/templates/buttons/"+name+".gif");
this.dialogParas=_b37;
},{execute:function(){
if(!this.dialog){
if(!this.dialogParas.contentFile||!this.dialogParas.contentClass){
alert("contentFile and contentClass should be set for dojo.widget.Editor2DialogCommand.dialogParas!");
return;
}
this.dialog=dojo.widget.createWidget("Editor2Dialog",this.dialogParas);
dojo.body().appendChild(this.dialog.domNode);
dojo.event.connect(this,"destroy",this.dialog,"destroy");
}
this.dialog.show();
},getText:function(){
return this.dialogParas.title||dojo.widget.Editor2DialogCommand.superclass.getText.call(this);
}});
dojo.provide("dojo.widget.Editor2Plugin.TableOperation");
dojo.event.topic.subscribe("dojo.widget.RichText::init",function(_b38){
if(dojo.render.html.ie){
_b38.contentDomPreFilters.push(dojo.widget.Editor2Plugin.TableOperation.showIETableBorder);
_b38.contentDomPostFilters.push(dojo.widget.Editor2Plugin.TableOperation.removeIEFakeClass);
}
_b38.getCommand("toggletableborder");
});
dojo.lang.declare("dojo.widget.Editor2Plugin.deletetableCommand",dojo.widget.Editor2Command,{execute:function(e){
var _b3a=dojo.withGlobal(this._editor.window,"getAncestorElement",dojo.html.selection,["table"]);
if(_b3a){
dojo.withGlobal(this._editor.window,"selectElement",dojo.html.selection,[_b3a]);
var _b3b="";
if(!e.altKey){
var rows=_b3a.rows;
for(var nrow=0;nrow<rows.length;nrow++){
var _b3e=dojo.widget.Editor2Plugin.TableOperation._getCellChildren(rows[nrow]);
for(var i=0;i<_b3e.length;i++){
_b3b+=_b3e[i].innerHTML;
}
}
}
this._editor.execCommand("delete");
if(_b3b.length>0){
this._editor.execCommand("inserthtml",_b3b);
}
}
},getState:function(){
return dojo.withGlobal(this._editor.window,"hasAncestorElement",dojo.html.selection,["table"])?dojo.widget.Editor2Manager.commandState.Enabled:dojo.widget.Editor2Manager.commandState.Disabled;
},getText:function(){
return "Delete Table (alt-click to destroy content)";
}});
dojo.lang.declare("dojo.widget.Editor2Plugin.toggletableborderCommand",dojo.widget.Editor2Command,function(){
this._showTableBorder=false;
dojo.event.connect(this._editor,"editorOnLoad",this,"execute");
},{execute:function(){
if(this._showTableBorder){
this._showTableBorder=false;
if(dojo.render.html.moz){
this._editor.removeStyleSheet(dojo.uri.dojoUri("src/widget/templates/Editor2/showtableborder_gecko.css"));
}else{
if(dojo.render.html.ie){
this._editor.removeStyleSheet(dojo.uri.dojoUri("src/widget/templates/Editor2/showtableborder_ie.css"));
}
}
}else{
this._showTableBorder=true;
if(dojo.render.html.moz){
this._editor.addStyleSheet(dojo.uri.dojoUri("src/widget/templates/Editor2/showtableborder_gecko.css"));
}else{
if(dojo.render.html.ie){
this._editor.addStyleSheet(dojo.uri.dojoUri("src/widget/templates/Editor2/showtableborder_ie.css"));
}
}
}
},getText:function(){
return "Toggle Table Border";
},getState:function(){
return (this._showTableBorder?dojo.widget.Editor2Manager.commandState.Latched:dojo.widget.Editor2Manager.commandState.Enabled);
}});
dojo.lang.declare("dojo.widget.Editor2Plugin.dialogtextcolorpickerCommand",dojo.widget.Editor2Command,{execute:function(_b40){
this._editor.getCommand("tabledialog").dialog.contentWidget.set_color(_b40);
},getText:function(){
return "Pick a new text color";
}});
dojo.lang.declare("dojo.widget.Editor2Plugin.dialogbgcolorpickerCommand",dojo.widget.Editor2Command,{execute:function(_b41){
this._editor.getCommand("tabledialog").dialog.contentWidget.set_backgroundColor(_b41);
},getText:function(){
return "Pick a new background color";
}});
dojo.lang.declare("dojo.widget.Editor2Plugin.dialogbordercolorpickerCommand",dojo.widget.Editor2Command,{execute:function(_b42){
this._editor.getCommand("tabledialog").dialog.contentWidget.set_borderColor(_b42);
},getText:function(){
return "Pick a new border color";
}});
dojo.lang.declare("dojo.widget.Editor2Plugin.inserttableCommand",dojo.widget.Editor2Command,{execute:function(){
dojo.widget.Editor2Plugin.TableOperation.callTableDialog(this._editor,"inserttable");
},getText:function(){
return "Insert Table";
},getState:function(){
return this._editor._inSourceMode?dojo.widget.Editor2Manager.commandState.Disabled:dojo.widget.Editor2Manager.commandState.Enabled;
}});
dojo.lang.declare("dojo.widget.Editor2Plugin.tablepropertiesCommand",dojo.widget.Editor2Command,{execute:function(){
dojo.widget.Editor2Plugin.TableOperation.callTableDialog(this._editor,"tableproperties");
},getText:function(){
return "Table Properties";
},getState:function(){
var _b43=(!this._editor._inSourceMode&&!this.wasSouceMode&&dojo.withGlobal(this._editor.window,"hasAncestorElement",dojo.html.selection,["table"]));
if(_b43!=this.TOstate){
this._editor.toolbarWidget.TObar.style.display=(_b43?"block":"none");
this.TOstate=_b43;
}
this.wasSouceMode=this._editor._inSourceMode;
return _b43?dojo.widget.Editor2Manager.commandState.Enabled:dojo.widget.Editor2Manager.commandState.Disabled;
},wasSourceMode:false,TOstate:false});
dojo.lang.declare("dojo.widget.Editor2Plugin.trpropertiesCommand",dojo.widget.Editor2Command,{execute:function(){
dojo.widget.Editor2Plugin.TableOperation.callTableDialog(this._editor,"trproperties");
},getText:function(){
return "Row Properties";
}});
dojo.lang.declare("dojo.widget.Editor2Plugin.tdpropertiesCommand",dojo.widget.Editor2Command,{execute:function(){
dojo.widget.Editor2Plugin.TableOperation.callTableDialog(this._editor,"tdproperties");
},getText:function(){
return "Cell Properties";
}});
dojo.lang.declare("dojo.widget.Editor2Plugin.insertrowbelowCommand",dojo.widget.Editor2Command,{execute:function(){
dojo.widget.Editor2Plugin.TableOperation._insertRow(this._editor,true);
},getText:function(){
return "Insert Row Below";
}});
dojo.lang.declare("dojo.widget.Editor2Plugin.insertrowaboveCommand",dojo.widget.Editor2Command,{execute:function(){
dojo.widget.Editor2Plugin.TableOperation._insertRow(this._editor,false);
},getText:function(){
return "Insert Row Above";
}});

dojo.lang.declare("dojo.widget.Editor2Plugin.deleterowsCommand",dojo.widget.Editor2Command,{execute:function(){
var _b44=dojo.withGlobal(this._editor.window,"getAncestorElement",dojo.html.selection,["td","th"]);
if(_b44!=null){
var _b45=_b44.parentNode.rowIndex;
var _b46=_b45;
var _b47=dojo.withGlobal(this._editor.window,"getAncestorElement",dojo.html.selection,["table"]);
if(_b47.rows.length<2){
alert("Cannot delete the last row!");
return;
}
var _b48="";
var _b49=dojo.widget.Editor2Plugin.TableOperation._countColumns(_b47);
var _b4a=new Array;
var rows=_b47.rows;
for(var nrow=0;nrow<rows.length;nrow++){
if(nrow!=_b46){
_b48+="\t\t<tr "+dojo.widget.Editor2Plugin.TableOperation._getAttributesHTML(rows[nrow])+">\n";
}
var _b4d=dojo.widget.Editor2Plugin.TableOperation._getCellChildren(rows[nrow]);
var _b4e=0,ch=null;
for(var i=0;i<_b49;i++){
if(_b4a[i]!==undefined){
rs=_b4a[i].rowSpan-1;
_b48+=dojo.widget.Editor2Plugin.TableOperation._nodeHTML(_b4a[i],rs,null,true);
_b4a[i]=undefined;
}
if(_b4e<_b4d.length){
ch=_b4d[_b4e];
if(ch.cellIndex==i){
var rs=ch.rowSpan;
if(nrow<_b46&&nrow+rs>_b46){
rs--;
}
if(nrow==_b46){
if(rs>1){
_b4a[_b4d[i].cellIndex]=_b4d[i];
}
}else{
_b48+=dojo.widget.Editor2Plugin.TableOperation._nodeHTML(_b4d[i],rs,null,true);
}
_b4e++;
}
}
}
if(nrow!=_b46){
_b48+="\n\t\t</tr>\n";
}
}
dojo.widget.Editor2Plugin.TableOperation._reinsertTable(this._editor,_b48,Math.max(_b45-1,0),_b44.cellIndex);
}
},getText:function(){
return "Delete Row";
}});

dojo.lang.declare("dojo.widget.Editor2Plugin.insertcolafterCommand",dojo.widget.Editor2Command,{execute:function(){
dojo.widget.Editor2Plugin.TableOperation._insertCol(this._editor,true);
},getText:function(){
return "Insert Column After";
}});
dojo.lang.declare("dojo.widget.Editor2Plugin.insertcolbeforeCommand",dojo.widget.Editor2Command,{execute:function(){
dojo.widget.Editor2Plugin.TableOperation._insertCol(this._editor,false);
},getText:function(){
return "Insert Column Before";
}});
dojo.lang.declare("dojo.widget.Editor2Plugin.deletecolsCommand",dojo.widget.Editor2Command,{execute:function(){
var _b52=dojo.withGlobal(this._editor.window,"getAncestorElement",dojo.html.selection,["td","th"]);
if(_b52!=null){
var _b53=dojo.withGlobal(this._editor.window,"getAncestorElement",dojo.html.selection,["table"]);
var _b54=_b52.parentNode.rowIndex;
var _b55=dojo.widget.Editor2Plugin.TableOperation._cellColumnNumber(_b53,_b52);
if(dojo.widget.Editor2Plugin.TableOperation._countColumns(_b53)<2){
alert("Cannot delete the last column!");
return;
}
var _b56="";
var rows=_b53.rows;
var cs,rs,_b5a,_b5b=[];
for(var nrow=0;nrow<rows.length;nrow++){
_b56+="\t\t<tr "+dojo.widget.Editor2Plugin.TableOperation._getAttributesHTML(rows[nrow])+">\n";
var _b5d=dojo.widget.Editor2Plugin.TableOperation._getCellChildren(rows[nrow]);
for(var i=0;i<_b5d.length;i++){
cs=_b5d[i].colSpan;
rs=_b5d[i].rowSpan;
_b5a=_b5d[i].cellIndex;
if(_b5b[nrow]!=-1){
if(_b5b[nrow]!=undefined){
_b5a+=_b5b[nrow];
}
if(_b5a<=_b55&&_b5a+cs>_b55){
cs--;
}
if(_b5a<_b55){
for(var j=1;j<rs;j++){
if(_b5b[j+nrow]==undefined){
_b5b[j+nrow]=cs;
}else{
_b5b[j+nrow]+=cs;
}
}
}
}
if(cs){
_b56+=dojo.widget.Editor2Plugin.TableOperation._nodeHTML(_b5d[i],null,cs,true);
}else{
for(var j=1;j<rs;j++){
_b5b[j+nrow]=-1;
}
}
}
_b56+="\n\t\t</tr>\n";
}
dojo.widget.Editor2Plugin.TableOperation._reinsertTable(this._editor,_b56,_b54,Math.max(_b52.cellIndex-1,0));
}
},getText:function(){
return "Delete Column";
}});


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


dojo.lang.declare("dojo.widget.Editor2Plugin.splitcellsCommand",dojo.widget.Editor2Command,{execute:function(){
dojo.widget.Editor2Plugin.TableOperation._splitCells(this._editor,1,1);
},getText:function(){
return "Split Merged Cell";
},getState:function(){
var _b79=dojo.withGlobal(this._editor.window,"getAncestorElement",dojo.html.selection,["td","th"]);
return (_b79&&(_b79.rowSpan>1||_b79.colSpan>1)?dojo.widget.Editor2Manager.commandState.Enabled:dojo.widget.Editor2Manager.commandState.Disabled);
}});
dojo.lang.declare("dojo.widget.Editor2Plugin.splitrowsCommand",dojo.widget.Editor2Command,{execute:function(){
dojo.widget.Editor2Plugin.TableOperation._splitCells(this._editor,1,null);
},getText:function(){
return "Split Merged Row";
},getState:function(){
var _b7a=dojo.withGlobal(this._editor.window,"getAncestorElement",dojo.html.selection,["td","th"]);
return (_b7a&&(_b7a.rowSpan>1)?dojo.widget.Editor2Manager.commandState.Enabled:dojo.widget.Editor2Manager.commandState.Disabled);
}});
dojo.lang.declare("dojo.widget.Editor2Plugin.splitcolsCommand",dojo.widget.Editor2Command,{execute:function(){
dojo.widget.Editor2Plugin.TableOperation._splitCells(this._editor,null,1);
},getText:function(){
return "Split Merged Column";
},getState:function(){
var _b7b=dojo.withGlobal(this._editor.window,"getAncestorElement",dojo.html.selection,["td","th"]);
return (_b7b&&(_b7b.colSpan>1)?dojo.widget.Editor2Manager.commandState.Enabled:dojo.widget.Editor2Manager.commandState.Disabled);
}});
dojo.lang.declare("dojo.widget.Editor2Plugin.togglethCommand",dojo.widget.Editor2Command,{execute:function(){
var xpad=[],_b7d=0;
if(dojo.render.html.moz){
var sel=this._editor.window.getSelection();
var i=0,_b80;
try{
while(range=sel.getRangeAt(i++)){
var cell=range.startContainer.childNodes[range.startOffset];
if(cell.nodeName.toLowerCase().match(/^t[dh]$/)){
_b80=cell.parentNode.rowIndex;
if(xpad[_b80]===undefined){
xpad[_b80]=[];
}
xpad[_b80][cell.cellIndex]=true;
_b7d++;
if(!_b82){
var _b82=cell,_b83=_b80;
}
}
}
}
catch(e){
}
}
if(_b7d==0){
var _b82=dojo.withGlobal(this._editor.window,"getAncestorElement",dojo.html.selection,["td","th"]);
if(!_b82){
alert("Please select a table cell");
return;
}
var _b83=_b82.parentNode.rowIndex;
xpad[_b83]=[];
xpad[_b83][_b82.cellIndex]=true;
}
var _b84=dojo.withGlobal(this._editor.window,"getAncestorElement",dojo.html.selection,["table"]);
var _b85="",_b86;
var rows=_b84.rows;
for(var nrow=0;nrow<rows.length;nrow++){
_b85+="\t\t<tr "+dojo.widget.Editor2Plugin.TableOperation._getAttributesHTML(rows[nrow])+">\n";
var _b89=dojo.widget.Editor2Plugin.TableOperation._getCellChildren(rows[nrow]);
for(var i=0;i<_b89.length;i++){
_b86=(xpad[nrow]&&xpad[nrow][i])?(_b89[i].nodeName.toLowerCase()=="td"?"th":"td"):null;
_b85+=dojo.widget.Editor2Plugin.TableOperation._nodeHTML(_b89[i],null,null,true,_b86);
}
_b85+="\n\t\t</tr>\n";
}
dojo.widget.Editor2Plugin.TableOperation._reinsertTable(this._editor,_b85,_b83,_b82.cellIndex);
},getText:function(){
return "Toggle Heading-Cell";
},getState:function(){
return (dojo.withGlobal(this._editor.window,"getAncestorElement",dojo.html.selection,["th"])?dojo.widget.Editor2Manager.commandState.Latched:dojo.widget.Editor2Manager.commandState.Enabled);
}});
dojo.widget.Editor2Plugin.TableOperation={commandList:["inserttable","toggletableborder","deletetable","tableproperties","trproperties","tdproperties","insertrowbelow","insertrowabove","deleterows","insertcolafter","insertcolbefore","deletecols","mergecells","splitcells","splitrows","splitcols","toggleth","dialogbordercolorpicker","dialogtextcolorpicker","dialogbgcolorpicker"],getCommand:function(_b8a,name){
if(name=="tabledialog"){
return new dojo.widget.Editor2DialogCommand(_b8a,"tabledialog",{contentFile:"dojo.widget.Editor2Plugin.InsertTableDialog",contentClass:"Editor2InsertTableDialog",title:"Edit Table",width:"660px",height:"450px"});
}
if(dojo.lang.find(dojo.widget.Editor2Plugin.TableOperation.commandList,name)>-1){
return new dojo.widget.Editor2Plugin[name+"Command"](_b8a,name);
}
},getToolbarItem:function(name){
var item;
if(dojo.lang.find(dojo.widget.Editor2Plugin.TableOperation.commandList,name)>-1){
item=new dojo.widget.Editor2ToolbarButton(name);
}
return item;
},getContextMenuGroup:function(name,_b8f){
return new dojo.widget.Editor2Plugin.TableContextMenuGroup(_b8f);
},callTableDialog:function(_b90,mode){
var di=_b90.getCommand("tabledialog");
di.mode=mode;
di.execute();
},showIETableBorder:function(dom){
var _b94=dom.getElementsByTagName("table");
dojo.lang.forEach(_b94,function(t){
dojo.html.addClass(t,"dojoShowIETableBorders");
});
return dom;
},removeIEFakeClass:function(dom){
var _b97=dom.getElementsByTagName("table");
dojo.lang.forEach(_b97,function(t){
dojo.html.removeClass(t,"dojoShowIETableBorders");
});
return dom;
},_getAttributesHTML:function(dom,_b9a){
var _b9b="",_b9c,val,_b9e,i;
if(dom && (_b9c=dom.attributes)) { 
if(!_b9a){
_b9a=[];
}
_b9a.push("disabled","tabindex","cols","datapagesize","hidefocus","contenteditable","_moz_resizing");
if(dojo.render.html.ie){
_b9a.push("style");
}
for(i=0;i<_b9c.length;i++){
val=_b9c[i].value;
_b9e=_b9c[i].name.toLowerCase();
if(val.length&&val!="null"&&(_b9e!="nowrap"||val!="false")){
if(!_b9a||(dojo.lang.find(_b9a,_b9e)==-1)){
_b9b+=_b9e+"=\""+val+"\" ";
}
}
}
if(dojo.render.html.ie){
var sty=dom.style;
if(sty){
sty=sty.cssText;
if(sty.length){
_b9b+="style=\""+sty+"\" ";
}
}
}
_b9b=_b9b.substr(0,_b9b.length-1);
return _b9b;} else return "";
},_nodeHTML:function(cell,_ba2,_ba3,_ba4,_ba5){
return (cell.nodeType==3?
cell.nodeValue:
"<"+(_ba5?_ba5:cell.nodeName.toLowerCase())+" "+((_ba2&&_ba3)?this._getAttributesHTML(cell,["colspan","rowspan"]):_ba2?this._getAttributesHTML(cell,["rowspan"]):_ba3?this._getAttributesHTML(cell,["colspan"]):this._getAttributesHTML(cell))+(_ba2>1?" rowspan=\""+_ba2+"\"":"")+(_ba3>1?" colspan=\""+_ba3+"\"":"")+">"+(_ba4===true?cell.innerHTML:_ba4===false?"<br>":_ba4)+"</"+(_ba5?_ba5:cell.nodeName.toLowerCase())+">");
},_getCellChildren:function(row){
var _ba7=[];
for(var i=0;i<row.childNodes.length;i++){
if(row.childNodes[i].nodeName.toLowerCase().match(/^t[dh]$/)){
_ba7.push(row.childNodes[i]);
}
}
return _ba7;
},_countColumns:function(_ba9){
var _bab=0;
var chNodes=dojo.widget.Editor2Plugin.TableOperation._getCellChildren(_ba9.rows[0]);
for(var i=0; i<chNodes.length; i++) _bab+=chNodes[i].colSpan;
return _bab;
},_cellColumnNumber:function(_bad,cell){
var cs,rs,_bb1,xpad=[],rows=_bad.rows;
var _bb4=cell.parentNode.rowIndex;
for(var nrow=0;nrow<=_bb4;nrow++){
if(xpad[nrow]===undefined){
xpad[nrow]=[];
}
var _bb6=dojo.widget.Editor2Plugin.TableOperation._getCellChildren(rows[nrow]);
_bb1=0;
for(var i=0;i<_bb6.length;i++){
cs=_bb6[i].colSpan;
rs=_bb6[i].rowSpan;
while(xpad[nrow][_bb1]!==undefined){
_bb1++;
}
if(nrow==_bb4&&cell.cellIndex==_bb6[i].cellIndex){
return _bb1;
}
for(var j=0;j<rs;j++){
for(var k=0;k<cs;k++){
if(j||k){
if(xpad[j+nrow]===undefined){
xpad[j+nrow]=[];
}
xpad[j+nrow][k+_bb1]=false;
}
}
}
_bb1+=cs;
}
}
}
,_reinsertTable:function(_bba,_bbb,row,col,_bbe,_bbf){
if(!_bbe){
_bbe=dojo.withGlobal(_bba.window,"getAncestorElement",dojo.html.selection,["table"]);
}
if(!_bbf){
_bbf="<table "+this._getAttributesHTML(_bbe)+">\n";
var _bc0=_bbe.childNodes,_bc1;
for(var i=0;i<_bc0.length;i++){
_bc1=_bc0[i].nodeName.toLowerCase();
if(_bc0[i].nodeType!=3&&_bc1!="tbody"&&_bc1!="tr"){
_bbf+=this._nodeHTML(_bc0[i],null,null,true);
}
}
}
_bbf+="\t<tbody "+this._getAttributesHTML(_bbe.getElementsByTagName("tbody")[0])+">\n";
if(!_bbb){
_bbb="";
var rows=_bbe.rows;
for(var nrow=0;nrow<rows.length;nrow++){
_bbb+="\t\t<tr "+dojo.widget.Editor2Plugin.TableOperation._getAttributesHTML(rows[nrow])+">"+rows[nrow].innerHTML+"\n\t\t</tr>\n";
}
}
if(row===undefined||row===null||col===undefined||col===null){
var _bc5=dojo.withGlobal(_bba.window,"getAncestorElement",dojo.html.selection,["td","th"]);
if(row===undefined||row===null){
row=(_bc5?_bc5.parentNode.cellIndex:0);
}
if(col===undefined||col===null){
col=(_bc5?_bc5.cellIndex:0);
}
}
dojo.withGlobal(_bba.window,"selectElement",dojo.html.selection,[_bbe]);
var _bc6,_bc7;
if(_bbe.previousSibling){
_bc7=_bbe.previousSibling;
}else{
_bc6=_bbe.parentNode;
}
if(dojo.render.html.ie){
_bba.execCommand("delete");
}
_bba.execCommand("inserthtml",_bbf+_bbb+"\t</tbody>\n</table>");
if(_bc7){
_bbe=_bc7.nextSibling;
}else{
_bbe=_bc6.firstChild;
}
if(_bbe&&_bbe.nodeName.toLowerCase()=="table"){
var _bc8=this._getCellChildren(_bbe.rows[row]);
for(var i=0;i<_bc8.length;i++){
if((_bc8[i].cellIndex+_bc8[i].colSpan)>col){
var _bc9=_bc8[i];
if(!dojo.render.html.ie){
_bc9=_bc9.lastChild;
}
dojo.withGlobal(_bba.window,"selectElement",dojo.html.selection,[_bc9]);
dojo.withGlobal(_bba.window,"collapse",dojo.html.selection,[true]);
break;
}
}
}
_bba._updateHeight();
},_insertRow:function(_bca,_bcb){
var _bcc=dojo.withGlobal(_bca.window,"getAncestorElement",dojo.html.selection,["td","th"]);
if(_bcc!=null){
var _bcd=_bcc.cellIndex;
var _bce=_bcc.parentNode.rowIndex;
if(_bcb){
_bce+=_bcc.rowSpan;
}
var _bcf=dojo.withGlobal(_bca.window,"getAncestorElement",dojo.html.selection,["table"]);
var _bd0="";
var _bd1=this._countColumns(_bcf);
var rows=_bcf.rows;
for(var nrow=0;nrow<Math.max(rows.length,_bce+1);nrow++){
if(nrow==_bce){
_bd0+="\t\t<tr "+this._getAttributesHTML(_bcc.parentNode)+">\n";
for(i=0;i<_bd1;i++){
_bd0+="<td><br></td>";
}
_bd0+="\n\t\t</tr>";
}
if(nrow<rows.length){
_bd0+="\t\t<tr "+this._getAttributesHTML(rows[nrow])+">\n";
var _bd5=this._getCellChildren(rows[nrow]);
for(var i=0;i<_bd5.length;i++){
var rs=_bd5[i].rowSpan;
if(nrow<_bce&&nrow+rs>_bce){
rs++;
_bd1-=_bd5[i].colSpan;
}
_bd0+=this._nodeHTML(_bd5[i],rs,null,true);
}
_bd0+="\n\t\t</tr>\n";
}
}
this._reinsertTable(_bca,_bd0,_bce,_bcd);
}
},_insertCol:function(_bd7,_bd8){
var _bd9=dojo.withGlobal(_bd7.window,"getAncestorElement",dojo.html.selection,["td","th"]);
if(_bd9!=null){
var _bda=_bd9.parentNode.rowIndex;
var _bdb=dojo.withGlobal(_bd7.window,"getAncestorElement",dojo.html.selection,["table"]);
var _bdc=this._cellColumnNumber(_bdb,_bd9);
if(_bd8){
_bdc+=_bd9.colSpan;
}
var _bdd="";
var rows=_bdb.rows;
var cs,rs,done,_be2,_be3=[],skip;
for(var nrow=0;nrow<rows.length;nrow++){
done=false;
skip=false;
_bdd+="\t\t<tr "+this._getAttributesHTML(rows[nrow])+">\n";
var _be6=this._getCellChildren(rows[nrow]);
for(var i=0;i<_be6.length;i++){
cs=_be6[i].colSpan;
rs=_be6[i].rowSpan;
_be2=_be6[i].cellIndex;
if(_be3[nrow]!=undefined){
_be2+=_be3[nrow];
}
if(_be2==_bdc){
_bdd+="<td><br></td>";
done=true;
}
if(_be2>_bdc){
skip=true;
}
if(_be2<_bdc){
for(var j=1;j<rs;j++){
if(_be3[j+nrow]==undefined){
_be3[j+nrow]=cs;
}else{
_be3[j+nrow]+=cs;
}
}
}
if(_be6[i].cellIndex<_bdc&&_be6[i].cellIndex+cs>_bdc){
cs++;
skip=true;
}
_bdd+=this._nodeHTML(_be6[i],null,cs,true);
}
if(!done&&!skip){
_bdd+="<td><br></td>";
}
_bdd+="\n\t\t</tr>\n";
}
this._reinsertTable(_bd7,_bdd,_bda,_bd9.cellIndex);
}
},_splitCells:function(_be9,_bea,_beb){
var _bec=dojo.withGlobal(_be9.window,"getAncestorElement",dojo.html.selection,["td","th"]);
if(_bec!=null){
var _bed=_bec.cellIndex;
var _bee=_bec.parentNode.rowIndex;
var _bef=dojo.withGlobal(_be9.window,"getAncestorElement",dojo.html.selection,["table"]);
var _bf0=this._cellColumnNumber(_bef,_bec);
var _bf1="";
var rows=_bef.rows;
var cs,rs,_bf5,done,_bf7=[];
for(var nrow=0;nrow<rows.length;nrow++){
_bf1+="\t\t<tr "+this._getAttributesHTML(rows[nrow])+">\n";
var _bf9=this._getCellChildren(rows[nrow]);
done=false;
_bf5=0;
for(var i=0;i<_bf9.length;i++){
cs=_bf9[i].colSpan;
rs=_bf9[i].rowSpan;
_bf5=_bf9[i].cellIndex;
if(_bf7[nrow]!=undefined){
_bf5+=_bf7[nrow];
}
if(_bf5==_bf0&&(nrow==_bee||(_bea&&nrow>=_bee&&nrow<_bee+_bec.rowSpan))){
for(var j=0;j<(_beb?_bec.colSpan:1);j++){
_bf1+=this._nodeHTML(_bec,_bea,_beb,(j==0&&nrow==_bee));
}
done=true;
}
if(_bf5!=_bf0||nrow!=_bee){
_bf1+=this._nodeHTML(_bf9[i],null,cs,true);
if(_bf5<_bf0){
for(var j=1;j<rs;j++){
_bf7[j+nrow]=_bf7[j+nrow]?_bf7[j+nrow]+cs:cs;
}
}
}
}
if(!done&&(nrow==_bee||(_bea&&nrow>=_bee&&nrow<_bee+_bec.rowSpan))){
for(var j=0;j<(_beb?_bec.colSpan:1);j++){
_bf1+=this._nodeHTML(_bec,_bea,_beb,(j==0&&nrow==_bee));
}
}
_bf1+="\n\t\t</tr>\n";
}
this._reinsertTable(_be9,_bf1,_bee,_bf0);
}
}};
dojo.widget.Editor2Manager.registerHandler(dojo.widget.Editor2Plugin.TableOperation.getCommand);
dojo.widget.Editor2ToolbarItemManager.registerHandler(dojo.widget.Editor2Plugin.TableOperation.getToolbarItem);
if(dojo.widget.Editor2Plugin.ContextMenuManager){
dojo.widget.Editor2Plugin.ContextMenuManager.registerGroup("Table",dojo.widget.Editor2Plugin.TableOperation.getContextMenuGroup);
dojo.declare("dojo.widget.Editor2Plugin.TableContextMenuGroup",dojo.widget.Editor2Plugin.SimpleContextMenuGroup,{createItems:function(){
this.items.push(dojo.widget.createWidget("Editor2ContextMenuItem",{caption:"Delete Table",command:"deletetable"}));
this.items.push(dojo.widget.createWidget("Editor2ContextMenuItem",{caption:"Table Property",command:"inserttable",iconClass:"TB_Button_Icon TB_Button_Table"}));
},checkVisibility:function(){
var _bfc=dojo.widget.Editor2Manager.getCurrentInstance();
var _bfd=dojo.withGlobal(_bfc.window,"hasAncestorElement",dojo.html.selection,["table"]);
if(dojo.withGlobal(_bfc.window,"hasAncestorElement",dojo.html.selection,["table"])){
this.items[0].show();
this.items[1].show();
return true;
}else{
this.items[0].hide();
this.items[1].hide();
return false;
}
}});
}

dojo.provide("dojo.widget.ColorPalette");
dojo.require("dojo.widget.*");
dojo.require("dojo.html.layout");
dojo.require("dojo.html.display");
dojo.require("dojo.html.selection");

dojo.widget.defineWidget(
"dojo.widget.ColorPalette",
dojo.widget.HtmlWidget,
{
palette: "7x10",

_palettes: {
"7x10": [["fff", "fcc", "fc9", "ff9", "ffc", "9f9", "9ff", "cff", "ccf", "fcf"],
["ccc", "f66", "f96", "ff6", "ff3", "6f9", "3ff", "6ff", "99f", "f9f"],
["c0c0c0", "f00", "f90", "fc6", "ff0", "3f3", "6cc", "3cf", "66c", "c6c"],
["999", "c00", "f60", "fc3", "fc0", "3c0", "0cc", "36f", "63f", "c3c"],
["666", "900", "c60", "c93", "990", "090", "399", "33f", "60c", "939"],
["333", "600", "930", "963", "660", "060", "366", "009", "339", "636"],
["000", "300", "630", "633", "330", "030", "033", "006", "309", "303"]],

"3x4": [["ffffff"/*white*/, "00ff00"/*lime*/, "008000"/*green*/, "0000ff"/*blue*/],
["c0c0c0"/*silver*/, "ffff00"/*yellow*/, "ff00ff"/*fuchsia*/, "000080"/*navy*/],
["808080"/*gray*/, "ff0000"/*red*/, "800080"/*purple*/, "000000"/*black*/]]
//["00ffff"/*aqua*/, "808000"/*olive*/, "800000"/*maroon*/, "008080"/*teal*/]];
},

buildRendering: function () {
this.domNode = document.createElement("table");
dojo.html.disableSelection(this.domNode);
dojo.event.connect(this.domNode, "onmousedown", function (e) {
e.preventDefault();
});
with (this.domNode) { // set the table's properties
cellPadding = "0"; cellSpacing = "1"; border = "1";
style.backgroundColor = "white";
}
var colors = this._palettes[this.palette];
for (var i = 0; i < colors.length; i++) {
var tr = this.domNode.insertRow(-1);
for (var j = 0; j < colors[i].length; j++) {
if (colors[i][j].length == 3) {
colors[i][j] = colors[i][j].replace(/(.)(.)(.)/, "$1$1$2$2$3$3");
}

var td = tr.insertCell(-1);
with (td.style) {
backgroundColor = "#" + colors[i][j];
border = "1px solid gray";
width = height = "15px";
fontSize = "1px";
}

td.color = "#" + colors[i][j];

td.onmouseover = function (e) { this.style.borderColor = "white"; }
td.onmouseout = function (e) { this.style.borderColor = "gray"; }
dojo.event.connect(td, "onmousedown", this, "onClick");

td.innerHTML = "&nbsp;";
}
}
},

onClick: function(/*Event*/ e) {
this.onColorSelect(e.currentTarget.color);
e.currentTarget.style.borderColor = "gray";
},

onColorSelect: function(color){
}
});


dojo.provide("dojo.widget.PopupContainer");

dojo.require("dojo.html.style");
dojo.require("dojo.html.layout");
dojo.require("dojo.html.selection");
dojo.require("dojo.html.iframe");
dojo.require("dojo.event.*");
dojo.require("dojo.widget.*");
dojo.require("dojo.widget.HtmlWidget");

dojo.declare(
"dojo.widget.PopupContainerBase",
null,
function(){
this.queueOnAnimationFinish = [];
},
{
isContainer: true,
templateString: '<div dojoAttachPoint="containerNode" style="display:none;position:absolute;" class="dojoPopupContainer" ></div>',
isShowingNow: false,
currentSubpopup: null,
beginZIndex: 1000,
parentPopup: null,
parent: null,
popupIndex: 0,
aroundBox: dojo.html.boxSizing.BORDER_BOX,
openedForWindow: null,

processKey: function(/*Event*/evt){
return false;
},

applyPopupBasicStyle: function(){
with(this.domNode.style){
display = 'none';
position = 'absolute';
}
},

aboutToShow: function() {
},

open: function(/*Integer*/x, /*Integer*/y, /*DomNode*/parent, /*Object*/explodeSrc, /*String?*/orient, /*Array?*/padding){
if (this.isShowingNow){ return; }
if(this.animationInProgress){
this.queueOnAnimationFinish.push(this.open, arguments);
return;
}

this.aboutToShow();

var around = false, node, aroundOrient;
if(typeof x == 'object'){
node = x;
aroundOrient = explodeSrc;
explodeSrc = parent;
parent = y;
around = true;
}

this.parent = parent;

dojo.body().appendChild(this.domNode);

explodeSrc = explodeSrc || parent["domNode"] || [];

var parentPopup = null;
this.isTopLevel = true;
while(parent){
if(parent !== this && (parent.setOpenedSubpopup != undefined && parent.applyPopupBasicStyle != undefined)){
parentPopup = parent;
this.isTopLevel = false;
parentPopup.setOpenedSubpopup(this);
break;
}
parent = parent.parent;
}

this.parentPopup = parentPopup;
this.popupIndex = parentPopup ? parentPopup.popupIndex + 1 : 1;

if(this.isTopLevel){
var button = dojo.html.isNode(explodeSrc) ? explodeSrc : null;
dojo.widget.PopupManager.opened(this, button);
}

if(this.isTopLevel && !dojo.withGlobal(this.openedForWindow||dojo.global(), dojo.html.selection.isCollapsed)){
this._bookmark = dojo.withGlobal(this.openedForWindow||dojo.global(), dojo.html.selection.getBookmark);
}else{
this._bookmark = null;
}

if(explodeSrc instanceof Array){
explodeSrc = {left: explodeSrc[0], top: explodeSrc[1], width: 0, height: 0};
}

with(this.domNode.style){
display="";
zIndex = this.beginZIndex + this.popupIndex;
}

if(around){
this.move(node, padding, aroundOrient);
}else{
this.move(x, y, padding, orient);
}
this.domNode.style.display="none";

this.explodeSrc = explodeSrc;

this.show();

this.isShowingNow = true;
},

move: function(/*Int*/x, /*Int*/y, /*Integer?*/padding, /*String?*/orient){

var around = (typeof x == "object");
if(around){
var aroundOrient=padding;
var node=x;
padding=y;
if(!aroundOrient){ //By default, attempt to open above the aroundNode, or below
aroundOrient = {'BL': 'TL', 'TL': 'BL'};
}
dojo.html.placeOnScreenAroundElement(this.domNode, node, padding, this.aroundBox, aroundOrient);
}else{
if(!orient){ orient = 'TL,TR,BL,BR';}
dojo.html.placeOnScreen(this.domNode, x, y, padding, true, orient);
}
},

close: function(/*Boolean?*/force){
if(force){
this.domNode.style.display="none";
}

if(this.animationInProgress){
this.queueOnAnimationFinish.push(this.close, []);
return;
}

this.closeSubpopup(force);
this.hide();
if(this.bgIframe){
this.bgIframe.hide();
this.bgIframe.size({left: 0, top: 0, width: 0, height: 0});
}
if(this.isTopLevel){
dojo.widget.PopupManager.closed(this);
}
this.isShowingNow = false;

if(this.parent){
setTimeout(
dojo.lang.hitch(this, 
function(){
try{
if(this.parent['focus']){
	this.parent.focus();
}else{
	this.parent.domNode.focus(); 
}
}catch(e){dojo.debug("No idea how to focus to parent", e);}
}
),
10
);
}

if(this._bookmark && dojo.withGlobal(this.openedForWindow||dojo.global(), dojo.html.selection.isCollapsed)){
if(this.openedForWindow){
this.openedForWindow.focus()
}
dojo.withGlobal(this.openedForWindow||dojo.global(), "moveToBookmark", dojo.html.selection, [this._bookmark]);
}
this._bookmark = null;
},

closeAll: function(/*Boolean?*/force){
if (this.parentPopup){
this.parentPopup.closeAll(force);
}else{
this.close(force);
}
},

setOpenedSubpopup: function(/*Widget*/popup) {
this.currentSubpopup = popup;
},

closeSubpopup: function(/*Boolean?*/force) {
if(this.currentSubpopup == null){ return; }

this.currentSubpopup.close(force);
this.currentSubpopup = null;
},

onShow: function() {
dojo.widget.PopupContainer.superclass.onShow.apply(this, arguments);
this.openedSize={w: this.domNode.style.width, h: this.domNode.style.height};
if(dojo.render.html.ie){
if(!this.bgIframe){
this.bgIframe = new dojo.html.BackgroundIframe();
this.bgIframe.setZIndex(this.domNode);
}

this.bgIframe.size(this.domNode);
this.bgIframe.show();
}
this.processQueue();
},

processQueue: function() {
if (!this.queueOnAnimationFinish.length) return;

var func = this.queueOnAnimationFinish.shift();
var args = this.queueOnAnimationFinish.shift();

func.apply(this, args);
},

onHide: function() {
dojo.widget.HtmlWidget.prototype.onHide.call(this);
if(this.openedSize){
with(this.domNode.style){
width=this.openedSize.w;
height=this.openedSize.h;
}
}

this.processQueue();
}
});

dojo.widget.defineWidget(
"dojo.widget.PopupContainer",
[dojo.widget.HtmlWidget, dojo.widget.PopupContainerBase], {
});


dojo.widget.PopupManager = new function(){
this.currentMenu = null;
this.currentButton = null;		// button that opened current menu (if any)
this.currentFocusMenu = null;	// the (sub)menu which receives key events
this.focusNode = null;
this.registeredWindows = [];

this.registerWin = function(/*Window*/win){
if(!win.__PopupManagerRegistered)
{
dojo.event.connect(win.document, 'onmousedown', this, 'onClick');
dojo.event.connect(win, "onscroll", this, "onClick");
dojo.event.connect(win.document, "onkey", this, 'onKey');
win.__PopupManagerRegistered = true;
this.registeredWindows.push(win);
}
};

this.registerAllWindows = function(/*Window*/targetWindow){
if(!targetWindow) { //see comment below
targetWindow = dojo.html.getDocumentWindow(window.top && window.top.document || window.document);
}

this.registerWin(targetWindow);

for (var i = 0; i < targetWindow.frames.length; i++){
try{
var win = dojo.html.getDocumentWindow(targetWindow.frames[i].document);
if(win){
this.registerAllWindows(win);
}
}catch(e){ /* squelch error for cross domain iframes */ }
}
};

this.unRegisterWin = function(/*Window*/win){
if(win.__PopupManagerRegistered)
{
dojo.event.disconnect(win.document, 'onmousedown', this, 'onClick');
dojo.event.disconnect(win, "onscroll", this, "onClick");
dojo.event.disconnect(win.document, "onkey", this, 'onKey');
win.__PopupManagerRegistered = false;
}
};

this.unRegisterAllWindows = function(){
for(var i=0;i<this.registeredWindows.length;++i){
this.unRegisterWin(this.registeredWindows[i]);
}
this.registeredWindows = [];
};

dojo.addOnLoad(this, "registerAllWindows");
dojo.addOnUnload(this, "unRegisterAllWindows");

this.closed = function(/*Widget*/menu){
if (this.currentMenu == menu){
this.currentMenu = null;
this.currentButton = null;
this.currentFocusMenu = null;
}
};

this.opened = function(/*Widget*/menu, /*DomNode*/button){
if (menu == this.currentMenu){ return; }

if (this.currentMenu){
this.currentMenu.close();
}

this.currentMenu = menu;
this.currentFocusMenu = menu;
this.currentButton = button;
};

this.setFocusedMenu = function(/*Widget*/menu){
this.currentFocusMenu = menu;
};

this.onKey = function(/*Event*/e){
if (!e.key) { return; }
if(!this.currentMenu || !this.currentMenu.isShowingNow){ return; }

var m = this.currentFocusMenu;
while (m){
if(m.processKey(e)){
e.preventDefault();
e.stopPropagation();
break;
}
m = m.parentPopup;
}
},

this.onClick = function(/*Event*/e){
if (!this.currentMenu){ return; }

var scrolloffset = dojo.html.getScroll().offset;

var m = this.currentMenu;

while (m){
if(dojo.html.overElement(m.domNode, e) || dojo.html.isDescendantOf(e.target, m.domNode)){
return;
}
m = m.currentSubpopup;
}

if (this.currentButton && dojo.html.overElement(this.currentButton, e)){
return;
}

this.currentMenu.close();
};
}

dojo.provide("dojo.widget.Editor2Plugin.DropDownList");

dojo.require("dojo.widget.Editor2");
dojo.require("dojo.widget.PopupContainer");
dojo.declare("dojo.widget.Editor2ToolbarDropDownButton", dojo.widget.Editor2ToolbarButton, {

onClick: function(){
if(this._domNode && !this._domNode.disabled && this._parentToolbar.checkAvailability()){
if(!this._dropdown){
this._dropdown = dojo.widget.createWidget("PopupContainer", {});
this._domNode.appendChild(this._dropdown.domNode);
}
if(this._dropdown.isShowingNow){
this._dropdown.close();
}else{
this.onDropDownShown();
this._dropdown.open(this._domNode, null, this._domNode);
}
}
},
destroy: function(){
this.onDropDownDestroy();
if(this._dropdown){
this._dropdown.destroy();
}
dojo.widget.Editor2ToolbarDropDownButton.superclass.destroy.call(this);
},
enableToolbarItem: function(){
this._domNode.disabled = false;
dojo.html.removeClass(this._domNode, 'dojoE2TB_SCFieldDisabled');
},

disableToolbarItem: function(){
this._domNode.disabled = true;
dojo.html.addClass(this._domNode, 'dojoE2TB_SCFieldDisabled');
},
onDropDownShown: function(){},
onDropDownDestroy: function(){}
});

dojo.declare("dojo.widget.Editor2ToolbarComboItem", dojo.widget.Editor2ToolbarDropDownButton,{

href: null,
create: function(node, toolbar){
dojo.widget.Editor2ToolbarComboItem.superclass.create.apply(this, arguments);
if(!this._contentPane){
dojo.require("dojo.widget.ContentPane");
this._contentPane = dojo.widget.createWidget("ContentPane", {preload: 'true'});
this._contentPane.addOnLoad(this, "setup");
this._contentPane.setUrl(this.href);
}
},

onMouseOver: function(e){
if(this._lastState != dojo.widget.Editor2Manager.commandState.Disabled){
dojo.html.addClass(e.currentTarget, this._parentToolbar.ToolbarHighlightedSelectStyle);
}
},
onMouseOut:function(e){
dojo.html.removeClass(e.currentTarget, this._parentToolbar.ToolbarHighlightedSelectStyle);
},

onDropDownShown: function(){
if(!this._dropdown.__addedContentPage){
this._dropdown.addChild(this._contentPane);
this._dropdown.__addedContentPage = true;
}
},

setup: function(){
},

onChange: function(e){
if(this._parentToolbar.checkAvailability()){
var name = e.currentTarget.getAttribute("dropDownItemName");
var curInst = dojo.widget.Editor2Manager.getCurrentInstance();
if(curInst){
var _command = curInst.getCommand(this._name);
if(_command){
_command.execute(name);
}
}
}
this._dropdown.close();
},

onMouseOverItem: function(e){
dojo.html.addClass(e.currentTarget, this._parentToolbar.ToolbarHighlightedSelectItemStyle);
},

onMouseOutItem: function(e){
dojo.html.removeClass(e.currentTarget, this._parentToolbar.ToolbarHighlightedSelectItemStyle);
}
});

dojo.declare("dojo.widget.Editor2ToolbarFormatBlockSelect", dojo.widget.Editor2ToolbarComboItem, {

href: dojo.uri.dojoUri("src/widget/templates/Editor2/EditorToolbar_FormatBlock.html"),

setup: function(){
dojo.widget.Editor2ToolbarFormatBlockSelect.superclass.setup.call(this);

var nodes = this._contentPane.domNode.all || this._contentPane.domNode.getElementsByTagName("*");
this._blockNames = {};
this._blockDisplayNames = {};
for(var x=0; x<nodes.length; x++){
var node = nodes[x];
dojo.html.disableSelection(node);
var name=node.getAttribute("dropDownItemName")
if(name){
this._blockNames[name] = node;
var childrennodes = node.getElementsByTagName(name);
this._blockDisplayNames[name] = childrennodes[childrennodes.length-1].innerHTML;
}
}
for(var name in this._blockNames){
dojo.event.connect(this._blockNames[name], "onclick", this, "onChange");
dojo.event.connect(this._blockNames[name], "onmouseover", this, "onMouseOverItem");
dojo.event.connect(this._blockNames[name], "onmouseout", this, "onMouseOutItem");
}
},

onDropDownDestroy: function(){
if(this._blockNames){
for(var name in this._blockNames){
delete this._blockNames[name];
delete this._blockDisplayNames[name];
}
}
},

refreshState: function(){
dojo.widget.Editor2ToolbarFormatBlockSelect.superclass.refreshState.call(this);
if(this._lastState != dojo.widget.Editor2Manager.commandState.Disabled){
var curInst = dojo.widget.Editor2Manager.getCurrentInstance();
if(curInst){
var _command = curInst.getCommand(this._name);
if(_command){
var format = _command.getValue();
if(format == this._lastSelectedFormat && this._blockDisplayNames){
return this._lastState;
}
this._lastSelectedFormat = format;
var label = this._domNode.getElementsByTagName("label")[0];
var isSet = false;
if(this._blockDisplayNames){
for(var name in this._blockDisplayNames){
if(name == format){
label.innerHTML = 	this._blockDisplayNames[name];
isSet = true;
break;
}
}
if(!isSet){
label.innerHTML = "&nbsp;";
}
}
}
}
}

return this._lastState;
}
});

dojo.declare("dojo.widget.Editor2ToolbarFontSizeSelect", dojo.widget.Editor2ToolbarComboItem,{

href: dojo.uri.dojoUri("src/widget/templates/Editor2/EditorToolbar_FontSize.html"),

setup: function(){
dojo.widget.Editor2ToolbarFormatBlockSelect.superclass.setup.call(this);

var nodes = this._contentPane.domNode.all || this._contentPane.domNode.getElementsByTagName("*");
this._fontsizes = {};
this._fontSizeDisplayNames = {};
for(var x=0; x<nodes.length; x++){
var node = nodes[x];
dojo.html.disableSelection(node);
var name=node.getAttribute("dropDownItemName")
if(name){
this._fontsizes[name] = node;
this._fontSizeDisplayNames[name] = node.getElementsByTagName('font')[0].innerHTML;
}
}
for(var name in this._fontsizes){
dojo.event.connect(this._fontsizes[name], "onclick", this, "onChange");
dojo.event.connect(this._fontsizes[name], "onmouseover", this, "onMouseOverItem");
dojo.event.connect(this._fontsizes[name], "onmouseout", this, "onMouseOutItem");
}
},

onDropDownDestroy: function(){
if(this._fontsizes){
for(var name in this._fontsizes){
delete this._fontsizes[name];
delete this._fontSizeDisplayNames[name];
}
}
},

refreshState: function(){
dojo.widget.Editor2ToolbarFormatBlockSelect.superclass.refreshState.call(this);
if(this._lastState != dojo.widget.Editor2Manager.commandState.Disabled){
var curInst = dojo.widget.Editor2Manager.getCurrentInstance();
if(curInst){
var _command = curInst.getCommand(this._name);
if(_command){
var size = _command.getValue();
if(size == this._lastSelectedSize && this._fontSizeDisplayNames){
return this._lastState;
}
this._lastSelectedSize = size;
var label = this._domNode.getElementsByTagName("label")[0];
var isSet = false;
if(this._fontSizeDisplayNames){
for(var name in this._fontSizeDisplayNames){
if(name == size){
label.innerHTML = 	this._fontSizeDisplayNames[name];
isSet = true;
break;
}
}
if(!isSet){
label.innerHTML = "&nbsp;";
}
}
}
}
}
return this._lastState;
}
});

dojo.declare("dojo.widget.Editor2ToolbarFontNameSelect", dojo.widget.Editor2ToolbarFontSizeSelect, {
href: dojo.uri.dojoUri("src/widget/templates/Editor2/EditorToolbar_FontName.html")
});


dojo.provide("dojo.widget.Editor2Plugin.ColorPicker")

dojo.require("dojo.widget.Editor2Plugin.DropDownList");
dojo.require("dojo.widget.ColorPalette");
dojo.declare("dojo.widget.Editor2ToolbarColorPaletteButton", dojo.widget.Editor2ToolbarDropDownButton, {

onDropDownShown: function(){
if(!this._colorpalette){
this._colorpalette = dojo.widget.createWidget("ColorPalette", {});
this._dropdown.addChild(this._colorpalette);

this.disableSelection(this._dropdown.domNode);
this.disableSelection(this._colorpalette.domNode);

dojo.event.connect(this._colorpalette, "onColorSelect", this, 'setColor');
dojo.event.connect(this._dropdown, "open", this, 'latchToolbarItem');
dojo.event.connect(this._dropdown, "close", this, 'enableToolbarItem');
}
},
enableToolbarItem: function(){
dojo.widget.Editor2ToolbarButton.prototype.enableToolbarItem.call(this);
},

disableToolbarItem: function(){
dojo.widget.Editor2ToolbarButton.prototype.disableToolbarItem.call(this);
},
setColor: function(color){
this._dropdown.close();
var curInst = dojo.widget.Editor2Manager.getCurrentInstance();
if(curInst){
var _command = curInst.getCommand(this._name);
if(_command){
_command.execute(color);
}
}
}
});

