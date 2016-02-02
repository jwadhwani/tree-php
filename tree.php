<?php
/**
 * JTree
 *
 * This class implements the Tree structure and is based on linked list using a hash table.
 * Using hash table prevents all possible recursive references and
 * allows for more efficient garbage collection. A particularly sore point in PHP.
 *
 * I have used my implementation of Doubly Linked list as my base.
 * You can find more information on it here:
 * http://phptouch.com/2011/03/15/doubly-linked-list-in-php/
 *
 * I have heavily relied on the following 2 references for their algorithms.
 * Beginning Algorithims by Simon Harris and James Ross. Wrox publishing.
 * Data Structures and Algorithms in Java Fourth Edition by Michael T. Goodrich
 * and Roberto Tamassia. John Wiley & Sons.
 *
 * Version 1.1: Modified the createNode function to take care of children nodes
 * created before parent nodes.
 * Minor updates based on Peter's comments.
 *
 * @package JTree
 * @author Jayesh Wadhwani
 * @copyright 2011 Jayesh Wadhwani.
 * @license  GNU GENERAL PUBLIC LICENSE 3.0
 * @version 1.1
 */
class JTree {
    /**
     * @var UID for the header node
     */
    private $_head;

    /**
     * @var size of list
     */
    private $_size;

    /**
     * @var hash table to store node objects
     */
    private $_list = array();

    /**
     * JTree::__construct()
     *
     * @return
     */
    public function __construct($buildHead = false) {
        if($buildHead === true){
            $this->_head = $this->createNode('HEAD');
        }

        $this->_size = 0;
    }

    /**
     * JTree::getList()
     *
     * Retreives the hash table
     *
     * @return array
     */
    public function getTree() {
        return $this->_list;
    }

    /**
     * JTree::getNode()
     * Given a UID get the node object
     *
     * @param mixed $uid
     * @return JNode/Boolean
     */
    public function getNode($uid) {
        if(empty($uid)) {
            throw new Exception('A unique id is required.');
        }
        $ret = false;
        //look for the node in the hash table
        //return false if not found
        if(array_key_exists($uid,$this->_list) === true) {
            $ret = $this->_list[$uid];
        }
        return $ret;
    }

    /**
     * JTree::setChild()
     *
     * This is a helper function. Given a UID for a node
     * set it as the next UID for the node.
     *
     * @param mixed $uid
     * @param mixed $childUid
     * @return void
     */
    public function setChild($uid, $childUid) {
        if(empty($uid) || empty($childUid)) {
            throw new Exception('Both a from and a to UIDs are required.');
        }
        //get the node object for this node uid
        $node = $this->getNode($uid);

        //node is available
        if($node !== false) {
            $node->setChild($childUid);
        }else{
            //node not available, need to create one

        }
    }

    /**
     * JTree::setParent()
     *
     * This is a helper function to set the parent uid
     *
     * @param mixed $uid - UID of the object to be processed on
     * @param mixed $prevUid - put this as next in the above object
     * @return void
     */
    public function setParent($uid, $parentUid) {
        if(empty($uid) || empty($parentUid)) {
            throw new Exception('Both a from and a to UIDs are required.');
        }
        $node = $this->getNode($uid);

        if($node !== false) {
            $node->setParent($parentUid);
        }
    }

    /**
     * JTree::createNode()
     *
     * Create a node, store in hash table
     * return the reference uid
     * @param mixed $value
     * @param string $uid
     * @return string $uid
     */
    public function createNode($value, $uid = null, $parentUid = null) {
        if(!isset($value)) {
            throw new Exception('A value is required to create a node');
        }


        //check if this node is already ready
        //in which case it must up for modification
        if(isset($this->_list[$uid])){
            $this->modifyNode($value, $uid, $parentUid);
        }else{
            //base node
            $node = new JNode($value, $uid, $parentUid);
            $uid = $node->getUid();
            $this->_list[$uid] = $node;
        }

        //now to check if the parent node is ready
        //if not prepare one now.
        if(isset($parentUid) && !isset($this->_list[$parentUid])){
            $parentNode = new JNode(null, $parentUid);
            $parentUid = $parentNode->getUid();
            $this->_list[$parentUid] = $parentNode;
        }

        //this node now becomes the child of
        //the parent node.
        if(isset($parentUid) && isset($this->_list[$parentUid])){
            $this->addChild($parentUid, $uid);
        }

        return $uid;
    }

