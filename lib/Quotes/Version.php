<?php
/**
 * Zikula Application Framework
 *
 * @copyright (c) 2002, Zikula Development Team
 * @link http://www.zikula.org
 * @version $Id: Version.php 441 2010-07-06 14:52:34Z drak $
 * @license GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * @package Zikula_Value_Addons
 * @subpackage Quotes
 */

class Quotes_Version extends Zikula_Version
{
    public function getMetaData()
    {
        $meta = array();

        $meta['displayname']    = $this->__('Quotes');
        $meta['description']    = $this->__('Random quotes');
        $meta['version']        = '2.3.1';
        //! this defines the module's url
        $meta['url']            = $this->__('quotes');
        $meta['contact']        = 'http://www.zikula.org';

        $meta['securityschema'] = array('Quotes::' => 'Author name::Quote ID');
        return $meta;
    }
}