<?php
/**
 * Created by PhpStorm.
 * User: mpfister
 * Date: 13/10/2016
 * Time: 10:58
 */

$return = [];


$return[ 'error_code' ]       = ( isset( $error_code ) ) ? $error_code : 0;
$return[ 'error_message' ]    = ( isset( $error_message ) ) ? $error_message : '';
$return[ 'validation_message' ] = ( isset( $validation_message ) ) ? $validation_message : '';
$return[ 'content' ]          = $content;

return $return;