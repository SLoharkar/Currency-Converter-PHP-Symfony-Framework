<?php

namespace App\Service;

use Psr\Log\LoggerInterface;


class CurrencyService{

    private $logger;

    
    /**
     * Constructor for the CurrencyService class.
     *
     * This constructor initializes the logger 
     *
     * @param LoggerInterface $logger The logger service for recording log messages.
    */
    public function __construct(LoggerInterface $logger){
        $this->logger = $logger;
    }


    /**
     * Converts a specified amount from one currency to multiple other currencies.
     *
     * @param array $currencies Array of currencies with their names and conversion rates.
     * @param string $from The currency code from which the amount is to be converted.
     * @param float $amount The amount to be converted.
     * @return array An array of converted currency values with their names and converted amounts.
    */
    public function convertCurrency($currencies, $from, $amount){

        // Initialize an empty array to hold the converted currency values
        $converted = [];

        if (!empty($from) && $amount > 0){

            $this->logger->info("Starting currency conversion", ['from' => $from, 'amount' => $amount]);

            // Limit the iteration to the first 10 currencies
            $currencies = array_slice($currencies, 0, 10);

            $this->logger->debug("Limited currencies array", ['currencies' => $currencies]);

                foreach ($currencies as $raw_data){

                    if ($raw_data['name'] != $from){

                        $convertedAmount = $amount * $raw_data['rate'];
                        $converted[] = [
                            'name' => $raw_data['name'],
                            'converted_amount' => $convertedAmount
                        ];

                        // Log the conversion details
                        $this->logger->info("Converting currency", [
                            'from' => $from,
                            'to' => $raw_data['name'],
                            'rate' => $raw_data['rate'],
                            'amount' => $amount,
                            'converted_amount' => $convertedAmount
                        ]);
                    }            
                }
        }
        else{
            // Log a message if 'from' currency is empty or amount is not greater than 0
            $this->logger->warning("Invalid input", ['from' => $from, 'amount' => $amount]);
        }
        // Return the array of converted currencies
        return $converted;        
    }


    /**
     * Loads currency data and returns an array of currencies with their names and rates.
     *
     * @return array An array of currencies with their names and rates.
    */
    public function loadCurrency(){

        $this->logger->info("Starting currency loading process");

        $result = $this->urlVerify();

        $this->logger->debug("URL verification result", ['result' => $result]);

        // Populate the $currencies array with data
        foreach ($result as $currencyCode => $raw_data){

            if (isset($raw_data['name']) && isset($raw_data['rate'])){

                $currencies[$currencyCode] = [
                    "name" => $raw_data['name'],
                    "rate" => $raw_data['rate']
                ];

                $this->logger->info("Currency added", [
                    'currencyCode' => $currencyCode,
                    'name' => $raw_data['name'],
                    'rate' => $raw_data['rate']
                ]);  
            }
            else{
                $this->logger->warning("Missing name or rate in raw data", ['currencyCode' => $currencyCode, 'raw_data' => $raw_data]);
            }
        }

        $this->logger->debug("Final currencies array", ['currencies' => $currencies]);

        // Return the populated currencies array
        return $currencies;

    }


    /**
     * Verifies the URL and fetches JSON data from the given URL.
     *
     * @return array The decoded JSON data as an associative array.
    */
    private function urlVerify(){

        // URL of the JSON file
        $jsonUrl = "http://www.floatrates.com/daily/usd.json";

        $this->logger->info("Fetching JSON data from URL", ['url' => $jsonUrl]);
        
        // Initialize cURL to fetch the JSON content
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $jsonUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        
        // Execute cURL request and get the JSON content
        $jsonContent = curl_exec($ch);
        $curlError = curl_error($ch);
        curl_close($ch);
        
        // Check if cURL executed properly
        if (!$jsonContent){
            $this->logger->error("Failed to fetch the JSON URL", ['url' => $jsonUrl, 'error' => $curlError]);
            die("Failed to fetch the JSON URL: $jsonUrl");
        }
        
        // Decode JSON content into PHP array
        $data = json_decode($jsonContent, true);
        
        // Check if JSON decoding was successful
        if (json_last_error() != JSON_ERROR_NONE){
            $this->logger->error("Failed to decode JSON", ['error' => json_last_error_msg()]);
            return [];
        }

        $this->logger->info("Successfully decoded JSON data", ['data' => $data]);

        // Return the decoded JSON data
        return $data;
    }

}
?>