<?php
/**
 * Zikula Application Framework
 * @copyright  (c) Zikula Development Team
 * @license    GNU/GPL
 * @category   Zikula_3rdParty_Modules
 * @package    Content_Management
 * @subpackage Quotes
 */

class Quotes_Controller_Ajax extends Zikula_Controller_AbstractAjax
{
    /**
     * This function sets active/inactive status.
     *
     * @param qid
     *
     * @return mixed true or Ajax error
     */
    public function setstatus()
    {
        $this->checkAjaxToken();
        $this->throwForbiddenUnless(SecurityUtil::checkPermission('Quotes::', '::', ACCESS_ADMIN));

        $qid = $this->request->request->get('qid', 0);
        $status = $this->request->request->get('status', 0);
        $alert = '';
  
        if ($qid == 0) {
            $alert .= $this->__('No ID passed.');
        } else {
            $item = array('qid' => $qid, 'status' => $status);
            $res = DBUtil::updateObject($item, 'quotes', '', 'qid');
            if (!$res) {
                $alert .= $item['qid'].', '. $this->__f('Could not change item, ID %s.', DataUtil::formatForDisplay($qid));
                if ($item['status']) {
                    $item['status'] = 0;
                } else {
                    $item['status'] = 1;
                }
            }
        }
        // get current status to return
        $item = ModUtil::apiFunc($this->name, 'user', 'get', array('qid' => $qid));
        if (!$item) {
            $alert .= $this->__f('Could not get data, ID %s.', DataUtil::formatForDisplay($qid));
        }

        return new Zikula_Response_Ajax(array('qid' => $qid, 'status' => $item['status'], 'alert' => $alert));
    }
}
