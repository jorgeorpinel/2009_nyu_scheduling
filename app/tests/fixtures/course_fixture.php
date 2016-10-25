<?php 
/* SVN FILE: $Id$ */
/* Course Fixture generated on: 2009-09-08 15:05:35 : 1252436735*/

class CourseFixture extends CakeTestFixture {
	var $name = 'Course';
	var $table = 'courses';
	var $fields = array(
		'id' => array('type'=>'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'period_id' => array('type'=>'integer', 'null' => false, 'default' => NULL),
		'full_number' => array('type'=>'string', 'null' => false, 'default' => NULL, 'length' => 12),
		'title' => array('type'=>'string', 'null' => false, 'default' => NULL),
		'credits' => array('type'=>'integer', 'null' => false, 'default' => NULL, 'length' => 4),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1))
	);
	var $records = array(array(
		'id'  => 1,
		'period_id'  => 1,
		'full_number'  => 'Lorem ipsu',
		'title'  => 'Lorem ipsum dolor sit amet',
		'credits'  => 1
	));
}
?>