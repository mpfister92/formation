function refresh(){
	var $comment_list = $('.js-comment-list');
	var date = $comment_list.data('date-last-update');
	$.ajax( {
		type : "POST",
		url  : $comment_list.data('url'),
		data : {
			date : date
		},
		dataType : 'json',
		success : function(data){
			if(data.content.success === true) {
				$comment_list.data('date-last-update',data.content.new_update_date);
				$.each(data.content.Comments, function(key,val){
					//si un id de commentaire existe deja (modification) on replace le commentaire
					var $fieldset_containing_id = $('fieldset[id-comment='+key+']');
					if($fieldset_containing_id.length){
						var to_replace = comment_buildCommentHTMLRendering( val );
						$fieldset_containing_id.replaceWith(to_replace);
					}
					//sinon on rajoute le commentaire en fin de page
					else {
						var $last_fieldset = $( 'fieldset:last' ) ;
						if ( $last_fieldset.length ) {
							$last_fieldset.after( comment_buildCommentHTMLRendering( val ) );
						}
						else {
							$comment_list.append( comment_buildCommentHTMLRendering( val ) );
							$( '.js-exists-comment' ).html( "" );
						}
						$last_fieldset.css({
							opacity : 0
						});
						$last_fieldset.fadeTo('slow',1);
					}
				});
			}
		}
	} );
}

setInterval( function() {
	refresh();
},20000 );