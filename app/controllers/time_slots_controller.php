<?php
/**
 * @copyright (c) New York University 2009
 * @author Jorge Orpinel
 */
class TimeSlotsController extends AppController {

	var $name = 'TimeSlots';
	var $helpers = array('Html', 'Form', 'Javascript');
	
	
	
	/** Shows a time-slot's usage and allows to create a new lesson whithin it.
	 * @param int $timeSlotId (in $this->param['id']) time-slot id to use
	 * @param NYUID $userId student being registered if known (must be registered for that course)
	 */
	function index($timeSlotId = null, $studentId = false) {
    if(isset($this->params['id'])) $timeSlotId = $this->params['id'];
    else { $this->cakeError('error404'); return; }
    
    if(isset($this->params['studentId'])) $studentId = $this->params['studentId'];
    
	  // access control: (admin or student)
    $userType = $this->Session->read('User.type');
    $userId = $this->Session->read('User.id');
	  if(empty($this->data) and $userType == 'student' and !$studentId)
	    { $this->redirect("/time_slots/$timeSlotId/$userId"); return; }
	  
	  if($userType=='student' and $studentId!=$userId) {
	    $this->cakeError('error404'); return; }
		
		$time_slot = $this->TimeSlot->query(	// super triple join to get the time-slot being IN this period
			'SELECT Period.* FROM'
			.' periods Period JOIN courses_periods as CP ON (Period.id = CP.period_id)'
				.' JOIN courses Course ON (CP.course_id = Course.id)'
					.' JOIN time_slots TimeSlot ON (Course.id = TimeSlot.course_id)'
			.' WHERE Period.id = '.$this->Session->read('Period.id')
				." AND TimeSlot.id = '$timeSlotId'"
				.' LIMIT 1;'
		);
		if(empty($time_slot)) { $this->cakeError('error404'); return; }	// :security (1)
//pr($time_slot);	// debug
		
		// Get timeslot and its course's students:
		$this->TimeSlot->Faculty->unbindModel(array(
			'hasMany'=>array('TimeSlot', 'Lesson'),
			'hasAndBelongsToMany'=>array('Course', 'Period'),
		));
		$this->TimeSlot->Course->unbindModel(array(
			'hasMany'=>array('TimeSlot'),
			'hasAndBelongsToMany'=>array('Faculty', 'Period'),
		));
		$this->TimeSlot->Lesson->unbindModel(array('belongsTo'=>array('TimeSlot')));
		$this->TimeSlot->Lesson->bindModel(array('belongsTo'=>array('Student'=>array('fields'=>'first_name, last_name'))));
		if(!$timeSlot = $this->TimeSlot->find('first', array('conditions'=>array('TimeSlot.id'=>$timeSlotId), 'recursive'=>2) ))
			{ $this->cakeError('error404'); return; }	// :security xxx duplicated check (1)
		
		if(empty($timeSlot['Course']['Student'])) {	// :validation - course must have students registered
			$this->Session->setFlash("There is no student(s) registered for this course, can't schedule a lesson.", array(), null, 'warning');
			$this->redirect('/admin/courses/'.$timeSlot['Course']['id']); return;
		}
		
		// xxx: this next is useful but would need to be implemented in schedule views and whenever else using existing lesosns
//		// strip WRONG lessons with unregistered courses: this should never be necessary!
//		foreach($timeSlot['Lesson'] as $l=>$lesson) {
//		  $isRegistered = false;
//		  foreach($timeSlot['Course']['Student'] as $student)
//		    if($student['id'] == $lesson['student_id']) $isRegistered = true;
//		  if(!$isRegistered) unset($timeSlot['Lesson'][$l]); // x x x: garbage in the DB
//		}
		
    // access control:
    if($userType == 'faculty') { // instructors have a more limited display
      $studentId = false;
      $this->set('isFaculty', true);
    }
    elseif($userType == 'student') { // if user is student, only works for his own courses:
      $isRegistered = false;
      foreach($timeSlot['Course']['Student'] as $student) if($student['id'] == $userId) { $isRegistered = true; break; }
      if(!$isRegistered) { $this->cakeError('error404'); return; } // :security
      $this->set('userId', $userId);
    }
    
		if(!empty($this->data)) {	// make a lesson! data sent:
		
		  // validate:
		  if(empty($this->data['Student']['id'])) {
		    $this->Session->setFlash('Please select a student from the list.', array(), null, 'warning');
		    $this->redirect('/time_slots/'.$timeSlot['TimeSlot']['id']);  // $studentId shouldn't be checked.
		    return;
		  }
		  
			$isRegistered = -1;	// find student registered in course or error404:
			foreach($timeSlot['Course']['Student'] as $s=>$student)
				if($student['id'] == $this->data['Student']['id']) { $isRegistered = $s; break; }
			if($isRegistered < 0) { $this->cakeError('error404'); return; }	// :security
			
			$timeValid = true; // check if time chosen is available in time-slot minus what's taken by it's lessons:
      if(!empty($this->data['Lesson']['minute']))
        if(is_numeric($this->data['Lesson']['minute']) and $this->data['Lesson']['minute']>0 and $this->data['Lesson']['minute']<60)
          $this->data['Lesson']['st']['min'] = str_pad($this->data['Lesson']['minute'], 2, '0', STR_PAD_LEFT);
        else $timeValid = false;  // could be more informative (see following)
      $duration = $this->_courseDuration($timeSlot['Course']['Student'][$isRegistered]['status'], $timeSlot['Course']['Student'][$isRegistered]['CoursesUser']['credits']);
			if($this->data['Lesson']['st']['hour'] == 12) $this->data['Lesson']['st']['hour'] = 0;
      $this->data['Lesson']['start_time'] = $this->data['Lesson']['st']['hour'].':'.$this->data['Lesson']['st']['min'].':00';
			$endHour = $this->data['Lesson']['st']['hour'] + (int)($duration/60);
			$endMin = $this->data['Lesson']['st']['min'] + $duration%60;
			// checks start/end times absolute range and coherence:
      if($this->data['Lesson']['st']['hour'] < 8)
        $timeValid = false;  // could be more informative (see following)
			if($endMin > 60) {$endMin %= 60; $endHour++;}
			if($endHour > 22)
			  $timeValid = false;  // could be more informative (see following)
			$this->data['Lesson']['end_time'] = str_pad($endHour, 2, '0', STR_PAD_LEFT).':'.str_pad($endMin, 2, '0', STR_PAD_LEFT).':00';
      if($this->data['Lesson']['end_time'] <= $this->data['Lesson']['start_time'])
        $timeValid = false;  // could be more informative (see following)
			// checks new lesson is in time-slot:
			if($this->data['Lesson']['start_time']<$timeSlot['TimeSlot']['start_time'] or $this->data['Lesson']['end_time']>$timeSlot['TimeSlot']['end_time'])
					$timeValid = false;	// could be more informative (see following)
			// if ok til now, checks for conflicts with any other lesson 
      if($timeValid) foreach($timeSlot['Lesson'] as $l=>$lesson) {// check free time xxx: old lessons assumed to be correct
        if($this->data['Student']['id'] == $lesson['student_id']) {$prevLesson = $l; continue;}  // xxx: assuming only no more than 1 lesson is scheduled
        if(($this->data['Lesson']['start_time']>$lesson['start_time'] and $this->data['Lesson']['start_time']>=$lesson['end_time'])
          or ($this->data['Lesson']['end_time']<=$lesson['start_time'] and $this->data['Lesson']['end_time']<$lesson['end_time'])) ;
         else $timeValid = false; // could be more informative (see following message)
      }
			
			if(!$timeValid) {	// :validation error
				$this->Session->setFlash('Time not available. Please retry.', array(), null, 'warning');
				$this->redirect('/time_slots/'.$timeSlot['TimeSlot']['id'].($studentId?('/'.$studentId):null));
				return;
			}
			
			// SAVE IT YAAAAAAAAY: note: lessons table now has a timeslot_student UNIQUE index
			
			// remove previous lessons for this student in this course.  xxx: A mysql transaction would be nice.
			if(isset($prevLesson)) $this->admin_cancel($timeSlot['Lesson'][$prevLesson]['id'], $studentId, true);
			
//pr($this->data);	// debug
//pr($timeSlot);	// debug
			$this->data['Lesson']['time_slot_id'] = $timeSlot['TimeSlot']['id'];
			$this->data['Lesson']['student_id'] = $this->data['Student']['id'];
			if(!$this->TimeSlot->Lesson->save($this->data['Lesson'])) {	// this should never occur...
				$this->Session->setFlash('Unexpected database error.'     // SPECIALLY if above delete (1) worked:
				  .(isset($prevLesson)?' WARNING: A PREVIOUS LESSON WAS DELETED FOR THIS STUDENT/COURSE BEFORE THE DB ERROR!':null)
				  .' Please try again.', array(), null, 'error');
				$this->redirect('/time_slots/'.$timeSlot['TimeSlot']['id'].($studentId?('/'.$studentId):null));
				return;
			}
			$this->Session->setFlash('Lesson created'.(isset($prevLesson)?' (replacing a previous one)':null), array(), null, 'success');
			// xxx: send email?
			$this->redirect("/time_slots/{$timeSlot['TimeSlot']['id']}/{$this->data['Student']['id']}");	// xx: return somewhere else?
			return;
		}
		
		$this->pageTitle = 'New "'.$timeSlot['Course']['title'].'" Lesson';
		
		// Prepare time-slot data:
		$timeSlot['Period'] = $time_slot[0]['Period'];
		$timeSlot['st'] = explode(':', $timeSlot['TimeSlot']['start_time']);	// 0 is the hour, 1 is the minute
		$timeSlot['et'] = explode(':', $timeSlot['TimeSlot']['end_time']);		// " "
		$timeSlot['duration'] = (60*$timeSlot['et'][0]+$timeSlot['et'][1]) - (60*$timeSlot['st'][0]+$timeSlot['st'][1]);	// in minutes
		$days = array('M'=>'Monday','T'=>'Tuesday','W'=>'Wednesday','R'=>'Thursday','F'=>'Friday','S'=>'Saturday');
		$timeSlot['day'] = $days[$timeSlot['TimeSlot']['day']];	// in minutes
		
		$this->set('timeSlot', $timeSlot);
		
		// Get student if sent:
		$student = array();
		if($studentId) {
			foreach($timeSlot['Course']['Student'] as $stu)
				if($stu['id'] == $studentId) { $student = $stu; break; }
			if(!empty($student)) {
				$student['CoursesUser']['duration'] = $this->_courseDuration($student['status'], $student['CoursesUser']['credits']);
				$this->set('student', $student);
			} // else xx: this would be the WRONG NYUID in the url option. right now its just ignored (see below)
		}
		// if none or wrong id, set the one(s) registered for the course:
		if(empty($student)) {
			if(count($timeSlot['Course']['Student']) == 1) {
				$student = $timeSlot['Course']['Student'][0];
				$student['CoursesUser']['duration'] = $this->_courseDuration($student['status'], $student['CoursesUser']['credits']);
				$this->set('student', $student);
			}
			else {
				foreach($timeSlot['Course']['Student'] as $student)
					$students[$student['id']] = $student['first_name'].' '.$student['last_name'].' ('.($student['status']=='n'?'non-':null)."major, {$student['CoursesUser']['credits']} cr: ".$this->_courseDuration($student['status'], $student['CoursesUser']['credits']).' min)';
				$this->set('students', $students);
			}
		}
	}

