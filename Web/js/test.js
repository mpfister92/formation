$(document).ready(function(){
	var $contenu = $('#contenu');
	
	var $form = $('.js-from-comment-news');
	var $champ_a = $form.find('input,textarea');
	var $erreur = $form.find('.js-error');
	
	//if($('text#pseudo')){
	//	var $auteur = $('#pseudo');
	//}
	
		
	$form.on('submit',function(event){
		console.log('Kikou');
		
		
		$.post(
			'/',
			{
				contenu : $contenu.val()
			},
			function(data){
				console.log(data);
			},
			'text'
		);
		
		return false;
	});
	
	/*$contenu.keyup(function(){
		if($(this).val().length < 5){
			$(this).css({
				borderColor:'red',
				color:'red'
			});
		}
		else{
			$(this).css({
				borderColor:'green',
				color:'green'
			});
		}
	});
	
	function verifier(champ){
		if(champ.val() == ""){
			$erreur.css('display','block');
			champ.css({
				borderColor:'red',
				color:'red'
			});
		}
	}*/
});