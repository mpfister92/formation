<?php
$return['success'] = $success;
$return['error_message'] = $error_message;
if (isset($Comment)) {
	$return["comment"]["contenu"] = $Comment['contenu'];
	$return["comment"]["date"] = $Comment['date'];
	$return["comment"]["auteur"] = $Comment['auteur'];
	$return["comment"]["fk_NMC"] = $Comment['fk_NMC'];
	$return["comment"]["fk_NNC"] = $Comment['fk_NNC'];
}
echo json_encode($return);