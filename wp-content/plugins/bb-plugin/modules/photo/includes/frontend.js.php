jQuery(function($) {
	<?php if ( 'lightbox' == $settings->link_type ) : ?>
	if (typeof $.fn.magnificPopup !== 'undefined') {
		$('.fl-node-<?php echo $id; ?> a').magnificPopup({
			type: 'image',
			closeOnContentClick: true,
			closeBtnInside: false,
			tLoading: '',
			preloader: true,
			callbacks: {
				open: function() {
					$('.mfp-preloader').html('<i class="fas fa-spinner fa-spin fa-3x fa-fw"></i>');
				}
			  }
		});
	}
	<?php endif; ?>

	$(function() {
		$( '.fl-node-<?php echo $id; ?> .fl-photo-img' )
			.on( 'mouseenter', function( e ) {
				$( this ).data( 'title', $( this ).attr( 'title' ) ).removeAttr( 'title' );
			} )
			.on( 'mouseleave', function( e ){
				$( this ).attr( 'title', $( this ).data( 'title' ) ).data( 'title', null );
			} );
	});
});
