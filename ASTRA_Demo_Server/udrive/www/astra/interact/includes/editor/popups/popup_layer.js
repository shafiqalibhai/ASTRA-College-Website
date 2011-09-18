
function __dlg_init() {
    window.focus();
	document.body.onkeypress = __dlg_close_on_esc;
    window.dialogArguments = window.parent.dialog_layer._arguments;
};

// closes the dialog and passes the return info upper.
function __dlg_close(val) {
	window.parent.dialog_layer._return(val);
	window.parent.dialog_layer.hide();
};

function __dlg_close_on_esc(ev) {
	ev || (ev = window.event);
	if (ev.keyCode == 27) {
		__dlg_close(null);
		return false;
	}
	return true;
};
