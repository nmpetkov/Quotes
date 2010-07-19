<?php
/**
 * Zikula Application Framework
 *
 * @copyright (c) 2002, Zikula Development Team
 * @link http://www.zikula.org
 * @version $Id: pnadmin.php 358 2009-11-11 13:46:21Z herr.vorragend $
 * @license GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * @package Zikula_Value_Addons
 * @subpackage Quotes
*/

/**
 * Quotes main administration function
 * @author The Zikula Development Team
 * @return string HTML string
 */
function Quotes_admin_main()
{
    // security check
    if (!SecurityUtil::checkPermission('Quotes::', '::', ACCESS_EDIT)) {
        return LogUtil::registerPermissionError();
    }

    // create output object
    $render = & pnRender::getInstance('Quotes', false);

    // return the output that has been generated by this function
    return $render->fetch('quotes_admin_main.htm');
}

/**
 * Display form to create a new quote
 * @author The Zikula Development Team
 * @return string HTML string
 */
function Quotes_admin_new()
{
    // security check
    if (!SecurityUtil::checkPermission('Quotes::', '::', ACCESS_ADD)) {
        return LogUtil::registerPermissionError();
    }

    // get all module vars
    $modvars = pnModGetVar('Quotes');

    // create output object
    $render = & pnRender::getInstance('Quotes', false);

    if ($modvars['enablecategorization']) {
        // load the category registry util
        if (!Loader::loadClass('CategoryRegistryUtil')) {
            pn_exit(__f('Error! Unable to load class [%s%]', 'CategoryRegistryUtil'));
        }
        $catregistry = CategoryRegistryUtil::getRegisteredModuleCategories('Quotes', 'quotes');

        $render->assign('catregistry', $catregistry);
    }
    $render->assign('status', 1);

    // assign the module vars
    $render->assign($modvars);

    return $render->fetch('quotes_admin_new.htm');
}

/**
 * Display full list of quotes
 * @author The Zikula Development Team
 * @return string HTML string
 */
function Quotes_admin_view($args)
{
    // security check
    if (!(SecurityUtil::checkPermission('Quotes::', '::', ACCESS_READ))) {
        return LogUtil::registerPermissionError();
    }

    // Get parameters from whatever input we need.
    $startnum = FormUtil::getPassedValue('startnum', isset($args['startnum']) ? $args['startnum'] : null, 'GET');
    $keyword  = FormUtil::getPassedValue('quotes_keyword', isset($args['quotes_keyword']) ? $args['quotes_keyword'] : '', 'POST');
    $author   = FormUtil::getPassedValue('quotes_author', isset($args['quotes_author']) ? $args['quotes_author'] : '', 'POST');
    $property = FormUtil::getPassedValue('quotes_property', isset($args['quotes_property']) ? $args['quotes_property'] : null, 'POST');
    $category = FormUtil::getPassedValue("quotes_{$property}_category", isset($args["quotes_{$property}_category"]) ? $args["quotes_{$property}_category"] : null, 'POST');
    $clear    = FormUtil::getPassedValue('clear', false, 'POST');
    if ($clear) {
        $property = $category = $keyword = $author = null;
    }

    $dom = ZLanguage::getModuleDomain('Quotes');

    // get all module vars
    $modvars = pnModGetVar('Quotes');

    if ($modvars['enablecategorization']) {
        // load the category registry util
        if (!Loader::loadClass('CategoryRegistryUtil')) {
            pn_exit(__f('Error! Unable to load class [%s%]', 'CategoryRegistryUtil'));
        }
        $catregistry  = CategoryRegistryUtil::getRegisteredModuleCategories('Quotes', 'quotes');
        $properties = array_keys($catregistry);

        // validate and build the category filter - mateo
        if (!empty($property) && in_array($property, $properties) && !empty($category)) {
            $catFilter = array($property => $category);
        }

        // assign a default property - mateo
        if (empty($property) || !in_array($property, $properties)) {
            $property = $properties[0];
        }

        // plan ahead for ML features
        $propArray = array();
        foreach ($properties as $prop) {
            $propArray[$prop] = $prop;
        }
    }

    // get the matching quotes
    $quotes = pnModAPIFunc('Quotes', 'user', 'getall',
                            array('startnum' => $startnum,
                                  'numitems' => $modvars['itemsperpage'],
                                  'keyword'  => $keyword,
                                  'author'   => $author,
                                  'category' => isset($catFilter) ? $catFilter : null,
                                  'catregistry'  => isset($catregistry) ? $catregistry : null));

    $items = array();
    foreach ($quotes as $key => $quote)
    {
        // options for the item
        $options = array();
        if (SecurityUtil::checkPermission('Quotes::', "$quote[author]::$quote[qid]", ACCESS_EDIT)) {
            $quotes[$key]['options'][] = array('url'   => pnModURL('Quotes', 'admin', 'modify', array('qid' => $quote['qid'])),
                                               'image' => 'xedit.gif',
                                               'title' => __('Edit', $dom));

            if (SecurityUtil::checkPermission('Quotes::', "$quote[author]::$quote[qid]", ACCESS_DELETE)) {
                $quotes[$key]['options'][] = array('url'   => pnModURL('Quotes', 'admin', 'delete', array('qid' => $quote['qid'])),
                                                   'image' => '14_layer_deletelayer.gif',
                                                   'title' => __('Delete', $dom));
            }
        }
        $items[] = $quotes[$key];
    }

    // create output object
    $render = & pnRender::getInstance('Quotes', false);

    // assign the default language
    $render->assign('lang', ZLanguage::getLanguageCode());

    // assign the items and modvars to the template
    $render->assign('quotes', $items);
    $render->assign($modvars);

    // add the current filters
    $render->assign('quotes_author', $author);
    $render->assign('quotes_keyword', $keyword);

    // assign the categories information if enabled
    if ($modvars['enablecategorization']) {
        $render->assign('catregistry', $catregistry);
        $render->assign('numproperties', count($propArray));
        $render->assign('properties', $propArray);
        $render->assign('property', $property);
        $render->assign("category", $category);
    }

    // assign the values for the smarty plugin to produce a pager
    $render->assign('pager', array('itemsperpage' => $modvars['itemsperpage'], 
                                   'numitems' => pnModAPIFunc('Quotes', 'user', 'countitems', 
                                                               array('keyword'  => $keyword,
                                                                     'author'   => $author,
                                                                     'category' => isset($catFilter) ? $catFilter : null)))); 

    return $render->fetch('quotes_admin_view.htm');
}

