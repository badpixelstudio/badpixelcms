<?php

namespace BadPixel;

use Tonic\Resource,
    Tonic\Response,
    Tonic\ConditionException;

if (is_file("../../include/alerts/alerts.ws.php")) {
    require_once("../../include/core/common.php");
    require_once("../../include/alerts/alerts.ws.php");


    /**
     * @uri /alerts/types
     */
    class AlertsTypesList extends Resource
    {
        /**
         * @method GET
         * @provides application/json; charset=utf-8
         * @json
         * @return Tonic\Response
         */
        public function AlertsTypesList() {
            $ws=new \ws_alerts(null);
            $salida=$ws->wsAlertsTypes();
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
     * @uri /alerts/list
     * @uri /alerts/list/:fromdate
     * @uri /alerts/list/:offset/:page
     * @uri /alerts/list/:fromdate/:offset/:page
     */
    class AlertsList extends Resource
    {
        /**
         * @method GET
         * @provides application/json; charset=utf-8
         * @json
         * @return Tonic\Response
         */
        public function AlertsList() {
    		$offset="";
    		$page="";
            $fromdate="";
    		if (isset($this->params['offset'])) { $offset=$this->params['offset']; }
    		if (isset($this->params['page'])) { $page=$this->params['page']; }
            if (isset($this->params['fromdate'])) { $fromdate=$this->params['fromdate']; }
    		$ws=new \ws_alerts(null);
    		$salida=$ws->wsAlertsList($page,$offset,$fromdate);
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
     * @uri /alerts/view/:id
     */
    class AlertsView extends Resource
    {
        /**
         * @method GET
         * @provides application/json; charset=utf-8
         * @json
         * @return Tonic\Response
         */
        public function AlertsView($id='') {
    		$ws=new \ws_alerts(null);
    		$salida=$ws->wsAlertsView($id);
    		return new Response(200, $salida);
        }
        protected function only($allowedName)
        {
            if (strtolower($allowedName) != strtolower($this->name)) throw new ConditionException;
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
     * @uri /alerts/register/:platform/:uid
     * @uri /alerts/register/:platform/:uid/:cats
     */
    class AlertsRegister extends Resource
    {
        /**
         * @method GET
         * @provides application/json
         * @json
         * @return Tonic\Response
         */
        public function AlertsRegister($platform,$uid) {
            $cats="{{all}}";
            if (isset($this->params['cats'])) { $cats=$this->params['cats']; }
            $ws=new \ws_alerts($_GET);
            $salida=$ws->wsAlertsSubscribe($platform,$uid,$cats);
            return new Response(200, $salida);
        }
         protected function json(){
            $this->before(function ($request) {
                if ($request->contentType == "application/json") {
                    $request->data = json_decode($request->data);
                }
            });
            $this->after(function ($response) {
                $response->contentType = "application/json";
                if (isset($_GET['jsonp'])) {
                    $response->body = $_GET['jsonp'].'('.json_encode($response->body, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES).');';
                } else {
                    $response->body = json_encode($response->body, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                }
            });
        }
    }

    /**
     * @uri /alerts/unregister/:platform/:uid
     * @uri /alerts/unregister/:platform/:uid/:cats
     */
    class AlertsUnRegister extends Resource
    {
        /**
         * @method GET
         * @provides application/json
         * @json
         * @return Tonic\Response
         */
        public function AlertsUnRegister($platform,$uid) {
            $cats="{{all}}";
            if (isset($this->params['cats'])) { $cats=$this->params['cats']; }
            $ws=new \ws_alerts($_GET);
            $salida=$ws->wsAlertsUnSubscribe($platform,$uid,$cats);
            return new Response(200, $salida);
        }
         protected function json(){
            $this->before(function ($request) {
                if ($request->contentType == "application/json") {
                    $request->data = json_decode($request->data);
                }
            });
            $this->after(function ($response) {
                $response->contentType = "application/json";
                if (isset($_GET['jsonp'])) {
                    $response->body = $_GET['jsonp'].'('.json_encode($response->body, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES).');';
                } else {
                    $response->body = json_encode($response->body, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                }
            });
        }
    }
}
?>