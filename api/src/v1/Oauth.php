<?php

namespace BadPixel;

use Tonic\Resource,
    Tonic\Response,
    Tonic\ConditionException;


require_once("../../include/core/common.php");
require_once("../../include/apps/apps.class.php");
require_once("../../include/users/users.ws.php");

/**
 * @uri /oauth/geturllogin/:consumerkey/:consumertoken
 */
class UserOauthURLLogin extends Resource {
    /**
     * @method GET
     * @provides application/json
     * @json
     * @return Tonic\Response
     */
    public function UserOauthURLLogin($consumerkey='',$consumertoken='',$username="",$password="") {
		if (isset($_GET['consumer_key'])) { $consumerkey=$_GET['consumer_key']; }
		if (isset($_GET['consumer_token'])) { $consumertoken=$_GET['consumer_token']; }
		$ws=new  \ws_user($_GET);
		$salida=$ws->ws_oauthlink($consumerkey,$consumertoken);
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
 * @uri /oauth/getaccesstoken
 */
class GetAccessToken extends Resource {
    /**
     * @method POST
     * @provides application/json
     * @json
     * @return Tonic\Response
     */
    public function GetAccessToken() {
        $consumerkey="";
        $consumertoken="";
        $username="";
        $passw="";
        $devicetype="";
        $deviceid="";
        if (isset($_POST['consumerkey'])) { $consumerkey=$_POST['consumerkey']; }
        if (isset($_POST['consumertoken'])) { $consumertoken=$_POST['consumertoken']; } 
        if (isset($_POST['username'])) { $username=$_POST['username']; }    
        if (isset($_POST['password'])) { $passw=$_POST['password']; }
        if (isset($_POST['devicetype'])) { $devicetype=$_POST['devicetype']; }
        if (isset($_POST['deviceid'])) { $deviceid=$_POST['deviceid']; }
        $ws=new \Apps($_GET);
        $salida=$ws->GetAccessToken($consumerkey,$consumertoken,$username,$passw,$devicetype,$deviceid);
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