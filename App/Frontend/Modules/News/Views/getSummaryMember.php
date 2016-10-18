<?php if ( !empty( $News_a ) ): ?>
	<h3>Voici le résumé de <?= $login_member ?> </h3>
	<?php foreach ( $News_a as $id_news => $News ): ?>
		<fieldset>
			<p>Par <em><?= $News[ 'auteur_news' ] ?></em>, le <?= $News[ 'dateAjout' ] ?></p>
			<h2><?= $News[ 'titre' ] ?></h2>
			<p><strong><?= nl2br( $News[ 'contenu' ] ) ?></strong></p>
			<?php foreach ( $News[ 'comments' ] as $Comment ): ?>
				<fieldset>
					<legend>
						le <?= $Comment[ 'date_comment' ] ?>
						<?= $Comment[ 'date_comment' ] == $Comment[ 'date_last_update' ] ? '' : ' dernière édition le ' . $Comment[ 'date_last_update' ] ?>
					</legend>
					<p class="contenu"><?= nl2br( htmlspecialchars( $Comment[ 'contenu_comment' ] ) ) ?></p>
				</fieldset>
			<?php endforeach; ?>
		</fieldset>
	<?php endforeach; ?>
<?php else: ?>
	<h3>Le résumé de <?= $login_member ?> est vide !</h3>
<?php endif; ?>