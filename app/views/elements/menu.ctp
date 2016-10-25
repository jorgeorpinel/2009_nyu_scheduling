<div id="menu">

<?$type = $session->read('User.type');
$userId = $session->read('User.id');

if($type == 'admin'):

		if(isset($clearCoursesSearch)): ?>
	<b><?=$html->link('All Courses', '/admin/courses', array('style'=>'color: #ff0;'))?></b>
<?	else: ?>
	<b><?=$html->link('Courses', '/admin/courses')?></b>
<?	endif;

		if(isset($clearFacultySearch)): ?>
	<?=$html->link('All Faculty', '/admin/faculty', array('style'=>'color: #ff0;'))?>
<?	else: ?>
	<?=$html->link('Faculty', '/admin/faculty')?>
<?	endif;

		if(isset($clearStudentsSearch)): ?>
	<?=$html->link('All Students', '/admin/students', array('style'=>'color: #ff0;'))?>
<?	else: ?>
	<?=$html->link('Students', '/admin/students')?>
<?	endif; ?>

	<?=$html->link('Users', '/admin/users')?>

<?else: ?>

	<?=$html->link('My Courses', '/my_courses')?>
<?$type = $session->read('User.type');
	if($type == 'student') $type .= 's';?>
	<?=$html->link('My Schedule', "/$type/schedule/$userId")?>
  
  | <?=$html->link('my info', "/$type/edit")?>

<?endif; ?>

	| <?=$html->link('logout', '/logout')?>
</div>