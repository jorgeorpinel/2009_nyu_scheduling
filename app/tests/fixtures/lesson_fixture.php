<?php 
/* SVN FILE: $Id$ */
/* Lesson Fixture generated on: 2009-09-08 16:36:20 : 1252442180*/

class LessonFixture extends CakeTestFixture {
	var $name = 'Lesson';
	var $table = 'lessons';
	var $fields = array(
		'id' => array('type'=>'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'course_id' => array('type'=>'integer', 'null' => false, 'default' => NULL),
		'time_slot_id' => array('type'=>'integer', 'null' => false, 'default' => NULL),
		'student_id' => array('type'=>'integer', 'null' => false, 'default' => NULL),
		'location_id' => array('type'=>'integer', 'null' => false, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1))
	);
	var $records = array(array(
		'id'  => 1,
		'course_id'  => 1,
		'time_slot_id'  => 1,
		'student_id'  => 1,
		'location_id'  => 1
	));
}
?>