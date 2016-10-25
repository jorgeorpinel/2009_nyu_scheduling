<?php
class TimeSlot extends AppModel {

	var $name = 'TimeSlot';
	var $validate = array(
		//'faculty_id' => array('notEmpty'),
		//'course_id' => array('notEmpty'),
		'location' => array('notEmpty'),
		'day' => array('notEmpty'),
		'start_time' => array('time'),
		'end_time' => array('time')
	);


	var $belongsTo = array(
		'Faculty' => array(
			'className' => 'User',
			'foreignKey' => 'faculty_id',
			'conditions' => "type = 'faculty'",
			'fields' => '',
			'order' => ''
		),
		'Course' => array(	// time for this course.
			'className' => 'Course',
			'foreignKey' => 'course_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

	// ---

	var $hasMany = array(
		'Lesson' => array(
			'className' => 'Lesson',
			'foreignKey' => 'time_slot_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		)
	);

}
?>