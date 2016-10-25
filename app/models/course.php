<?php
class Course extends AppModel {

	var $name = 'Course';
	var $validate = array(
		'title' => array('notEmpty'),
	);

	var $hasMany = array(
		'TimeSlot' => array(	// time slot(s) (for each professor) propossed for this course, although...
			'className' => 'TimeSlot',
			'foreignKey' => 'course_id',
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
	
	// ---

	var $hasAndBelongsToMany = array(
		'Faculty' => array(
			'className' => 'User',
			'joinTable' => 'courses_users',
			'foreignKey' => 'course_id',
			'associationForeignKey' => 'user_id',
			'unique' => true,
			'conditions' => "type = 'faculty'",
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'finderQuery' => '',
			'deleteQuery' => '',
			'insertQuery' => ''
		),
		'Student' => array(
			'className' => 'User',
			'joinTable' => 'courses_users',
			'foreignKey' => 'course_id',
			'associationForeignKey' => 'user_id',
			'unique' => true,
			'conditions' => "type = 'student'",
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'finderQuery' => '',
			'deleteQuery' => '',
			'insertQuery' => ''
		),
		'Period' => array(
			'className' => 'Period',
			'joinTable' => 'courses_periods',
			'foreignKey' => 'course_id',
			'associationForeignKey' => 'period_id',
			'unique' => true,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'finderQuery' => '',
			'deleteQuery' => '',
			'insertQuery' => ''
		),
	);

}
?>