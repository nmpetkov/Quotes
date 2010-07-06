<?php
/**
 * Zikula Application Framework
 *
 * @copyright (c) 2002, Zikula Development Team
 * @link http://www.zikula.org
 * @version $Id: Version.php 437 2010-07-06 13:24:38Z drak $
 * @license GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * @package Zikula_Value_Addons
 * @subpackage Quotes
*/

$dom = ZLanguage::getModuleDomain('Quotes');

$modversion['name']           = 'Quotes';
$modversion['displayname']    = __('Quotes', $dom);
$modversion['description']    = __('Random quotes', $dom);
$modversion['version']        = '2.3';
//! this defines the module's url
$modversion['url']            = __('quotes', $dom);
$modversion['changelog']      = '';
$modversion['credits']        = 'pndocs/credits.txt';
$modversion['help']           = '';
$modversion['license']        = '';
$modversion['official']       = 1;
$modversion['author']         = 'The Zikula Development Team';
$modversion['contact']        = 'http://www.zikula.org';

$modversion['securityschema'] = array('Quotes::' => 'Author name::Quote ID');
