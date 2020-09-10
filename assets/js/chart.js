require('chart.js/dist/Chart.js');

var rootIncidents = document.querySelector('#studentIncidents');
var jsonIncidents = rootIncidents.dataset.items;
jsonIncidents = JSON.parse(jsonIncidents);

incidentsDates = [];
incidentsPoints = [];
incidentsDetails = [];
incidentsTeacher = [];
incidentsTeam = [];

jsonIncidents['incidents'].forEach(function(val) {
    incidentsDates.push(val['date']);
    incidentsPoints.push(val['currentPoints']);
    incidentsTeacher.push(val['teacher']);
    incidentsTeam.push(val['team']);

    incidentsGroupedByDate = [];
    val['incidents'].forEach(function(key) {
        incidentsGroupedByDate.push([key['incident']]);
    });
    incidentsDetails.push(incidentsGroupedByDate);
});


var ctx = document.getElementById('reliability-chart');

var chart = new Chart(ctx, {
    type: 'line',
    responsive: true,
    aspectRatio: 2,

    data: {
        labels: incidentsDates,
        datasets: [{
            label: 'Note de fiabilité',
            afterLabel: incidentsDetails + incidentsTeam + incidentsTeacher,
            fontSize: 16,
            data: incidentsPoints,
            borderWidth: 2,
            fill: false,
            borderColor: 'rgb(75, 192, 192)',
            lineTension: 0.1
        }]
    },

    options: {
        maintainAspectRatio: false,
        tooltips: {
            enabled: true,
            mode: 'single',
            callbacks: {
                footer: function(tooltipItems) {
                    if (incidentsTeam[tooltipItems[0].index]) {
                        var intervenant = 'Membre de l\'équipe : ' + incidentsTeam[tooltipItems[0].index];
                    } else if (incidentsTeacher[tooltipItems[0].index]) {
                        var intervenant = 'Intervenant : ' + incidentsTeacher[tooltipItems[0].index];
                    }
                    return [
                        'Incident/s : ' + incidentsDetails[tooltipItems[0].index],
                        intervenant
                    ];
                }
            }
        }
    }
});


$('#js-semesters').change(function(){
    var semesterId = $(this).val();
   $.ajax({
        type: "GET",
        url: '/studentpoints/' + studentId + '/' + semesterId + '/',
        success: function(jsonData) {

            var jsonIncidents = jsonData.incidents;

            incidentsDates = [];
            incidentsPoints = [];
            incidentsDetails = [];
            incidentsTeacher = [];
            incidentsTeam = [];

            jsonIncidents.forEach(function(val) {
                incidentsDates.push(val['date']);
                incidentsPoints.push(val['currentPoints']);
                incidentsTeacher.push(val['teacher']);
                incidentsTeam.push(val['team']);

                incidentsGroupedByDate = [];
                val['incidents'].forEach(function(key) {
                    incidentsGroupedByDate.push([key['incident']]);
                });
                incidentsDetails.push(incidentsGroupedByDate);
            });

            removeData(chart);
            addData(chart, incidentsDates, incidentsPoints, incidentsDetails, incidentsTeam, incidentsTeacher);
        }
    });
});

chart.render();


function addData(chart, label, data) {
    chart.data.labels = label;
    chart.data.datasets.forEach(function(dataset) {
        dataset.data = data;
});
    chart.update();
}

function removeData(chart) {
    chart.data.labels = [];
    chart.data.datasets.forEach(function(dataset) {
        dataset.data = [];
    });
    chart.update();
}
