<p>Par <em><?= $news_author ?></em>, le <?= $news[ 'dateAjout' ]->format( 'd/m/Y à H\hi' ) ?></p>
<h2><?= $news[ 'titre' ] ?></h2>
<p><?= nl2br( $news[ 'contenu' ] ) ?></p>

<?php if ( $news[ 'dateAjout' ] != $news[ 'dateModif' ] ) { ?>
	<p style="text-align: right;">
		<small><em>Modifiée le <?= $news[ 'dateModif' ]->format( 'd/m/Y à H\hi' ) ?></em></small>
	</p>
<?php } ?>


<p><a href="<?= $add_comment ?>">Ajouter un commentaire</a></p>


<?php if ( empty( $comments ) ): ?>
	<p>Aucun commentaire n'a encore été posté. Soyez le premier à en laisser un !</p>
<?php endif; ?>

<?php foreach ( $links as $author_date_content => $link ): ?>
	<?php $string_array = explode( '|', $author_date_content ) ?>
	<fieldset>
		<legend>
			Posté par
			<strong>
				<?= $string_array[ 0 ] ?>
			</strong>
			le <?= $string_array[ 1 ] ?>
			<?php foreach ( $link as $action => $item ): ?>
				<?php if ( $item != null ): ?>
					<a href="<?= $item ?>"><?= $action ?></a>
				<?php endif; ?>
			<?php endforeach; ?>
		</legend>
		<p><?= nl2br( htmlspecialchars( $string_array[ 2 ] ) ) ?></p>
	</fieldset>
<?php endforeach; ?>

<p><a href="<?= $add_comment ?>">Ajouter un commentaire</a></p>
