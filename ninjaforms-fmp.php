<?php

/**
 * Plugin Name:       Claris FileMaker Data API for Ninja Forms
 * Description:       Send Claris FileMaker Data API call for Ninja Form submissions
 * Version:           0.1
 * Author:            NRG Software, LLC.
 * Author URI:        http://nrgsoft.com/
 * License:           MIT
 * Text Domain:       nrgsoft
 */

/**
 * @tag filemaker_data_api
 * @callback filemaker_data_api
 */
add_action( 'filemaker_data_api', 'ninja_forms_processing_callback' );
/**
 * @param $form_data array
 * @return void
 */
function ninja_forms_processing_callback( $form_data ){
	// data passed from form
    $form_id       = $form_data[ '$form_id' ];
    $form_fields   = $form_data[ 'fields' ];
    $form_settings = $form_data[ 'settings' ];
    $form_title    = $form_data[ 'settings' ][ 'title' ];
    
    // your filemaker host settings
    $host = 'yourserver.com'; // your server hostname
	$dbname = 'Webhook'; // your database name
	$layout = 'Webhook'; // your layout name
	$user = 'Webhook'; // your username
	$pass = 'Webhook'; // your password 
	$script = 'Process Webhook'; // your script name
	
	// convert form data to nvp
	foreach( $form_fields as $field ){
        $field_id    = $field[ 'id' ];
        $field_key   = $field[ 'key' ];
        $field_value = $field[ 'value' ];
        $form_nvp[$field_key]=$field_value;
    }

	// get a token from the host
	$url = 'https://'.$host.'/fmi/data/v1/databases/'.rawurlencode($dbname).'/sessions';
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,$url);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);          
	curl_setopt($ch, CURLOPT_HTTPHEADER, array ( 'Content-Type: application/json', 'Authorization: Basic ' . base64_encode ($user . ':' . $pass)));
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_TIMEOUT, 5);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS,'{}');
	curl_setopt($ch, CURLOPT_VERBOSE, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$login_result = curl_exec($ch);
	curl_close ($ch);
	
	$login_result = json_decode ($login_result, true);
	
	// Check for Error Message
	$errorCode = $login_result['messages'][0]['code'];
	$errorMessage = $login_result['messages'][0]['message'];
		
	if ($errorCode !== '0') { 
		// Login Error
		$errorResult = 'Login Error: '. $errorMessage. ' (' . $errorCode . ')';
		// echo $errorResult;
	} else {
		$errorResult = '';
		$token = $login_result['response']['token'];
		// echo 'token: '.$token.'<br>';
	}
 
	// FileMaker Script to run
	$runScript = array('script' => $script);
	 
	// create an array for the form data
	$form_encoded = base64_encode ( json_encode ( $form_nvp)) ; // encode form data for transfer	
	$record['form_title'] = $form_title;
	$record['form_id'] = $form_id;
	$record['form_data'] = $form_encoded;
	$data['fieldData'] =  $record;

	$json = json_encode (array_merge($data, $runScript));

	// create record
	$url = 'https://'.$host.'/fmi/data/v1/databases/'.rawurlencode($dbname).'/layouts/'.rawurlencode($layout).'/records';
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,$url);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);          
	curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json","Authorization: Bearer ".$token));
	//curl_setopt($ch, CURLOPT_HEADER, 0);
	//curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS,	$json);
	curl_setopt($ch, CURLOPT_TIMEOUT, 5);
	curl_setopt($ch, CURLOPT_VERBOSE, 1);
	curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$create_result = curl_exec($ch);
	curl_close ($ch);

	$create_result = json_decode ($create_result, true);

	// logout
	$url = 'https://'.$host.'/fmi/data/v1/databases/'.rawurlencode($dbname).'/sessions/'.$token;
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,$url);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);          
	curl_setopt($ch, CURLOPT_HTTPHEADER, 'Content-Type: application/json');
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
	curl_setopt($ch, CURLOPT_TIMEOUT, 5);
	curl_setopt($ch, CURLOPT_POSTFIELDS,'');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_VERBOSE, 1);
	$logout_result = curl_exec($ch);
	curl_close ($ch);

	$logout_result = json_decode ($logout_result, true);

}


?>
