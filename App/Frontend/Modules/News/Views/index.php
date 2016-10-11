<?php if ( !empty( $links ) ): ?>
	<?php foreach ( $links as $titre_and_contenu => $link ): ?>
		<?php $string_array = explode('|',$titre_and_contenu) ?>
		<h2><a href="<?= $link ?>"><?= $string_array[0] ?></a></h2>
		<p><?= nl2br( $string_array[1] ) ?></p>
	<?php endforeach; ?>

<?php else: ?>
	<h2>Aucune news pour le moment !</h2>
<?php endif; ?>


