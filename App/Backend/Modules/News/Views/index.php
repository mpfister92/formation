<?php use OCFram\Router; ?>

<p style="text-align: center">Il y a actuellement <?= $number_news ?> news. En voici la liste :</p>

<table>
	<tr>
		<th>Auteur</th>
		<th>Titre</th>
		<th>Date d'ajout</th>
		<th>Dernière modification</th>
		<th>Action</th>
	</tr>
	<?php foreach ( $links as $auteur_titre_ajout_modif => $link ): ?>
		<?php $string_array = explode('|',$auteur_titre_ajout_modif) ?>
		<tr><td><?= $string_array[0] ?>
		</td><td><?= $string_array[1] ?>
		</td><td>le <?= $string_array[2] ?>
		</td><td><?= ( $string_array[2] == $string_array[3] ? '-' : 'le ' . $string_array[3] ) ?>
		</td><td><a href="<?= $link ?>"><img src="/images/update.png" alt="Modifier" /></a>
		<a href="<?= $link ?>"><img src="/images/delete.png" alt="Supprimer" /></a></td></tr>
	<?php endforeach; ?>
</table>