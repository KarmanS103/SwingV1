<!--index.php-->
<?php

// read ticker text input from swing.html
$sectorinput = filter_input(INPUT_POST, 'sector');
if (!empty($sectorinput)) {
}
else {
    $sectorinput = financials;
    echo "DEFAULT TO FINANCIALS: NO SECTOR GIVEN";
}

// mysql db properties
$host = "127.0.0.1";
$username = "root";
$password = "sesame";
$dbname = "swing";

// create connection with db
$con = new mysqli($host, $username, $password, $dbname);

/* sector properties

$sector_id : the sector_id of the $sectorinput
$sector_tickers : the tickers in the $sectorinput
// EDIT ^^^ : couldn't figure out how to return multiple rows as multiple objects
            : instead, returning the first ticker in each sector
 */

$sector_id = $con->query("SELECT sector_id as ID FROM sector WHERE sector_name = '$sectorinput'")->fetch_object()->ID;
$sector_tickers = $con->query("SELECT ticker as TICKERS FROM company WHERE sector_id = $sector_id")->fetch_object()->TICKERS;

// close connection with db
$con->close();

?>

<html lang="en" style="margin-left: 20px; background-color:white">
<head>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <meta charset="UTF-8">
</head>
<body>
<!--page header-->
<h1>Swing</h1>

<!--back button-->
<a href="swing.html"><button class="w3-button w3-light-gray" type="button" href="welcome.html">Back</button></a>

<!--list of stocks in $sectorinput-->
<h2>Here's a stock in <?php echo $sectorinput;?>:</h2>

<form action="http://localhost/" method="post">
    <!--ticker insert submit button-->
    <input value='<?php echo $sector_tickers;?>' name="ticker" class="w3-button w3-light-gray" type="submit" value="Show me the money">
</form>



</body>
</html>




