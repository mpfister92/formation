<?php
$return['success'] = $success;
if (isset($error_message)) {
	$return[ 'error_message' ] = $error_message;
}
if (isset($error_code)) {
	$return[ 'error_code' ] = $error_code;
}
if (isset($validation_message)) {
	$return[ 'validation_message' ] = $validation_message;
}
if (isset($Comment)) {
	$return["comment"]["contenu"] = $Comment['contenu'];
	$return["comment"]["date"] = $Comment['date_formated'];
	$return["comment"]["auteur"] = $Comment['auteur'];
	$return["comment"]["fk_NNC"] = $Comment['fk_NNC'];
	if(isset($Comment['link_update']) && isset($Comment['link_delete'])) {
		$return[ "comment" ][ "link_update" ] = $Comment[ 'link_update' ];
		$return[ "comment" ][ "link_delete" ] = $Comment[ 'link_delete' ];
	}
}

return $return;
