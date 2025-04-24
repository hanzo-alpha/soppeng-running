**Contoh Penggunaan Tripay SDK PHP**

```
<?php

require 'path/to/your/vendor/autoload.php';

use ZerosDev\TriPay\Client as TriPayClient;
use ZerosDev\TriPay\Support\Constant;
use ZerosDev\TriPay\Support\Helper;
use ZerosDev\TriPay\Transaction;

$merchantCode = 'T12345';
$apiKey = 'd1cfd***********888ed3';
$privateKey = 'd1cfd***********888ed3';
$mode = Constant::MODE_DEVELOPMENT;
$guzzleOptions = []; // Your additional Guzzle options (https://docs.guzzlephp.org/en/stable/request-options.html)

$client = new TriPayClient($merchantCode, $apiKey, $privateKey, $mode, $guzzleOptions);
$transaction = new Transaction($client);

/**
 * `amount` will be calculated automatically from order items
 * so you don't have to enter it
 * In this example, amount will be 40.000
 */
$result = $transaction
    ->addOrderItem('Gula', 10000, 1)
    ->addOrderItem('Kopi', 6000, 5)
    ->create([
        'method' => 'BRIVA',
        'merchant_ref' => 'INV123',
        'customer_name' => 'Nama Pelanggan',
        'customer_email' => 'email@konsumen.id',
        'customer_phone' => '081234567890',
        'expired_time' => Helper::makeTimestamp('6 HOUR'), // see Supported Time Units
    ]);

echo $result->getBody()->getContents();

/**
* For debugging purpose
*/
$debugs = $client->debugs();
echo json_encode($debugs, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
```
