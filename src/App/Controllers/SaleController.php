<?php
namespace Src\App\Controllers;

use Cielo\API30\Merchant;

use Cielo\API30\Ecommerce\Environment;
use Cielo\API30\Ecommerce\Sale;
use Cielo\API30\Ecommerce\CieloEcommerce;
use Cielo\API30\Ecommerce\Payment;
use Cielo\API30\Ecommerce\CreditCard;

use Cielo\API30\Ecommerce\Request\CieloRequestException;

use Src\App\Models\SaleGateway;
use Src\App\Validators\SaleValidator;

class SaleController {

    private $enviroment;
    private $merchant;

    private $saleGateway;

    // constructor-related attrs
    private $requestMethod;
    private $saleId;

    public function __construct($db, $requestMethod, $saleId)
    {
        $this->db = $db;
        $this->requestMethod = $requestMethod;
        $this->saleId = $saleId;
        
        $this->saleGateway = new SaleGateway($db);

        $this->setupCieloEnviroment();
    }

    public function processRequest()
    {
        switch ($this->requestMethod) {
            case 'GET':
                if ($this->saleId) {
                    $response = $this->getSale($this->saleId);
                } else {
                    $response = $this->getAllSales();
                };
                break;
            case 'POST':
                if ($this->saleId) {
                    $response = $this->notFoundResponse();
                } else {
                    $response = $this->createSale();
                };
                break;
            default:
                $response = $this->notFoundResponse();
                break;
        }
        header($response['status_code_header']);
        if ($response['body']) {
            echo $response['body'];
        }
    }

    private function setupCieloEnviroment()
    {
        $this->environment = Environment::sandbox();
        $this->merchant = new Merchant(
            getenv('MERCHANT_ID'), 
            getenv('MERCHANT_KEY')
        );
    }

    private function createSale()
    {
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);

        $errors = SaleValidator::getAllInputErrors($input);
        
        if(boolval($errors)){
            return $this->unprocessableEntityResponse($errors);
        }

        $sale = new Sale(strval(rand()));

        $customer = $sale->customer($input['customerName']);

        $payment = $sale->payment($input['paymentTotal']);
        $payment->setType(Payment::PAYMENTTYPE_CREDITCARD)
                ->creditCard("123", CreditCard::VISA)
                ->setExpirationDate("12/2020")
                ->setCardNumber("0000000000000001")
                ->setHolder($input['customerName']);

        try {
            $sale = (new CieloEcommerce($this->merchant, $this->environment))->createSale($sale);
        
            $paymentId = $sale->getPayment()->getPaymentId();

            $saleId = $this->saleGateway->insert($paymentId, $input);

            $response['status_code_header'] = 'HTTP/1.1 201 Created';
            $response['body'] = json_encode(array(
                "message" => "Venda efetuada com sucesso!",
                "sale" => array(
                    "id" => $saleId,
                    "paymentId" => $paymentId,
                    "customerName" => $input['customerName'],
                    "paymentTotal" => $input['paymentTotal']
                ))
            );
        
        } catch (CieloRequestException $e) {
            $error = $e->getCieloError();

            $response['status_code_header'] = 'HTTP/1.1 500 Internal Server Error';
            $response['body'] = json_encode(array(
                "error" => $error
            ));
        }
        return $response;
    }

    private function getSale($saleId) {
        $result = $this->saleGateway->find($saleId);

        if(!$result) {
            return $this->resourceNotFoundResponse();
        }

        $PaymentId = $result['paymentId'];

        $response = $this->fetchTransactionFromCieloApi($PaymentId); 

        return $response;
    }

    private function fetchTransactionFromCieloApi($PaymentId) {
        // I could use curl lib to do this job
        $url = "https://apiquerysandbox.cieloecommerce.cielo.com.br/1/sales/" .
                $PaymentId;
        $opts = array(
            'http'=>array(
              'method'=>"GET",
              'header'=>"Content-Type: application/json; charset=utf-8\r\n" .
                        "MerchantId: " . getenv('MERCHANT_ID') . "\r\n" .
                        "MerchantKey: " . getenv('MERCHANT_KEY') . "\r\n"
            )
          );
        $context = stream_context_create($opts);
        $file = file_get_contents($url, false, $context);

        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = $file;
        return $response;
    }

    private function getAllSales() {
        $result = $this->saleGateway->findAll();
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function unprocessableEntityResponse($errors)
    {
        $response['status_code_header'] = 'HTTP/1.1 422 Unprocessable Entity';
        $response['body'] = json_encode([
            'errors' => array($errors)
        ]);
        return $response;
    }

    private function notFoundResponse()
    {
        $response['status_code_header'] = 'HTTP/1.1 404 Not Found';
        $response['body'] = json_encode(array("error" => "Endpoint inválido! Consulte a documentação da API."));
        return $response;
    }

    private function resourceNotFoundResponse()
    {
        $response['status_code_header'] = 'HTTP/1.1 404 Not Found';
        $response['body'] = json_encode(array(
            "error" => "A venda solicitada não existe!"
        ));
        return $response;
    }
}
