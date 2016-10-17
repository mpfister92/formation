$( document ).ready( function() {
	
	var $form = $( '.js-form-comment-news' );
	
	$form.on( 'submit', function( event ) {
		var $current_form = $( this );
		
		var $champ_a = $current_form.find( '[name=auteur],textarea' );
		var $erreur  = $current_form.find( '.js-error' );
		var $contenu = $current_form.find( 'textarea' );
		var $valid   = $current_form.find( '.js-valid' );
		
		refresh();
		
		var p_data = {
			contenu : $contenu.val()
		};
		
		var $auteur = $current_form.find( '[name=auteur]' );
		
		if ( 1 === $auteur.length ) {
			p_data.auteur = $auteur.val();
		}
		
		$.ajax( {
			type     : "POST",
			url      : $current_form.data( 'ajax-url' ),
			data     : p_data,
			dataType : 'json',
			success  : function( data ) {
				//si l'insertion a échoué, on affiche le message d'erreur correspondant et on indique
				//le champ qui a provoqué l'erreur
				if ( false === data.content.success ) {
					$valid.html( "" );
					$erreur.html( "" );
					$champ_a.css( {
						borderColor : '#eee'
					} );
					
					$current_form.find('[name='+data.content.name+']')
								 .before($("<div class='js-error'></div>").text(data.content.error_message))
						.css({
							borderColor:'red'
						});
					return;
				}
				//si l'insertion a réussi, on insère le commentaire et on affiche un message de validation
				$contenu.css( {
					borderColor : '#eee'
				} );
				$auteur.css( {
					borderColor : '#eee'
				} );
				
				var $last_fieldset = $('fieldset:last');
				var $coments_a = $('.js-comment-list');
				
				if($last_fieldset.length) {
					$last_fieldset.after( comment_buildCommentHTMLRendering( data.content.comment ) );
				}
				else{
					$coments_a.append(comment_buildCommentHTMLRendering(data.content.comment));
					$('.js-exists-comment').html("");
				}
				
				$('fieldset:last').css({
					opacity : 0
				});
				$('fieldset:last').fadeTo('slow',1);
				
				$valid.html( data.content.validation_message );
				$erreur.html("");
				$( '[name=contenu]' ).val( "" );
				$( '[name=auteur]' ).val( "" );
				
				$coments_a.data('date-last-update',data.content.new_update_date);
			}
		} );
		return false;
	} );
} );

function comment_buildCommentHTMLRendering( comment ) {
	return $( '<fieldset></fieldset>' ).attr('id',comment.id)
		.append( $( '<legend></legend>' )
			.append( 'Posté par ', $( '<strong></strong>' ).text( comment.auteur ), ' le ' + comment.date + ' ' )
			.append( comment.link_update ? $( '<a></a>' )
				.attr( "href", comment.link_update )
				.text( 'Modifier' ) : '', comment.link_update && comment.link_delete ? ' - ' : '', comment.link_delete ? $( '<a></a>' )
				.attr( "href", comment.link_delete )
				.text( 'Supprimer' ) : '' ), $( '<p></p>' )
			.text( comment.contenu ) );
}


var $submit_field = $('[name=submit]');
$submit_field.on('mouseover',function(clic){
	var $this = $(this);
	$this.jrumble({
		x : 2,
		y : 2,
		rotation : 1,
		speed : 70
	});
	$this.css({
		borderColor : '#237fbe',
		fontWeight : 'bold'
	});
	$this.trigger('startRumble');
});

$submit_field.on('mouseleave',function(clic){
	var $this = $(this);
	$this.css({
		borderColor : '#eee',
		fontWeight : 'normal'
	});
	$this.trigger('stopRumble');
});





