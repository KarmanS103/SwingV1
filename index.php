<!--index.php-->
<?php


// read ticker text input from swing.html
$tickerinput = filter_input(INPUT_POST, 'ticker');
if (!empty($tickerinput)) {
}
else {
    $tickerinput = aapl;
    echo "DEFAULT TO AAPL: NO TICKER GIVEN";
}

// mysql db properties
$host = "127.0.0.1";
$username = "root";
$password = "sesame";
$dbname = "swing";

// create connection with db
$con = new mysqli($host, $username, $password, $dbname);
if(!$con){
    die("Connection failed: " . $con->error);
}
/* date variables

$currentdate : the relative current date
$oneweekago : the date one week before the $currentdate
$twoweekago : the date two weeks before the $currentdate
$onemonthago : the date one month before the $currentdate
$oneyearago : the date one year before the $currentdate


THIS STUFF SHOULD PROBABLY BE TAKEN OUT
$currentdate = new DateTime('08/19/2018');      date > '2009-06-29 16:00:44';
$currentdateformat = date_format($currentdate, 'Y-m-d H:i:s');
$swagdate = date_format('08/23/2017', 'Y-m-d H:i:s');
$datetime = date_create()->format('Y-m-d H:i:s');
$currentdate = date_create()
$currentdate = date("2017-08-08 00:00:00");
*/


/* db statistics & variables

/ the term 'input' in these comments refers to the text that is inputted by the user on the main page of the application
/ the input should be a ticker symbol of a stock in the db

$ticker : the company_id of the input
$companyname : the company name of the ticker inputted by the user

// dates
$currentdate : the inputted current date
$oneweekago : the date one week before the $currentdate
$twoweekago : the date two weeks before the $currentdate
$onemonthago : the date one month before the $currentdate
$oneyearago : the date one year before the $currentdate

// statistics
$currentvolume : the volume of the input on the current day
$averagevolume : the average volume of the input over the last year
$averagevolumeweek : the average volume of the input over the last week

$fiftytwoweekhigh : the max close of the input over the last year
$fiftytwoweeklow : the min close of the input over the last year
$twoweeklow : the min close of the input over the last two weeks

$openvalue : the open of the input on the current day
$closevalue : the close of the input on the current day
$weekagoclose : the close of the input one week before the $currentdate
$monthagoclose : the close of the input one month before the $currentdate
$averagecloseweek : the average close of the input over the last week

$daychange : the percent change between the open and close value of the input on the current day
$weekchange : the percent change between the $weekagoclose and the close value of the input on the current day
$monthchange : the percent change between the $monthagoclose and the close value of the input on the current day
*/

$ticker = $con->query("SELECT company_id as ID FROM company WHERE ticker = '$tickerinput'")->fetch_object()->ID;
$companyname = $con->query("SELECT name FROM company WHERE company_id = $ticker")->fetch_object()->name;

$currentdate = 20180808;
$oneweekago = $con->query("SELECT date AS extractdate FROM daily_price WHERE date > $currentdate - 7 AND company_id = $ticker ORDER BY date LIMIT 1")->fetch_object()->extractdate;
$twoweekago = $con->query("SELECT date AS extractdate FROM daily_price WHERE date > $currentdate - 86 AND company_id = $ticker ORDER BY date LIMIT 1")->fetch_object()->extractdate;
$onemonthago = $con->query("SELECT date AS extractdate FROM daily_price WHERE date > $currentdate - 100 AND company_id = $ticker ORDER BY date LIMIT 1")->fetch_object()->extractdate;
$oneyearago = $con->query("SELECT date AS extractdate FROM daily_price WHERE date > $currentdate - 10000 AND company_id = $ticker ORDER BY date LIMIT 1")->fetch_object()->extractdate;

$currentvolume = $con->query("SELECT round(volume, 0) AS volume FROM daily_price WHERE date = '$yearagodate' AND company_id = $ticker")->fetch_object()->volume;
$averagevolume = $con->query("SELECT round(avg(volume), 0) AS avgvol FROM daily_price WHERE date > '$yearagodate' AND company_id = $ticker")->fetch_object()->avgvol;
$averagevolumeweek = $con->query("SELECT round(avg(volume), 0) AS avgweeklyvol FROM daily_price WHERE date > $currentdate - 7 AND company_id = $ticker")->fetch_object()->avgweeklyvol;

