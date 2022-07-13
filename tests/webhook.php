<?php 

require '../src/Preference.php';
require '../src/Item.php';
require '../src/PaymentMethods.php';

$access_token = ''; // exemplo: APP_USR-0000000000000000-000000-a11aa1a111a1a1a1a111111a11a11aa1-00000000

$preference = new Ezeksoft\MercadoPago\Preference($access_token);
$preference->wook(function($args) {
    extract($args); // $object, $request, $method, $status, $status_detail, $first_item_id, $transaction_id

    # caso voce esteja recebendo um webhook de compra aprovada
    if ($status == 'approved')
    {
        require 'Transaction.php'; // isso voce pode apagar, eh um exemplo de consulta ao banco de dados

        # localiza no banco de dados o pedido que tem o id do item 0 da Preference
        $transaction = Transaction::whereRaw("transaction_id = '$first_item_id'")->first();
        $transaction->status = $status;
        $transaction->save();

        // TODO: ...
    }

});