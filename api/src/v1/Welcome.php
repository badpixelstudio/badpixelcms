<?php
namespace Tyrell;

use Tonic\Resource,
    Tonic\Response;

require_once("../../include/core/common.php");
require_once("../../include/core/core.ws.php");

/**
 * Introduction resource to the examples.
 *
 * Creates a HTML resource at the root of your Tonic application that explains and links
 * to the other example resources within the Tyrell namespace.
 *
 * @uri /
 */
class Welcome extends Resource
{
    /**
     * Returns the welcome message.
     * @method GET
     */
    public function welcomeMessage(){
        $Core=new \Core(null);
        $site=siteTitle;
        $body = <<<END
<!doctype html>
<title>API</title>
<h1>API de $site</h1>
<p>Este mensaje indica que no has hecho una llamada a un m&eacute;todo v&aacute;lido de la API REST</p>
<p>&nbsp;</p>
<p>Visita el &Aacute;rea de Desarrolladores de $site</a> para aprender a utilizar la API REST.</p>
END;
        return new Response(Response::OK, $body, array(
            'content-type' => 'text/html'
        ));
    }

}