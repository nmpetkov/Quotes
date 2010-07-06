<?php
/**
 * Zikula Application Framework
 *
 * @copyright (c) 2002, Zikula Development Team
 * @link http://www.zikula.org
 * @version $Id: Quote.php 439 2010-07-06 14:49:42Z drak $
 * @license GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * @package Zikula_Value_Addons
 * @subpackage Quotes
 */

class Quotes_Block_Quote extends Zikula_Block
{
    /**
     * Init quotes block
     * @author The Zikula Development Team <erik@slooff.com>
     * @link http://www.zikula.org
     */
    public function init()
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
    public function info()
    {
        return array('module' => 'Quotes',
                'text_type' => $this->__('Quote'),
                'text_type_long' => $this->__('Random quote block'),
                'allow_multiple' => true,
                'form_content' => false,
                'form_refresh' => false,
                'show_preview' => true);
    }

    /**
     * Display quotes block
     * @author The Zikula Development Team <erik@slooff.com>
     * @link http://www.zikula.org
     * @param 'blockinfo' blockinfo array
     * @return HTML String
     */
    public function display($blockinfo)
    {
        // security check
        if (!SecurityUtil::checkPermission('Quotes:Quoteblock:', "$blockinfo[title]::", ACCESS_READ)) {
            return;
        }

        // check if the quotes module is available
        if (!ModUtil::available('Quotes')) {
            return;
        }

        // count the number of quotes in the db
        $total  = ModUtil::apiFunc('Quotes', 'user', 'countitems');

        mt_srand((double)microtime()*1000000);

        $quote = array();
        // display an error if there are less than two quotes in the db
        // otherwise assign a random quote to the template
        if ($total < 2) {
            $quote['error'] = $this->__('There are too few Quotes in the database');
        } else {
            $random = mt_rand(0,($total));
            $quotes = ModUtil::apiFunc('Quotes', 'user', 'getall', array('numitems' => 1, 'startnum' => $random));
            // assign the first quote in the result set (there will only ever be one...)
            $quote = $quotes[0];
            $quote['error'] = false;
        }

        $this->view->assign('quote', $quote);
        $this->view->assign('bid', $blockinfo['bid']);

        // get the block output from the template
        $blockinfo['content'] = $this->view->fetch('quotes_block_quote.htm');

        // return the rendered block
        return BlockUtil::themeBlock($blockinfo);
    }
}