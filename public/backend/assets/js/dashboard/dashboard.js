$(function () {
    $.getJSON('/ajax/chart/order', function (response) {
        console.log(response)
        var orderoptions = {
            series: [
                {
                    name: "Order Count",
                    type: "column",
                    data: [response[0].order_count, response[1].order_count, response[2].order_count, response[3].order_count, response[4].order_count, response[5].order_count, response[6].order_count, response[7].order_count, response[8].order_count, response[9].order_count, response[10].order_count, response[11].order_count],
                },
                {
                    name: "Order Count",
                    type: "line",
                    data: [response[0].order_count, response[1].order_count, response[2].order_count, response[3].order_count, response[4].order_count, response[5].order_count, response[6].order_count, response[7].order_count, response[8].order_count, response[9].order_count, response[10].order_count, response[11].order_count],
                },
            ],
            chart: {
                height: 300,
                type: "line",
                stacked: false,
                toolbar: {
                    show: false,
                },
                dropShadow: {
                    enabled: true,
                    enabledOnSeries: [1],
                    top: 0,
                    left: 0,
                    blur: 15,
                    color: "var(--theme-deafult)",
                    opacity: 0.3,
                },
            },
            stroke: {
                width: [0, 3],
                curve: "smooth",
            },
            dataLabels: {
                enabled: true,
                enabledOnSeries: [1],
            },
            colors: ["rgba(170, 175, 203, 0.2)", "var(--theme-deafult)"],
            grid: {
                borderColor: "var(--chart-border)",
            },
            plotOptions: {
                bar: {
                    columnWidth: "20%",
                },
            },

            fill: {
                type: ["solid", "gradient"],
                gradient: {
                    shade: "light",
                    type: "vertical",
                    shadeIntensity: 0.5,
                    gradientToColors: ["var(--theme-deafult)", "#d867ac"],
                    opacityFrom: 0.8,
                    opacityTo: 0.8,
                    colorStops: [
                        {
                            offset: 0,
                            color: "#d867ac",
                            opacity: 1,
                        },
                        {
                            offset: 30,
                            color: "#d867ac",
                            opacity: 1,
                        },
                        {
                            offset: 50,
                            color: "var(--theme-deafult)",
                            opacity: 1,
                        },
                        {
                            offset: 80,
                            color: "var(--theme-deafult)",
                            opacity: 1,
                        },
                        {
                            offset: 100,
                            color: "var(--theme-deafult)",
                            opacity: 1,
                        },
                    ],
                },
            },
            labels: [
                response[0].month,
                response[1].month,
                response[2].month,
                response[3].month,
                response[4].month,
                response[5].month,
                response[6].month,
                response[7].month,
                response[8].month,
                response[9].month,
                response[10].month,
                response[11].month,
            ],
            markers: {
                size: 0,
            },
            yaxis: {
                min: 0,
                //max: 20,
                tickAmount: 1,
                labels: {
                    formatter: function (val) {
                        return val + "Nos";
                    },
                    style: {
                        fontSize: "12px",
                        fontFamily: "Rubik, sans-serif",
                        colors: "var(--chart-text-color)",
                    },
                },
            },
            xaxis: {
                tooltip: {
                    enabled: false,
                },
                labels: {
                    style: {
                        fontSize: "10px",
                        fontFamily: "Rubik, sans-serif",
                        colors: "var(--chart-text-color)",
                    },
                },
            },
            tooltip: {
                shared: true,
                intersect: false,
            },
            legend: {
                show: false,
            },
        };

        var orderchart = new ApexCharts(
            document.querySelector("#order-chart"),
            orderoptions
        );
        orderchart.render();
    });
});