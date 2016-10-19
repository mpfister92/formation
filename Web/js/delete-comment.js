/**
 * Created by mpfister on 18/10/2016.
 */
$( 'body' ).on( 'click', 'a[name=delete]', function() {
	var $this      = $( this );
	var id_comment = $this.attr( "id-comment" );
	
	$.ajax( {
		type     : "POST",
		url      : $( this ).attr( 'delete-url' ),
		data     : {
			id_comment : id_comment
		},
		dataType : 'json',
		success  : function( data ) {
			//si succès, on supprime le commentaire
			if ( true === data.content.success ) {
				var $target_fieldset = $( 'fieldset[id-comment=' + data.content.comment.id + ']' );
				$target_fieldset.fadeOut('slow', function(){
					$(this).remove();
					//si on supprime le dernier commentaire on réaffiche le message
					if ( $( 'fieldset:last' ).length == 0 ) {
						$( '.js-exists-comment' ).html( "Aucun commentaire n'a encore été posté. Soyez le premier à en laisser un !" );
					}
				});
			}
		}
	} );
} );
