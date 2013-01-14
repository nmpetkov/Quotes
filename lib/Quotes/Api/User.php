<?php
/**
 * Zikula Application Framework
 * @copyright  (c) Zikula Development Team
 * @license    GNU/GPL
 * @category   Zikula_3rdParty_Modules
 * @package    Content_Management
 * @subpackage Quotes
 */

class Quotes_Api_User extends Zikula_AbstractApi
{
    /**
     * process user input and form a WHERE clause
     * @return string SQL where clause
     */
    private function _process_args(&$args)
    {
        // optional arguments.
        if (!isset($args['startnum']) || !is_numeric($args['startnum'])) {
            $args['startnum'] = -1;
        }
        if (!isset($args['numitems']) || !is_numeric($args['numitems'])) {
            $args['numitems'] = -1;
        }
        if (!isset($args['author'])) {
            $args['author'] = null;
        }
        if (!isset($args['keyword'])) {
            $args['keyword'] = null;
        }
        if (!isset($args['category'])) {
            $args['category'] = null;
        }
        if (!isset($args['catFilter']) || !is_numeric($args['catFilter'])) {
            $args['catFilter'] = array();
        }
        if (!isset($args['rootCat'])) {
            $args['rootCat'] = 0;
        }

        // build the where clause
        $wheres = array();
        if (isset($args['qid'])) {
            $wheres[] = "qid = ".DataUtil::formatForStore($args['qid']);
        }
        if ($args['author']) {
            $wheres[] = "author = '".DataUtil::formatForStore($args['author'])."'";
        }
        if (isset($args['status'])) {
            $wheres[] = "status = '".DataUtil::formatForStore($args['status'])."'";
        }

        if ($args['category']){
            if (is_array($args['category'])) {
                $args['catFilter'] = $args['category'];
            } else {
                $args['catFilter'][] = $args['category'];
            }
            $args['catFilter']['__META__'] = array('module' => 'Quotes');
        }

        if ($args['keyword']) {
            $wheres[] = "quote LIKE '%".DataUtil::formatForStore($args['keyword'])."%'";
        }

        $args['where'] = implode(' AND ', $wheres);

        return $args['where'];
    }

    /**
     * Get all Quotes
     * @author The Zikula Development Team
     * @author Greg Allan
     * @return array array containing quote id, quote, author
     */
    public function getall($args)
    {
        // security check
        if (!SecurityUtil::checkPermission('Quotes::', '::', ACCESS_READ)) {
            return array();
        }

        $where = $this->_process_args($args);
        $sort = isset($args['sort']) && $args['sort'] ? $args['sort'] : '';
        $sortdir = isset($args['sortdir']) && $args['sortdir'] ? $args['sortdir'] : 'ASC';
        if ($sort) {
            if ($sort=='qid') $sort .= ' '.$sortdir;
            else $sort .= ' '.$sortdir.', qid '.$sortdir;
        } else {
            $sort = 'qid DESC';
        }

        // define the permissions filter to use
        $permFilter = array();
        $permFilter[] = array('realm' => 0,
                'component_left'   => 'Quotes',
                'component_middle' => '',
                'component_right'  => '',
                'instance_left'    => 'author',
                'instance_middle'  => '',
                'instance_right'   => 'qid',
                'level'            => ACCESS_READ);

        $args['catFilter'] = array();
        if (isset($args['category']) && !empty($args['category'])){
            if (is_array($args['category'])) {
                $args['catFilter'] = $args['category'];
            } elseif (isset($args['property'])) {
                $property = $args['property'];
                $args['catFilter'][$property] = $args['category'];
            }
            $args['catFilter']['__META__'] = array('module' => 'Quotes');
        }

        // get the object array from the db
        $objArray = DBUtil::selectObjectArray('quotes', $where, $sort, $args['startnum'], $args['numitems'], '', $permFilter, $args['catFilter']);
        
        // check for an error with the database code, and if so set an appropriate
        // error message and return
        if ($objArray === false) {
            return LogUtil::registerError($this->__('Error! Could not load any quotes.'));
        }

        // need to do this here as the category expansion code can't know the
        // root category which we need to build the relative path component
        if ($objArray && isset($args['catregistry']) && $args['catregistry']) {
            ObjectUtil::postProcessExpandedObjectArrayCategories($objArray, $args['catregistry']);
        }

        // return the items
        return $objArray;
    }

    /**
     * Get Quote
     * @author The Zikula Development Team
     * @author Greg Allan
     * @param 'args['qid']' quote id
     * @return array item array
     */
    public function get($args)
    {
        // argument check
        if (!isset($args['qid']) || !is_numeric($args['qid'])) {
            return LogUtil::registerArgsError();
        }

        // define the permissions filter to use
        $permFilter = array();
        $permFilter[] = array('realm' => 0,
                'component_left'   => 'Quotes',
                'component_middle' => '',
                'component_right'  => '',
                'instance_left'    => 'author',
                'instance_middle'  => '',
                'instance_right'   => 'qid',
                'level'            => ACCESS_READ);

        // get the quote
        $quote = DBUtil::selectObjectByID('quotes', $args['qid'], 'qid', null, $permFilter);

        // return the fetched object or false
        return ($quote ? $quote : false);
    }

    /**
     * Count Quotes
     * @author The Zikula Development Team
     * @author Greg Allan
     * @return int count of items
     */
    public function countitems($args)
    {
        // optional arguments.
        if (isset($args['category']) && !empty($args['category'])){
            if (is_array($args['category'])) {
                $args['catFilter'] = $args['category'];
            } elseif (isset($args['property'])) {
                $property = $args['property'];
                $args['catFilter'][$property] = $args['category'];
            }
        }

        if (!isset($args['catFilter'])) {
            $args['catFilter'] = array();
        }

        $where = $this->_process_args($args);

        return DBUtil::selectObjectCount('quotes', $where, 'qid', false, $args['catFilter']);
    }

    /**
     * get random item
     * @return mixed array, or false on failure
     * @param 'args['catFilter']' if exist category filter
     */
    function getrandom($args)
    {
        $quote = array();

        if (!SecurityUtil::checkPermission('Quotes::', '::', ACCESS_READ)) {
            return $quote;
        }

        $total  = ModUtil::apiFunc($this->name, 'user', 'countitems', $args); # count the number of quotes in the db

        $args['numitems'] = 1;
        if ($total < 1) {
            // display an error if there are less than two quotes in the db, otherwise assign a random quote to the template
            $quote['error'] = __('There are too few Quotes in the database');
        } else if ($total == 1) {
            $quotes = ModUtil::apiFunc($this->name, 'user', 'getall', $args);
            $quote = $quotes[0];
        } else {
            $random = mt_rand(1, $total);
            $args['startnum'] = $random - 1; // MySql OFFSET first row is 0
            $args['sort'] = 'qid';
            $quotes = ModUtil::apiFunc($this->name, 'user', 'getall', $args);
            $quote = $quotes[0]; // assign the first quote in the result set
        }

        // Return the items
        return $quote;
    }
}