<?php
/**
 * Zikula Application Framework
 *
 * @copyright (c) 2002, Zikula Development Team
 * @link http://www.zikula.org
 * @version $Id: quote.php 358 2009-11-11 13:46:21Z herr.vorragend $
 * @license GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * @package Zikula_Value_Addons
 * @subpackage Quotes
 */

/**
 * Init quotes block
 * @author The Zikula Development Team <erik@slooff.com>
 * @link http://www.zikula.org
 */
function Quotes_quoteblock_init()
{
    // Security
    SecurityUtil::registerPermissionSchema('Quotes:Quoteblock:', 'Block title::');
}

/**
 * Return Quotes blockinfo array
 * @author The Zikula Development Team <erik@slooff.com>
 * @link http://www.zikula.org
 * @return array
 */
function Quotes_quoteblock_info()
{
    $dom = ZLanguage::getModuleDomain('Quotes');

    return array('module' => 'Quotes',
                 'text_type' => __('Quote', $dom),
                 'text_type_long' => __('Random quote block', $dom),
                 'allow_multiple' => true,
                 'form_content' => false,
                 'form_refresh' => false,
                 'show_preview'    => true,
                 'admin_tableless' => true);
}

/**
 * Display quotes block
 * @author The Zikula Development Team <erik@slooff.com>
 * @link http://www.zikula.org
 * @param 'blockinfo' blockinfo array
 * @return HTML String
 */
function quotes_quoteblock_display($blockinfo)
{
    // security check
    if (!SecurityUtil::checkPermission('Quotes:Quoteblock:', "$blockinfo[title]::", ACCESS_READ)) {
        return;
    }
    // check if the quotes module is available
    if (!pnModAvailable('Quotes')) {
        return;
    }
    // Get current content
    $vars = pnBlockVarsFromContent($blockinfo['content']);
    // filter desplay by hour of the day - N.Petkov
	$a_datetime = getdate();
	if (isset($vars['hourfrom']) and $vars['hourfrom']>-1 and $a_datetime["hours"]<$vars['hourfrom']) return "";
	if (isset($vars['hourto']) and $vars['hourto']>-1 and $a_datetime["hours"]>$vars['hourto']) return "";
	if (isset($vars['wdayfrom']) and $vars['wdayfrom']>-1 and $a_datetime["wday"]<$vars['wdayfrom']) return "";
	if (isset($vars['wdayto']) and $vars['wdayto']>-1 and $a_datetime["wday"]>$vars['wdayto']) return "";
	if (isset($vars['mdayfrom']) and $vars['mdayfrom']>-1 and $a_datetime["mday"]<$vars['mdayfrom']) return "";
	if (isset($vars['mdayto']) and $vars['mdayto']>-1 and $a_datetime["mday"]>$vars['mdayto']) return "";
	if (isset($vars['monfrom']) and $vars['monfrom']>-1 and $a_datetime["mon"]<$vars['monfrom']) return "";
	if (isset($vars['monto']) and $vars['monto']>-1 and $a_datetime["mon"]>$vars['monto']) return "";
    // Setting of the Defaults
    if (!isset($vars['category'])) {
        $vars['category'] = null;
    }
	// Implementation cached content
	$enable_cache = true;
	$write_to_cache = false;	# flag
	$cache_time = 120; # seconds
	if (isset($vars['cache_time'])) $cache_time = $vars['cache_time'];
	$content = "";
	if ($enable_cache and $cache_time>0) {
		$cachefilestem = 'quote_' . $blockinfo['bid'];
	    $cachedir = pnConfigGetVar('temp');
	    if (StringUtil::right($cachedir, 1)<>'/') $cachedir .= '/';
	    if (isset($vars['cache_dir']) and !empty($vars['cache_dir'])) $cachedir .= $vars['cache_dir'];
	    else $cachedir .= 'any_cache';
	    $cachefile = $cachedir .'/'. $cachefilestem;
	   // attempt to load from cache
		if (file_exists($cachefile)) {
			$file_time = filectime($cachefile);
			$now = time();
			$diff = ($now - $file_time);
			if ($diff <= $cache_time) {
			    $content = file_get_contents($cachefile);
			}
		}
		if (empty($content)) $write_to_cache = true; # not loaded, flag to write to cache later
	}
	if (empty($content)) {
	    // Create output object
	    $dom = ZLanguage::getModuleDomain('Quotes');
	    $render = & pnRender::getInstance('Quotes');
	    mt_srand((double)microtime()*1000000);
	    $quote = array();
	    $apiargs = array();
	    $apiargs['status'] = 1;
	    // Make a category filter only if categorization is enabled in News module
	    $enablecategorization = pnModGetVar('Quotes', 'enablecategorization');
	    if ($enablecategorization) {
	        // load the categories system
	        if (!Loader::loadClass('CategoryRegistryUtil')) {
	            return LogUtil::registerError(__f('Error! Could not load [%s] class.'), 'CategoryRegistryUtil', $dom);
	        }
	        // Get the registrered categories for the module
	        $catregistry  = CategoryRegistryUtil::getRegisteredModuleCategories('Quotes', 'quotes');
	        $apiargs['catregistry'] = $catregistry;
	        $apiargs['category'] = $vars['category'];
	    }
	    // display an error if there are less than two quotes in the db, otherwise assign a random quote to the template
	    $total  = pnModAPIFunc('Quotes', 'user', 'countitems', $apiargs); # count the number of quotes in the db
	    if ($total < 2) {
	        $quote['error'] = __('There are too few Quotes in the database', $dom);
	    } else {
	        $random = mt_rand(1, $total);
	        $apiargs['numitems'] = 1;
	        $apiargs['startnum'] = $random;
	        $quotes = pnModAPIFunc('Quotes', 'user', 'getall', $apiargs);
	        // assign the first quote in the result set (there will only ever be one...)
	        $quote = $quotes[0];
	        $quote['error'] = false;
	    }
	    $render->assign('quote', $quote);
	    $render->assign('bid', $blockinfo['bid']);
	    $content = $render->fetch('quotes_block_quote.htm'); # get the block output from the template
	}
	if ($write_to_cache and !empty($content)) {
	   // attempt to write to cache if not loaded before
		if (!file_exists($cachedir)) {
			mkdir($cachedir, 0777); # attempt to make the dir
		}
		if (!file_put_contents($cachefile, $content)) {
			//echo "<br />Could not save data to cache. Please make sure your cache directory exists and is writable.<br />";
		}
	}
	$blockinfo['content'] = $content;
	
    // return the rendered block
    return pnBlockThemeBlock($blockinfo);
}

