<?php

class jscom{
    var $prefix = 'jsc_';
    var $callables  = array();
    var $method     = 'POST';
    var $uri = '';

    function jscom($calls = NULL, $pars = NULL){
        $this->uri = $_SERVER['REQUEST_URI'];
        if(!is_null($calls)) $this->addCallables($calls);
        if(is_array($pars)){
            foreach($pars as $parname=>$par){
                if(!is_null($par)) $this->setPar($parname, $par);
            }
        }
    }
    function noCacheHeader ()
    {
        header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header ("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
        header ("Cache-Control: no-cache, must-revalidate");
        header ("Pragma: no-cache");
    }
    function handler()
    {
        $args = array();
        $mode = '';
        if(!empty($_GET[$this->prefix."mn"])){ $mode = 'get'; $rqst = $_GET; $this->noCacheHeader(); }
        if(!empty($_POST[$this->prefix."mn"])){ $mode = 'post'; $rqst = $_POST; }
        if(empty($mode)){ return; }
        $methodName = $rqst[$this->prefix.'mn'];
        $args       = $rqst[$this->prefix.'args'];
        if(in_array($methodName, $this->callables)){
            $res = call_user_func_array($methodName, $args);
        }else{
            $res = "ERROR: $methodName not callable";
        }
        if(is_array($res)){
            $r = array();
            foreach($res as $k=>$v){ $r[] = "'$v'"; }
            $res = "[".join(', ', $r)."]";
        }
        echo $res;
        exit;
    }
    function genJsCode(){
        ob_start();
        ?>
        var method = "<?php echo $this->method; ?>";

         function createComObj() {
             var co;
            try{
                co = new ActiveXObject("Msxml2.XMLHTTP");
            }catch(err1){
                try{
                    co = new ActiveXObject("Microsoft.XMLHTTP");
                }catch(err2) {
                    co = null;
                }
            }
            if(!co && typeof XMLHttpRequest != "undefined")
                co = new XMLHttpRequest();
            if(!co){
                alert("Could not create connection object.");
            }
            return co;
        }
        // callbackOrId - callback function reference for processing result or html tag id to store result
        function jsCom(methodName, args, callbackOrId) {
            var i, n;
            var comObj;
            var uri;
            var post_data = null;

            uri = "<?php echo $this->uri; ?>";
            arstr = "<?php echo$this->prefix; ?>mn="+escape(methodName);
            for (i = 0; i < args.length; i++){ arstr += "&<?php echo$this->prefix; ?>args[]=" + escape(args[i]); }
            if(method == "GET"){
                uri += ((uri.indexOf("?") == -1) ? "?" : "&" ) + arstr;
                uri += "&<?php echo$this->prefix; ?>x=" + new Date().getTime();
            }else{ post_data = arstr; }

            comObj = createComObj();
            comObj.callbackOrId = callbackOrId;
            comObj.open(method, uri, true);
            if (method == "POST") {
                comObj.setRequestHeader("Method", "POST " + uri + " HTTP/1.1");
                comObj.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            }
            comObj.onreadystatechange = function() {
                if(comObj.readyState != 4) return;
                var data = comObj.responseText;
                if(typeof comObj.callbackOrId == "function"){
                    callbackOrId(data);
                }else if(typeof comObj.callbackOrId == "object"){
                    eval("res="+data);
                    for(i=0; i<comObj.callbackOrId.length; i++){
                        setResult(comObj.callbackOrId[i], res[i])
                    }
                }else if(typeof comObj.callbackOrId != "undefined"){
                    setResult(comObj.callbackOrId, data)
                }else{
                    alert('result from server: '+data);
                }
            }
            comObj.send(post_data);
            delete comObj;
        }
        function setResult(id, data){
            var el = document.getElementById(id) ;
            if(el.tagName == 'INPUT'){ el.value = data; }
            else{ el.innerHTML = data; }
        }

        <?php
        $code = ob_get_contents();
        ob_end_clean();
        return $code;
    }
    function addCallables() {
        $n = func_num_args();
        for ($i = 0; $i < $n; $i++) {
            $a = func_get_arg($i);
            if(is_array($a)) $this->callables = array_merge($this->callables, $a);
            else $this->callables[] = $a;
        }
    }
    function setPar($parName, $value = NULL) {
        switch($parName){
            case"method":
            case"uri":
                $this->{$parName} = $value;
            break;
        }
    }
}
?>
