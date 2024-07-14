<?php

namespace App\Service;

use Psr\Log\LoggerInterface;


class CurrencyService{

    private $rates;
    private $logger;

    
    /**
     * Constructor for the CurrencyService class.
     *
     * This constructor initializes the logger and sets up simulated exchange rates
     * for various currencies. The exchange rates are hard-coded for simplicity.
     *
     * @param LoggerInterface $logger The logger service for recording log messages.
    */
    public function __construct(LoggerInterface $logger){

        $this->logger = $logger;

        // Simulated exchange rates
        $this->rates = [
            'USD' => 1.0,
            'EUR' => 0.85,
            'JPY' => 110.0,
            'GBP' => 0.75,
            'INR' => 75.0 // Example rate, 1 USD = 75 INR
        ];
    }


    /**
     * Convert an amount from one currency to multiple other currencies.
     *
     * This method takes a source currency and an amount, and converts the amount
     * to other currencies based on predefined exchange rates.
     *
     * @param string $from The source currency code.
     * @param float $amount The amount to be converted.
     * @return array The converted amounts in other currencies.
    */
    public function convert($from, $amount){

        $converted = [];

        $this->logger->info('Conversion requested.', ['from' => $from, 'amount' => $amount]);

        if (isset($this->rates[$from])){

            $this->logger->info('Source currency rate found.', ['rate' => $this->rates[$from]]);

            foreach ($this->rates as $currency => $rate){

                if ($currency != $from){
                    $converted[$currency] = $amount * ($rate / $this->rates[$from]);
                    $this->logger->info('Converted amount.', ['currency' => $currency, 'amount' => $converted[$currency]]);
                }
            }
        }
        else{
            $this->logger->warning('Source currency rate not found.', ['from' => $from]);
        }

        return $converted;
    }  


    /**
     * Load available currencies.
     *
     * This method returns a list of available currencies
     *
     * @return array An array containing the available currencies.
    */
    public function loadCurrency(){

        $this->logger->info('Loading available currencies.');

        $currencies = array_keys($this->rates);

        $this->logger->info('Available currencies loaded.', ['currencies' => $currencies]);

        return [
            'currencies' => $currencies
        ];
    }

}
?>