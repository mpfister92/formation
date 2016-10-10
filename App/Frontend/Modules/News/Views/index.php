<?php use OCFram\Linking; ?>

<?php
if ( !empty( $listeNews ) ) {
	foreach ( $listeNews as $news ) {
		?>
		<h2><a href="<?= Linking::provideRoute('Frontend','News','show',['id' => $news['id']]) ?>"><?= $news['titre'] ?></a></h2>
		<p><?= nl2br( $news[ 'contenu' ] ) ?></p>
		<?php
	}
}

else {
	?>
	<h2>Aucune news pour le moment !</h2>
	<?php
}
?>


