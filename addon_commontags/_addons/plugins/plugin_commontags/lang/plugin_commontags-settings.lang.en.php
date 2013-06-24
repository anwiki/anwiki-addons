<?php
$lang = array (
	"setting_simple_items_label" => "Simple tags",
	"setting_simple_items_single" => "simple tag",
	"setting_simple_items_plural" => "simple tags",
	"setting_simple_items_explain" => 'Simple tags will replace searched expressions by a simple replacement string (like preg_replace_all).<br/>Use $1, $2... for retrieving matched values.<br/>Use {t_("...")} and {g_("...")} for retrieving translations.',
	"setting_simple_items_search_label" => "Search expressions",
	"setting_simple_items_search_single" => "search expression",
	"setting_simple_items_search_plural" => "search expressions",
	"setting_simple_items_replace_label" => "Replace with",
	
	"setting_callback_items_label" => "Callbacks tags",
	"setting_callback_items_single" => "callbacks tag",
	"setting_callback_items_plural" => "callbacks tags",
	"setting_callback_items_explain" => 'Callbacks tags will replace searched expressions by dynamic PHP code (PHP eval must be enabled).<br/>Use $a[1], $a[2]... for retrieving matched values.<br/>Use the PHP variable $me as a reference to the plugin instance.',
	"setting_callback_items_search_label" => "Search expressions",
	"setting_callback_items_search_single" => "search expression",
	"setting_callback_items_search_multiple" => "search expressions",
	"setting_callback_items_replace_label" => "Replace with",
	
);
?>