<div class="exercise-records">
	<h1>``$exercise['exercise_type']``</h1>
    <h2>🇬🇧 ``$exercise['exercise_type_en']``</h2>
    <?php foreach ($weight_records as $record): ?>
    <div style="display: flex; margin-bottom: 8px">
        <div class="feed-workout__avatar">
            <a href="``PATH_PREFIX``/profile/``$record['user_id']``">
                <img src="``PATH_PREFIX``/``$record['avatar']``" class="avatar">
            </a>
        </div>
        <div style="padding: 8px">
            <div>``$record['name']``</div>
            <div class="date">``$record['date']``</div>
            <div>``$record['max_weight']`` kg</div>
        </div>

    </div>
    <?php endforeach ?>
</div>
