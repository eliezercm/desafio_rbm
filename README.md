# Running this project

Use the dependency manager [Composer](https://getcomposer.org/) to install all required dependencies.

Navigate to the project directory and then:
```bash
composer install
```

Edit `.env` file with your correct MySQL database credentials

```
DB_HOST=localhost
DB_PORT=3306
DB_NAME=desafio_rbm
DB_USER=user22
DB_PASSWD=whois4
```

Run `seed.php` to create `sale` table and store some sample data

```bash
php seed.php
```

Now, you can start the API

```bash
php -S 127.0.0.1:8000 -t public
```

The api is now running at `http://127.0.0.1:8000/`, you are able to make http requests.

[Here](https://www.getpostman.com/collections/4a41d82dcfde165aa937) is my Postman collection so you can import it.

# API endpoints

---

### GET /sale
Returns an array of all sales stored on database

**Response**

```
[
  {
    "id": "1",
    "paymentId": "eae0dfbb-8e6f-44cc-a05a-917e7ddddca1",
    "customerName": "Eliezer Martins",
    "total": "120"
  },
  {
    "id": "2",
    "paymentId": "76d5410e-8a40-491c-ad73-9273a92c69ba",
    "customerName": "David Heinnemeier",
    "total": "370"
  },
  {
    "id": "3",
    "paymentId": "9b278b70-bf12-4407-afdb-7f4ba00459f7",
    "customerName": "Rasmus Lerdorf",
    "total": "877"
  },
  {
    "id": "4",
    "paymentId": "cddb4f65-4bd0-4f84-9795-dce813094bcd",
    "customerName": "Dan Abramov",
    "total": "450"
  },
  ...
]
```
___

### GET /sale/:id
Returns all sale details from Cielo API Sandbox

**URL Parameters**

|          Name | Required |  Type   | Description                                                                                                                                                         |
| -------------:|:--------:|:-------:| ------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
|     `id` | yes | string  | The sale id                                                                |

**Request**
<br />`GET http://127.0.0.1:8000/sale/1`

**Response**

```
{
  "MerchantOrderId": "1175537952",
  "Customer": {
    "Name": "Eliezer Martins",
    "Address": {}
  },
  "Payment": {
    "ServiceTaxAmount": 0,
    "Installments": 1,
    "Interest": "ByMerchant",
    "Capture": false,
    "Authenticate": false,
    "CreditCard": {
      "CardNumber": "000000******0001",
      "Holder": "Eliezer Martins",
      "ExpirationDate": "12/2020",
      "Brand": "Visa"
    },
    "ProofOfSale": "4546007",
    "Tid": "1204014546007",
    "AuthorizationCode": "383220",
    "PaymentId": "eae0dfbb-8e6f-44cc-a05a-917e7ddddca1",
    "Type": "CreditCard",
    "Amount": 120,
    "ReceivedDate": "2019-12-04 13:45:45",
    "Currency": "BRL",
    "Country": "BRA",
    "Provider": "Simulado",
    "Status": 1,
    "Links": [
      {
        "Method": "GET",
        "Rel": "self",
        "Href": "https://apiquerysandbox.cieloecommerce.cielo.com.br/1/sales/eae0dfbb-8e6f-44cc-a05a-917e7ddddca1"
      },
      {
        "Method": "PUT",
        "Rel": "capture",
        "Href": "https://apisandbox.cieloecommerce.cielo.com.br/1/sales/eae0dfbb-8e6f-44cc-a05a-917e7ddddca1/capture"
      },
      {
        "Method": "PUT",
        "Rel": "void",
        "Href": "https://apisandbox.cieloecommerce.cielo.com.br/1/sales/eae0dfbb-8e6f-44cc-a05a-917e7ddddca1/void"
      }
    ]
  }
}
```
___

### POST /sale
Store a sale integrated with Cielo API 3.0

**Parameters**

|          Name | Required |  Type   | Description                                                                                                                                                         |
| -------------:|:--------:|:-------:| ------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
|     `customerName` | yes | string  | The name of sale customer |
|    `paymentTotal` | yes | integer  | Total value of the current sale |

**Request body**
```
{
  "customerName": "Tim Berners-Lee",
  "paymentTotal": 1991
}
```

**Response**

```
{
  "message": "Venda efetuada com sucesso!",
  "sale": {
    "id": "13",
    "paymentId": "5085f755-53e3-495d-b9df-0acfee391523",
    "customerName": "Tim Berners-Lee",
    "paymentTotal": 1991
  }
}
```
___
