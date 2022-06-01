<template>
    <v-app id="home">
        <v-card elevation=0>
            <v-row>
                <v-col cols="6">
                    <v-card>
                        <v-row>
                            <v-form @submit.prevent="add">
                                <v-col cols="12" align="center">
                                    <v-date-picker 
                                        v-model="fields.date"
                                        :error-messages="errors && errors.date ? errors.date[0] : ''">
                                    </v-date-picker>
                                </v-col>
                                <v-col cols="12" align="center">
                                    <v-text-field
                                        v-model="fields.amount"
                                        single-line
                                        type="number"
                                        class="amount-input"
                                        label="Ingrese Monto"
                                        :error-messages="errors && errors.amount ? errors.amount[0] : ''"
                                    />
                                </v-col>
                                <v-col cols="12">
                                    <v-btn 
                                        class="add-button"
                                        color="primary"
                                        outlined 
                                        type="submit">
                                            Agregar
                                    </v-btn>
                                </v-col>
                            </v-form>
                        </v-row>
                    </v-card>
                </v-col>
                <v-col cols="6">
                    <v-card>
                        <div class="chart-area" ref="chartdiv">
                        </div>
                    </v-card>
                </v-col>
            </v-row>
        </v-card>
    </v-app>
</template>

<style scoped>
    .form-container {
        margin: auto;
    }

    .chart-area {
        width: 100%;
        height: 500px;
    }

    .amount-input {
        width: 80%;
    }

    .add-button {
        margin-left: 4.5em;
    }
</style>

<script>
    import * as am4core from "@amcharts/amcharts4/core";
    import * as am4charts from "@amcharts/amcharts4/charts";
    import am4themes_animated from "@amcharts/amcharts4/themes/animated";

    am4core.useTheme(am4themes_animated);

  export default {
    data () {
        return {
            fields: {
                date: (new Date(Date.now() - (new Date()).getTimezoneOffset() * 60000)).toISOString().substr(0, 10),
                amount: null
            },
            errors: {
                date: null,
                amount: null
            },
            rules: {
                required: value => !!value || "Required."
            },
            statistics: []
        }
    },

    mounted() {
        axios.get('/api/statistics/get').then(response => {
            this.statistics = response.data;
            this.generateChart();
        }).catch(error => {
            if (error.response.status === 422) {
            this.errors = error.response.data.errors || {};
            }
        });
    },

    beforeDestroy() {
        if (this.chart) {
        this.chart.dispose();
        }
    },

    watch: {
        statistics(newValue, oldValue) {
            if (this.chart) {
                this.chart.data = newValue;
            }
        }
    },

    methods:{
        add:function(){
            axios.post('/api/statistics/add', this.fields).then(response => {
                this.fields.date = (new Date(Date.now() - (new Date()).getTimezoneOffset() * 60000)).toISOString().substr(0, 10);
                this.fields.amount = "";
                this.errors = null;
                this.statistics = response.data.data;
            }).catch(error => {
                if (error.response.status === 422) {
                this.errors = error.response.data.errors || {};
                }
            });
        },

        generateChart:function(){
            let chart = am4core.create(this.$refs.chartdiv, am4charts.XYChart);

            chart.paddingRight = 20;

            chart.data = this.statistics;

            let dateAxis = chart.xAxes.push(new am4charts.DateAxis());
            dateAxis.renderer.grid.template.location = 0;

            let valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
            valueAxis.tooltip.disabled = true;
            valueAxis.renderer.minWidth = 35;

            let series = chart.series.push(new am4charts.LineSeries());
            series.dataFields.dateX = "date";
            series.dataFields.valueY = "value";

            series.tooltipText = "{valueY.value}";
            series.strokeWidth = 0.5;
            series.fillOpacity = 1;
            chart.cursor = new am4charts.XYCursor();

            let scrollbarX = new am4charts.XYChartScrollbar();
            scrollbarX.series.push(series);
            chart.scrollbarX = scrollbarX;

            this.chart = chart;
        }
    },
  }
</script>