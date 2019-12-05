<?php
namespace Src\App\Validators;

final class SaleValidator {

  public static function getAllInputErrors($input){
    $paymentTotal = isset($input['paymentTotal']) ? $input['paymentTotal'] : null;
    $paymentTotalError = self::validatePaymentTotal($paymentTotal);

    $customerName = isset($input['customerName']) ? $input['customerName'] : null;
    $customerNameError = self::validateCustomerName($customerName);

    $errors = array();

    if(\boolval($paymentTotalError)){
      $errors['paymentTotal'] = $paymentTotalError;
    };

    if(\boolval($customerNameError)){
      $errors['customerName'] = $customerNameError;
    };

    return $errors;
  }

  public static function validatePaymentTotal($paymentTotal = null){
    if(!isset($paymentTotal)) {
      return "paymentTotal está vazio!";
    }

    if(!is_integer($paymentTotal)) {
      return "paymentTotal deve ser um integer";
    }

    if($paymentTotal<=0) {
      return "paymentTotal deve ser maior que zero";
    }

    return "";
  }

  public static function validateCustomerName($customerName = null){
    if(!isset($customerName)) {
      return "customerName está vazio!";
    }

    if(!is_string($customerName)) {
      return "customerName deve ser uma string";
    }

    $length = strlen($customerName);

    if($length < 3 || $length > 100) {
      return "customerName deve ter entre 3 e 100 caracteres";
    }

    return "";
  }

}