$fiftytwoweekhigh = $con->query("SELECT max(close) AS maxhigh FROM daily_price where date > '$yearagodate' and company_id = $ticker")->fetch_object()->maxhigh;
$fiftytwoweeklow = $con->query("SELECT min(close) AS minlow FROM daily_price where date > '$yearagodate' and company_id = $ticker")->fetch_object()->minlow;
$twoweeklow = $con->query("SELECT min(close) AS minlow FROM daily_price where date > $currentdate - 86 and company_id = $ticker")->fetch_object()->minlow;

$openvalue = $con->query("SELECT open FROM daily_price where company_id = $ticker and date = $currentdate")->fetch_object()->open;
$closevalue = $con->query("SELECT close FROM daily_price where company_id = $ticker and date = $currentdate")->fetch_object()->close;
$weekagoclose = $con->query("SELECT close as weekagoclose FROM daily_price where company_id = $ticker and date = '$oneweekago'")->fetch_object()->weekagoclose;
$monthagoclose = $con->query("SELECT close as monthagoclose FROM daily_price where company_id = $ticker and date = '$onemonthago'")->fetch_object()->monthagoclose;
$averagecloseweek = $con->query("SELECT round(avg(close), 2) AS avgweeklyclose FROM daily_price WHERE date = '$oneweekago' AND company_id = $ticker")->fetch_object()->avgweeklyclose;

$daychange = $con->query("SELECT round((close-open)/open * 100, 2) AS daychange FROM daily_price where company_id = $ticker and date = $currentdate")->fetch_object()->daychange;
$weekchange = $con->query("SELECT round((close - $weekagoclose) / $weekagoclose * 100, 2) AS weekchange FROM daily_price where company_id = $ticker and date = $currentdate ")->fetch_object()->weekchange;
$monthchange = $con->query("SELECT round((close- $monthagoclose) /$monthagoclose * 100, 2) AS monthchange FROM daily_price where company_id = $ticker and date = $currentdate")->fetch_object()->monthchange;

/* risk calculations
$riskascale : a scalar that used to modify the weight of $riska when computing $totalrisk

$riska : formula to calculate risk
$riskb : formula to calculate risk
$riskc : formula to calculate risk
$riskd : formula to calculate risk
$risktotal : calculated risk value of the input

$riskcolor : color of $riskleveltext
$riskleveltext : comprehensible significance of $risktotal
*/

$riskascale = 3.4;

$riska = ($currentvolume - $averagevolume) / $averagevolume * $riskascale;
$riskb = ($averagevolumeweek - $averagevolume) / $averagevolume;
$riskc = $daychange;
$riskd = ($closevalue - $averagecloseweek) / $averagecloseweek * 45;
$risktotal = $riska + $riskb + $riskc + $riskd;

// function to determine $riskcolor and $riskleveltext
if ($risktotal > 100) {
$riskcolor = 'red';
$riskleveltext = 'high risk';
}
else {
    $riskcolor = 'green';
    $riskleveltext = 'low risk';
}

/* recommendation calculations

$recoa : formula to calculate recommendation
$recob : formula to calculate recommendation
$recoc : formula to calculate recommendation
$recototal : calculated recommendation value of the input

$recocolor : color of $recoleveltext
$recoleveltext : comprehensible significance of $recototal
*/

$recoa = ($fiftytwoweekhigh - $openvalue) / $openvalue;
$recob = ($fiftytwoweeklow - $openvalue) / $openvalue;
$recoc = 1/(($openvalue - $twoweeklow)/$twoweeklow);
$recototal = $recoa + $recob + $recoc;

// function to determine $recocolor and $recoleveltext
if ($recototal > 100) {
    $recocolor = 'green';
    $recoleveltext = 'buy';
}
else if ($recototal > 50){
    $recocolor = 'yellow';
    $recoleveltext = 'hold';
}
else {
    $recocolor = 'red';
    $recoleveltext = 'sell';
}

/* statistic color

$daychangecolor : color of $daychange text
$weekchangecolor : color of $weekchange text
$monthchangecolor : color of $monthchange text
 */

// WE CAN PROBABLY CONDENSE THIS CODE

// function to determine $daychangecolor
if ($daychange * 100 > 0) {
    $daychangecolor = 'green';
}
else {
    $daychangecolor = 'red';
}

