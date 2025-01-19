import { Controller } from '@hotwired/stimulus';
import Highcharts from 'highcharts';

export default class extends Controller {
    static targets = ['chart']

    static values = {
        turnoverData: Object
    }

    connect() {
        Highcharts.chart(this.chartTarget, {
            credits: {enabled: false},
            legend: {enabled: false},
            title: {text: ''},
            chart: {
                type: 'column'
            },
            xAxis: {
                categories: this.turnoverDataValue.categories,
                labels: {
                    autoRotation: false,
                    style: {
                        color: '#5B5B5B',
                    }
                },
                lineColor: '#C5D0E7',

            },
            yAxis: {
                title: false,
                gridLineWidth: 0,
                labels: {
                    enabled: false
                }
            },
            plotOptions: {
                series: {
                    dataLabels: {
                        enabled: true,
                        formatter: function () {
                            return Highcharts.numberFormat(this.y, 0, ',', '.') + ' EUR';
                        },
                        crop: false,
                        overflow: 'allow',
                        style: {
                            textOutline: 0,
                            color: '#5B5B5B'
                        }
                    }
                }
            },
            series: [{
                name: 'Credit',
                color: '#259E4D',
                data: this.turnoverDataValue.chartData.credit,
                enableMouseTracking: false,
            }, {
                name: 'Debit',
                color: '#E95740',
                data: this.turnoverDataValue.chartData.debit,
                enableMouseTracking: false,
            }]
        });
    }
}
