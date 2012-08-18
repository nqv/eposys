<?php
/*
--------------------------------------------------------------------------------
     File:  extend_smarty.php

   Extend:  Smarty
   Author:  Quoc Viet [aFeLiOn]
    Begin:  2006-03-02

   Syntax:  new extend_smarty($template_dir);

  Require:
   + Class: Smarty

  Comment:
--------------------------------------------------------------------------------
*/
class extend_smarty extends Smarty
{
	function extend_smarty($template_dir = './template/')
	{
		$this->Smarty();
		$this->template_dir = $template_dir; 

		$this->compile_dir = SMARTY_DIR.'templates_c/'; 
		$this->config_dir = SMARTY_DIR.'configs/'; 
		$this->plugins_dir = SMARTY_DIR.'plugins/';

		$this->cache_dir = SMARTY_DIR.'cache/';
		$this->caching = true;

		$this->debug_tpl = 'C:/server/htdocs/eposys/include/smarty/debug.tpl';
		//$this->debugging = true; 
   }
}
?>
