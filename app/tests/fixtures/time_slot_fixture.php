<?php 
/* SVN FILE: $Id$ */
/* TimeSlot Fixture generated on: 2009-09-09 11:52:43 : 1252511563*/

class TimeSlotFixture extends CakeTestFixture {
	var $name = 'TimeSlot';
	var $table = 'time_slots';
	var $fields = array(
		'id' => array('type'=>'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'faculty_id' => array('type'=>'integer', 'null' => false, 'default' => NULL),
		'days' => array('type'=>'string', 'null' => false, 'default' => NULL, 'length' => 7),
		'start_time' => array('type'=>'time', 'null' => false, 'default' => NULL),
		'end_time' => array('type'=>'time', 'null' => false, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1))
	);
	var $records = array(array(
		'id'  => 1,
		'faculty_id'  => 1,
		'days'  => 'Lorem',
		'start_time'  => '11:52:43',
		'end_time'  => '11:52:43'
	));
}
?>