// To make [tt] work, we need to add an exception to the [font] BBC.
sceditor.formats.bbcode.set(
	'font', {
		format: function (element, content) {
			var element = $(element);
			var font;

			// Get the raw font value from the DOM
			if (!element.is('font') || !(font = element.attr('face'))) {
				font = element.css('font-family');
			}

			// Strip all quotes
			font = font.replace(/['"]/g, '');

			// Here is our exception to make [tt] work.
			if (font === 'monospace') {
				return content;
			}

			return '[font=' + font + ']' + content + '[/font]';
		}
	}
);

// This is the format info for [tt] itself.
sceditor.formats.bbcode.set(
	'tt', {
		tags: {
			font: {
				face: 'monospace'
			}
		},
		format: '[tt]{0}[/tt]',
		html: '<font face="monospace">{0}</font>'
	}
);

// The button to insert
sceditor.command.set(
	'tt', {
		state: function (parent, firstBlock) {
			// SCEditor will only load one CSS file, so this the only way to get custom CSS into it.
			this.css('font[face="monospace"] { background-color: rgba(127, 127, 127, 0.25); }');

			if (this.inSourceMode()) {
				return 0;
			}

			let currNode = sceditor.dom.closest(this.currentNode(), 'font');

			if (!currNode) {
				return 0;
			}

			let font = currNode.getAttribute('face');

			if (font === 'monospace') {
				return 1;
			}

			return 0;
		},
		// Called when editor is in WYSIWYG mode.
		exec: function(caller) {
			let currNode = sceditor.dom.closest(this.currentNode(), 'font');

			if (!currNode) {
				this.execCommand('fontname', 'monospace');
			} else {
				let font = currNode.getAttribute('face');

				if (font === 'monospace') {
					this.execCommand('removeFormat');
				} else {
					this.execCommand('fontname', 'monospace');
				}
			}
		},
		// Called when editor is in source mode.
		txtExec: function(caller) {
			this.insert('[tt]', '[/tt]');
		}
	}
);
