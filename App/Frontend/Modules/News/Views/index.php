<?php
if ( !empty( $listeNews ) ) {
	foreach ( $listeNews as $news ) {
		?>
		<h2><a href="news-<?= $news['id'] ?>.html"><?= $news['titre'] ?></a></h2>
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


