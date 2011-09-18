//We're using dialog code from HTMLArea.  To make that work, we need these bits - copied from htmlarea.js instead of including the whole 80KB of mess!!

HTMLArea=this;

agt = navigator.userAgent.toLowerCase();
HTMLArea.is_WinIE = ((HTMLArea.agt.indexOf("msie") != -1) && (HTMLArea.agt.indexOf("opera") == -1) && (agt.indexOf("mac") == -1));
HTMLArea.is_gecko  = (navigator.product == "Gecko");

HTMLArea._addEvent = function(el, evname, func) {
	if (HTMLArea.is_WinIE) {
		el.attachEvent("on" + evname, func);
	} else {
		if (HTMLArea.is_gecko) {
			el.addEventListener(evname, func, true);
		}
	}
};

/* HTMLArea._addEvents = function(el, evs, func) { */
/* 	for (var i in evs) { */
/* 		HTMLArea._addEvent(el, evs[i], func); */
/* 	} */
/* }; */

HTMLArea._removeEvent = function(el, evname, func) {
	if (HTMLArea.is_WinIE) {
		el.detachEvent("on" + evname, func);
	} else {
		if (HTMLArea.is_gecko) {
			el.removeEventListener(evname, func, true);
		}
	}
};

/* HTMLArea._removeEvents = function(el, evs, func) { */
/* 	for (var i in evs) { */
/* 		HTMLArea._removeEvent(el, evs[i], func); */
/* 	} */
/* }; */

HTMLArea._stopEvent = function(ev) {
	if (HTMLArea.is_WinIE) {
		ev.cancelBubble = true;
		ev.returnValue = false;
	} else {
		if (HTMLArea.is_gecko) {
			ev.preventDefault();
			ev.stopPropagation();
		}
	}
};
function __dlg_close(val) {alert('ok');
	opener.Dialog._return(val);
	window.close();
};