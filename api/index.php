<?php
$start = microtime(true);
@header('Access-Control-Allow-Origin: *');

include_once('./simple_html_dom.php');

if (!isset($_REQUEST['sat_1_reading'])) 			$_REQUEST['sat_1_reading'] = '';
if (!isset($_REQUEST['sat_1_math'])) 				$_REQUEST['sat_1_math'] = '';
if (!isset($_REQUEST['sat_subject_first'])) 		$_REQUEST['sat_subject_first'] = '';
if (!isset($_REQUEST['sat_subject_second'])) 		$_REQUEST['sat_subject_second'] = '';
if (!isset($_REQUEST['sat_subject_third'])) 		$_REQUEST['sat_subject_third'] = '';
if (!isset($_REQUEST['class_size'])) 				$_REQUEST['class_size'] = '';
if (!isset($_REQUEST['rank_type'])) 				$_REQUEST['rank_type'] = '';
if (!isset($_REQUEST['rank_value'])) 				$_REQUEST['rank_value'] = '';

$data =  array(
	'sat_1_reading' =>  $_REQUEST['sat_1_reading'],
	'sat_1_math' => $_REQUEST['sat_1_math'],
	'sat_subject_first' => $_REQUEST['sat_subject_first'],
	'sat_subject_second' => $_REQUEST['sat_subject_second'],
	'sat_subject_third' => $_REQUEST['sat_subject_third'],
	'class_size' => $_REQUEST['class_size'],
	'rank_type' => $_REQUEST['rank_type'],
	'rank_value' => $_REQUEST['rank_value']
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
$fields_string = '';
foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
rtrim($fields_string, '&');

//open connection
$ch = curl_init();

//set the url, number of POST vars, POST data
curl_setopt($ch,CURLOPT_URL, $url);
curl_setopt($ch,CURLOPT_POST, count($fields));
curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);

//execute post
$result = curl_exec($ch);

$errno = curl_errno($ch);
if ($result == false) {
    $error_message = curl_strerror($errno);
    if (!$error_message) $$error_message = 'no data';
    header('Content-Type: application/json');
	//close connection
	curl_close($ch);

	die(json_encode(array('message' => $error_message, 'errno' => $errno, 'result' => $result, 'input' => $fields)));
}

//close connection
curl_close($ch);

// Init DOM extract
$html = str_get_html($result);

// Find
$scores = array();
foreach ($html->find('#section_0 > div > div > h2 > strong > font') as $e) {
	$scores[] = $e->innertext;
}

$result = array(
	'score' => 0,
	'rank' => 0
);

if (isset($scores[0]) && isset($scores[1])) {
	$result['score'] = intval($scores[0]);
	$result['rank'] = intval(trim(str_replace('out of 9', '', $scores[1])));
}

$result['input'] = $fields;
$result['time'] = microtime(true) - $start;

header('Content-Type: application/json');
echo json_encode($result);
