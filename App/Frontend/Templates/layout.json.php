<?php
/**
 * Created by PhpStorm.
 * User: mpfister
 * Date: 13/10/2016
 * Time: 10:58
 */

$return = [];


$return[ 'master_code' ]       = ( isset( $master_code) ) ? $master_code: 0;
$return[ 'master_error' ]    = ( isset( $master_error ) ) ? $master_error: '';
$return[ 'content' ]          = $content;


return $return;