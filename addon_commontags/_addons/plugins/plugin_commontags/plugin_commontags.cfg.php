<?php
$cfg = array(
	"simple_items" => array(
		array(
			"replace" => 
'<div style="border:1px solid #efdb00; padding:5px; background-color:#fffde5;">
	<span style="color:#efdb00; text-decoration:underline">{t_("note")}</span> $1
</div>',
			"search" => array("!<note>(.*?)</note>!si")
		),
		array(
			"replace" => 
'<div style="border:1px solid #1cb02a; padding:5px; background-color:#c2ffc8;">
	<span style="color:#1cb02a; text-decoration:underline">{t_("tip")}</span> $1
</div>',
			"search" => array("!<tip>(.*?)</tip>!si")
		),
		array(
			"replace" => 
'<div style="border:1px solid #FF0000; padding:5px; background-color:#ffd9d9;">
	<span style="color:#FF0000; text-decoration:underline">{t_("warn")}</span> $1
</div>',
			"search" => array("!<warn>(.*?)</warn>!si")
		),
		array(
			"replace" => 
'<div style="margin-left:6em; padding-left:0.3em; border-left:1px solid #485cf4;">
	<span style="color:#485CF4">{t_("example")}</span> $1
</div>',
			"search" => array("!<example>(.*?)</example>!si")
		),
		array(
			"replace" => 
'<div style="border:1px solid #000000; border-left:2px solid #000000; padding:0em; background-color:#e7e7e7; font-size:0.8em; font-family:Courier New, Courier, monospace;">
	<ol style="padding:5px 0px 0px 20px;">
		<pluginbind_renderCode>$1</pluginbind_renderCode>
	</ol>
</div>',
			"search" => array("!<code>(.*?)</code>!si")
		)
	)
);

?>