/**
 * modify block settings
 *
 * @author       The Zikula Development Team
 * @param        array       $blockinfo     a blockinfo structure
 * @return       output      the bock form
 */
function quotes_quoteblock_modify($blockinfo)
{
    // Get current content
    $vars = pnBlockVarsFromContent($blockinfo['content']);
    // Defaults
    if (!isset($vars['hourfrom'])) {
        $vars['hourfrom'] = -1;
    }
    if (!isset($vars['hourto'])) {
        $vars['hourto'] = -1;
    }
    if (!isset($vars['monfrom'])) {
        $vars['monfrom'] = -1;
    }
    if (!isset($vars['monto'])) {
        $vars['monto'] = -1;
    }
    if (!isset($vars['mdayfrom'])) {
        $vars['mdayfrom'] = -1;
    }
    if (!isset($vars['mdayto'])) {
        $vars['mdayto'] = -1;
    }
    if (!isset($vars['wdayfrom'])) {
        $vars['wdayfrom'] = -1;
    }
    if (!isset($vars['wdayto'])) {
        $vars['wdayto'] = -1;
    }
    if (!isset($vars['cache_time'])) {
        $vars['cache_time'] = 120;
    }
    if (!isset($vars['cache_dir'])) {
        $vars['cache_dir'] = 'any_cache';
    }
    $dom = ZLanguage::getModuleDomain('Quotes');
    // Create output object
    $render = pnRender::getInstance('Quotes', false);
    $render->caching = false; # Admin output changes often, we do not want caching
    // Select categories only if enabled for the module
    $enablecategorization = pnModGetVar('Quotes', 'enablecategorization');
    if ($enablecategorization) {
        // load the categories system
        if (!Loader::loadClass('CategoryRegistryUtil')) {
            return LogUtil::registerError(__f('Error! Could not load [%s] class.'), 'CategoryRegistryUtil', $dom);
        }
        // Get the registrered categories for the module
        $catregistry  = CategoryRegistryUtil::getRegisteredModuleCategories('Quotes', 'quotes');
        $render->assign('catregistry', $catregistry);
    }
    $render->assign('enablecategorization', $enablecategorization);
    // assign the vars
    $render->assign($vars);
    $render->assign('hours', range(0, 23));
    $render->assign('months', range(1, 12));
    $render->assign('wdays', range(1, 7));
    $render->assign('mdays', range(1, 31));
    // return the output
    return $render->fetch('quotes_block_quote_modify.htm');
}

/**
 * update block settings
 *
 * @author       The Zikula Development Team
 * @param        array       $blockinfo     a blockinfo structure
 * @return       $blockinfo  the modified blockinfo structure
 */
function quotes_quoteblock_update($blockinfo)
{
    // Get current content
    $vars = pnBlockVarsFromContent($blockinfo['content']);

    // alter the corresponding variable
    $vars['hourfrom'] = FormUtil::getPassedValue('hourfrom');
    $vars['hourto'] = FormUtil::getPassedValue('hourto');
    $vars['monfrom'] = FormUtil::getPassedValue('monfrom');
    $vars['monto'] = FormUtil::getPassedValue('monto');
    $vars['mdayfrom'] = FormUtil::getPassedValue('mdayfrom');
    $vars['mdayto'] = FormUtil::getPassedValue('mdayto');
    $vars['wdayfrom'] = FormUtil::getPassedValue('wdayfrom');
    $vars['wdayto'] = FormUtil::getPassedValue('wdayto');
    $vars['cache_time'] = FormUtil::getPassedValue('cache_time');
    $vars['cache_dir'] = FormUtil::getPassedValue('cache_dir');
    $vars['category'] = FormUtil::getPassedValue('category', null);

    // write back the new contents
    $blockinfo['content'] = pnBlockVarsToContent($vars);

    // clear the block cache
    $render = pnRender::getInstance('Quotes');
    $render->clear_cache('quotes_block_quote.htm');

    return $blockinfo;
}
