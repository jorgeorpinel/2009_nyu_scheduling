<h2>Sign in to the Scheduling System</h2>

<div class="mini message">
	<b>DEMO:</b> Sample student credentials are N16176849:student.<br/>
	More users and info at https://github.com/jorgeorpinel/2009_nyu_scheduling#usage.
</div>


<div class="right isolated" style="width: 336px;">
	This is the new private lessons scheduling system.
</div>


<?=$form->create('User', array('id'=>'login', 'class'=>'isolated', 'url'=>'/login'))?>

	<h3>User Authentication</h3>

	<?=$form->error('information', 'User not recognized', array('class'=>'mini error'))?>

	<div class="input">
		<label for="PeriodId">Period</label>&nbsp;
		<select id="PeriodId" name="data[Period][id]">
			<option> - - - </option>
<?	foreach($periods as $y=>$year): foreach($year as $p=>$period): ?>
			<option value="<?=$p?>"<?=$p==$currentPeriod?' selected="selected"':null?>><?=$period.' '.$y?></option?>
<?	endforeach; endforeach; ?>
		</select>
	</div>
	<?=$form->input('id', array('between'=>'&gt; ', 'type'=>'text', 'value'=>'N16176849', 'label'=>'<b>&gt;</b> NYU ID Number'))?>
	<?=$form->input('password', array('between'=>'* ', 'value'=>'student', 'label'=>'<b>*</b> Password'))?>

<?$javascript->link('prototype.js', false); ?>
	<a href="javascript:window.location='<?=$html->url('/users/password')?>'+'/'+$('UserId').value">Reset my password (1st login or forgotten)</a>

	<div class="clear kicker"></div>

<?=$form->end('Submit')?>

<?pr($this->data)?>
