<?php
namespace BadPixel;

use Tonic\Resource,
    Tonic\Response,
    Tonic\ConditionException;


require_once("../../include/core/common.php");
require_once("../../include/singlepages/singlepages.ws.php");

/**
 * @uri /singlepages/list
 * @uri /singlepages/list/:offset
 * @uri /singlepages/list/:offset/:page  
 */
class SinglePagesList extends Resource
{
    /**
     * @method GET
     * @provides application/json
     * @json
     * @return Tonic\Response
     */
    public function SinglePagesList($day='') {
		$offset="";
		$page="";
		if (isset($this->params['offset'])) { $offset=$this->params['offset']; }
		if (isset($this->params['page'])) { $page=$this->params['page']; }			
		$ws=new \ws_singlepages($_GET);
		$salida=$ws->wsSinglePagesList($page,$offset);
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
 * @uri /singlepages/view/:id
 */
class SinglePagesView extends Resource
{
    /**
     * @method GET
     * @provides application/json
     * @json
     * @return Tonic\Response
     */
    public function SinglePagesView($id='') {
		$ws=new  \ws_singlepages($_GET);
		$salida=$ws->wsSinglePagesView($id);
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