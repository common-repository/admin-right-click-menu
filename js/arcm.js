var arcm_sel = typeof arcm_selector !== 'undefined' ? arcm_selector : 'body';
var $ = jQuery;
$(window).load( function() {
	// move admin to body
    $( '#wpadminbar' ).appendTo( $( 'body' ) );
} );

$( arcm_sel ).bind("contextmenu", function(e) {
	$("#wpadminbar")
	.stop(true, true)
	.toggle(150)
	.css({
		left: e.pageX + "px",
		top:  e.pageY + "px"
	});
	e.preventDefault();
});

$(document).bind("mousedown", function(e) {
	if (!$(e.target).parents("#wpadminbar").length > 0) {	
		$("#wpadminbar").hide(150);
	}
});