    /**
     * JTree::modifyNode()
     *
     * Modify a node's value and parentUid if different
     * return the reference uid
     * @param mixed $value
     * @param string $uid
     * @param string $parentUid
     * @return string $uid
     */
    public function modifyNode($value, $uid, $parentUid){
        $node = $this->getNode($uid);
        if($node !== false){
            $node->setValue($value);
            $node->setParent($parentUid);
        }
        return $uid;
    }

    /**
     * JTree::addChild()
     *
     * @param mixed $parentUid
     * @param mixed $childUid
     * @return
     */
    public function addChild($parentUid = null, $childUid) {
        if(empty($childUid)) {
            throw new Exception('A UID for the child is required.');
        }
        //if no parent assume it is the head
        if(empty($parentUid)) {
            $parentUid = $this->_head;
        }

        if($parentUid == $childUid){
            return $childUid;
        }
        //parent points to child
        $this->setChild($parentUid, $childUid);

        //child points to parent
        $this->setParent($childUid, $parentUid);

        return $childUid;
    }



    /**
     * JTree::addFirst()
     * Add the first child right after the head
     *
     * @param mixed $uid
     * @return void
     */
    public function addFirst($uid) {
        if(empty($uid)) {
            throw new Exception('A unique ID is required.');
        }
        $this->addChild($this->_head, $uid);
    }

    /**
     * JTree::getChildren()
     *
     * This is a helper function to get the child node uids given the node uid
     *
     * @param mixed $uid
     * @return mixed
     */
    public function getChildren($uid) {
        if(empty($uid)) {
            throw new Exception('A unique ID is required.');
        }

        $node = $this->getNode($uid);

        if($node !== false) {
            return $node->getChildren();
        }
    }

    /**
     * JTree::getParent()
     *
     * This is a helper function to get the
     * parent node uid
     *
     * @param mixed $uid
     * @return string $uid
     */
    public function getParent($uid) {
        if(empty($uid)) {
            throw new Exception('A unique ID is required.');
        }
        $ret = false;
        $node = $this->getNode($uid);

        if($node !== false) {
            $ret = $node->getParent();
        }
        return $ret;
    }

    /**
     * JTree::getValue()
     *
     * @param mixed $uid
     * @return
     */
    public function getValue($uid) {
        if(empty($uid)) {
            throw new Exception('A unique ID is required.');
        }

        $node = $this->getNode($uid);
        return $node->getValue();
    }
}

/**
 * JNode
 *
 * This is a simple class to construct a node
 * Please note that each node object will be
 * eventually stored in a hash table where the
 * hash will be a UID.
 *
 * Note that in comparison to thee Doubly Linked List implementation
 * the children are now stored in an array
 *
 * @package JTree
 * @author Jayesh Wadhwani
 * @copyright Jayesh Wadhwani
 * @version 2011
 */
class JNode {
    /**
     * @var _value for the value field
     */
    private $_value;
    /**
     * @var _parent uid of the parent node
     */
    private $_parent;
    /**
     * @var _children collection of uids for the child nodes
     */
    private $_children = array();
    /**
     * @var _uid for this node
     */
    private $_uid;

    /**
     * JNode::__construct()
     *
     * @param mixed $value
     * @param mixed $uid
     * @return void
     */
    public function __construct($value = null, $uid = null, $parentUid = null) {
        $this->setValue($value);
        $this->setUid($uid);
        $this->setParent($parentUid);
    }

    /**
     * JNode::setUid()
     *
     * @param mixed $uid
     * @return
     */
    public function setUid($uid = null) {
        //if uid not supplied...generate
        if(empty($uid)) {
            $this->_uid = uniqid();
        } else {
            $this->_uid = $uid;
        }
    }

