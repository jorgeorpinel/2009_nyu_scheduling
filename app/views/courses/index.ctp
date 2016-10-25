<h2><?=$this->pageTitle?></h2>



<!-- div class="right" style="margin-right: 5px;">
  Order by
  <select id="CourseOrder">
    <option value="cid">course number</option>
    <option value="cid">course title</option>
    <option value="nid">instructor NYU ID number</option>
    <option value="name">instructor name</option>
    <option value="time">most time available</option>
  </select>
</div -->



<h3><?=$currentPeriod['Period']['type'].' '.$currentPeriod['Period']['year']?>
  Private Lessons</h3>

<?if(empty($courses)):?>
  <div class="warning">No courses have been loaded in this system yet.</div>
<?endif;?>
<?$i = 0;
  foreach($courses as $course): ?>

<table style="border: none; width: auto;"><tr>
  <th style="border: none;">
    <?php echo $course['id']; ?>&nbsp;&nbsp;&nbsp;</th>
  <th style="border: none;"><?=$html->link($course['title'], '/course/'.$course['id'])?>&nbsp;&nbsp;&nbsp;</th>
</tr></table>

<?  if(count($course['Faculty']) != 0): ?>
<div class="isolated"><table>
  <tr class="unbold"><th>Name</th><th>Email</th><th>Times Available</th></tr>
<?  endif; ?>
  
<?  foreach($course['Faculty'] as $instructor): ?>

  <tr class="highlight">
    <td><b><?=$instructor['first_name'].' '.$instructor['last_name']?></b></td>
    <td><?php echo $instructor['email']; ?></td>
    <td><table>
<?    if(empty($course['TimeSlot'])):?>
      <tr><td><b>TBA</b></td></tr>
<?    endif;?>
<?    $noTs = true;
      foreach($course['TimeSlot'] as $ts=>$timeSlot):
        if($timeSlot['faculty_id'] == $instructor['id']) {
          $timeSlot['start_time'] = strftime('%I:%M %p', strtotime($timeSlot['start_time']));
          $timeSlot['end_time'] = strftime('%I:%M %p', strtotime($timeSlot['end_time'])); ?>
      <tr><td class="selectable" onClick="window.location='<?=$html->url('/time_slots/'.$timeSlot['id'].(isset($isStudent)?'/'.$session->read('User.id'):null)); ?>'">
        <?=$html->link('<b>'.$timeSlot['day'].'</b> from '.$timeSlot['start_time'].' to '.$timeSlot['end_time'].' @ '.$timeSlot['location']
        , "/time_slots/{$timeSlot['id']}".(isset($isStudent)?'/'.$session->read('User.id'):null), array(), false, false)?>
      </td></tr>
<?        unset($course['TimeSlot'][$ts]); $noTs = false;
        } 
      endforeach; ?>
    </table></td>
  </tr>

<?  endforeach;
  if(count($course['Faculty']) != 0): ?>
</table></div>
<?  else: ?>
<div class="mini warning">No faculty assigned yet. <?=$html->link('Assign now', "/admin/courses/edit/{$course['id']}#faculty")?></div>
<?  endif; ?>

<?endforeach; ?>



<?//pr($currentPeriod); // debug ?>
<?//pr($courses); // debug ?>