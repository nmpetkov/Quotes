<?php
/**
 * Zikula Application Framework
 *
 * @copyright (c) 2002, Zikula Development Team
 * @link http://www.zikula.org
 * @version $Id: Adminform.php 439 2010-07-06 14:49:42Z drak $
 * @license GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * @package Zikula_Value_Addons
 * @subpackage Quotes
 */

class Quotes_Controller_Adminform extends Zikula_Controller
{
    /**
     * Create a new quote
     * @author The Zikula Development Team
     * @param 'qquote' quote text
     * @param 'qauthor' quote author
     * @return bool true if create success, false otherwise
     */
    public function create($args)
    {
        // confirm authorisation code.
        if (!SecurityUtil::confirmAuthKey()) {
            return LogUtil::registerAuthidError(ModUtil::url('Quotes', 'admin', 'main'));
        }

        // get parameters from whatever input we need.
        $quote = FormUtil::getPassedValue('quote', isset($args['quote']) ? $args['quote'] : null, 'POST');

        // notable by its absence there is no security check here.
        // create the quote
        $qid = ModUtil::apiFunc('Quotes', 'admin', 'create', $quote);
        if ($qid != false) {
            // success
            LogUtil::registerStatus($this->__('Done! Quote created.'));
        }

        return System::redirect(ModUtil::url('Quotes', 'admin', 'view'));
    }

    /**
     * Update quote
     *
     * Takes info from edit form and passes to API
     * @author The Zikula Development Team
     * @param 'qid' Quote id to delete
     * @param 'qauther' Author of quote to delete
     * @param 'confirm' Delete confirmation
     * @return mixed HTML string if confirm is null, true otherwise
     */
    public function update($args)
    {
        // confirm authorisation code.
        if (!SecurityUtil::confirmAuthKey()) {
            return LogUtil::registerAuthidError(ModUtil::url('Quotes', 'admin', 'view'));
        }

        // get parameters from whatever input we need.
        $quote = FormUtil::getPassedValue('quote', isset($args['quote']) ? $args['quote'] : null, 'POST');

        // check to see if we have been passed $objectid, the generic item identifier.
        if (!empty($quote['objectid'])) {
            $quote['qid'] = $quote['objectid'];
        }

        // notable by its absence there is no security check here.
        // update the quote
        if (ModUtil::apiFunc('Quotes', 'admin', 'update', $quote)) {
            // success
            LogUtil::registerStatus($this->__('Done! Quote updated.'));
        }

        // this function generated no output, and so now it is complete we redirect
        // the user to an appropriate page for them to carry on their work
        return System::redirect(ModUtil::url('Quotes', 'admin', 'view'));
    }

    /**
     * Update Quotes Config
     * @author The Zikula Development Team
     * @return string HTML string
     */
    public function updateconfig()
    {
        // security check
        if (!SecurityUtil::checkPermission('Quotes::', '::', ACCESS_ADMIN)) {
            return LogUtil::registerPermissionError();
        }

        // confirm authorisation code.
        if (!SecurityUtil::confirmAuthKey()) {
            return LogUtil::registerAuthidError(ModUtil::url('Quotes', 'admin', 'view'));
        }

        // update module variables.
        $itemsperpage = FormUtil::getPassedValue('itemsperpage', 25, 'POST');
        $this->setSet('itemsperpage', $itemsperpage);

        $enablecategorization = (bool)FormUtil::getPassedValue('enablecategorization', false, 'POST');
        $this->setSet('enablecategorization', $enablecategorization);

        // let any other modules know that the modules configuration has been updated
        $this->callHooks('module', 'updateconfig', 'Quotes', array('module' => 'Quotes'));

        // the module configuration has been updated successfuly
        LogUtil::registerStatus($this->__('Done! Module configuration updated.'));

        // this function generated no output, and so now it is complete we redirect
        // the user to an appropriate page for them to carry on their work
        return System::redirect(ModUtil::url('Quotes', 'admin', 'view'));
    }
}
