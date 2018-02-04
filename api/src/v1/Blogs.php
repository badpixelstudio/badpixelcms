<?php

namespace BadPixel;

use Tonic\Resource,
    Tonic\Response,
    Tonic\ConditionException;


require_once("../../include/core/common.php");
require_once("../../include/blogs/blogs.ws.php");

/**
 * @uri /blogs/list
 */
class BlogsList extends Resource
{
    /**
     * @method GET
     * @provides application/json
     * @json
     * @return Tonic\Response
     */
    public function Blogs() {
		$ws=new \ws_blog($_GET);
		$salida=$ws->wsBlogList();	
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
 * @uri /blogs/entries
 * @uri /blogs/entries/:id 
 */
class BlogsEntries extends Resource
{
    /**
     * @method GET
     * @provides application/json
     * @json
     * @return Tonic\Response
     */
    public function BlogsEntries($id=0) {
		$radio="0.003";
		$ws=new \ws_blogEntries($_GET);
		$salida=$ws->wsEntriesList($id);
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
 * @uri /blogs/entry/:id 
 */
class BlogsEntry extends Resource
{
    /**
     * @method GET
     * @provides application/json
     * @json
     * @return Tonic\Response
     */
    public function BlogsEntry($id=0) {
        $radio="0.003";
        $ws=new \ws_blogEntries($_GET);
        $salida=$ws->wsEventsView($id);
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