<?php

// these should be computed like they are in index.php
$currentdate = 20180808;
$oneweekago = $currentdate - 7;
$twoweekago = $currentdate - 86;
$onemonthago = $currentdate - 100;
$oneyearago = $currentdate - 10000;
$startdate = $onemonthago;

// stock chart's data source
$tickerinput = aapl;

// setting header to json
header('Content-Type: application/json');

// create connection with db
$con = new mysqli("127.0.0.1", "root", "sesame", "swing");

if(!$con){
    die("Connection failed: " . $con->error);
}

// pull chart data from db
$result = $con->query("SELECT date, close FROM daily_price JOIN company USING (company_id) WHERE ticker = '$tickerinput' AND date > $startdate ORDER BY date");

// loop through the returned data
$data = array();
foreach ($result as $row) {
    $data[] = $row;
}

// free memory associated with result
$result->close();

// close connection with db
$con->close();

//now print the data
print json_encode($data);
?>
