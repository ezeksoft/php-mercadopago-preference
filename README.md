### Crie um link de pagamento do Mercado Pago

```php
<?php 

require '../src/Preference.php';
require '../src/Item.php';
require '../src/PaymentMethods.php';

$access_token = ''; // exemplo: APP_USR-0000000000000000-000000-a11aa1a111a1a1a1a111111a11a11aa1-00000000

# Configurar preferencia
$preference = new Ezeksoft\MercadoPago\Preference($access_token);
$preference->marketplace_fee = 5; // split de R$ 5,00
$preference->auto_return = "approved";
$preference->notification_url = "https://meusite.com/wook_mercadopago";
$preference->statement_descriptor = 'MINHA EMPRESA';
$preference->external_reference = 'REF958';
$preference->back_urls = [
    "success" => "https://meusite.com/success",
    "failure" => "https://meusite.com/failure",
    "pending" => "https://meusite.com/pending"
];

# Adicionar item
$item = new Ezeksoft\MercadoPago\Item;
$item->id = 'MP'.time().uniqid();
$item->title = "Curso de PHP";
$item->currency_id = "BRL";
$item->picture_url = "https://www.mercadopago.com/org-img/MP3/home/logomp3.gif";
$item->description = "Você vai desenvolver com uma das linguagens mais populares no mercado";
$item->category_id = "service";
$item->quantity = 1;
$item->unit_price = 59.90;
$preference->items[] = $item;

# Configurar metodos de pagamento
$payment_methods = new Ezeksoft\MercadoPago\PaymentMethods;
$payment_methods->installments = 12;
$payment_methods->excluded_payment_types = [['id' => 'ticket', 'id' => 'pix']];
$preference->payment_methods = $payment_methods;

# Gera sua preferencia
$response = $preference->save();

# Link do checkout
echo $preference->get_link();

```