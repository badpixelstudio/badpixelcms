/*! JSLoader v. 1.0 | (cc) 2017 Badpixel Studios | badpixel.es
Utility to load Javascript resources without influencing the upload speed.
Improves WPO performance

Use:
Load this script last and always after all other scripts:
<script id="jsloader" src="../path/to/jsloader.js"></script>

Add scripts inline with tag: data-jsloader="scripts url separated by commas".

Replace the type="text/javascript" tag with type="text/jsloader", or add 
it if it does not exist in the page's scripts.

How does it work:
JSLoader looks for all the scripts of the document whose type is "text/jsloader"
and performs the sequential loading of all of them. 
Finally, it loads the inline type scripts, and executes them.

Notice:
It should be noted that it does not influence the load of defer or async resources. 
Therefore addons that use these delayed loading methods do not need to be processed 
by JSLoader, unless they require the preloading of frameworks, such as JQuery, if 
these scripts are loaded with JSLoader

This script is delivered as-is and without any warranty or additional support

Credits:
Programming by Israel Garcia for Badpixel Studios

License: 
Creative Commons CC-BY-SA 4.0 https://creativecommons.org/licenses/by-sa/4.0/
 */

var loader_scripts=[];
var tagparam=document.getElementById('jsloader');
tagparam=tagparam.getAttribute('data-jsloader');
if (tagparam!=null) {
    tagparam=tagparam.trim();
    tagparam=tagparam.split(",");
    tagparam.forEach(function (item) {
        var add=item.trim();
        loader_scripts.push(add);
    });
}
var items=document.getElementsByTagName('script');
var len=items.length;
if (len>0) {
    for(var i=0; i<len; i++) {
        if (items[i].getAttribute('type')=='text/jsloader') {
            var src=items[i].getAttribute("src");
            if (src!=null) {
                src=src.trim();
                loader_scripts.push(src);
                items[i].setAttribute('type','text/jsload');
            }
        }
    }
}

runLoader();

function runLoader() {
    if(loader_scripts.length>0) {
        var script = document.createElement("script")
        script.type = "text/javascript";
        if (script.readyState){  //IE
            script.onreadystatechange = function(){
                console.log(script.readyState);
            if (script.readyState == "loaded" ||
                script.readyState == "complete"){
                    script.onreadystatechange = null;
                    loader_scripts.splice(0,1);
                    runLoader();
                }
            };
        } else {  //Others
            script.onload = function(){
                loader_scripts.splice(0,1);
                runLoader();
            };
        }
        script.src = loader_scripts[0];
        document.getElementsByTagName("head")[0].appendChild(script);
    } else {
        var inline=document.getElementsByTagName('script');
        var len=inline.length;
        for(var i=0; i<len; i++) {
            if (inline[i].getAttribute('type')=='text/jsloader') {
                var code=inline[i].text;
                var script = document.createElement("script")
                //script.type = "text/javascript";
                script.text=code;
                inline[i].text="";
                inline[i].setAttribute('type','text/jsload');
                document.getElementsByTagName("head")[0].appendChild(script);
            }
        }
    }
}