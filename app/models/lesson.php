<?php
class Lesson extends AppModel {

	var $name = 'Lesson';
	var $validate = array(
		'time_slot_id' => array('numeric'),	// xxx: why leave this?
		//'student_id' => array('notEmpty'),	// xxx: regex?
		'location' => array('notEmpty')
	);
	
	// ---

	var $belongsTo = array(
		'TimeSlot' => array(
			'className' => 'TimeSlot',
			'foreignKey' => 'time_slot_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Student' => array(
			'className' => 'User',
			'foreignKey' => 'student_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

}
?>