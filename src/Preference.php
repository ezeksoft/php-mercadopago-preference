<?php 

namespace Ezeksoft\MercadoPago;

class Preference
{

    /**
     * @var String $link    Link gerado para o checkout
     */

    public String $link = '';


    /**
     * Para obter sua chave, acesse https://www.mercadopago.com.br/developers/panel/credentials
     * @var String $access_token    Sua chave de acesso
     */

    public String $access_token = '';


    /**
     * @param String $access_token  Sua chave de acesso
     */
    
    public function __construct(String $access_token)
    {
        $this->access_token = $access_token; // seta chave de acesso
    }


    /**
     * @return String   Link gerado para o checkout
     */

    public function get_link() : String
    {
        return $this->response->init_point ?? '';
    }


    /**
     * @return String|Null   JSON da requisicao
     */

    public function get_payload() : ?String
    {
        return json_encode($this);
    }


    /**
     * verb POST
     * Realiza requisicao para gerar uma preferencia
     * @return Object   Resposta da requisicao
     */

    public function save() : Object
    {
        $payload = $this->get_payload();

        $curl = curl_init('https://api.mercadopago.com/checkout/preferences');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, ["Authorization: Bearer {$this->access_token}", "Content-Type: application/json"]);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $payload);
        $response = curl_exec($curl);
        curl_close($curl);
        $obj = json_decode($response);
        $this->response = $obj;
        return $obj;

    }


    /**
     * @param Closure $callback Executa seu codigo de callback sobre o que fazer apos receber os dados de WebHook   
     */

    public function wook(Closure $callback) : Void
    {
        $json = file_get_contents('php://input');
        $request = json_decode($json);

        $transaction_id = $request->data->id ?? '';

        $url = "https://api.mercadopago.com/v1/payments/$transaction_id";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Accept: */*",
            "Authorization: Bearer {$this->access_token}",
            "Content-Type: application/json", 
            "Connection: keep-alive"
        ]);

        $response = curl_exec($ch);
        curl_close($ch);
        $object = json_decode($response);

        $method = $object->payment_method_id ?? '';
        $status = $object->status ?? '';
        $status_detail = $object->status_detail ?? '';
        $item_0 = $object->additional_info->items[0] ?? '';
        $first_item_id = $item_0->id ?? '';

        $callback(compact('object', 'request', 'method', 'status', 'status_detail', 'first_item_id', 'transaction_id'));
    }
}