<?php

namespace BadPixel;

use Tonic\Resource,
    Tonic\Response,
    Tonic\ConditionException;


require_once("../../include/core/common.php");
require_once("../../include/routes/routes.ws.php");

/**
 * @uri /routes
 * @uri /routes/:offset
 * @uri /routes/:offset/:page  
 */
class RoutesList extends Resource
{
    /**
     * @method GET
     * @provides application/json; charset=utf-8
     * @json
     * @return Tonic\Response
     */
    public function RoutesList() {
		$offset="";
		$page="";
		if (isset($this->params['offset'])) { $offset=$this->params['offset']; }
		if (isset($this->params['page'])) { $page=$this->params['page']; }			
		$ws=new \ws_routes($_GET);
		$salida=$ws->wsRoutesList($page,$offset);
		return new Response(200, $salida);
    }

    /**
     * Condition method to turn output into JSON.
     *
     * This condition sets a before and an after filter for the request and response. The
     * before filter decodes the request body if the request content type is JSON, while the
     * after filter encodes the response body into JSON.
     */
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
 * @uri /route/:id
 */
class RouteView extends Resource
{
    /**
     * @method GET
     * @provides application/json; charset=utf-8
     * @json
     * @return Tonic\Response
     */
    public function RouteView($id='') {
		$ws=new  \ws_routes($_GET);
		$salida=$ws->wsRouteView($id);
		return new Response(200, $salida);
    }

    /**
     * Condition method for above methods.
     *
     * Only allow specific :name parameter to access the method
     */
    protected function only($allowedName)
    {
        if (strtolower($allowedName) != strtolower($this->name)) throw new ConditionException;
    }

    /**
     * Condition method to turn output into JSON.
     *
     * This condition sets a before and an after filter for the request and response. The
     * before filter decodes the request body if the request content type is JSON, while the
     * after filter encodes the response body into JSON.
     */
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