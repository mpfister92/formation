<?php
/**
 * @var \Entity\Comment[] $List_comments_a
 */
?>

<p>Par <a href="<?= $News['link_auteur'] ?>"><em><?= $news_author ?></em></a>,le <?= $News[ 'dateAjout' ]->format( 'd/m/Y à H\hi' ) ?></p>
<h2><?= $News[ 'titre' ] ?></h2>
<p><?= nl2br( $News[ 'contenu' ] ) ?></p>

<?php if ( $News[ 'dateAjout' ] != $News[ 'dateModif' ] ) { ?>
	<p style="text-align: right;">
		<small><em>Modifiée le <?= $News[ 'dateModif' ]->format( 'd/m/Y à H\hi' ) ?></em></small>
	</p>
<?php } ?>

<h3>Postez votre commentaire</h3>
<form id="form-top" action="insertComment.php" class="js-form-comment-news	" data-ajax-url="<?= $url_response_form ?>">
	
	<div class="js-valid"></div>
	<p>
		<?= $add_comment_form ?>
		
		<input type="submit" id="send_form_top" name="submit" value="Commenter" />
	</p>
</form>

<?php if ( empty( $Comments_a ) ): ?>
	<p class="js-exists-comment">Aucun commentaire n'a encore été posté. Soyez le premier à en laisser un !</p>
<?php endif; ?>

<div class="js-comment-list" data-url="<?= $for_refresh_url ?>" data-date-last-update="<?= $last_update_date ?>">
	<?php foreach ( $Comments_a as $Comment ): ?>
		<fieldset class="comment" id-comment="<?= $Comment[ 'id' ] ?>">
			<legend>
				Posté par
				<strong>
					<?php if (isset($Comment['link_summary'])): ?>
						<a href="<?= $Comment['link_summary'] ?>"><?= $Comment[ 'auteur' ] ?></a>
					<?php else: ?>
						<strong><?= $Comment[ 'auteur' ] ?></strong>
					<?php endif; ?>
				</strong>
				le <?= $Comment[ 'date_formated' ] ?>
				<?php if ( isset( $Comment[ 'link_update' ] ) && ( isset( $Comment[ 'link_delete' ] ) ) ): ?>
					<a class="js-update-url" href="<?= $Comment[ 'link_update' ] ?>" >Modifier</a> -
					<a name="delete" id-comment="<?= $Comment['id'] ?>" delete-url="<?= $for_delete_comment_url ?>">Supprimer</a>
				<?php endif; ?>
			</legend>
			<p class="contenu"><?= nl2br( htmlspecialchars( $Comment[ 'contenu' ] ) ) ?></p>
		</fieldset>
	<?php endforeach; ?>
</div>

<h3>Postez votre commentaire</h3>
<form id="form-bot" action="insertComment.php" class="js-form-comment-news" data-ajax-url="<?= $url_response_form ?>">
	
	<div class="js-valid"></div>
	<p>
		<?= $add_comment_form ?>
		
		<input type="submit" id="send_form_bot" name="submit" value="Commenter" />
	</p>
</form>


