<?php

namespace BadPixel;

use Tonic\Resource,
    Tonic\Response,
    Tonic\ConditionException;

if (is_file("../../include/videoacta/videoacta.ws.php")) { 
    require_once("../../include/core/common.php");
    require_once("../../include/videoacta/videoacta.ws.php");

    /**
     * @uri /videoacta/list
     * @uri /videoacta/list/:fromdate
     */
    class VideoActaList extends Resource
    {
        /**
         * @method GET
         * @provides application/json; charset=utf-8
         * @json
         * @return Tonic\Response
         */
        public function VideoActaList($fromdate = "") {
            $ws=new \ws_videoacta(null);
            $salida=$ws->wsVideoActaList($fromdate);
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
     * @uri /videoacta/view/:id
     */
    class VideoActaView extends Resource
    {
        /**
         * @method GET
         * @provides application/json; charset=utf-8
         * @json
         * @return Tonic\Response
         */
        public function VideoActaView($id='') {
            $ws=new \ws_videoacta(null);
            $salida=$ws->wsVideoActaView($id);
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
?>