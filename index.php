<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style.css">
	<title>GetCoined | ROI Calculator</title>
	<link rel="icon" href="media/icofile.ico" type="image/x-icon"/>
	<link href="https://fonts.googleapis.com/css2?family=PT+Mono&display=swap" rel="stylesheet">
	<script src="investcalc.js"></script>
</head>
<body>
<div id="global_page">
<div id="global_header">
	<div id="banner_layout">
	<a href="index.html"><img id="banner_img" src="media/banner.png" alt="GetCoined banner"></a>
	<nav id="banner_nav">
	<a id="ROI_Calc" href="index.html">ROI Calculator</a>
	<a href="https://www.getcoined.io/">Home</a>
	</nav>
	</div> <!--banner_layout-->
	</div> <!--global_header-->
	<div id="global_body">
	<div id="mid_body">
	<div id="content_container">
	<div id="blank_space"> 
	</div><!--Blankspace divider-->
	<div id="content">
	<div id="instructions_container">
	<p id="ROI_banner">What is the ROI Calculator?</p>
	<p id="instructions">The GetCoined Return on Investment (ROI) Calculator is a performance measure used to evaluate the 
	efficiency of an investment in Bitcoin. This calculator returns a ratio between net profit and cost of investment.
	</p>
	</div> <!--instructions_container-->
	<div id="form_container">
	<form action="index.php" method="post"> 
		<div id="date_form">
		<label for="date">Date invested in Bitcoin</label>
		<input id="date_field" name="date" type="date" value="2020-04-01">
		</div> <!--date_form-->
		<div id="invest_form">
		<label for="investment">Amount of investestment</label>
		<span id="currencyinput">$ <input id="invest_field" type="number" name="investment"></span>
		<input type="submit" name="submit" value="Submit" onclick="calcROI();">
		</div> <!--invest_form-->
	</form><br>
	<?php
	//Check if the form is submitted
	if ( isset ( $_POST['submit'] ) ) {
		//Get Amount of investment from Textfield
		$invest_amt = $_REQUEST['investment'];
		//Get Date invested from Textfield and convert it to alphabetical for use with scraper
		$timestamp = strtotime($_POST['date']); //Get UNIX Timestamp to be parsed to day/month/year
		$day=date('d',$timestamp); //Get Numeric Day
		$month=date('M',$timestamp); //Get Alphabetic Month
		$year=date('Y',$timestamp); //Get Numeric Year
		$searchDate=$month . ' ' . $day . ', ' . $year; //Date for scraping on coinmarketcap.com	
		//Link to scraper library
		include('simple_html_dom.php');
		$html =	file_get_html('https://coinmarketcap.com/currencies/bitcoin/historical-data/?start=20130501&end=20200528');
		$table = $html->find('tr[class="cmc-table-row"]'); //Create table from coinmarketcap.com
		for ( $i = 0; $i < sizeof($table); $i++ ){ //Search through array for row with correct date
			if (strpos($table[$i], $searchDate) !== false)	{
				$scraped_datarow = $i; //index of the row of data we are looking for
			} else{
				//Do nothing
			}
		}
		$tablevalues = $table[$scraped_datarow]->plaintext; //put tablerow into plaintext variable
		$prefilter = $tablevalues; //prefilter for date
		$line = str_replace($searchDate, "", $prefilter); //Remove Date

		$length = strlen($line); //More String manipulation adding spaces after 2 decimals
		$result = ''; //String to store result of String manipulation
		$k=0; //Variable to iterate through loop
		while ($k<$length){
			if ($line[$k] == '.'){
				$result .= $line[$k];
				$result .= $line[$k+1];
				$result .= $line[$k+2];
				$result .= ' ';
				$k += 3;
			}
			else if ($line[$k] == ' '){
				$k++;
			}
			else{
				$result .= $line[$k];
				$k++;
			}
		} //Added spaces after 2 decimals
		
		$bitcoindata = explode(" ", $result); //Split String by spaces
		$open = $bitcoindata[0]; //Opening value of bitcoin
		$high = $bitcoindata[1]; //Highest peak value during day of bitcoin
		$low = $bitcoindata[2]; //Lowest value during day of bitcoin
		$close = $bitcoindata[3]; //Closing value of bitcoin
		
		$invest_amt = str_replace(',','',$invest_amt); //Take out comma for division
		$close = str_replace(',','',$close); //Take out comma for division
		$bitcoin_amt = ($invest_amt/$close); //Amount of Bitcoin bought on date

		$current_price = $html->find('span[class="cmc-details-panel-price__price"]', 0)->plaintext; //Get current price of BTC from coinmarketcap.com
		$current_price = str_replace('$','',$current_price); //Take out $ sign for division
		$current_price = str_replace(',','',$current_price); //Take out comma for division
		$current_worth = ($bitcoin_amt*$current_price); //Get value of user's bitcoins

		$percent_change = ($current_worth/$invest_amt)*100; //Calc percentage fluxuation
		$ROI_final = ($current_worth-$invest_amt); //Calc final ROI value

		//Print out on submit the results below
		echo 'On ' . $searchDate . ' the price of BTC closed at $' . $close . '<br>
		The price of BTC right now is $' . $current_price . '/BTC<br>
		You bought ' . $bitcoin_amt . ' BTC for $' . $invest_amt . ' and it\'s now worth $' . number_format($current_worth, 2, '.', '') . '<br>
		You have seen a ' . number_format($percent_change, 2, '.', '') . '% difference. Your ROI is $' . number_format($ROI_final, 2, '.', '');
	}
	?>
	</div> <!--form_container-->
	</div> <!--content-->
	</div> <!--content_container-->
	</div> <!--mid_body-->
	</div> <!--global_body-->
</div> <!--global_page-->
</body>
</html>
