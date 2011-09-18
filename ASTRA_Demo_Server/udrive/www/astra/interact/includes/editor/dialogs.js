// htmlArea v3.0 - Copyright (c) 2003-2004 interactivetools.com, inc.
// This copyright notice MUST stay intact for use (see license.txt).
//
// Portions (c) dynarch.com, 2003-2004
//
// A free WYSIWYG editor replacement for <textarea> fields.
// For full source code and docs, visit http://www.interactivetools.com/
//
// Version 3.0 developed by Mihai Bazon.
//   http://dynarch.com/mishoo
//

// $Id: dialogs.js,v 1.1 2006/12/15 00:51:54 websterb4 Exp $


// Though "Dialog" looks like an object, it isn't really an object.  Instead
// it's just namespace for protecting global symbols.

function Dialog(url, action, init) {
	if (typeof init == "undefined") {
		init = window;	// pass this window object by default
	}
	Dialog._geckoOpenModal(url, action, init);
};

Dialog._parentEvent = function(ev) {
	if (Dialog._modal && !Dialog._modal.closed) {
		Dialog._modal.focus();
		HTMLArea._stopEvent(ev);
	}
};

// should be a function, the return handler of the currently opened dialog.
Dialog._return = null;

// constant, the currently opened dialog
Dialog._modal = null;

// the dialog will read it's args from this variable
Dialog._arguments = null;

Dialog._geckoOpenModal = function(url, action, init) {

// kludge to prevent window resizing ugliness
    var file = url.substring(url.lastIndexOf('/') + 1, url.lastIndexOf('.'));
    var x,y,scrollme;
    scrollme="no";
    switch(file) {
        case "insert_audio": x = 500; y = 466; break;
        case "insert_image": x = 600; y = 466; break;
        case "insert_video": x = 674; y = 466; break;
        case "insert_interactive": x = 700; y = 544; break;
        case "insert_smiley": x = 160; y = 220; break;
        case "select_character": x = 280; y = 275; break;
        case "select_color": x = 238; y = 195; break;
        case "insert_table": x = 440; y = 268; break;
        case "link":     x = 660; y = 436; break;
        case "insert_tag":     x = 400; y = 400; scrollme="yes"; break;
        default: x = 50; y = 50;
    }

    var lx = (screen.width - x) / 2;
    var tx = (screen.height - y) / 2;
    var dlg = window.open(url, "hadialog", "toolbar=no,menubar=no,personalbar=no, width="+ x +",height="+ y +",scrollbars="+ scrollme +",resizable=no, left="+ lx +", top="+ tx +"");
	Dialog._modal = dlg;
	Dialog._arguments = init;

	// capture some window's events
	function capwin(w) {
		HTMLArea._addEvent(w, "click", Dialog._parentEvent);
		HTMLArea._addEvent(w, "mousedown", Dialog._parentEvent);
		HTMLArea._addEvent(w, "focus", Dialog._parentEvent);
	};
	// release the captured events
	function relwin(w) {
		HTMLArea._removeEvent(w, "click", Dialog._parentEvent);
		HTMLArea._removeEvent(w, "mousedown", Dialog._parentEvent);
		HTMLArea._removeEvent(w, "focus", Dialog._parentEvent);
	};
	capwin(window);
	// capture other frames
    if(document.all) {
		for (var i = 0; i < window.frames.length; capwin(window.frames[i++]));
	}
	
	// make up a function to be called when the Dialog ends.
	Dialog._return = function (val) {
		if (val && action) {
			action(val);
		}
		relwin(window);
		// capture other frames
	    if(document.all) {
			for (var i = 0; i < window.frames.length; relwin(window.frames[i++]));
		}
		Dialog._modal = null;
	};
};
