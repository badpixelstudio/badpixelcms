<?php

namespace BadPixel;

use Tonic\Resource,
    Tonic\Response,
    Tonic\ConditionException;

if (is_file("../../include/sedeelectronica/sedeelectronica.ws.php")) {
    require_once("../../include/core/common.php");
    require_once("../../include/sedeelectronica/sedeelectronica.ws.php");

    /**
     * @uri /sede/check
     */
    class SedeCheck extends Resource
    {
        /**
         * @method GET
         * @provides application/json; charset=utf-8
         * @json
         * @return Tonic\Response
         */
        public function SedeCheck() {
    		$ws=new \ws_SedeElectronica(null);
    		$salida=$ws->wsCheckActiveWS();
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


    /**
     * @uri /sede/datetime
     */
    class SedeDateTime extends Resource
    {
        /**
         * @method GET
         * @provides application/json; charset=utf-8
         * @json
         * @return Tonic\Response
         */
        public function SedeDateTime() {
            $ws=new \ws_SedeElectronica(null);
            $salida=$ws->wsGetCurrentDateTime();
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


    /**
     * @uri /sede/closedays
     */
    class SedeClosedDays extends Resource
    {
        /**
         * @method GET
         * @provides application/json; charset=utf-8
         * @json
         * @return Tonic\Response
         */
        public function SedeClosedDays() {
            $ws=new \ws_SedeElectronica(null);
            $salida=$ws->wsShowClosedDays();
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


    /**
     * @uri /sede/csv/check/:csv
     */
    class SedeCSVCheck extends Resource
    {
        /**
         * @method GET
         * @provides application/json; charset=utf-8
         * @json
         * @return Tonic\Response
         */
        public function SedeCSVCheck($csv) {
            $ws=new \ws_SedeElectronica(null);
            $salida=$ws->wsCheckCSV($csv);
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

    /**
     * @uri /sede/csv/download/:csv
     */
    class SedeCSVDownload extends Resource
    {
        /**
         * @method GET
         * @provides application/json; charset=utf-8
         */
        public function SedeCSVDownload($csv) {
            $ws=new \ws_SedeElectronica(null);
            $salida=$ws->wsDownloadCSV($csv);
            return new Response(200, $salida);
        }
    }


    /**
     * @uri /sede/receipt/check/:nif/:receipt
     */
    class SedeReceiptCheck extends Resource
    {
        /**
         * @method GET
         * @provides application/json; charset=utf-8
         * @json
         * @return Tonic\Response
         */
        public function SedeReceiptCheck($nif,$receipt) {
            $receipt=str_replace("-", "/", $receipt);
            $ws=new \ws_SedeElectronica(null);
            $salida=$ws->wsCheckReceipt($nif,$receipt);
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

    /**
     * @uri /sede/receipt/download/:nif/:receipt
     */
    class SedeReceiptDownload extends Resource
    {
        /**
         * @method GET
         * @provides application/json; charset=utf-8
         */
        public function SedeReceiptDownload($nif,$receipt) {
            $receipt=str_replace("-", "/", $receipt);
            $ws=new \ws_SedeElectronica(null);
            $salida=$ws->wsDownloadReceipt($nif,$receipt);
            return new Response(200, $salida);
        }
    }


    /**
     * @uri /sede/login/:nif/:passw
     */
    class SedeLogin extends Resource
    {
        /**
         * @method GET
         * @provides application/json; charset=utf-8
         * @json
         * @return Tonic\Response
         */
        public function SedeLogin($nif,$passw) {
            $ws=new \ws_SedeElectronica(null);
            $salida=$ws->wsLoginBasicAuth($nif,$passw);
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

    /**
     * @uri /sede/changepassword/:nif/:newpassw
     * @uri /sede/changepassword/:nif/:newpassw/:oldpassw
     */
    class SedeChangePassw extends Resource
    {
        /**
         * @method GET
         * @provides application/json; charset=utf-8
         * @json
         * @return Tonic\Response
         */
        public function SedeChangePassw($nif,$newPassw) {
            $oldPassw="";
            if (isset($this->params['oldpassw'])) { $oldPassw=$this->params['oldpassw']; }
            $ws=new \ws_SedeElectronica(null);
            $salida=$ws->wsSetLoginData($nif,$oldPassw,$newPassw);
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

    /**
     * @uri /sede/personaldata/:nif
     */
    class SedePersonalData extends Resource
    {
        /**
         * @method GET
         * @provides application/json; charset=utf-8
         * @json
         * @return Tonic\Response
         */
        public function SedePersonalData($nif) {
            $ws=new \ws_SedeElectronica(null);
            $salida=$ws->wsCitizenPersonalData($nif);
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

    /**
     * @uri /sede/files/:nif
     */
    class SedeFilesList extends Resource
    {
        /**
         * @method GET
         * @provides application/json; charset=utf-8
         * @json
         * @return Tonic\Response
         */
        public function SedeFilesList($nif) {
            $ws=new \ws_SedeElectronica(null);
            $salida=$ws->wsCitizenGetExptes($nif);
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

    /**
     * @uri /sede/files/view/:nif/:number
     */
    class SedeFilesView extends Resource
    {
        /**
         * @method GET
         * @provides application/json; charset=utf-8
         * @json
         * @return Tonic\Response
         */
        public function SedeFilesView($nif,$number) {
            $number=str_replace("-", "/", $number);
            $ws=new \ws_SedeElectronica(null);
            $salida=$ws->wsCitizenGetExpteData($nif,$number);
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

    /**
     * @uri /sede/inbox/:nif
     */
    class SedeInbox extends Resource
    {
        /**
         * @method GET
         * @provides application/json; charset=utf-8
         * @json
         * @return Tonic\Response
         */
        public function SedeInbox($nif) {
            $ws=new \ws_SedeElectronica(null);
            $salida=$ws->wsCitizenGetInputs($nif);
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


    /**
     * @uri /sede/outbox/:nif
     */
    class SedeOutbox extends Resource
    {
        /**
         * @method GET
         * @provides application/json; charset=utf-8
         * @json
         * @return Tonic\Response
         */
        public function SedeOutbox($nif) {
            $ws=new \ws_SedeElectronica(null);
            $salida=$ws->wsCitizenGetOutputs($nif);
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


    /**
     * @uri /sede/csv/internal-download/:nif/:csv
     */
    class SedeCSVInternalDownload extends Resource
    {
        /**
         * @method GET
         * @provides application/json; charset=utf-8
         */
        public function SedeCSVInternalDownload($nif,$csv) {
            $ws=new \ws_SedeElectronica(null);
            $salida=$ws->wsCitizenGetCSV($nif,$csv);
            return new Response(200, $salida);
        }
    }

    /**
     * @uri /sede/water/counters/list/:nif
     */
    class SedeWaterCounterList extends Resource
    {
        /**
         * @method GET
         * @provides application/json; charset=utf-8
         * @json
         * @return Tonic\Response
         */
        public function SedeWaterCounterList($nif) {
            $ws=new \ws_SedeElectronica(null);
            $salida=$ws->wsCitizenGetWaterCounterList($nif);
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

    /**
     * @uri /sede/water/counters/receipts/:nif/:counter
     */
    class SedeWaterReceiptsList extends Resource
    {
        /**
         * @method GET
         * @provides application/json; charset=utf-8
         * @json
         * @return Tonic\Response
         */
        public function SedeWaterReceiptsList($nif,$counter) {
            $ws=new \ws_SedeElectronica(null);
            $salida=$ws->wsCitizenGetWaterReceiptsList($nif,$counter);
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

    /**
     * @uri /sede/receipts/list/:nif
     */
    class SedeReceiptsList extends Resource
    {
        /**
         * @method GET
         * @provides application/json; charset=utf-8
         * @json
         * @return Tonic\Response
         */
        public function SedeReceiptsList($nif) {
            $ws=new \ws_SedeElectronica(null);
            $salida=$ws->wsCitizenGetReceiptsList($nif);
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

    /**
     * @uri /sede/receipts/view/:nif/:receipt
     */
    class SedeReceiptsView extends Resource
    {
        /**
         * @method GET
         * @provides application/json; charset=utf-8
         */
        public function SedeCSVInternalDownload($nif,$receipt) {
            $receipt = str_replace("-", "/", $receipt);
            $ws=new \ws_SedeElectronica(null);
            $salida=$ws->wsCitizenGetReceipt($nif,$receipt);
            return new Response(200, $salida);
        }
    }


    /**
     * @uri /sede/census/data/:nif
     */
    class SedeCensusData extends Resource
    {
        /**
         * @method GET
         * @provides application/json; charset=utf-8
         * @json
         * @return Tonic\Response
         */
        public function SedeCensusData($nif) {
            $ws=new \ws_SedeElectronica(null);
            $salida=$ws->wsCitizenCensusData($nif);
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

    /**
     * @uri /sede/census/vp/:nif
     */
    class SedeCensusVP extends Resource
    {
        /**
         * @method GET
         * @provides application/json; charset=utf-8
         */
        public function SedeCensusVP($nif) {
            $ws=new \ws_SedeElectronica(null);
            $salida=$ws->wsGetCensusGetVP($nif);
            return new Response(200, $salida);
        }
    }

    /**
     * @uri /sede/census/vc/:nif
     */
    class SedeCensusVC extends Resource
    {
        /**
         * @method GET
         * @provides application/json; charset=utf-8
         */
        public function SedeCensusVC($nif) {
            $ws=new \ws_SedeElectronica(null);
            $salida=$ws->wsGetCensusGetVC($nif);
            return new Response(200, $salida);
        }
    }
} ?>