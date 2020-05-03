<div class="gym">
	<div class="gym-details">
		<div class="gym-details__name">
			``$gym["name"]``
		</div>
		<div class="gym-details__map">
			Lokalizacja siłowni: ``$gym["lat"].",".$gym["long"]``
		</div>
		<div class="gym-details__picture">
			<a href='https://www.google.com/maps/search/?api=1&query=``$gym["lat"].",".$gym["long"]``' target='_blank'>
				<img src="``PATH_PREFIX``/``$album[0]['path']``" alt="">
			</a>
		</div>
	</div>

	<div class='gym-records'>
		<h2>Rekordy siłowni:</h2>
		<?php foreach($records as $exercise): ?>
			<div class="gym-record">
				<h3>``$exercise["exercise_type"]``</h3>
				<h4>``$exercise["weight"]`` kg</h4>
				<img src="``PATH_PREFIX``/``$exercise['avatar']``">
				<br>
				<span>``$exercise["name"]``</span>
				<span>``$exercise["date"]``</span>
			</div>
		<?php endforeach ?>
	</div>

	<div class="gym-frequenters">
		<h2>Stali bywalcy:</h2>
		<?php foreach ($frequenters as $user): ?>
		<div class="gym-frequenter">
			<img src="``PATH_PREFIX``/``$user['avatar']``">
			<br>
			<span>``$user["name"]``</span>
			<br>
			<span>``$user["visits"]``</span> wizyt
		</div>
		<?php endforeach ?>
	</div>
</div>