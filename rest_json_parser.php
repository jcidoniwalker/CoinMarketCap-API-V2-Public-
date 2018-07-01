<?php
    $quoteCurrency = "USD"; // The currency you want the coins to be quoted in (applies to price, marketcap, volume, percent change, etc.)
    $sleepPeriod = 3; // CoinMarketCap allows 30 requests per minute -- Adjust this to avoid rate-limiting (the more coins, the higher the sleep period should be)

    $i = 0; // Used for our while loop
    do {
        if(is_int($i / 100)) { // When $i = 100, 200, 300 condition
            $startParam = $i + 1; // Start all indexing + 1 because CMC starts listing id's from 1, not 0
            $cmcRequest = GetJSON_Array("https://api.coinmarketcap.com/v2/ticker/?start=" . $startParam . "&convert=" . $quoteCurrency); // Get and decode the JSON array from CMC's API link
            $numCoins = ceil($cmcRequest['metadata']['num_cryptocurrencies'] / 100) * 100; // Round up to the nearest hundred, eg. (Number of coins on CMC: 1439 - $numCoins after ceil = 1500)
            
            foreach($cmcRequest['data'] as $key=>$value) {
                // Print the name and price of the resultset (for each coin) looped through) - this is for demonstration purposes, please read the next multi-line comment
                print $value['name'] . " - " . $value['quotes'][$quoteCurrency]['price'] . "\n";

                /*
                    This foreach loop is a blank canvas (intentionally). Inside this loop is every coin's dataset from
                    the API. A copy of the structure is provided below--and, is subject to change.

                    You can do here whatever it is that you would like to do with the results of ALL coins
                    provided by CoinMarketCap's API. Have fun!

                    Example of result set in this loop:

                        "id": 1, 
                        "name": "Bitcoin", 
                        "symbol": "BTC", 
                        "website_slug": "bitcoin", 
                        "rank": 1, 
                        "circulating_supply": 17126087.0, 
                        "total_supply": 17126087.0, 
                        "max_supply": 21000000.0, 
                        "quotes": {
                            "USD": {
                                "price": 6378.52, 
                                "volume_24h": 4515580000.0, 
                                "market_cap": 109239088451.0, 
                                "percent_change_1h": 0.61, 
                                "percent_change_24h": 0.4, 
                                "percent_change_7d": 3.87
                            }
                        }, 
                        "last_updated": 1530479598
                */

            }

            sleep($sleepPeriod); // Sleep to avoid rate-limiting
         }

        $i++; // Update our loop count by 1 for every coin provided by the API
    } while($i < $numCoins); // Loop until we get to the max limit for the amount of coins on CMC.

    function GetJSON_Array($url) { // Fetches a JSON decoded array from a given URL, if unsuccessful it will stop the program
            
        echo "Making request to " . $url . "\n";
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
?>
