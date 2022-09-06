<template>
    <div class="table-responsive">
        <canvas ref="barChart" :style="{height: height || '500px', width: width || '100%'}"></canvas>
    </div>
</template>

<script>
// Chart.js
require('admin-lte/plugins/chart.js/Chart.min');
export default {
    name: "BarChart",
    props: {
        scales: {
            type: Object,
            default: () => ({}),
            required: true,
        },
        chartData: {
            type: Object,
            default: () => null,
            required: true,
        },
        height: {
            type: String | Number,
            default: '',
            required: false
        },
        width: {
            type: String|Number,
            default: "",
            required: false
        }
    },
    mounted() {
        if(!this.barChartCanvas) {
            this.barChartCanvas = this.$refs.barChart.getContext("2d");
        }
        this.$nextTick(() => {
            if (this.barChartCanvas) {
                new Chart(this.barChartCanvas, {
                    type: "bar",
                    data: this.chartData,
                    options: {...this.barChartOptions, scales: this.scales}
                });
            }
        });
    },
    data() {
        return {
            barChartCanvas: null,
            barChartOptions: {
                //Boolean - Whether the scale should start at zero, or an order of magnitude down from the lowest value
                scaleBeginAtZero: true,
                //Boolean - Whether grid lines are shown across the chart
                scaleShowGridLines: true,
                //String - Colour of the grid lines
                scaleGridLineColor: "rgba(0,0,0,.05)",
                //Number - Width of the grid lines
                scaleGridLineWidth: 1,
                //Boolean - Whether to show horizontal lines (except X axis)
                scaleShowHorizontalLines: true,
                //Boolean - Whether to show vertical lines (except Y axis)
                scaleShowVerticalLines: true,
                //Boolean - If there is a stroke on each bar
                barShowStroke: true,
                //Number - Pixel width of the bar stroke
                barStrokeWidth: 2,
                //Number - Spacing between each of the X value sets
                barValueSpacing: 5,
                //Number - Spacing between data sets within X values
                barDatasetSpacing: 1,
                //String - A legend template
                legendTemplate: "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<datasets.length; i++){%><li><span style=\"background-color:<%=datasets[i].fillColor%>\"></span><%if(datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>",
                //Boolean - whether to make the chart responsive
                responsive: true,
                maintainAspectRatio: true,
                datasetFill: false,
            },
        };
    },
    methods: {}
}
</script>
