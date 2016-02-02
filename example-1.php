<?php

include('tree.php');
/**
 * JTreeRecursiveIterator
 *
 * To use a recursive iterator you have to extend of the RecursiveIteratorIterator
 * As an example I have built an unordered list
 * For detailed information on please see RecursiveIteratorIterator
 * http://us.php.net/manual/en/class.recursiveiteratoriterator.php
 *
 * @package   JTree
 * @author Jayesh Wadhwani
 * @copyright Jayesh Wadhwani
 * @license  GNU GENERAL PUBLIC LICENSE 3.0
 * @version 1.0 2011
 */
class JTreeRecursiveIterator extends RecursiveIteratorIterator {
    /**
     * @var _jTree the JTree object
     */
    private $_jTree;
    /**
     * @var _str string with ul/li string
     */
    private $_str;

    /**
     * JTreeRecursiveIterator::__construct()
     *
     * @param mixed $jt - the tree object
     * @param mixed $iterator - the tree iterator
     * @param mixed $mode
     * @param integer $flags
     * @return
     */
    public function __construct(JTree $jt, $iterator, $mode = RecursiveIteratorIterator::LEAVES_ONLY, $flags = 0) {

        parent::__construct($iterator, $mode, $flags);
        $this->_jTree = $jt;
        $this->_str = "<ul>\n";
    }

    /**
     * JTreeRecursiveIterator::endChildren()
     * Called when end recursing one level.(See manual)
     * @return void
     */
    public function endChildren() {
        parent::endChildren();
        $this->_str .= "</ul></li>\n";
    }

    /**
     * JTreeRecursiveIterator::callHasChildren()
     * Called for each element to test whether it has children. (See Manual)
     *
     * @return mixed
     */
    public function callHasChildren() {
        $ret = parent::callHasChildren();
        $value = $this->current()->getValue();

        if($ret === true) {
            $this->_str .= "<li>{$value}<ul>\n";
        } else {
            $this->_str .= "<li>{$value}</li>\n";
        }
        return $ret;
    }

    /**
     * JTreeRecursiveIterator::__destruct()
     * On destruction end the list and display.
     * @return void
     */
    public function __destruct() {
        $this->_str .= "</ul>\n";
        echo $this->_str;
    }

}

$categories = array();
$categories[] = array('id' => 1, 'weather_condition' => 'weather', 'parent_id' => 9999);
$categories[] = array('id' => 2, 'weather_condition' => 'Earthquakes', 'parent_id' => 1);
$categories[] = array('id' => 3, 'weather_condition' => 'Major', 'parent_id' => 2);
$categories[] = array('id' => 4, 'weather_condition' => 'Minor', 'parent_id' => 2);
$categories[] = array('id' => 5, 'weather_condition' => 'Fires', 'parent_id' => 9);
$categories[] = array('id' => 6, 'weather_condition' => 'Rain', 'parent_id' => 1);
$categories[] = array('id' => 7, 'weather_condition' => 'Flooding', 'parent_id' => 6);
$categories[] = array('id' => 8, 'weather_condition' => 'Washout', 'parent_id' => 6);
$categories[] = array('id' => 9, 'weather_condition' => 'Hurricanes', 'parent_id' => 1);

//create a new tree object
$jt = new JTree();

//iterate building the tree
foreach($categories as $category) {
    $uid = $jt->createNode($category['weather_condition'],$category['id'], $category['parent_id']);
}

//update: removed third variable. Use defaults
$it = new JTreeRecursiveIterator($jt, new JTreeIterator($jt->getTree()));

//iterate to create the ul list
foreach($it as $k => $v) {}

