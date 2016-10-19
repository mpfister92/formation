<?php if ( !empty( $News_a ) ): ?>
	<h3>Voici le résumé de <?= $login_member ?> </h3>
	<h4 style="text-align : right">
		<small>A ecrit <?= $number_news ?> news et <?= $number_comments ?> commentaire(s)</small>
	</h4>
	<?php foreach ( $News_a as $id_news => $News ): ?>
		<fieldset style="border-width: 5px; margin-bottom: 5px">
			<p>Par
				<em>
					<?php if ( $News[ 'auteur_news' ] != $login_member ): ?>
						<a href="<?= $News[ 'link_summary' ] ?>"><?= $News[ 'auteur_news' ] ?></a>
					<?php else: ?>
						<?= $News[ 'auteur_news' ] ?>
					<?php endif; ?>
				</em>, le <?= $News[ 'dateAjout' ] ?></p>
			<?php if ( $News[ 'dateAjout' ] != $News[ 'dateModif' ] ) { ?>
				<p style="text-align: right;">
					<small><em>Modifiée le <?= $News[ 'dateModif' ] ?></em></small>
				</p>
			<?php } ?>
			<h2><a href="<?= $News[ 'link_news' ] ?>"><?= $News[ 'titre' ] ?></a></h2>
			<p><strong><?= nl2br( $News[ 'contenu' ] ) ?></strong></p>
			<?php foreach ( $News[ 'comments' ] as $Comment ): ?>
				<fieldset style="border-width : 1px; margin-bottom: 5px; margin-right: 5px; margin-left: 5px ">
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