<?php
/**
 * Zikula Application Framework
 *
 * @copyright (c) 2002, Zikula Development Team
 * @link http://www.zikula.org
 * @version $Id: Admin.php 439 2010-07-06 14:49:42Z drak $
 * @license GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * @package Zikula_Value_Addons
 * @subpackage Quotes
 */

class Quotes_Api_Admin extends Zikula_Api
{
    /**
     * Create Quote
     * @author Greg Allan
     * @author The Zikula Development Team
     * @param 'args['qquote']' quote text
     * @param 'args['qauthor']' quote author
     * @return id of quote if success, false otherwise
     */
    public function create($quote)
    {
        // the argument associative array represents an object/row
        // argument check
        if (!isset($quote['quote']) || !isset($quote['author'])) {
            return LogUtil::registerArgsError();
        }

        // security check
        if (!SecurityUtil::checkPermission('Quotes::', '::', ACCESS_EDIT)) {
            return LogUtil::registerPermissionError();
        }

        // insert the object and check the return value for error
        $res = DBUtil::insertObject($quote, 'quotes', 'qid');
        if (!$res) {
            return LogUtil::registerError($this->__('Error! Quote creation failed.'));
        }

        // let any hooks know that we have created a new item.
        $this->callHooks('item', 'create', $quote['qid'], array('module' => 'Quotes'));

        // return the id of the newly created item to the calling process
        return $quote['qid'];
    }

    /**
     * Delete Quote
     * @author Greg Allan
     * @author The Zikula Development Team
     * @param 'args['qid']' quote id
     * @return true if success, false otherwise
     */
    public function delete($args)
    {
        // argument check
        if (!isset($args['qid']) || !is_numeric($args['qid'])) {
            return LogUtil::registerArgsError();
        }

        // get the existing quote
        $item = ModUtil::apiFunc('Quotes', 'user', 'get', array('qid' => $args['qid']));
        if (!$item) {
            return LogUtil::registerError($this->__('No such Quote found.'));
        }

        // delete the quote and check the return value for error
        $res = DBUtil::deleteObjectByID('quotes', $args['qid'], 'qid');
        if (!$res) {
            return LogUtil::registerError($this->__('Error! Quote deletion failed.'));
        }

        // delete any object category mappings for this item
        ObjectUtil::deleteObjectCategories($item, 'quotes', 'qid');

        // let any hooks know that we have deleted an item.
        $this->callHooks('item', 'delete', $args['qid'], array('module' => 'Quotes'));

        // let the calling process know that we have finished successfully
        return true;
    }

    /**
     * Update Quote
     * @author Greg Allan
     * @author The Zikula Development Team
     * @param 'args['qid']' quote ID
     * @param 'args['qquote']' quote text
     * @param 'args['qauthor']' quote author
     * @return true if success, false otherwise
     */
    public function update($quote)
    {
        // the argument associative array represents an object/row
        // argument check
        if (!isset($quote['qid']) || !isset($quote['quote']) || !isset($quote['author'])) {
            return LogUtil::registerArgsError();
        }

        // get the existing quote
        $item = ModUtil::apiFunc('Quotes', 'user', 'get', array('qid' => $quote['qid']));
        if (!$item) {
            return LogUtil::registerError($this->__('No such Quote found.'));
        }

        // security check(s)
        // check permissions for both the original and modified quotes
        if (!SecurityUtil::checkPermission('Quotes::', "$item[author]::$quote[qid]", ACCESS_EDIT)) {
            return LogUtil::registerPermissionError();
        }
        if (!SecurityUtil::checkPermission('Quotes::', "$quote[author]::$quote[qid]", ACCESS_EDIT)) {
            return LogUtil::registerPermissionError();
        }

        // update the quote and check return value for error
        $res = DBUtil::updateObject($quote, 'quotes', '', 'qid');
        if (!$res) {
            return LogUtil::registerError($this->__('Error! Quote update failed.'));
        }

        // new hook functions
        ModUtil::callHooks('item', 'update', $quote['qid'], array('module' => 'Quotes'));

        // let the calling process know that we have finished successfully
        return true;
    }

    /**
     * get available admin panel links
     *
     * @return array array of admin links
     */
    public function getlinks()
    {
        $links = array();

        if (SecurityUtil::checkPermission('Quotes::', '::', ACCESS_EDIT)) {
            $links[] = array('url' => ModUtil::url('Quotes', 'admin', 'view'), 'text' => $this->__('View Quotes List'));
        }
        if (SecurityUtil::checkPermission('Quotes::', '::', ACCESS_ADD)) {
            $links[] = array('url' => ModUtil::url('Quotes', 'admin', 'newquote'), 'text' => $this->__('Create a Quote'));
        }
        if (SecurityUtil::checkPermission('Quotes::', '::', ACCESS_ADMIN)) {
            $links[] = array('url' => ModUtil::url('Quotes', 'admin', 'modifyconfig'), 'text' => $this->__('Settings'));
        }

        return $links;
    }
}