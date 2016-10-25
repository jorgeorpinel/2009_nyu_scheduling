<h2><?=$this->pageTitle?></h2>

<?=$form->create('User', array('url'=>"/{$this->data['User']['type']}/edit/{$this->data['User']['id']}"))?>

  <p>This user, with ID <b><?=$this->data['User']['id']?></b> is part of the <b><?=$this->data['User']['type']?></b>.</p>
  <?=$form->input('password', array('after'=>' leave blank to keep the existing one'))?>

  <h3 class="separated">Personal Information</h3>
  <?=$form->input('first_name')?>
  <?=$form->input('last_name')?>

  <h3 class="separated">Contact Information</h3>
  <?=$form->input('email', array('after'=>' This will be the preferred channel of communication.'))?>
  <?=$form->input('telephone', array('after'=>' not shared with other users'))?>

<?if(!empty($this->data['User']['status'])): ?>
  <p>This student has a <b><?=$this->data['User']['status']=='n'?'non-':null?>major</b> status in the music department.</p>
<?endif; ?>

  <div class="clear kicker"></div>
  <?=$form->end('Save these Changes', array('class'=>'separated'))?>

<?//pr($this->data)	// debug ?>
