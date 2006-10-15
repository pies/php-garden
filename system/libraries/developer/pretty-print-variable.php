<?php

class GardenLog
{
	function error (Error $error, $toScreen=false) {
		self::toFile($error);
		self::toScreen($error);
		self::toDatabase($error);
	}
	
	function toFile(Error $error) {
		extract($error->toArray());
		$path = DIR_ROOT.'/logs/'.date('y-m-d').'_'.ucfirst(strtolower($signal)).'.log';
		$msg  = date('H:i:s')." {$component} {$signal} at {$place} - {$title}\n";
		return ($log = fopen($path, 'a+')) && 
		       fwrite($log, $msg) && 
		       fclose($log);
	}

	function toScreen(Error $error) {
		extract($error->toArray());
		$frmt  = formatter('<li>%s <em>in</em> %s</li>');
		$msg   = date('H:i:s')." {$component} {$signal} at {$place} - {$title}\n";
		$trace = join(array_map($frmt, format_trace($error->getTrace()) ));
		return 
<<<HTML
<style type="text/css">
.Debug { font-family:Calibri,Arial,sans-serif; border:1px solid #D86; padding:0 0 .7em .5em; margin:.5em 0 }
.Debug PRE { font-family:Consolas,Courier,monospace; margin:0; }
.Debug SMALL { color:#777; }
.Debug H1 { font-size:1.3em; margin:5px 0 0 0; color: #F75; }
.Debug H1 SMALL { font-size:.8em; color: #777; }
.Debug H1 SMALL STRONG { color: #000; }
.Debug H2 { font-size:1em; background-color:#EE9; margin:.5em 0 0 -8px; padding:.3em 10px; }
.Debug H3 { font-size:1em; margin:0 -8px; padding:0 10px .2em 10px; }
.Debug H4 { font-size:.9em; margin:7px 0 10px 1px; float:left; }
.Debug UL { font-size:1em; margin:5px 5px 0 0; padding-left:1.7em; list-style:none; }
</style>
<div class="Debug">
<h1>Unhandled exception
<small>at $place</small></h1>
<h2>$msg</h2>
<h4>Via:</h4>
<ul>$trace</ul>
</div>
HTML;
	}

	function toDatabase(Error $error) {
		$data = $error->toArray();
		$sql = 'INSERT INTO messages ('.join(',',array_keys($data)).') VALUES ('.join(','.$data).')';
		return ($db = sqlite_open(DIR_ROOT.'/data/system.db')) &&
		       sqlite_query($db, $sql) &&
		       sqlite_close($db);
	}
}

/**
 * Formats and simplifies the call stack backtrace log
 *
 * @param  array $backtrace The call stack to simplify
 * @return array            Formatted and simplified backtrace log
 */

function debug ($arg1=null, $arg2=null /* ... */) {
	$vars = func_get_args();
	$bt = array_reverse(debug_backtrace($arg1));

	$c = $bt[count($bt)-1];
	$call_line = str_replace(DIR_ROOT, '', $c['file']).':'.$c['line'];
	
	print <<<CSS
<style type="text/css">
.Debug     { font-family:sans-serif; border:2px solid #DDD; background-color:#DDD; padding:5px; font-size:13px; }
.Debug H3  { font-size:13px; margin:0 0 10px; padding:0; background-color:#DDD; color:#000; }
.Debug P   { margin:10px 0 0 0; }
.Debug PRE { font-size:14px; padding:3px 4px; margin:5px 0 0 0; background-color:#FFF; font-family:Consolas, monospace; }
</style>
CSS;
	print "<div class=\"Debug\">\n<h3>Debug at $call_line</h3>\n";

	foreach ($vars as $var) {
		print "<pre>".nl2br(to_string($var, true)).'</pre>';
	}
	
	$c = 0;
	print "<p>\n";
	foreach ($bt as $step) {
		$file = str_replace(DIR_ROOT, '', $step['file']);
		$line = $step['line'];
		$call = "<strong>".$step['function'].'</strong>('.join(', ', array_map('to_string', $step['args'])).')';
		print "#$c <strong>$file</strong> called $call at line <strong>$line</strong><br/>\n";
		$c++;
	}
	print '</p>';
	
	print "</div>";
}


/* Build a debug window */
function system_window ($title, $content, $color='#CDE') {
	if (!is_array($content)) $content = array($content);

	$rnd = 'debug_'.md5(microtime());
	$OUT = '';

	if (!defined('DEBUG_JS_IN_PAGE')) {
		define('DEBUG_JS_IN_PAGE', true);
		$OUT .= <<<EOT
<a href="#" id="DEBUG_BUTTON" onclick="DEBUG.SA(event);return false" style="position:absolute;top:10px;right:10px;background-color:#D44;border:3px solid #FFF;padding:2px 4px;text-decoration:none;font-family:Calibri,sans-serif;font-size:12px;font-weight:bold;color:#FFF">SHOW DEBUG (<span id="DEBUG_COUNTER"></span>)</a>
<script type="text/javascript">
var __ = {
	ID:   function(id){ return typeof(id)=='string'? document.getElementById(id): id; },
	RM:   function(id){ var N = __.ID(id); N.parentNode.removeChild(N); },
	TAG:  function(tag, ctx){ return (__.ID(ctx)||document).getElementsByTagName(tag); },
	SHOW: function(id){ var N=__.ID(id); N.style.display=''; return N; },
	HIDE: function(id){ var N=__.ID(id); N.style.display='none'; return N; }
};
var DEBUG = {
	WINDOWS: 0,
	SA: function (event) {
		var divs = __.TAG('div');
		for (var ii=0; ii<divs.length; ++ii) {
			var DIV = divs[ii];
			if ('SystemWindow' == DIV.className) __.SHOW(DIV);
		}
		var link = event? event.target||event.srcElement: __.ID('DEBUG_BUTTON');
		link.onclick=DEBUG.HA;
		link.innerHTML = link.innerHTML.replace(/SHOW/, 'HIDE');
	},
	HA: function (event) {
		var divs = __.TAG('div');
		for (var ii=0; ii<divs.length; ++ii) {
			var DIV = divs[ii];
			if ('SystemWindow'==DIV.className) __.HIDE(DIV);
		}
		var link = (event.target||event.srcElement);
		link.onclick=DEBUG.SA;
		link.innerHTML = link.innerHTML.replace(/HIDE/, 'SHOW');
	},
	ADD: function (id,hide) {
		this.WINDOWS++;
		this.REFRESH();
		if (hide) __.HIDE(id);
	},
	REFRESH: function () {
		var C = __.ID('DEBUG_COUNTER');
		if (this.WINDOWS) C.innerHTML = this.WINDOWS; else __.HIDE(C.parentNode);
	},
	RM: function (link) {
		__.RM(link.parentNode.parentNode);
		this.WINDOWS--;
		this.REFRESH();
		return false;
	}
}
EOT;

		if (DEBUG) $OUT .= "DEBUG.SA()";
		$OUT .= "</script>";
	}

	$text_color = color_mix($color, '#333', .3);
	$btn_color  = color_mix($color, '#000', .6);

	$OUT.="<div class=\"SystemWindow\" id='$rnd' style='font-family:Calibri,sans-serif;padding:0;margin:.2em .5em;*height:1%;display:block;*display:inline;border:4px solid $color;background-color:#FFF;padding:0;clear:both'>";
	$OUT.="<h3 style='*white-space:nowrap;margin:0;color:$text_color;font-size:15px;padding:0 0 3px 1px;font-weight:bold;*float:left;clear:both;background-color:$color'>";
	$OUT.="<span onmousedown=\"return DEBUG.RM(event.target||event.src);\" style=\"font-size:19px;text-decoration:none;font-weight:bold;color:$btn_color;padding:0 3px;line-height:15px;margin:0 5px 0 0;cursor:pointer;max-height:200px;overflow:auto\">&times;</span>$title";
	$OUT.="</h3>\n";

	foreach ($content as $var) {
		$OUT.="<div style='display:table;background-color:#FFF;font-family:Consolas,monospace;font-size:14px;padding:2px 2px 0;overflow:auto;max-height:200px;font-weight:normal'>\n$var\n</div>";
	}
	$OUT.="</div>";
	$hide = DEBUG || $color=='#CDE'?'false':'true';
	$OUT.="<script type=\"text/javascript\"><!--\nDEBUG.ADD('$rnd',$hide);// --></script>";

	return $OUT;
}

function debug ($var) {
	$traces = debug_backtrace();
	$places = array();
	foreach ($traces as $trace) {
		$places[] = str_replace(array(DIR_ROOT,'\\'), array('','/'), $trace['file'].':'.$trace['line']);
	}
	$place = str_replace(':', ', line ', array_shift($places));

	$trace = debug_backtrace();
	$trace = array_shift($trace);
	$place = str_replace(array(DIR_ROOT,'\\'), array('','/'), $trace['file'].', line '.$trace['line']);
	$vars = array();
	foreach (func_get_args() as $var) {
		$vars[] = nl2br(toString($var));
	}

	$places = join(" ", $places);
	print system_window("<u>DEBUG</u> at <span title=\"$places\">$place</span>", $vars, '#CDE');
	return true;
}
?>