dojo.provide("dojo.widget.Editor2Plugin.bFilters");
dojo.widget.Editor2Plugin.bFilters={
	tidy_tags: function(str) {
		return str.replace(/<(\/?)b( [^>]*)?>/g,"<$1strong$2>").
			replace(/<(\/?)i( [^>]*)?>/g,"<$1em$2>").

			// kill unwanted tags
			replace(/<\?xml:[^>]*>/g, '').       // Word xml
			replace(/<\/?st1:[^>]*>/g,'').     // Word SmartTags
			replace(/<\/?[a-z]\:[^>]*>/g,'').  // All other funny Word non-HTML stuff
			replace(/<(\/?)(h[1-6]+)[^>]*>/gi,'<$1$2>').

			// kill double-tags - except br tags!
replace(/<(b[a-qs-z][a-z]*|[ac-z][a-z]*|br[a-z]+)><\1>/gi,'<$1>').
replace(/<\/(b[a-qs-z][a-z]*|[ac-z][a-z]*|br[a-z]+)><\/\1>/gi,'<\/$1>').

			// nuke double spaces
			replace(/  */gi,' ').
			
			// nuke weird high-ascii crud
			replace(/\x96|\x99/g,"").

			//decode-script, object, embed:
			replace(/<img[^>]*XXEDITOR_SCRIPTXX([^\"]*)XXEDITOR_SCRIPTXX\"[^>]*\>/gi,
				function(NN,scri) {return unescape(scri);});
	},

	untidy_tags: function(str) {
		//encode-script: throw in a unique marker char for end script, then replace
		return str.replace(/<\/script>/gi,String.fromCharCode(0xed)).
			replace(/<script([^\xed]*?)\xed/gi,function(NN,scri) {			
				return '<img src="'+dojo.uri.dojoUri('../editor/images/')+			'EDITOR_SCRIPT_placeholder.gif?XXEDITOR_SCRIPTXX'+escape('<script'+scri+'</script>')+'XXEDITOR_SCRIPTXX">';
			}).

		// object or embed placeholder
		replace(/<\/object>/gi,String.fromCharCode(0xed)).
		replace(/<object([^\xed]*?)\xed/gi,function(NN,scri) {			
			return '<img src="'+dojo.uri.dojoUri('../editor/images/')+			'embedPlaceholder.gif?XXEDITOR_SCRIPTXX'+escape('<object'+scri+'</object>')+'XXEDITOR_SCRIPTXX">';
		}).
		replace(/<embed([^>]*?[^\/])\/?>(<\/embed>)?/gi,function(NN,scri) {			
			return '<img src="'+dojo.uri.dojoUri('../editor/images/')+			'embedPlaceholder.gif?XXEDITOR_SCRIPTXX'+escape('<embed'+scri+'/>')+'XXEDITOR_SCRIPTXX">';
		}). 
			
		replace(/<(\/?)strong( [^>]*)?>/g,"<$1b$2>").
			replace(/<(\/?)em( [^>]*)?>/g,"<$1i$2>");
	}
}