<div class="exercise-records">
	<h1>``$exercise['exercise_type']``</h1>
    <h2>🇬🇧 ``$exercise['exercise_type_en']``</h2>
    <div style="max-width: 500px">
        <canvas id="exercise-max-weight"></canvas>
    </div>
    <div style="max-width: 500px">
        <canvas id="exercise-max-volume"></canvas>
    </div>
    <div style="max-width: 500px">
        <canvas id="exercise-estimated-1rm"></canvas>
    </div>
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

<script>
var type_id = ``$exercise["type_id"]``;
api.get(`exercises/past?type_id=${type_id}`, (r, data) => {
    initCharts(data.history, {
        weight: "#exercise-max-weight",
        rm: "#exercise-estimated-1rm",
        volume: "#exercise-max-volume"
    });
});
</script>