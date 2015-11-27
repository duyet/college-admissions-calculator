<?php
$data =  array(
    'sat_1_reading' =>  $_REQUEST['sat_1_reading'] or '',
    'sat_1_math' => $_REQUEST['sat_1_math'] or '',
    'sat_subject_first' => $_REQUEST['sat_subject_first'] or '',
    'sat_subject_second' => $_REQUEST['sat_subject_second'] or '',
    'sat_subject_third' => $_REQUEST['sat_subject_third'] or '',
    'class_size' => $_REQUEST['class_size'] or '',
    'rank_type' => $_REQUEST['rank_type'] or 'exact',
    'rank_value' => $_REQUEST['rank_value'] or ''
    );

$url = 'http://www.toptieradmissions.com/resources/college-calculator/';
$fields = array(
	'form_submitted' => true,
	'sat1v' => $data['sat_1_reading'],
	'sat1m' => $data['sat_1_math'],
	'sat2a' => $data['sat_subject_first'],
	'sat2b' => $data['sat_subject_second'],
	'sat2c' => $data['sat_subject_third'],
	'classsize' => $data['class_size'],
	'method' => $data['rank_type'],
	'rank' => $data['rank_value'],
	'decile' => $data['rank_value'],
	'quintile' => $data['rank_value'],
	'quartile' => $data['rank_value'],
	'gpa' => $data['rank_value'],
	'submit' => 'Calculate'
);

//url-ify the data for the POST
foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
rtrim($fields_string, '&');

//open connection
$ch = curl_init();

//set the url, number of POST vars, POST data
curl_setopt($ch,CURLOPT_URL, $url);
curl_setopt($ch,CURLOPT_POST, count($fields));
curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);

//execute post
$result = curl_exec($ch);

//close connection
curl_close($ch);