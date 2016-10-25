<?php
class Period extends AppModel {

	var $name = 'Period';
	var $validate = array(
		'type' => array('inList', array('Fall', 'Spring', 'Summer')),
		'year' => array('numeric')
	);

	var $hasAndBelongsToMany = array(	// is related to students
		'Student' => array(
			'className' => 'User',
			'joinTable' => 'periods_students',
			'foreignKey' => 'period_id',
			'associationForeignKey' => 'student_id',
      'unique' => true,
      'dependant' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'finderQuery' => '',
			'deleteQuery' => '',
			'insertQuery' => ''
		),
		'Course' => array(
			'className' => 'Course',
			'joinTable' => 'courses_periods',
			'foreignKey' => 'period_id',
			'associationForeignKey' => 'course_id',
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