<?php
/**
 * @var \Entity\News[] $List_news_a
 */
?>

<?php if ( !empty( $List_news_a ) ): ?>
	<?php foreach ( $List_news_a as $News ): ?>
		<h2><a href="<?= $News['link'] ?>"><?= $News['titre'] ?></a></h2>
		<p><?= nl2br( $News['contenu'] ) ?></p>
	<?php endforeach; ?>

<?php else: ?>
	<h2>Aucune news pour le moment !</h2>
<?php endif; ?>


