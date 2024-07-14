<?php

namespace App\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Service\CurrencyService;


class CurrencyController extends AbstractController{
    
    private $currencyService;
    private $logger;


    /**
     * Constructor for the CurrencyController.
     *
     * This constructor initializes the CurrencyController with the necessary services:
     * - CurrencyService: Provides currency-related functionalities.
     * - LoggerInterface: Used for logging information, errors, and other messages.
     *
     * @param CurrencyService $currencyService The service to handle currency-related operations.
     * @param LoggerInterface $logger The logger service for logging messages.
    */
    public function __construct(CurrencyService $currencyService, LoggerInterface $logger){
        $this->currencyService = $currencyService;
        $this->logger = $logger;
    }


    /**
     * Handles the currency conversion view and form submission.
     *
     * This method provides the currency conversion view and handles the form submission
     * for converting an amount from one currency to another.
     *
     * @Route("/user/currency-converter", name="currency_converter")
    */
    public function currencyView(Request $request){

        $this->logger->info('Accessing currency converter view.');

        // Load the available currencies from the currency service
        $currencies = $this->currencyService->loadCurrency();
        $converted = [];

        if ($request->isMethod('POST')){

            // Retrieve the 'from_currency' and 'amount' from the request
            $from = $request->request->get('from_currency');
            $amount = (float) $request->request->get('amount');

            try {
                // Call the conversion service and get the result
                $converted = $this->currencyService->convert($from, $amount);

                $this->logger->info("Successfully converted $amount from $from.");
            }
            catch(\Exception $e){
                $this->logger->error('Currency conversion failed: ' . $e->getMessage());
            }
        }

        return $this->render('currency_converter.html.twig', [
            'currencies' => $currencies['currencies'],
            'converted' => $converted
        ]);    
    }
}
?>