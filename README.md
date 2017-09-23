# coinmarket_json_parser

Joshua Cidoni-Walker | Florida Atlantic Universtiy
09/23/2017

This is a simple script that will fetch a JSON decoded array from a REST API (source: coinmarketcap.com)
It will then prepare data for entry (with protection from SQL injections) into a MySQL database
(coin name, coin price, current unix timestamp) using OOP-MySQLi constructs.
