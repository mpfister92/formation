/**
 * Created by mpfister on 18/10/2016.
 */
$('body').on( 'click','a[name=delete]', function() {
	var $this = $(this);
	var id_comment = $this.attr("id-comment");
	
	$.ajax( {
		type     : "POST",
		url      : $(this).attr( 'delete-url' ),
		data     : {
			id_comment : id_comment
		},
		dataType : 'json',
		success  : function( data ) {
			if ( true === data.content.success ) {
				$( 'fieldset[id-comment=' + data.content.comment.id + ']' ).remove();
			}
		}
	});
});
