<?php
/**
 * @var \Entity\Comment[] $List_comments_a
 */
?>

<p>Par <em><?= $news_author ?></em>, le <?= $News[ 'dateAjout' ]->format( 'd/m/Y à H\hi' ) ?></p>
<h2><?= $News[ 'titre' ] ?></h2>
<p><?= nl2br( $News[ 'contenu' ] ) ?></p>

<?php if ( $News[ 'dateAjout' ] != $News[ 'dateModif' ] ) { ?>
	<p style="text-align: right;">
		<small><em>Modifiée le <?= $News[ 'dateModif' ]->format( 'd/m/Y à H\hi' ) ?></em></small>
	</p>
<?php } ?>


<p><a href="<?= $add_comment ?>">Ajouter un commentaire</a></p>


<?php if ( empty( $List_comments_a ) ): ?>
	<p>Aucun commentaire n'a encore été posté. Soyez le premier à en laisser un !</p>
<?php endif; ?>

<?php foreach ( $List_comments_a as $Comment ): ?>
	<fieldset>
		<legend>
			Posté par
			<strong>
				<?= $Comment['comment_author'] ?>
			</strong>
			le <?= $Comment['date_formated'] ?>
			<a href="<?= $Comment['link_update'] ?>">Modifier</a> -
			<a href="<?= $Comment['link_delete'] ?>">Supprimer</a> -
		</legend>
		<p><?= nl2br( htmlspecialchars( $Comment['contenu'] ) ) ?></p>
	</fieldset>
<?php endforeach; ?>

<p><a href="<?= $add_comment ?>">Ajouter un commentaire</a></p>
