<?php
namespace BadPixel;

use Tonic\Resource,
    Tonic\Response,
    Tonic\ConditionException;


require_once("../../include/core/common.php");
if (is_file("../../include/payments/payments.ws.php")) {
    require_once("../../include/payments/payments.ws.php");

    /**
     * @uri /payments/getlink
     */
    class GetURLPayment extends Resource
    {
        /**
         * @method POST
         * @provides application/json
         * @json
         * @return Tonic\Response
         */
        public function GetURLPayment() {	
    		$ws=new \ws_payments($_GET);
    		$salida=$ws->wsGetURLPayment();
    		return new Response(200, $salida);
        }

        protected function json(){
            $this->before(function ($request) {
                if ($request->contentType == "application/json; charset=utf-8") {
                    $request->data = json_decode($request->data);
                }
            });
            $this->after(function ($response) {
                $response->contentType = "application/json; charset=utf-8";
                if (isset($_GET['jsonp'])) {
                    $response->body = $_GET['jsonp'].'('.json_encode($response->body, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES).');';
                } else {
                    $response->body = json_encode($response->body, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                }
            });
        }
    }
}