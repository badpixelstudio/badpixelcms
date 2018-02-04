<?php

namespace BadPixel;

use Tonic\Resource,
    Tonic\Response,
    Tonic\ConditionException;


require_once("../../include/core/common.php");
require_once("../../include/promomovies/promomovies.ws.php");

/**
 * @uri /promomovies/mytickets
 */
class PromoMoviesMyTickets extends Resource
{
    /**
     * @method GET
     * @provides application/json
     * @json
     * @return Tonic\Response
     */
    public function PromoMoviesMyTickets() {
        $ws=new \ws_promomovies($_GET);
        $salida=$ws->wsPromoMoviesGetMyTickets();
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
 * @uri /promomovies/registerticket
 */
class PromoMoviesRegisterTickets extends Resource
{
    /**
     * @method POST
     * @provides application/json
     * @json
     * @return Tonic\Response
     */
    public function PromoMoviesRegisterTickets() {
        $ws=new \ws_promomovies($_GET);
        $salida=$ws->wsPromoMoviesRegisterTicket();
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
 * @uri /promomovies/deleteticket/:id
 */
class PromoMoviesDeleteTicket extends Resource
{
    /**
     * @method GET
     * @provides application/json
     * @json
     * @return Tonic\Response
     */
    public function PromoMoviesDeleteTicket($id=0) {
        $ws=new \ws_promomovies($_GET);
        $salida=$ws->wsPromoMoviesDeleteTicket($id);
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
 * @uri /promomovies/awards
 */
class PromoMoviesAwards extends Resource
{
    /**
     * @method GET
     * @provides application/json
     * @json
     * @return Tonic\Response
     */
    public function PromoMoviesAwards() {
		$ws=new \ws_promomovies($_GET);
		$salida=$ws->wsPromoMoviesGetAwards();
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
 * @uri /promomovies/mycoupons
 */
class PromoMoviesCoupons extends Resource
{
    /**
     * @method GET
     * @provides application/json
     * @json
     * @return Tonic\Response
     */
    public function PromoMoviesCoupons() {
        $ws=new \ws_promomovies($_GET);
        $salida=$ws->wsPromoMoviesGetCoupons();
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
?>