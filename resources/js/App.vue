<template>
    <div class="container-fluid py-3">
        <div class="row">
            <div class="col-3">
                <datetime v-model="startDate" type="datetime" :format="dt.DATETIME_MED" @close="getData"></datetime>
            </div>
            <div class="col-3">
                <datetime v-model="endDate" type="datetime" :format="dt.DATETIME_MED" @close="getData"></datetime>
            </div>
            <div class="col-3">
                <input type="number" v-model="delta" placeholder="Time between datapoints" @change="getData">
            </div>
        </div>
        <LineGraph :chart-data="chartData" :options="chartOptions" />
    </div>
</template>

<script>
import { DateTime } from "luxon";
import LineGraph from './Components/LineGraph.vue';

export default {
    name: "App",
    components: {
        LineGraph
    },
    data() {
        return {
            delta: 24,
            startDate: DateTime.local().minus({ days: 7 }).startOf('day').toISODate(),
            endDate: DateTime.local().endOf('day').toISODate(),
            chartData: {},
            chartOptions: {
                responsive: true,
                maintainAspectRatio: false
            },
            chartColors: {
                humidity: '#ff0000',
                light: '#00ff00',
                sound: '#0000ff',
                temperature: '#ffff00',
                vibration: '#ff00ff',
                secondary: '#00ffff',
            }
        };
    },
    computed: {
        dt() {
            return DateTime;
        },
        dateInterval() {
            let s = DateTime.fromISO(this.startDate);
            let e = DateTime.fromISO(this.endDate).minus({ seconds: 1 });

            return 'start='+s.toSeconds()+"&end="+e.toSeconds();
        }
    },
    mounted() {
        this.getData();
    },
    methods: {
        getData() {
            fetch('api/v1/graph?delta='+this.delta+'&'+this.dateInterval)
                .then((res) => res.json())
                .then((result) => {
                    const data = result.data;

                    const labels = [];
                    const pivot = {};
                    for (const key in data) {
                        if (data.hasOwnProperty(key)) {
                            const dataPoints = data[key];

                            const d = DateTime.fromSeconds(parseInt(key, 10));
                            labels.push(d.toLocaleString(DateTime.DATETIME_SHORT_WITH_SECONDS));

                            for (const dataPoint of dataPoints) {
                                if (typeof pivot[dataPoint.sensor] === "undefined") {
                                    pivot[dataPoint.sensor] = {
                                        fill: false,
                                        label: dataPoint.sensor,
                                        borderColor: this.chartColors[dataPoint.sensor],
                                        data: []
                                    };
                                }

                                pivot[dataPoint.sensor].data.push(dataPoint.avg);
                            }
                        }
                    }

                    const datasets = [];
                    for (const key in pivot) {
                        if (pivot.hasOwnProperty(key)) {
                            const dataset = pivot[key];
                            while (dataset.data.length < labels.length) {
                                dataset.data.push(0);
                            }

                            datasets.push(dataset);
                        }
                    }

                    this.chartData = {
                        labels,
                        datasets
                    };
                });
        }
    }
};
</script>
