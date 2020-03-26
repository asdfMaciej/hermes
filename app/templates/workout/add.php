<form action="``PATH_PREFIX``/workout/add" method="post">
	<input type="hidden" name="action" value="add">
	<input type="text" name="name" placeholder="Nazwa"><br>
	<input type="number" name="reps" placeholder="Ilość powtórzeń"><br>
	<input type="number" name="weight" placeholder="Waga na powtórzeniu"><br>
	<input type="hidden" name="type_id" value="1">
	<input type="hidden" name="gym_id" value="1">
	<input type="hidden" name="failure" value="0">
	<input type="submit" value="Dodaj trening">
</form>