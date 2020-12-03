/**
 * Use class for usability.
 * Constructor gets the canvas id and create local variable of Chart type
 */
class ChartBuilder
{
    constructor(canvasID)
    {
        this.canvasID = canvasID;
        this.Chart = Chart;
        this.myChart;
    }

    buildChart(dataPoints, dataLines) {
        let ctx = this.canvasID.getContext('2d');

        // Global Options:
        this.Chart.defaults.global.defaultFontColor = 'black';
        this.Chart.defaults.global.defaultFontSize = 16;

        // Local Chart options
        let options = {
            scales: {
                xAxes:[{
                    ticks: {
                        beginAtZero: false
                    },
                    scaleLabel: {
                        display: true,
                        labelString: 'X',
                        fontSize: 20
                    }
                }],
                yAxes: [{
                    ticks: {
                        beginAtZero: false
                    },
                    scaleLabel: {
                        display: true,
                        labelString: 'Y',
                        fontSize: 20
                    }
                }]
            },
            legend: {
                labels: {
                    fontSize: 16
                }
            }
        }

        // Data options
        let data = {
            datasets: [{
                type: 'scatter',
                label: 'Source Dataset',
                backgroundColor: "black",
                fill: true,
                pointBorderColor: "black",
                pointBackgroundColor: "red",
                pointBorderWidth: 2,
                pointRadius: 4,
                data: dataPoints,

                borderWidth: 2
            },
                {
                    type: 'line',
                    label: 'Regression',
                    fill: false,
                    borderColor: "red",
                    borderWidth: 3,
                    pointRadius: 0,
                    data: dataLines
                }
            ]
        };

        // build chart
        this.myChart = new this.Chart(ctx, {
            type: 'scatter',
            data: data,
            options: options
        });
    }

    setFontSize(fontGlobal)
    {
        // set global font size
        //this.Chart.defaults.global.defaultFontSize = fontGlobal;

        // set/change the actual font-size
        this.myChart.options.scales.xAxes[0].ticks.minor.fontSize = fontGlobal;
        this.myChart.options.scales.yAxes[0].ticks.minor.fontSize = fontGlobal;

        // set proper spacing for resized font
        this.myChart.options.scales.xAxes[0].ticks.fontSize = fontGlobal;
        this.myChart.options.scales.yAxes[0].ticks.fontSize = fontGlobal;

        // set axis label font size
        this.myChart.options.scales.xAxes[0].scaleLabel.fontSize = fontGlobal;
        this.myChart.options.scales.yAxes[0].scaleLabel.fontSize = fontGlobal;

        // change title labels font size (don't work properly)
        //this.myChart.options.legend.labels.fontSize = fontGlobal;

        // update chart to apply new font-size
        this.myChart.update();
    }
}

// create object for ChartBuilder
//let chartBuilder = new  ChartBuilder(document.getElementById('myChart'));
// let dataPoints = [{x: 0, y: 0}, {x: 1, y: 1}, {x: 2, y: 2}, {x: 3, y: 3}, {x: 4, y: 4}, {x: 5, y: 5}];
// let dataLine = [{x: 0, y: 0}, {x: 1, y: 1}, {x: 2, y: 2}, {x: 3, y: 3}, {x: 4, y: 4}, {x: 5, y: 5}];
//chartBuilder.buildChart(dataPoint, dataLines);