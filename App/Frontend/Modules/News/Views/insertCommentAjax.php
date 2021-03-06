<?php
$return['success'] = $success;

if (isset($Comment)) {
	$return['new_update_date'] = $new_update_date;
	$return["comment"]["contenu"] = $Comment['contenu'];
	$return["comment"]["date"] = $Comment['date_formated'];
	$return["comment"]["auteur"] = $Comment['auteur'];
	$return["comment"]["fk_NNC"] = $Comment['fk_NNC'];
	$return["comment"]["id"] = $Comment["id"];
	$return["comment"]["fk_NCE"] = $Comment["fk_NCE"];
	if(isset($Comment['link_update']) && isset($Comment['link_delete'])) {
		$return[ "comment" ][ "link_update" ] = $Comment[ 'link_update' ];
		$return[ "comment" ][ "link_delete" ] = $Comment[ 'link_delete' ];
	}
	if(isset($Comment['summary_link'])){
		$return['comment']['summary_link'] = $Comment['summary_link'];
	}
}
if (isset($error_message)) {
	$return["error_message"] = $error_message;
	$return["name"] = $name;
	$return["error_code"] = $error_code;
}
if (isset($validation_message)) {
	$return["validation_message"] = $validation_message;
	$return["name"] = $name;
}

return $return;
