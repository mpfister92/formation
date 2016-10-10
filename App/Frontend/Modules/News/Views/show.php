<p>Par <em><?= $news_author ?></em>, le <?= $news[ 'dateAjout' ]->format( 'd/m/Y à H\hi' ) ?></p>
<h2><?= $news[ 'titre' ] ?></h2>
<p style="display:bloc;"><?= nl2br( $news[ 'contenu' ] ) ?></p>

<?php if ( $news[ 'dateAjout' ] != $news[ 'dateModif' ] ) { ?>
	<p style="text-align: right;">
		<small><em>Modifiée le <?= $news[ 'dateModif' ]->format( 'd/m/Y à H\hi' ) ?></em></small>
	</p>
<?php } ?>


<p><a href="commenter-<?= $news[ 'id' ] ?>.html">Ajouter un commentaire</a></p>


<?php
if ( empty( $comments ) ) {
	?>
	<p>Aucun commentaire n'a encore été posté. Soyez le premier à en laisser un !</p>
	<?php
}


foreach ( $comments as $comment ) {
	?>
	<fieldset>
		<legend>
			Posté par
			<strong>
				<?php if ( $comment[ 'auteur' ] != null ): ?>
					<?= htmlspecialchars( $comment[ 'auteur' ] ) ?>
				<?php else: ?>
					<?= htmlspecialchars($manager->getLoginMemberFromId($comment['member'])) ?>
				<?php endif; ?>
			</strong>
			le <?= $comment[ 'date' ]->format( 'd/m/Y à H\hi' ) ?>
			<?php if ( $user->isAuthenticated() ): ?>
				<?php if ( $user->getStatus() == 'admin' ): ?>
					<a href="admin/comment-update-<?= $comment[ 'id' ] ?>.html">Modifier</a> |
					<a href="admin/comment-delete-<?= $comment[ 'id' ] ?>.html">Supprimer</a>
				<?php elseif ( $comment[ 'auteur' ] != 'admin' && ( $user->getLogin() == $comment[ 'auteur' ] || $user->getLogin() == $news_author || $user->getStatus() == 'admin' ) ): ?>
					<a href="admin/comment-update-<?= $comment[ 'id' ] ?>.html">Modifier</a> |
					<a href="admin/comment-delete-<?= $comment[ 'id' ] ?>.html">Supprimer</a>
				<?php endif; ?>
			<?php endif; ?>
		</legend>
		<p><?= nl2br( htmlspecialchars( $comment[ 'contenu' ] ) ) ?></p>
	</fieldset>
	<?php
}
?>

<p><a href="commenter-<?= $news[ 'id' ] ?>.html">Ajouter un commentaire</a></p>
