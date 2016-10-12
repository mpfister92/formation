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


<?php if ( empty( $List_comments_a ) ): ?>
	<p>Aucun commentaire n'a encore été posté. Soyez le premier à en laisser un !</p>
<?php endif; ?>

<?php foreach ( $List_comments_a as $Comment ): ?>
	<fieldset>
		<legend>
			Posté par
			<strong>
				<?= $Comment[ 'comment_author' ] ?>
			</strong>
			le <?= $Comment[ 'date_formated' ] ?>
			<?php if ( isset( $Comment[ 'link_update' ] ) && ( isset( $Comment[ 'link_delete' ] ) ) ): ?>
				<a href="<?= $Comment[ 'link_update' ] ?>">Modifier</a> -
				<a href="<?= $Comment[ 'link_delete' ] ?>">Supprimer</a>
			<?php endif; ?>
		</legend>
		<p><?= nl2br( htmlspecialchars( $Comment[ 'contenu' ] ) ) ?></p>
	</fieldset>
<?php endforeach; ?>

<h3>Postez votre commentaire</h3>
<form id="form" action="" class="js-from-comment-news">
	
	<div class="js-error"></div>
	<p>
		<?= $add_comment_form ?>
		
		<input type="submit" id="envoi" value="Commenter" />
	</p>
</form>