// function to determine $weekchangecolor
if ($weekchange * 100 > 0) {
    $weekchangecolor = 'green';
}
else {
    $weekchangecolor = 'red';
}

// function to determine $monthchangecolor
if ($monthchange * 100 > 0) {
    $monthchangecolor = 'green';
}
else {
    $monthchangecolor = 'red';
}

// close connection with db
$con->close();

//$colors = array("red", "green", "blue", "yellow");
//foreach ($colors as $value) {
    //echo "$value <br>";
?>

<!--<a href="index.php?tickerinput=10">assign variable to 10</a>-->

<!--html-->
<html style="margin-left: 20px; background-color:white">
    <head>
        <!--W3 CSS stylesheet-->
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
        <meta charset="UTF-8">
        <title>Swing</title>
        <!--stockchart style-->
        <style>
            .chart-container{
                height: 240px;
                width: 480px;
            }
        </style>
        <script type="text/javascript" src="js/jquery.min.js"></script>
        <script type="text/javascript" src="js/chart.min.js"></script>
        <script type="text/javascript" src="chartdata.js"></script>
    </head>
<body>
<!--page header-->
<h1>Swing</h1>

<!--back button-->
<a href="swing.html"><button class="w3-button w3-light-gray" type="button" href="welcome.html">Back</button></a>
<br>

<!--ticker, company name, close value, day change value-->
<table style="width: 480px">
    <tr>
    <th><h2 style="text-transform: uppercase; text-align: left;"><?php echo $tickerinput;?></h2></th>
    <th><h2 style="text-transform: uppercase; text-align: right; color:<?php echo $daychangecolor;?>; "><?php echo $closevalue; ?></h2></th>
    </tr>
    <tr>
        <td style="text-transform:uppercase; text-align: left;"><?php echo $companyname;?></td>
        <td style="text-align: right; color:<?php echo $daychangecolor;?>;"><?php echo $daychange;?>%</td>
    </tr>
</table>

<!--stockchart-->
<div class="chart-container">
    <canvas id="chartdatacanvas"></canvas>
</div>

<!--chart time selector-->
<div class="w3-show-inline-block">
    <div class="w3-bar">
        <button style="width:120px" class="w3-bar-item w3-button w3-dark-gray">YEAR</button>
        <button style="width:120px" class="w3-bar-item w3-button w3-light-gray">1/2 YEAR</button>
        <button style="width:120px" class="w3-bar-item w3-button w3-dark-gray">MONTH</button>
        <button style="width:120px" class="w3-bar-item w3-button w3-light-gray">WEEK</button>
    </div>
</div>
<br>

<!--statistics-->
<table style="width: 480px;">
    <th>
        <!--general statistics-->
<table style="width: 300px;" class="w3-table w3-light-gray">
    <tr>
        <th>avg volume</th>
    <td> <?php echo $averagevolume; ?></td>
    </tr>
    <tr>
        <th>current volume</th>
    <td><?php echo $currentvolume; ?></td>
    </tr>
    <tr>
        <th>day % change</th>
        <td style="color: <?php echo $daychangecolor;?>;"><?php echo $daychange; ?></td>
    </tr>
    <tr>
        <th>week % change</th>
    <td style="color: <?php echo $weekchangecolor;?>;"><?php echo $weekchange; ?></td>
    </tr>
    <tr>
        <th>month % change</th>
    <td style="color: <?php echo $monthchangecolor;?>;"><?php echo $monthchange; ?></td>
    </tr>
    <tr>
        <th>52 week high</th>
        <td><?php echo $fiftytwoweekhigh; ?></td>
    </tr>
    <tr>
        <th>52 week low</th>
        <td><?php echo $fiftytwoweeklow; ?></td>
    </tr>
</table>
    </th>
<th>
    <!--risk and recommendation-->
    <table style="width: 180px;" class="w3-table">
    <tr>
        <th style="color:<?php echo $riskcolor; ?>; text-align: center; font-size: 2em;"><?php echo $riskleveltext; ?></th>
    </tr>
    <tr>
        <th style="color:<?php echo $recocolor; ?>; text-align: center; font-size: 2em;"><?php echo $recoleveltext; ?></th>
    </tr>
</table>
</th>
</table>


<?php
//$colors = array("red", "green", "blue", "yellow");
//foreach ($colors as $value) {
  //  echo "$value <br>";
//}
?>

</body>

</html>