/**
 * Edit quote
 * @author The Zikula Development Team
 * @param 'qid' Quote id to delete
 * @param 'qauther' Author of quote to delete
 * @param 'confirm' Delete confirmation
 * @return mixed HTML string if confirm is null, true otherwise
 */
function Quotes_admin_modify($args)
{
    // get parameters from whatever input we need.
    $qid = FormUtil::getPassedValue('qid', isset($args['qid']) ? $args['qid'] : null, 'GET');
    $objectid = FormUtil::getPassedValue('objectid', isset($args['objectid']) ? $args['objectid'] : null, 'GET');

    // check to see if we have been passed $objectid, the generic item identifier.
    if (!empty($objectid)) {
        $qid = $objectid;
    }

    $dom = ZLanguage::getModuleDomain('Quotes');

    // get the quote
    $quote = pnModAPIFunc('Quotes', 'user', 'get', array('qid' => $qid));
    if (!$quote) {
        return DataUtil::formatForDisplayHTML(__('No such Quote found.', $dom));
    }

    // security check
    if (!SecurityUtil::checkPermission('Quotes::', "$quote[author]::$qid", ACCESS_EDIT)) {
        return LogUtil::registerPermissionError();
    }

    // get all module vars
    $modvars = pnModGetVar('Quotes');

    // create output object
    $render = & pnRender::getInstance('Quotes', false);

    if ($modvars['enablecategorization']) {
        // load the category registry util
        if (!($class = Loader::loadClass('CategoryRegistryUtil'))) {
            pn_exit(__f('Error! Unable to load class [%s%]', 'CategoryRegistryUtil'));
        }
        $catregistry = CategoryRegistryUtil::getRegisteredModuleCategories('Quotes', 'quotes');

        $render->assign('catregistry', $catregistry);
    }

    // assign the item and module vars
    $render->assign($quote);
    $render->assign($modvars);

    // return the output that has been generated by this function
    return $render->fetch('quotes_admin_modify.htm');
}

/**
 * Delete selected quote
 * @author The Zikula Development Team
 * @param 'qid' Quote id to delete
 * @param 'qauther' Author of quote to delete
 * @param 'confirm' Delete confirmation
 * @return mixed HTML string if confirm is null, true otherwise
 */
function Quotes_admin_delete($args)
{
    // get parameters from whatever input we need.
    $qid = FormUtil::getPassedValue('qid', isset($args['qid']) ? $args['qid'] : null, 'GETPOST');
    $objectid = FormUtil::getPassedValue('objectid', isset($args['objectid']) ? $args['objectid'] : null, 'POST');
    $confirmation = FormUtil::getPassedValue('confirmation', null, 'POST');
    if (!empty($objectid)) {
        $qid = $objectid;
    }

    $dom = ZLanguage::getModuleDomain('Quotes');

    // get the quote
    $item = pnModAPIFunc('Quotes', 'user', 'get', array('qid' => $qid));
    if ($item == false) {
        return LogUtil::registerError (__('No such Quote found.', $dom));
    }

    // security check
    if (!SecurityUtil::checkPermission('Quotes::', "$item[author]::$qid", ACCESS_DELETE)) {
        return LogUtil::registerPermissionError();
    }

    // check for confirmation.
    if (empty($confirmation)) {
        // no confirmation yet - display a suitable form to obtain confirmation
        // of this action from the user

        // create output object
        $render = & pnRender::getInstance('Quotes', false);

        // quote id
        $render->assign('qid', $qid);

        // return the output that has been generated by this function
        return $render->fetch('quotes_admin_delete.htm');
    }

    // if we get here it means that the user has confirmed the action

    // confirm authorisation code.
    if (!SecurityUtil::confirmAuthKey()) {
        return LogUtil::registerAuthidError(pnModURL('Quotes', 'admin', 'view'));
    }

    // delete the quote
    if (pnModAPIFunc('Quotes', 'admin', 'delete', array('qid' => $qid))) {
        LogUtil::registerStatus(__('Done! Quote deleted.', $dom));
    }

    // This function generated no output, and so now it is complete we redirect
    // the user to an appropriate page for them to carry on their work
    return pnRedirect(pnModURL('Quotes', 'admin', 'view'));
}

/**
 * Search quote database by keyword - unfinished obviously.
 * @author The Zikula Development Team
 * @return string HTML string
 */
function Quotes_admin_modifyconfig()
{
    $dom = ZLanguage::getModuleDomain('Quotes');

    // security check
    if (!SecurityUtil::checkPermission('Quotes::', '::', ACCESS_ADMIN)) {
        return LogUtil::registerPermissionError();
    }

    // create output object
    $render = & pnRender::getInstance('Quotes', false);

    // number of items to display per page + catmapcount
    $render->assign(pnModGetVar('Quotes'));

    // return the output that has been generated by this function
    return $render->fetch('quotes_admin_modifyconfig.htm');
}
