<?php
class User extends AppModel {

	var $name = 'User';
	var $validate = array(
		//'password' => array('notEmpty'),
		'type' => array('enum'=>array('rule'=>array('inList', array('admin', 'faculty', 'student')))),
		'first_name' => array('notEmpty'),
		'last_name' => array('notEmpty'),
		'email' => array('email'),
		//'telephone' => array('notEmpty'),
		'status' => array('enum'=>array('rule'=>array('inList', array('m', 'n')))),
	);

	var $hasMany = array(
		'TimeSlot' => array(	// User may have time slots, if faculty member
			'className' => 'TimeSlot',
			'foreignKey' => 'faculty_id',
			'dependent' => true,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
		'Lesson' => array(
			'className' => 'Lesson',
			'foreignKey' => 'student_id',
			'dependent' => true,
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

	//The Associations below have been created with all possible keys, those that are not needed can be removed
	var $hasAndBelongsToMany = array(
		'Course' => array(
			'className' => 'Course',
			'joinTable' => 'courses_users',
			'foreignKey' => 'user_id',
			'associationForeignKey' => 'course_id',
			'unique' => true,
			'conditions' => '',	// could be only for the current period
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'finderQuery' => '',
			'deleteQuery' => '',
			'insertQuery' => ''
		),
		'Period' => array(	// only students have period activity status
			'className' => 'Period',
			'joinTable' => 'periods_students',
//			'with'=>'PeriodsStudent',	// for .status field usage
			'foreignKey' => 'student_id',
			'associationForeignKey' => 'period_id',
			'unique' => true,
			'conditions' => '', //  User.type = 'student'
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'finderQuery' => '',
			'deleteQuery' => '',
			'insertQuery' => ''
		)
	);
	
	
	function beforeSave() {
	  // xxx: check User.id format? regxp, length, etc
	  
		// encodes password with MD5:
		if(!empty($this->data['User']['password']))
			$this->data['User']['password'] = md5($this->data['User']['password']);
			
		// only admin A1 himself can edit himself
    if($this->data['User']['id']=='A1' and $this->Session->read('User.id')!='A1') return false;
			
		return true;
	}
	
	
	function beforeDelete() {
		// user A1 is godlike:
		if($this->data['User']['id'] == 'A1') return false;
			
		return true;
	}

}
?>