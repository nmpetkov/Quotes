<?php
/**
 * Zikula Application Framework
 *
 * @copyright (c) 2002, Zikula Development Team
 * @link http://www.zikula.org
 * @version $Id: Installer.php 437 2010-07-06 13:24:38Z drak $
 * @license GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * @package Zikula_Value_Addons
 * @subpackage Quotes
*/

/**
 * Init quotes module
 * @author The Zikula Development Team
 * @return true if init successful, false otherwise
 */
function Quotes_init()
{
    $dom = ZLanguage::getModuleDomain('Quotes');

    // create table
    if (!DBUtil::createTable('quotes')) {
        return false;
    }

    // set up config variables
    $modvars = array(
        'itemsperpage' => 25,
        'enablecategorization' => true,
        'catmapcount' => true
    );

    // create our default category
    if (!_quotes_createdefaultcategory()) {
        LogUtil::registerStatus(__('Warning! Could not create the default Quotes category tree. If you want to use categorisation with Quotes, register at least one property for the module in the Category Registry.', $dom));
        $modvars['enablecategorization'] = false;
    }

    // set up module variables
    ModUtil::setVars('Quotes', $modvars);

    // initialisation successful
    return true;
}

/**
 * Upgrade quotes module
 * @author The Zikula Development Team
 * @return true if init successful, false otherwise
 */
function Quotes_upgrade($oldversion)
{
    $dom = ZLanguage::getModuleDomain('Quotes');

    // update table
    if (!DBUtil::changeTable('quotes')) {
        return false;
    }

    // upgrade dependent on old version number
    switch ($oldversion)
    {
        case '1.3':
            // version 1.3 was shipped with .72x/.75
            ModUtil::setVar('Quotes', 'itemsperpage', 25);
            // we don't need these variables anymore
            ModUtil::delVar('Quotes', 'detail');
            ModUtil::delVar('Quotes', 'table');

        case '1.5':
            // version 1.5 was shipped with .76x
            // migrate the quotes into the default category
            if (!_quotes_migratecategories()) {
                return LogUtil::registerError(__('Error! Update attempt failed.', $dom));
            }

        case '2.0':
            // remove the mapcatcount variable
            ModUtil::delVar('Quotes', 'catmapcount');

        case '2.1':
            // add the categorization variable
            ModUtil::setVar('Quotes', 'enablecategorization', true);

        case '2.2':
        case '2.3':
            // further upgrade routines
    }

    // upgrade success
    return true;
}

/**
 * Delete quotes module
 * @author The Zikula Development Team
 * @return true if init successful, false otherwise
 */
function Quotes_delete()
{
    if (!DBUtil::dropTable('quotes')) {
        return false;
    }

    // delete module variables
    ModUtil::delVar('Quotes');

    // delete entries from category registry
    ModUtil::dbInfoLoad('Categories');
    DBUtil::deleteWhere('categories_registry', "crg_modname = 'Quotes'");
    DBUtil::deleteWhere('categories_mapobj', "cmo_modname = 'Quotes'");

    // deletion successful
    return true;
}

function _quotes_createdefaultcategory($regpath = '/__SYSTEM__/Modules/Global')
{
    // load necessary classes
    Loader::loadClass('CategoryUtil');
    Loader::loadClassFromModule('Categories', 'Category');
    Loader::loadClassFromModule('Categories', 'CategoryRegistry');

    // get the language
    $lang = ZLanguage::getLanguageCode();
    $dom = ZLanguage::getModuleDomain('Quotes');

    // get the category path for which we're going to insert our place holder category
    $rootcat = CategoryUtil::getCategoryByPath('/__SYSTEM__/Modules');
    $qCat    = CategoryUtil::getCategoryByPath('/__SYSTEM__/Modules/Quotes');

    if (!$qCat) {
        // create placeholder for all our migrated categories
        $cat = new Categories_DBObject_Category();
        $cat->setDataField('parent_id', $rootcat['id']);
        $cat->setDataField('name', 'Quotes');
        $cat->setDataField('display_name', array($lang => __('Quotes', $dom)));
        $cat->setDataField('display_desc', array($lang => __('Random quotes', $dom)));
        if (!$cat->validate('admin')) {
            return false;
        }
        $cat->insert();
        $cat->update();
    }

    // get the category path for which we're going to insert our upgraded categories
    $rootcat = CategoryUtil::getCategoryByPath($regpath);
    if ($rootcat) {
        // create an entry in the categories registry
        $registry = new Categories_DBObject_CategoryRegistry();
        $registry->setDataField('modname', 'Quotes');
        $registry->setDataField('table', 'quotes');
        $registry->setDataField('property', 'Main');
        $registry->setDataField('category_id', $rootcat['id']);
        $registry->insert();
    } else {
        return false;
    }

    return true;
}

/**
 * migrate old local categories to the categories module
 */
function _quotes_migratecategories()
{
    // load necessary classes
    Loader::loadClass('CategoryUtil');
    Loader::loadClassFromModule('Categories', 'Category');
    Loader::loadClassFromModule('Categories', 'CategoryRegistry');

    // get the language file
    $lang = ZLanguage::getLanguageCode();
    $dom = ZLanguage::getModuleDomain('Quotes');
    $catPath = '/__SYSTEM__/Modules/Quotes';

    // create root category and entry in the categories registry
    _quotes_createdefaultcategory($catPath);

    // get the category path for which we're going to insert our upgraded categories
    $rootcat = CategoryUtil::getCategoryByPath($catPath);

    // create placeholder for all our migrated quotes
    $cat = new Categories_DBObject_Category();
    $cat->setDataField('parent_id', $rootcat['id']);
    $cat->setDataField('name', 'Imported');
    $cat->setDataField('display_name', array($lang => __('Imported quotes', $dom)));
    $cat->setDataField('display_desc', array($lang => __('Quotes imported from .7x version', $dom)));
    if (!$cat->validate('admin')) {
        return false;
    }
    $cat->insert();
    $cat->update();
    $catid = $cat->getDataField('id');
    unset($cat);

    // migrate page category assignments
    $prefix = System::getVar('prefix');
    $sql = "SELECT pn_qid FROM {$prefix}_quotes";
    $result = DBUtil::executeSQL($sql);
    $quotes = array();
    for (; !$result->EOF; $result->MoveNext()) {
        $quotes[] = array('qid' => $result->fields[0],
                         '__CATEGORIES__' => array('Main' => $catid),
                         '__META__' => array('module' => 'Quotes'));
    }

    foreach ($quotes as $quote) {
        if (!DBUtil::updateObject($quote, 'quotes', '', 'qid')) {
            return LogUtil::registerError(__('Error! Update attempt failed.', $dom));
        }
    }

    return true;
}