    /**
     * JNode::getUid()
     *
     * @return string uid
     */
    public function getUid() {
        return $this->_uid;
    }

    /**
     * JNode::setValue()
     *
     * @param mixed $value
     * @return void
     */
    public function setValue($value) {
        if($this->_value !== $value){
            $this->_value = $value;
        }
    }

    /**
     * JNode::getValue()
     *
     * @return mixed
     */
    public function getValue() {
        return $this->_value;
    }

    /**
     * JNode::getParent()
     *
     * gets the uid of the parent node
     *
     * @return string uid
     */
    public function getParent() {
        return $this->_parent;
    }

    /**
     * JNode::setParent()
     *
     * @param mixed $parent
     * @return void
     */
    public function setParent($parent) {
        if($this->_parent !== $parent){
            $this->_parent = $parent;
        }
    }

    /**
     * JNode::getChildren()
     *
     * @return mixed
     */
    public function getChildren() {
        return $this->_children;
    }

    /**
     * JNode::setChild()
     *
     * A child node's uid is added to the childrens array
     *
     * @param mixed $child
     * @return void
     */
    public function setChild($child) {
        if(!empty($child)) {
            $this->_children[] = $child;
        }
    }

    /**
     * JNode::anyChildren()
     *
     * Checks if there are any children
     * returns ture if it does, false otherwise
     *
     * @return bool
     */
    public function anyChildren() {
        $ret = false;

        if(count($this->_children) > 0) {
            $ret = true;
        }
        return $ret;
    }

    /**
     * JNode::childrenCount()
     *
     * returns the number of children
     *
     * @return bool/int
     */
    public function childrenCount() {
        $ret = false;
        if(is_array($this->_children)){
            $ret = count($this->_children);
        }
        return $ret;
    }
}

/**
 * JTreeIterator
 *
 * The Tree structure would be incomplete if I did not include a
 * iterator. There is nothing special about this iterator and its implementation
 * is pretty standard.
 * I have extended the arrayIterator because I am using an array for my hash table.
 * Note that I have not implemented the next and rewind methods as I do not need to
 * special with these. So the parent(ArrayIterator) methods will be called by default.
 *
 * @package
 * @author  Jayesh Wadhwani
 * @copyright Jayesh Wadhwani
 * @version 2011
 */
class JTreeIterator extends ArrayIterator implements RecursiveIterator {
    /**
     * @var _list this is the hash table
     */
    private $_list = array();
    /**
     * @var _next this is for the children
     */
    private $_next = array();
    /**
     * @var _position the iterator position
     */
    private $_position;

    /**
     * JTreeIterator::__construct()
     *
     * @param mixed $list - the hash table
     * @param mixed $tree -
     * @return JTreeIterator
     */
    public function __construct(array $list, array $tree = null) {
        $this->_list = $list;

        if(is_null($tree)) {
            reset($this->_list);
            $next = current($this->_list);
            //UPDATE: start with current node rather than the children
            //this way the root node is displayed.
            $this->_next = array(key($this->_list));//$next->getChildren();
        } else {
            $this->_next = $tree;
        }

        parent::__construct($this->_next);
    }

    /**
     * JTreeIterator::current()
     *
     * @return
     */
    public function current() {
        //get the object uid from the hash table
        //then get the object
        $current = parent::current();
        $nObj = $this->_list[$current];
        return $nObj;
    }


    /**
     * JTreeIterator::key()
     *
     * @return
     */
    public function key() {
        $key = parent::key();
        $key = $this->_next[$key];
        return $key;
    }

    /**
     * JTreeIterator::hasChildren()
     *
     * @return mixed
     */
    public function hasChildren() {
        $next = $this->_list[$this->key()];
        $next = $next->anyChildren();
        return $next;
    }

    /**
     * JTreeIterator::getChildren()
     *
     * @return JTreeIterator
     */
    public function getChildren() {
        $childObj = $this->_list[$this->key()];
        $children = $childObj->getChildren();
        return new JTreeIterator($this->_list, $children);
    }
}

