<?php

namespace BadPixel;

use Tonic\Resource,
    Tonic\Response,
    Tonic\ConditionException;


require_once("../../include/core/common.php");
require_once("../../include/calendar/events.ws.php");

/**
 * @uri /calendar/events
 * @uri /calendar/events/:day
 */
class Calendar extends Resource
{
    /**
     * @method GET
     * @provides application/json
     * @json
     * @return Tonic\Response
     */
    public function Events($day='') {
		$ws=new \ws_events($_GET);
		switch ($day) {
			case 'next':
				$salida=$ws->wsEventsNext();
				break;
			case 'spotted':
				$salida=$ws->wsEventsSpotted();
				break;
			case 'export':
				$salida=$ws->wsEventsExport();
				break;
			default:
				$salida=$ws->wsEventsList($day);
				break;
		}
		
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
 * @uri /calendar/near/:geo
 * @uri /calendar/near/:geo/:radio 
 */
class CalendarGeo extends Resource
{
    /**
     * @method GET
     * @provides application/json
     * @json
     * @return Tonic\Response
     */
    public function ListaCategoria($geo='') {
		$radio="0.003";
		if (isset($this->params['radio'])) { $radio=$this->params['radio']; }
		$ws=new \ws_events($_GET);
		$salida=$ws->wsEventsList("","",$geo,$radio);
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
 * @uri /calendar/event/:id
 */
class EventView extends Resource
{
    /**
     * @method GET
     * @provides application/json
     * @json
     * @return Tonic\Response
     */
    public function VerEvento($id='') {
		$ws=new  \ws_events($_GET);
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

/**
 * @uri /calendar/setalarm
 */
class SetAlarm extends Resource
{
    /**
     * @method POST
     * @provides application/json
     * @json
     * @return Tonic\Response
     */
    public function SetAlarm() {
        $ws=new  \ws_events($_GET);
        $method="";
        $destination="";
        $date="";
        $id=0;
        if (isset($_POST['method'])) { $method=$_POST['method']; }
        if (isset($_POST['destination'])) { $destination=$_POST['destination']; }
        if (isset($_POST['date'])) { $date=$_POST['date']; }
        if (isset($_POST['id'])) { $id=$_POST['id']; }
        $salida=$ws->wsSetAlarm($method,$destination,$date,$id);
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
 * @uri /calendar/checkalarm
 */
class CheckAlarm extends Resource
{
    /**
     * @method POST
     * @provides application/json
     * @json
     * @return Tonic\Response
     */
    public function CheckAlarm() {
        $ws=new  \ws_events($_GET);
        $type="";
        $id=0;
        $method="";
        $destination="";
        if (isset($_POST['type'])) { $type=$_POST['type']; }
        if (isset($_POST['id'])) { $id=$_POST['id']; }
        if (isset($_POST['method'])) { $method=$_POST['method']; }
        if (isset($_POST['destination'])) { $destination=$_POST['destination']; }
        $salida=$ws->wsCheckAlarm($type,$id,$method,$destination);
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
 * @uri /calendar/deletealarm/:id
 */
class DeleteAlarm extends Resource
{
    /**
     * @method GET
     * @provides application/json
     * @json
     * @return Tonic\Response
     */
    public function DeleteAlarm($id=0) {
        $ws=new  \ws_events($_GET);
        $salida=$ws->wsDeleteAlarm($id);
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
 * @uri /calendar/sendevent
 */
class EventSend extends Resource
{
    /**
     * @method POST
     * @provides application/json
     * @json
     * @return Tonic\Response
     */
    public function SendEvent() {
        $ws=new  \ws_events($_GET);
        $salida=$ws->ws_sendevent();
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
 * @uri /calendar/getcities
 */
class EventGetCities extends Resource
{
    /**
     * @method GET
     * @provides application/json
     * @json
     * @return Tonic\Response
     */
    public function GetCities() {
        $ws=new  \ws_events($_GET);
        $salida=$ws->ws_getcities();
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
 * @uri /calendar/ical
 * @uri /calendar/ical/:id
 */
class EventsIcal extends Resource
{
    /**
     * @method GET
     * @return Tonic\Response
     */
    public function Ical($id=1) {
		$ws=new  \ws_events($id);
		$salida=$ws->wsiCal($id);
		return new Response(200, $salida);
    }
}

