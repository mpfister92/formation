<?php
if(null == $Comments_a) {
	$return[ 'success' ] = false;
}

else{
	$return['success'] = true;
	foreach ($Comments_a as $Comment){
		$return['Comments'][$Comment['id']]['id'] = $Comment['id'];
		$return['Comments'][$Comment['id']]['contenu'] = $Comment['contenu'];
		$return['Comments'][$Comment['id']]["date"] = $Comment['date_formated'];
		$return['Comments'][$Comment['id']]["auteur"] = $Comment['auteur'];
		$return['Comments'][$Comment['id']]["fk_NNC"] = $Comment['fk_NNC'];
		$return['Comments'][$Comment['id']]["fk_NMC"] = $Comment['fk_NMC'];
		if(isset($Comment['link_update']) && isset($Comment['link_delete'])) {
			$return['Comments'][$Comment['id']]["link_update"] = $Comment['link_update'];
			$return['Comments'][$Comment['id']]["link_delete"] = $Comment['link_delete'];
		}
	}
}

return $return;