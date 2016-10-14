$( document ).ready( function() {
	
	var $form = $( '.js-form-comment-news' );
	
	$form.on( 'submit', function( event ) {
		var $current_form = $( this );
		
		var $champ_a = $current_form.find( '[name=auteur],textarea' );
		var $erreur  = $current_form.find( '.js-error' );
		var $contenu = $current_form.find( 'textarea' );
		var $valid   = $current_form.find( '.js-valid' );
		
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
				$( 'fieldset:last' ).after( comment_buildCommentHTMLRendering( data.content.comment ) );
				$valid.html( data.content.validation_message );
				$erreur.html("");
				$( '[name=contenu]' ).val( "" );
				$( '[name=auteur]' ).val( "" );
				
			}
		} );
		return false;
	} );
} );

setInterval( function() {
	var last_id_comment = $( 'fieldset:last' ).attr( 'id' );
	console.log(last_id_comment);
	$.ajax( {
		type : "POST",
		url  : $('.js-comment-list').data('url'),
		data : {
			id : last_id_comment
		},
		dataType : 'json',
		success : function(data){
			if(data.content.success === true) {
				$.each(data.content.Comments, function(key,val){
					//console.log(val);
					$( 'fieldset:last' ).after( comment_buildCommentHTMLRendering( val ) );
				});
				
			}
		}
	} );
	
	//
}, 3000 );


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


