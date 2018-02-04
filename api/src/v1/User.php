<?php
namespace BadPixel;

use Tonic\Resource,
    Tonic\Response,
    Tonic\ConditionException;


require_once("../../include/core/common.php");
require_once("../../include/users/users.ws.php");

/**
 * @uri /user/me
 */
class UserMe extends Resource
{
    /**
     * @method GET
     * @provides application/json
     * @json
     * @return Tonic\Response
     */
    public function UserMe() {	
		$ws=new \ws_user($_GET);
		$salida=$ws->ws_me();
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
 * @uri /user/sendactivation
 * @uri /user/sendactivation/:lang
 */
class UserActivate extends Resource
{
    /**
     * @method POST
     * @provides application/json
     * @json
     * @return Tonic\Response
     */
    public function UserActivate() {
        $ws=new  \ws_user($_GET);
        $lang="";
        if (isset($this->params['lang'])) { $lang=$this->params['lang']; }
        $salida=$ws->wsSendActivation($_POST['email'],$lang);
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
 * @uri /user/changepassw
 * @uri /user/changepassw/:lang
 */
class UserChangePassw extends Resource
{
    /**
     * @method POST
     * @provides application/json
     * @json
     * @return Tonic\Response
     */
    public function UserChangePassw() {
        $ws=new  \ws_user($_GET);
        $lang="";
        if (isset($this->params['lang'])) { $lang=$this->params['lang']; }
        $salida=$ws->wsSendPassword($_POST['email'],$lang);
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
 * @uri /user/postedit
 * @uri /user/postedit/:lang
 */
class UserEdit extends Resource{
    /**
     * @method POST
     * @provides application/json
     * @json
     * @return Tonic\Response
     */
    public function UserEdit() {
        $ws=new  \ws_user($_GET);
        $lang="";
        if (isset($this->params['lang'])) { $lang=$this->params['lang']; }
        $salida=$ws->wsPostEdit($_POST,$lang);
        return new Response(200, $salida);
    }
    protected function only($allowedName){
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
 * @uri /user/createaccount
 * @uri /user/createaccount/:lang
 */
class UserCreate extends Resource{
    /**
     * @method POST
     * @provides application/json
     * @json
     * @return Tonic\Response
     */
    public function UserCreate() {
        $ws=new  \ws_user($_GET);
        $lang="";
        if (isset($this->params['lang'])) { $lang=$this->params['lang']; }
        $salida=$ws->wsCreateAccount($_POST,$lang);
        return new Response(200, $salida);
    }
    protected function only($allowedName) {
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
 * @uri /user/socialaccount
 */
class UserSocialAccount extends Resource{
    /**
     * @method POST
     * @provides application/json
     * @json
     * @return Tonic\Response
     */
    public function UserSocialAccount() {
        $ws=new  \ws_user($_GET);
        $salida=$ws->wsLoginSocial($_POST);
        return new Response(200, $salida);
    }
    protected function only($allowedName) {
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
 * @uri /user/checkfield
 */
class UserCheckFields extends Resource{
    /**
     * @method POST
     * @provides application/json
     * @json
     * @return Tonic\Response
     */
    public function UserCheckFields() {
        $ws=new  \ws_user($_GET);
        $salida=$ws->wsCheckField($_POST['id'],$_POST['field'],$_POST['value']);
        return new Response(200, $salida);
    }
    protected function only($allowedName){
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
 * @uri /user/alarms
 */
class UserAlarms extends Resource{
    /**
     * @method GET
     * @provides application/json
     * @json
     * @return Tonic\Response
     */
    public function UserAlarms() {
		$ws=new  \ws_user($_GET);
		$salida=$ws->ws_alarms();
		return new Response(200, $salida);
    }
    protected function only($allowedName){
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
 * @uri /user/likes
 */
class UserLikes extends Resource{
    /**
     * @method GET
     * @provides application/json
     * @json
     * @return Tonic\Response
     */
    public function UserLikes() {
		$ws=new  \ws_user($_GET);
		$salida=$ws->ws_likes('+');
		return new Response(200, $salida);
    }
    protected function only($allowedName){
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
 * @uri /user/nolikes
 */
class UserNoLikes extends Resource {
    /**
     * @method GET
     * @provides application/json
     * @json
     * @return Tonic\Response
     */
    public function UserNoLikes() {
		$ws=new  \ws_user($_GET);
		$salida=$ws->ws_likes('-');
		return new Response(200, $salida);
    }
    protected function only($allowedName){
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
 * @uri /user/sendlike
 */
class UserSendLike extends Resource{
    /**
     * @method POST
     * @provides application/json
     * @json
     * @return Tonic\Response
     */
    public function UserSendLike() {
        $ws=new  \ws_user($_GET);
        $salida=$ws->ws_sendlike($_POST['url'],$_POST['type']);
        return new Response(200, $salida);
    }
    protected function only($allowedName){
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
 * @uri /user/sendcomment
 */
class UserSendComment extends Resource{
    /**
     * @method POST
     * @provides application/json
     * @json
     * @return Tonic\Response
     */
    public function UserSendLike() {
        $ws=new  \ws_user($_GET);
        $salida=$ws->ws_sendcomment();
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
 * @uri /user/login
 */
class UserLogin extends Resource{
    /**
     * @method POST
     * @provides application/json
     * @json
     * @return Tonic\Response
     */
    public function UserLogin() {
        $ws=new  \ws_userlogin($_GET);
        $username="";
        $password="";
        $devicetype="";
        $deviceid="";
        if (isset($_POST['username'])) { $username=$_POST['username']; }
        if (isset($_POST['password'])) { $password=$_POST['password']; }
        if (isset($_POST['devicetype'])) { $devicetype=$_POST['devicetype']; }
        if (isset($_POST['deviceid'])) { $deviceid=$_POST['deviceid']; }
        $salida=$ws->wsLogin($username,$password,$devicetype,$deviceid);
        return new Response(200, $salida);
    }
    protected function only($allowedName){
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
 * @uri /user/registerdevice
 */
class UserRegisterDevice extends Resource{
    /**
     * @method POST
     * @provides application/json
     * @json
     * @return Tonic\Response
     */
    public function UserRegisterDevice() {
        $ws=new  \ws_userlogin($_GET);
        $userid=0;
        $devicetype="";
        $deviceid="";
        if (isset($_POST['userid'])) { $userid=$_POST['userid']; }
        if (isset($_POST['devicetype'])) { $devicetype=$_POST['devicetype']; }
        if (isset($_POST['deviceid'])) { $deviceid=$_POST['deviceid']; }
        $salida=$ws->wsRegisterDevice($userid,$devicetype,$deviceid);
        return new Response(200, $salida);
    }
    protected function only($allowedName){
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
 * @uri /user/unregisterdevice/:iddevice
 */
class UserUnregisterDevice extends Resource {
    /**
     * @method GET
     * @provides application/json
     * @json
     * @return Tonic\Response
     */
    public function UserUnregisterDevice($iddevice='') {
        $ws=new  \ws_userlogin($_GET);
        $salida=$ws->wsUnregisterDevice($iddevice);
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