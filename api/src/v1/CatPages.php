<?php

namespace BadPixel;

use Tonic\Resource,
    Tonic\Response,
    Tonic\ConditionException;

if (is_file("../../include/catpages/catspages.ws.php")) {
    require_once("../../include/core/common.php");
    require_once("../../include/catpages/catspages.ws.php");

    /**
     * @uri /catpages/cats/list
     */
    class CategoryList extends Resource
    {
        /**
         * @method GET
         * @provides application/json; charset=utf-8
         * @json
         * @return Tonic\Response
         */
        public function CategoryList() {
            $ws=new \ws_cats(null);
            $l = 0;
            $salida=$ws->wsCatsList($l);
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
     * @uri /catpages/cats/view/:id
     */
    class CatView extends Resource
    {
        /**
         * @method GET
         * @provides application/json; charset=utf-8
         * @json
         * @return Tonic\Response
         */
        public function CatView($id='') {
            $ws=new \ws_cats(null);
            $salida=$ws->wsCatView($id);
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
     * @uri /catpages/pages/list/:id
     * @uri /catpages/pages/list/:id/:fromdate
     * @uri /catpages/pages/list/:id/:offset/:page
     * @uri /catpages/pages/list/:id/:fromdate/:offset/:page
     */
    class PagesList extends Resource
    {
        /**
         * @method GET
         * @provides application/json; charset=utf-8
         * @json
         * @return Tonic\Response
         */
        public function PagesList($id="") {
    		$offset="";
    		$page="";
            $fromdate = "";
    		if (isset($this->params['offset'])) { $offset=$this->params['offset']; }
    		if (isset($this->params['page'])) { $page=$this->params['page']; }
            if (isset($this->params['fromdate'])) { $fromdate=$this->params['fromdate']; }
    		$ws=new \ws_catpage(null);
    		$salida=$ws->wsCatPageList($id,$page,$offset,$fromdate);
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
     * @uri /catpages/pages/view/:id
     */
    class PageView extends Resource
    {
        /**
         * @method GET
         * @provides application/json; charset=utf-8
         * @json
         * @return Tonic\Response
         */
        public function VerPagina($id='') {
    		$ws=new \ws_catpage(null);
    		$salida=$ws->wsCatPageView($id);
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