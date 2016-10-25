<h3><?=$this->pageTitle?></h3>

<?=$form->create('User', array('url'=>"/admin/$urlType/add"))?>
  <?=$form->hidden('type', array('value'=>$type))?>
  
<?if($type == 'admin'): ?>
  <?=$form->input('id', array('type'=>'text', 'label'=>'username', 'after'=>' max 9 letters'))?>
<?else: ?>
  <?=$form->input('id', array('type'=>'text', 'label'=>'NYU ID #', 'after'=>' will be used as username'))?>
<?endif; ?>
  <div class="mini message">Password will be auto-assigned and given to user on his 1st login.</div>
  
<?if($type == 'student'): ?>
  <?=$form->input('status', array('label'=>'Major status', 'options'=>array('m'=>'major', 'n'=>'non-major'), 'empty'=>' - select one - '))?>
  <? // todo: active periods?>
<?endif; ?>
  
  <?=$form->input('first_name', array('label'=>'First Name'))?>
  <?=$form->input('last_name', array('label'=>'Last Name'))?>
  
  <?=$form->input('email')?>
  <?=$form->input('telephone')?>

  <div class="clear kicker"></div>
  <?=$form->end('Create')?>

<?pr($this->data)?>