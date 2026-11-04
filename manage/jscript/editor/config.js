CKEDITOR.editorConfig = function( config )
{
	config.resize_enabled = false;
	config.fontSize_defaultLabel = '12';
	config.contentsCss = 'jscript/editor/contents.css';
	config.resize_minWidth = '800';
	config.height = '150';
	config.language = 'ja';
//	config.uiColor = '#EEE';
	config.enterMode = CKEDITOR.ENTER_BR;
	config.shiftEnterMode = CKEDITOR.ENTER_P;
	config.removePlugins = 'elementspath';

	config.toolbar_Full =
 	[
		['FontSize','TextColor'],
		['Bold','Italic','Underline'],
		['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
		['Link','Unlink'],
		['Source']
   ];

	config.toolbar_NonLink =
 	[
		['FontSize','TextColor'],
		['Bold','Italic','Underline'],
		['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
		['Source']
   ];

	config.toolbar_OnlyFont =
	[
		['FontSize']
	];
   
	config.toolbar_Smartphone = [			
		[ 'Link' ],
		[ 'FontSize' ],
		[ 'Image' ],
		[ 'Source' ],
		[ 'Bold', 'Italic', 'Underline', 'Strike', '-', 'RemoveFormat' ],
		[ 'Undo', 'Redo' ],
		[ 'TextColor', 'BGColor' ]
	];

	config.font_style =
	{
		styles	: { 'font-size' : '12' },
	overrides	: [ { element : 'font', attributes : { 'size' : 12 } } ]

	};
};
