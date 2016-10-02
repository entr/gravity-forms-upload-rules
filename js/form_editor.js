(function($,window,document,undefined) {
	
	function gforms_uprules_dims_ruletype_change( type ) {
		
		$('#field_uprules_dims_ruletype').val( type );
		switch( type ) {
			case 'exact':
				$('.uprules_dims_fields_exact').show();
				$('.uprules_dims_fields_conditional').hide();
				break;
			case 'conditional':
				$('.uprules_dims_fields_conditional').show();
				$('.uprules_dims_fields_exact').hide();
				break;
			default:
				$('.uprules_dims_fields_exact, .uprules_dims_fields_conditional').hide();
		}
	}

	$(document).bind('gform_load_field_settings', function(e,field,form) {
		$('#field_uprules_filesize').val(field.uprules_filesize_limit);
		$('#field_uprules_filesize_dim').val(field.uprules_filesize_dim);
		
		$('#field_uprules_dims_exact_width').val( field.uprules_dims_exact_width );
		$('#field_uprules_dims_exact_height').val( field.uprules_dims_exact_height );
		$('#field_uprules_dims_minwidth').val( field.uprules_dims_minwidth );
		$('#field_uprules_dims_minheight').val( field.uprules_dims_minheight );
		$('#field_uprules_dims_maxwidth').val( field.uprules_dims_maxwidth );
		$('#field_uprules_dims_maxheight').val( field.uprules_dims_maxheight );
		
		gforms_uprules_dims_ruletype_change( field.uprules_dims_ruletype );
	});
	
	$(function(){
		
		$('#field_uprules_dims_ruletype').change( function() { gforms_uprules_dims_ruletype_change( $(this).val() ); } );
	});
	
	window.fieldSettings.fileupload += ', .uprules_filesize_setting, .uprules_dimensions_setting';
	window.fieldSettings.post_image += ', .uprules_filesize_setting, .uprules_dimensions_setting';
	
	if ( $.isFunction( window['SetDefaultValues_fileupload'] ) ) {
		window['__SetDefaultValues_fileupload'] = window['SetDefaultValues_fileupload'];
	}
		
	window['SetDefaultValues_fileupload'] = function(field) {
		if ( $.isFunction( window['__SetDefaultValues_fileupload'] ) )
			field = window['__SetDefaultValues_fileupload'](field);
			
		field = $.extend( field, {
			uprules_filesize_dim	:	'kb'
		} );
		return field;
	};
	
	if ( $.isFunction( window['SetDefaultValues_post_image'] ) ) {
		window['__SetDefaultValues_post_image'] = window['SetDefaultValues_post_image'];
	}
		
	window['SetDefaultValues_post_image'] = function(field) {
		if ( $.isFunction( window['__SetDefaultValues_post_image'] ) )
			field = window['__SetDefaultValues_post_image'](field);
			
		field = $.extend( field, {
			uprules_filesize_dim	:	'kb'
		} );
		
		return field;
	};
	
})(jQuery,window,document);