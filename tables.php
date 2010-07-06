<?php
/**
 * Zikula Application Framework
 *
 * @copyright (c) 2002, Zikula Development Team
 * @link http://www.zikula.org
 * @version $Id: tables.php 439 2010-07-06 14:49:42Z drak $
 * @license GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * @package Zikula_Value_Addons
 * @subpackage Quotes
*/

/**
 * Get Quotes pntable array
 * @author The Zikula Development Team
 * @return array
 */
function Quotes_tables()
{
    // initialise table array
    $pntable = array();

    // full table definition
    $pntable['quotes'] = DBUtil::getLimitedTablename('quotes');
    $pntable['quotes_column'] = array('qid'    => 'pn_qid',
                                      'quote'  => 'pn_quote',
                                      'author' => 'pn_author');
    $pntable['quotes_column_def'] = array('qid'    => 'I4 NOTNULL AUTO PRIMARY',
                                          'quote'  => 'X',
                                          'author' => 'C(150) NOTNULL');

    // enable categorization services
    $pntable['quotes_db_extra_enable_categorization'] = ModUtil::getVar('Quotes', 'enablecategorization', true);
    $pntable['quotes_primary_key_column'] = 'qid';

    // add standard data fields
    ObjectUtil::addStandardFieldsToTableDefinition($pntable['quotes_column'], 'pn_');
    ObjectUtil::addStandardFieldsToTableDataDefinition($pntable['quotes_column_def']);

    // return table information
    return $pntable;
}
