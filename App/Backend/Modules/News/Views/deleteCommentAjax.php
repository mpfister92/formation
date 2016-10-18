<?php
if(null == $deleted_Comment) {
	$return[ 'success' ] = false;
}

else{
	$return['success'] = true;
	$return["comment"]["id"] = $deleted_Comment['id'];
}

return $return;