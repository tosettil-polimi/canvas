<template>
    <div style="height: 300px;">
        <canvas id="stats" />
    </div>
</template>

<script>
import moment from 'moment';
import Chart from 'chart.js';
import i18n from '../mixins/i18n';

export default {
    name: 'line-chart',

    props: {
        views: {
            type: Object,
            required: true,
        },

        visits: {
            type: Object,
            required: true,
        },
    },

    mixins: [i18n],

    mounted() {
        let ref = this;
        this.createChart('stats', {
            type: 'line',
            data: {
                labels: Object.keys(this.views),
                datasets: [
                    {
                        label: this.i18n.visits,
                        data: Object.values(this.visits),
                        backgroundColor: ['rgba(158, 213, 237, 0.5)'],
                        borderColor: ['rgb(84, 175, 204)'],
                        borderWidth: 3,
                    },
                    {
                        label: this.i18n.views,
                        data: Object.values(this.views),
                        backgroundColor: ['rgba(3, 168, 124, .5)'],
                        borderColor: ['#03a87c'],
                        borderWidth: 3,
                    },
                ],
            },
            options: {
                legend: {
                    display: false,
                },
                animation: {
                    duration: 0,
                },
                hover: {
                    mode: 'nearest',
                    intersect: true,
                    animationDuration: 0,
                },
                responsiveAnimationDuration: 0,
                maintainAspectRatio: false,
                responsive: true,
                lineTension: 1,
                elements: {
                    point: {
                        radius: 0,
                        backgroundColor: '#03a87c',
                        borderColor: '#03a87c',
                    },
                },
                tooltips: {
                    mode: 'index',
                    displayColors: false,
                    intersect: false,
                    position: 'nearest',
                    callbacks: {
                        label: function (tooltipItem) {
                            if (tooltipItem.datasetIndex === 0) {
                                return ref.uniqueVisitorLabel(tooltipItem.value);
                            } else if (tooltipItem.datasetIndex === 1) {
                                return ref.viewLabel(tooltipItem.value);
                            }
                        },
                        labelTextColor: function (tooltipItem) {
                            if (tooltipItem.datasetIndex === 0) {
                                return 'rgb(84, 175, 204)';
                            } else if (tooltipItem.datasetIndex === 1) {
                                return '#03a87c';
                            }
                        },
                        title: function (tooltipItem) {
                            return moment(tooltipItem[0].label, 'YYYY-MM-DD').format('dddd, MMMM Do YYYY');
                        },
                    },
                },
                scales: {
                    yAxes: [
                        {
                            ticks: {
                                fontColor: '#718096',
                                beginAtZero: true,
                                padding: 25,
                                display: true,
                                autoSkip: true,
                                maxTicksLimit: 6,
                            },
                            gridLines: {
                                borderDash: [8, 4],
                                color: '#718096',
                            },
                        },
                    ],
                    xAxes: [
                        {
                            ticks: {
                                fontColor: '#718096',
                                display: true,
                                autoSkip: true,
                                maxTicksLimit: 8,
                                callback: function (value) {
                                    return moment(value, 'YYYY-MM-DD').format('MMM Do');
                                },
                            },
                            gridLines: {
                                display: false,
                            },
                        },
                    ],
                },
            },
        });
    },

    methods: {
        createChart(chartId, chartData) {
            new Chart(document.getElementById(chartId), {
                type: chartData.type,
                data: chartData.data,
                options: chartData.options,
            });
        },

        viewLabel(value) {
            if (Number(value) === 1) {
                return value + ' ' + this.i18n.view;
            } else {
                return value + ' ' + this.i18n.views_simple;
            }
        },

        uniqueVisitorLabel(value) {
            if (Number(value) === 1) {
                return value + ' ' + this.i18n.unique_visit;
            } else {
                return value + ' ' + this.i18n.unique_visits;
            }
        },
    },
};
</script>
