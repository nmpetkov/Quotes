<?php
/**
 * Zikula Application Framework
 * @copyright  (c) Zikula Development Team
 * @license    GNU/GPL
 * @category   Zikula_3rdParty_Modules
 * @package    Content_Management
 * @subpackage Ephemerides
 */

class Quotes_Controller_User extends Zikula_AbstractController
{
    /**
     * the main user function
     *
     * @param array $args Arguments.
     *
     * @return string html string
     */
    public function main($args)
    {
        return $this->display($args);
    }

    /**
     * display given item
     *
     * @param $args array Arguments array.
     *
     * @return string html string
     */
    public function display($args)
    {
        $qid   = (int)FormUtil::getPassedValue('qid', isset($args['qid']) ? $args['qid'] : null, 'REQUEST');
        $objectid = (int)FormUtil::getPassedValue('objectid', isset($args['objectid']) ? $args['objectid'] : null, 'REQUEST');

        if (!empty($objectid)) {
            $qid = $objectid;
        }
        if (!isset($args['qid']) and !empty($qid)) {
            $args['qid'] = $qid;
        }

        // Chek permissions
        $this->throwForbiddenUnless(SecurityUtil::checkPermission('Quotes::', '::', ACCESS_READ), LogUtil::getErrorMsgPermission());

        // check if the contents are cached.
        $template = 'quotes_user_display.tpl';
        if ($this->view->is_cached($template)) {
            return $this->view->fetch($template);
        }

        // get item
        if (isset($args['qid']) and $args['qid']>0) {
            $items = ModUtil::apiFunc($this->name, 'user', 'getall', $args);
            $quote = $items[0];
        } else {
            $quote = ModUtil::apiFunc($this->name, 'user', 'getrandom', $args);
        }

        $this->view->assign('quote', $quote);
        $this->view->assign('ballooncolor', 'grey');

        return $this->view->fetch($template);
    }
    }
