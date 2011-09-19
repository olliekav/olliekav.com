var Kimili = window.Kimili || {};

(Kimili.Flash = function() {
	
	return {
	
		embed : function() {
			
			if (typeof this.configUrl !== 'string' || typeof tb_show !== 'function') {
				return;
			}
			
			var url = this.configUrl + ((this.configUrl.match(/\?/)) ? "&" : "?") + "TB_iframe=true";
			
			tb_show('Kimili Flash Embed', url , false);
		}
		
	};
	
}());

/*
	Generator specific script
*/

(Kimili.Flash.Generator = function(){
	
	var toggleSection = function(toggle, $toggleable) {
		console.log($toggleable.css('display'));
		if ($toggleable.css('display') === "" || $toggleable.css('display') === "block") {
			$toggleable.css('display', 'none');
			toggle.firstChild.nodeValue = "+";
		}
		else {
			$toggleable.css('display', 'block');
			toggle.firstChild.nodeValue = "-";
		}
	};
	
	var toggleAttsParamsContainer = function() {
		var $tb = jQuery("#toggleAttsParams");
		var $tc = jQuery("#toggleAttsParamsContainer");
		if ($tc.css('display') === "" || $tc.css('display') === "none") {
			$tc.css('display','block');
			$tb.get(0).firstChild.nodeValue = Kimili.Flash.Generator.i18n.less;
		}
		else {
			$tc.css('display','none');
			$tb.get(0).firstChild.nodeValue = Kimili.Flash.Generator.i18n.more;
		}
	};
	
	var buildTag = function() {
		
		var $generator = jQuery('#KFE_Generator'),
			tag = '[kml_flashembed',
			fversion = "",
			width = "",
			height = "";
		
		if ($generator.length === 0) {
			return "";
		}
		
		// Get the basic attributes.
		$generator.find('input[type=text],input:checked,select').each(function(){
			
			var $this = jQuery(this);
			
			switch(this.name) {
				case "major":
				case "minor":
					fversion += (this.value + ".");
					break;
					
				case "release":
					fversion += this.value;
					tag += ' fversion="' + fversion + '"';
					break;
					
				case "width":
					width = this.value;
					break;
				
				case "height":
					height = this.value;
					break;

				case "unit":
					tag += ' width="' + width + ((this.value === 'percentage') ? '%' : '') + '"';
					tag += ' height="' + height + ((this.value === 'percentage') ? '%' : '') + '"';
					break;
					
				default:
					if ($this.val() !== "") {
						tag += ' ' + $this.attr('name') + '="' + $this.val() + '"';
					}
			}
		});
		
		// Parse out the fvars
		$generator.find('textarea#fvars').each(function(){
			var $this = jQuery(this);
			if ($this.attr('value') !== "") {
				tag += ' ' + $this.attr('name') + '="' + $this.attr('value') + '"';
			}
		});
		
		// Parse out the Alternative Content
		$generator.find('textarea#alternativeContent').each(function(){
			var $this = jQuery(this);
			if ($this.attr('value') !== "") {
				tag += ']\n' + $this.attr('value') + '\n[/kml_flashembed]';
			} else {
				tag += '/]';
			}
		});
		
		return tag;
		
	};
	
	var insertTag = function() {
		
		var tag = buildTag() || "";
		var win = window.parent || window;
				
		if ( typeof win.tinyMCE !== 'undefined' && ( win.ed = win.tinyMCE.activeEditor ) && !win.ed.isHidden() ) {
			win.ed.focus();
			if (win.tinymce.isIE)
				win.ed.selection.moveToBookmark(win.tinymce.EditorManager.activeEditor.windowManager.bookmark);

			win.ed.execCommand('mceInsertContent', false, tag);
		} else {
			win.edInsertContent(win.edCanvas, tag);
		}
		
		// Close Lightbox
		win.tb_remove();
		
	};
	
	return {
		
		initialize : function() {
			
			if (typeof jQuery === 'undefined') {
				return;
			}
			
			jQuery("#publishingMethod").change(function(){
				var pm = this.selectedIndex,
					wrap = jQuery('#toggleReplaceId'),
					el = jQuery('#replaceId');
				wrap.css('display', (pm ? "block" : "none"));
				el.attr('value', (pm ? el.attr('value') : ""));
			}).trigger('change');
			
			jQuery("#togglePublishingMethodHelp").click(function(e) {
				e.preventDefault();
				var el = jQuery("#publishingMethodHelp");
				el.css('display', (el.css('display') === "block" ? "none" : "block"));
			});
			
			jQuery("#toggleAlternativeContentHelp").click(function(e) {
				e.preventDefault();
				var el = jQuery("#alternativeContentHelp");
				el.css('display', (el.css('display') === "block" ? "none" : "block"));
			});
			jQuery("#toggleSWFObjectReference").click(function(e) {
				e.preventDefault();
				var el = jQuery("#SWFObjectReference");
				el.css('display', (el.css('display') === "block" ? "none" : "block"));	
			});
			jQuery("#toggleAutohideHelp").click(function(e) {
				e.preventDefault();
				var el = jQuery("#autohideHelp");
				el.css('display', (el.css('display') === "block" ? "none" : "block"));
			});
			jQuery("#toggleReplaceIdHelp").click(function(e) {
				e.preventDefault();
				var el = jQuery("#replaceIdHelp");
				el.css('display', (el.css('display') === "block" ? "none" : "block"));
			});
			jQuery("#toggle1").click(function(e) {
				e.preventDefault();
				toggleSection(this, jQuery("#toggleable1"));
			});
			jQuery("#toggle2").click(function(e) {
				e.preventDefault();
				toggleSection(this, jQuery("#toggleable2"));
			});
			jQuery("#toggle3").click(function(e) {
				e.preventDefault();
				toggleSection(this, jQuery("#toggleable3"));
			});
			
			jQuery("#toggleAttsParams").click(function(e) {
				e.preventDefault();
				toggleAttsParamsContainer();
			});
			jQuery("#addFlashvar").onclick = function() {
				flashvarsTotal++;
				addFlashvar();
				return false;
			};
			jQuery("#generate").click(function(e) {
				e.preventDefault();
				insertTag();
			});
			jQuery("#clear").click(function(e) {
				e.preventDefault();
				var win = window.parent || window;
				win.tb_remove();
			});
			
		}
		
	};
	
}());