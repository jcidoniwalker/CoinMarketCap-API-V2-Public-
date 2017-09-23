<?php

/* 
Joshua Cidoni
This is a simple script that will fetch a JSON decoded array from a REST API (source: coinmarketcap.com)
It will then prepare data for entry (with protection from SQL injections) into a MySQL database (coin name, coin price, current unix timestamp) using OOP-MySQLi constructs.
09/23/2017
*/

function ConnectTo_MYSQL($host, $username, $password, $db, $port) { // Returns a succesful MySQLi Object, or stops the program
	
	$mysqli = new mysqli($host, $username, $password, $db, $port);
	if ($mysqli->connect_errno) {
		die ("Failed to connect to MySQL: (" . $mysqli->connect_errno . ") \n" . $mysqli->connect_error);
	}
	
	return $mysqli;
}

function GetJSON_Array($url) { // Fetches a JSON decoded array from a given URL, if unsuccessful it will stop the program
	
	$ch = curl_init();
	
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_USERAGENT, "Parser");
	
	$ch_headers = ['Accept:application/json'];
	curl_setopt($ch, CURLOPT_HTTPHEADER, $ch_headers);
	
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,  2);
	
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);

	$response = curl_exec($ch);
	
	if(curl_errno($ch)) {
		print curl_error($ch) . "\n";
		die();
	 }
	 
	 return json_decode($response,true);
	
}

/* MAIN BODY OF THE SCRIPT STARTS BELOW */

// Simply greet the user
print ("USD Cryptocoin Valuations (REST API) [Source: CoinmarketCap]\n");

// Get a MySQL connection 
$mysqli_Conn = ConnectTo_MYSQL("127.0.0.1","root","","cryptodev","3306");

// Get the JSON array from the source URL
$coin_List = GetJSON_Array("https://api.coinmarketcap.com/v1/ticker/");

// At this point we have the JSON data. Loop through the array, prepare, and insert or update the data into our database depending if the record already exists
foreach($coin_List as $coinKey=>$coinValue) {
	
	// Attempt to insert the data into a MySQL database, using prepared statements for protection against SQL Injections
	if ($stmt = $mysqli_Conn->prepare("INSERT INTO coins(`name`,`price_usd`,`timestamp`) VALUES (?,?,?) ON DUPLICATE KEY UPDATE price_usd=?,timestamp=?")) {
		$stmt->bind_param("sdidi", $coinValue['symbol'], $coinValue['price_usd'], time(), $coinValue['price_usd'],time());
		
		// If the execution of the final query fails, it will print the error but continue onto the next coin.
		if($stmt->execute())
			print "Successfully inserted/updated " . $coinValue['name'] . " to the database\n";
		else 
			print $stmt->error . "\n"; 
			
		$stmt->close();
	}
	
}

// The script has executed successfully at this point, close the database connection.
$mysqli_Conn->close();

print("The script ran successfully. Goodbye\n");
?>