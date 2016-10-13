$( document ).ready( function() {
	
	var $form = $( '.js-form-comment-news' );
	
	$form.on( 'submit', function( event ) {
		var $current_form = $( this );
		
		var $champ_a = $current_form.find( '[name=auteur],textarea' );
		var $erreur  = $current_form.find( '.js-error' );
		var $contenu = $current_form.find( 'textarea' );
		var $valid = $current_form.find( '.js-valid' );
		
		//event.preventDefault();
		
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
				if ( false === data.content.success ) {
					$erreur.html( "" );
					$erreur.prepend( data.error_message );
					$champ_a.css({
						borderColor:'#eee'
					});
					if ( 2 === data.error_code ) {
						$contenu.css( {
							borderColor : 'red'
						} )
					}
					if ( 1 === data.error_code ) {
						$contenu.css( {
							borderColor : 'red'
						} );
						$auteur.css( {
							borderColor : 'red'
						} )
					}
					if ( 3 === data.error_code ) {
						$auteur.css( {
							borderColor : 'red'
						} )
					}
					if ( 4 === data.error_code ) {
						$auteur.css( {
							borderColor : 'red'
						} )
					}
					return;
				}
				
				$contenu.css( {
					borderColor : '#eee'
				} );
				$auteur.css( {
					borderColor : '#eee'
				} );
				$( 'fieldset:last' ).after( comment_buildCommentHTMLRendering( data.content.comment ) );
				$erreur.html(data.validation_message);
				$( '[name=contenu]' ).val( "" );
				$( '[name=auteur]' ).val( "" );
				
			}
		} );
		return false;
	} );
} );

function comment_buildCommentHTMLRendering( comment ) {
	return $( '<fieldset></fieldset>' )
		.append( $( '<legend></legend>' )
			.append( 'Posté par ', $( '<strong></strong>' ).text( comment.auteur ), ' le ' + comment.date + ' ' )
			.append( comment.link_update ? $( '<a></a>' )
				.attr( "href", comment.link_update )
				.text( 'Modifier' ) : '', comment.link_update && comment.link_delete ? ' - ' : '', comment.link_delete ? $( '<a></a>' )
				.attr( "href", comment.link_delete )
				.text( 'Supprimer' ) : '' ), $( '<p></p>' )
			.text( comment.contenu ) );
}