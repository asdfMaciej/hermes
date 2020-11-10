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
var api = new API();

function initCharts(exerciseHistory, selectors) {
    var dataset = exerciseHistory;
    var weights = [], volume = [], maxes = [], 
        reps = [], sets = [],
        labels = [];

    for (let item of dataset) {
        weights.push({t: item.date, y: item.max_weight});
        volume.push({t: item.date, y: item.volume});
        maxes.push({t: item.date, y: item.estimated_1rm});

        sets.push({t: item.date, y: item.sets});
        reps.push({t: item.date, y: item.reps});
        
        labels.push(item.date);
    }

    var isBodyweightExercise = weights.filter((item) => {return parseFloat(item.y) > 0}).length === 0;

    var maxWeight = document.querySelector(selectors.weight).getContext("2d");
    var estimated1RM = document.querySelector(selectors.rm).getContext("2d");
    var maxVolume = document.querySelector(selectors.volume).getContext("2d");

    if (isBodyweightExercise) {
        chartOptions.scales.xAxes[0].distribution = 'series';
        chartOptions.scales.xAxes[0].offset = true;
        chartOptions.scales.yAxes = [{
            ticks: {
                beginAtZero: true
            }
        }];
       var repsChart = new Chart(maxWeight, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Łączna ilość powtórzeń',
                    data: reps,
                    backgroundColor: '#ea6565',
                    borderColor: '#d32f2f',
                    borderWidth: 1,
                    barPercentage: 0.9
                }]
            },
            options: chartOptions
        }); 
        
        var setsChart = new Chart(maxVolume, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Ilość serii',
                    data: sets,
                    backgroundColor: '#65eab9',
                    borderColor: '#50bd95',
                    borderWidth: 1,
                    barPercentage: 0.9
                }]
            },
            options: chartOptions
        }); 
    } else {
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
    }
    
}
