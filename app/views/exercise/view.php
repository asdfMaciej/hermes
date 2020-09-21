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
var bgColor = [
    'rgba(255, 99, 132, 0.2)',
    'rgba(54, 162, 235, 0.2)',
    'rgba(255, 206, 86, 0.2)',
    'rgba(75, 192, 192, 0.2)',
    'rgba(153, 102, 255, 0.2)',
    'rgba(255, 159, 64, 0.2)'
];
var borderColor = [
    'rgba(255,99,132,1)',
    'rgba(54, 162, 235, 1)',
    'rgba(255, 206, 86, 1)',
    'rgba(75, 192, 192, 1)',
    'rgba(153, 102, 255, 1)',
    'rgba(255, 159, 64, 1)'
];
var chartOptions = {
    scales: {
        xAxes: [{
            type: 'time',
            distribution: 'linear'
        }]
    }
};

var dataset = <?php echo json_encode($user_history, JSON_UNESCAPED_UNICODE); ?>;
var weights = [], volume = [], maxes = [], labels = [];
for (let item of dataset) {
    weights.push({t: item.date, y: item.max_weight});
    volume.push({t: item.date, y: item.volume});
    maxes.push({t: item.date, y: item.estimated_1rm});
    labels.push(item.date);
}
var maxWeight = document.getElementById("exercise-max-weight").getContext("2d");
var estimated1RM = document.getElementById("exercise-estimated-1rm").getContext("2d");
var maxVolume = document.getElementById("exercise-max-volume").getContext("2d");


var weightChart = new Chart(maxWeight, {
    type: 'line',
    data: {
        labels: labels,
        datasets: [{
            label: 'Maksymalne obciążenie na treningu [kg]',
            data: weights,
            backgroundColor: '#ffffff00',
            borderColor: borderColor,
            borderWidth: 3
        }]
    },
    options: chartOptions
});

var estimated1rmChart = new Chart(estimated1RM, {
    type: 'line',
    data: {
        labels: labels,
        datasets: [{
            label: 'Szacowany max na 1 powtórzenie [kg]',
            data: maxes,
            backgroundColor: '#ffffff00',
            borderColor: borderColor,
            borderWidth: 3
        }]
    },
    options: chartOptions
});

var volumeChart = new Chart(maxVolume, {
    type: 'line',
    data: {
        labels: labels,
        datasets: [{
            label: 'Objętość na treningu [kg]',
            data: volume,
            backgroundColor: bgColor,
            borderColor: borderColor,
            borderWidth: 1
        }]
    },
    options: chartOptions
});


</script>