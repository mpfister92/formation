<?php
/**
 * @var \Entity\News[] $List_news_a
 */
?>

<p style="text-align: center">Il y a actuellement <?= $number_news ?> news. <br />
	<?php if ( $number_news > 0 ): ?>
		En voici la liste :
	<?php else: ?>
		<a href="<?= $add_news ?>"><strong>Ajoutez</strong></a> une news pour accéder au gestionnaire !
	<?php endif; ?>
</p>

<?php if ( $number_news > 0 ): ?>
	<table>
		<tr>
			<th>Auteur</th>
			<th>Titre</th>
			<th>Date d'ajout</th>
			<th>Dernière modification</th>
			<th>Actions</th>
		</tr>
		<?php foreach ( $News_a as $News ): ?>
			<tr>
				<td><?= $News[ 'Member' ]->login() ?>
				</td>
				<td><?= $News[ 'titre' ] ?>
				</td>
				<td>le <?= $News[ 'dateAjoutFormated' ] ?>
				</td>
				<td><?= ( $News[ 'dateAjout' ] == $News[ 'dateModif' ] ? '-' : 'le ' . $News[ 'dateModifFormated' ] ) ?>
				</td>
				<td>
					<a href="<?= $News[ 'link_edition' ] ?>"><img src="/images/Modifier.png" alt="Modifier" /></a>
					<a href="<?= $News[ 'link_delete' ] ?>"><img src="/images/Supprimer.png" alt="Supprimer" /></a>
				</td>
			</tr>
		<?php endforeach; ?>
	</table>
<?php endif; ?>