		/** Calculates a lesson's duration according to the student's major/non-major status and the credits he's registered for.
		 * Used in index()
		 * @param String $status 'm' for major or 'n' for non-major
		 * @param int $credits 2, 3,or 4 credits the student is registered for in a course
		 * @returns duration in minutes: 30 or 60. Returns 0 if parameters are not valid.
		 */
		function _courseDuration($status, $credits) {
			$duration = array('m'=>array(2=>30, 3=>60), 'n'=>array(2=>30, 4=>60));
			if(isset($duration[$status][$credits])) return $duration[$status][$credits];
			
			return 0;
		}


  /** Cancel a lesson
   * No view for this one
   * @param int $lessonId DB lessons table id of the lesson to remove
   * @param NYUID $studentId (optional) specify who's scheduling are we trying to perform for redirection purposes
   * $param bool $return if true means another method is using cancel. xxx: assumes lessonId passed exists
   */
  function admin_cancel($lessonId, $studentId = false, $return = false) {
    if(!$return) { // normal behavior (/admin/time_slots/cancel)
      $this->autoRender = false;
      if(!$lesson = $this->TimeSlot->Lesson->find('first', array('conditions'=>"id = $lessonId", 'fields'=>'time_slot_id, student_id', 'recursive'=>-1)))
      { $this->cakeError('error404'); return; } // :security (the lesson doesnt exist)
      if($this->Session->read('User.type') == 'student')
        if($this->Session->read('User.id') != $lesson['Lesson']['student_id'])
        { $this->cakeError('error404'); return; } // :security (the <student>user in session is not the lesson's student)
      $studentId = $lesson['Lesson']['student_id'];
    }
    
    if(!$this->TimeSlot->Lesson->del($lessonId)) {  // this should never occur (1)
      $this->Session->setFlash('Unexpected database error while attempting to cancel an existing lesson. Please try again.', array(), null, 'error');
      $this->redirect('/time_slots/'.$lessonId.($studentId?('/'.$studentId):null));
      if($return) return false;
      return;
    }
    // xxx: email?
    if($return) return true;  // when caled from inside another method
    
    $this->Session->setFlash('The lesson was canceled.', array(), null, 'success');
    $this->redirect('/time_slots/'.$lesson['Lesson']['time_slot_id'].($studentId?'/'.$studentId:null));
  }
  

  
  /** @todo Creates a new time-slot (on individual basis)
   * View at /admin/time_slots/create/($courseID/$facultyId)
   * @param $courseID      full number of the offered course
   * @param $facultyId     (optional) NYUID of the faculty giving
   */
  function admin_create($courseID, $facultyId = false) {
    if(!empty($this->data)) {  // link user - course! new time-slot data sent:
      pr($this->data);
    }
    
    echo $courseID; echo $facultyId;
  }
  

  /** @todo Edits an existing time-slot
   * View at /admin/time_slots/create/($courseID/$facultyId)
   * @param $timeSlotId  time-slot id to edit
   */
  function admin_edit($timeSlotId) {
    if(!empty($this->data)) {  // link user - course! new time-slot data sent:
      pr($this->data);
    }
    
    echo $timeSlotId;
  }
}
?>