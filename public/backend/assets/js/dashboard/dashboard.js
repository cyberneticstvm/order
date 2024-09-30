$(function () {
    $.getJSON('/ajax/chart/order', function (response) {
        //console.log(response)
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
                        return val + "#";
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

    $.getJSON('/ajax/chart/order/comparison/'+ 0, function (response) {
        //console.log(response)
        var ordercomparisonchartoptions = {
            series: [
              {
                name: "Total",
                data: [response.ord[0].order_count, response.ord[1].order_count, response.ord[2].order_count, response.ord[3].order_count, response.ord[4].order_count, response.ord[5].order_count, response.ord[6].order_count, response.ord[7].order_count, response.ord[8].order_count, response.ord[9].order_count, response.ord[10].order_count, response.ord[11].order_count],
              },
              {
                name: "Delivered",
                data: [response.ord[0].dcount, response.ord[1].dcount, response.ord[2].dcount, response.ord[3].dcount, response.ord[4].dcount, response.ord[5].dcount, response.ord[6].dcount, response.ord[7].dcount, response.ord[8].dcount, response.ord[9].dcount, response.ord[10].dcount, response.ord[11].dcount],
              },
              {
                name: "Balance",
                data: [response.ord[0].bcount, response.ord[1].bcount, response.ord[2].bcount, response.ord[3].bcount, response.ord[4].bcount, response.ord[5].bcount, response.ord[6].bcount, response.ord[7].bcount, response.ord[8].bcount, response.ord[9].bcount, response.ord[10].bcount, response.ord[11].bcount],
              },
            ],
            chart: {
              type: "bar",
              height: 270,
              toolbar: {
                show: false,
              },
            },
            plotOptions: {
              bar: {
                horizontal: false,
                columnWidth: "50%",
              },
            },
            dataLabels: {
              enabled: false,
            },
            stroke: {
              show: true,
              width: 6,
              colors: ["transparent"],
            },
            grid: {
              show: true,
              borderColor: "var(--chart-border)",
              xaxis: {
                lines: {
                  show: true,
                },
              },
            },
            colors: ["#FFA941", "var(--theme-deafult)", "#f54132"],
            xaxis: {
              categories: [
              response.ord[0].month,
              response.ord[1].month,
              response.ord[2].month,
              response.ord[3].month,
              response.ord[4].month,
              response.ord[5].month,
              response.ord[6].month,
              response.ord[7].month,
              response.ord[8].month,
              response.ord[9].month,
              response.ord[10].month,
              response.ord[11].month],
              tickAmount: 12,
              tickPlacement: "between",
              labels: {
                style: {
                  fontFamily: "Rubik, sans-serif",
                },
              },
              axisBorder: {
                show: false,
              },
              axisTicks: {
                show: false,
              },
            },
            yaxis: {
              min: 0,
              max: 1000,
              tickAmount: 5,
              tickPlacement: "between",
              labels: {
                style: {
                  fontFamily: "Rubik, sans-serif",
                },
              },
            },
            fill: {
              opacity: 1,
            },
            legend: {
              position: "top",
              horizontalAlign: "left",
              fontFamily: "Rubik, sans-serif",
              fontSize: "14px",
              fontWeight: 500,
              labels: {
                colors: "var(--chart-text-color)",
              },
              markers: {
                width: 6,
                height: 6,
                radius: 12,
              },
              itemMargin: {
                horizontal: 10,
              },
            },
            responsive: [
              {
                breakpoint: 1366,
                options: {
                  plotOptions: {
                    bar: {
                      columnWidth: "80%",
                    },
                  },
                  grid: {
                    padding: {
                      right: 0,
                    },
                  },
                },
              },
              {
                breakpoint: 992,
                options: {
                  plotOptions: {
                    bar: {
                      columnWidth: "70%",
                    },
                  },
                },
              },
              {
                breakpoint: 576,
                options: {
                  plotOptions: {
                    bar: {
                      columnWidth: "60%",
                    },
                  },
                  grid: {
                    padding: {
                      right: 5,
                    },
                  },
                },
              },
            ],
          };
        var chartordercomparison = new ApexCharts(
            document.querySelector("#order-comparison-chart"),
            ordercomparisonchartoptions
          );
          chartordercomparison.render();
    });

    $.getJSON('/ajax/chart/sales/comparison/'+ 0, function (response) {
      console.log(response)
      var ordercomparisonchartoptions = {
          series: [
            {
              name: "Total",
              data: [response.ord[0].total, response.ord[1].total, response.ord[2].total, response.ord[3].total, response.ord[4].total, response.ord[5].total, response.ord[6].total, response.ord[7].total, response.ord[8].total, response.ord[9].total, response.ord[10].total, response.ord[11].total],
            },
            {
              name: "Advance",
              data: [response.ord[0].advance, response.ord[1].advance, response.ord[2].advance, response.ord[3].advance, response.ord[4].advance, response.ord[5].advance, response.ord[6].advance, response.ord[7].advance, response.ord[8].advance, response.ord[9].advance, response.ord[10].advance, response.ord[11].advance],
            },
            {
              name: "Balance",
              data: [response.ord[0].balance, response.ord[1].balance, response.ord[2].balance, response.ord[3].balance, response.ord[4].balance, response.ord[5].balance, response.ord[6].balance, response.ord[7].balance, response.ord[8].balance, response.ord[9].balance, response.ord[10].balance, response.ord[11].balance],
            },
          ],
          chart: {
            type: "bar",
            height: 270,
            toolbar: {
              show: false,
            },
          },
          plotOptions: {
            bar: {
              horizontal: false,
              columnWidth: "50%",
            },
          },
          dataLabels: {
            enabled: false,
          },
          stroke: {
            show: true,
            width: 6,
            colors: ["transparent"],
          },
          grid: {
            show: true,
            borderColor: "var(--chart-border)",
            xaxis: {
              lines: {
                show: true,
              },
            },
          },
          colors: ["#FFA941", "var(--theme-deafult)", "#f54132"],
          xaxis: {
            categories: [
            response.ord[0].month,
            response.ord[1].month,
            response.ord[2].month,
            response.ord[3].month,
            response.ord[4].month,
            response.ord[5].month,
            response.ord[6].month,
            response.ord[7].month,
            response.ord[8].month,
            response.ord[9].month,
            response.ord[10].month,
            response.ord[11].month],
            tickAmount: 12,
            tickPlacement: "between",
            labels: {
              style: {
                fontFamily: "Rubik, sans-serif",
              },
            },
            axisBorder: {
              show: false,
            },
            axisTicks: {
              show: false,
            },
          },
          yaxis: {
            min: 0,
            max: 5000000,
            tickAmount: 5,
            tickPlacement: "between",
            labels: {
              style: {
                fontFamily: "Rubik, sans-serif",
              },
            },
          },
          fill: {
            opacity: 1,
          },
          legend: {
            position: "top",
            horizontalAlign: "left",
            fontFamily: "Rubik, sans-serif",
            fontSize: "14px",
            fontWeight: 500,
            labels: {
              colors: "var(--chart-text-color)",
            },
            markers: {
              width: 6,
              height: 6,
              radius: 12,
            },
            itemMargin: {
              horizontal: 10,
            },
          },
          responsive: [
            {
              breakpoint: 1366,
              options: {
                plotOptions: {
                  bar: {
                    columnWidth: "80%",
                  },
                },
                grid: {
                  padding: {
                    right: 0,
                  },
                },
              },
            },
            {
              breakpoint: 992,
              options: {
                plotOptions: {
                  bar: {
                    columnWidth: "70%",
                  },
                },
              },
            },
            {
              breakpoint: 576,
              options: {
                plotOptions: {
                  bar: {
                    columnWidth: "60%",
                  },
                },
                grid: {
                  padding: {
                    right: 5,
                  },
                },
              },
            },
          ],
        };
      var chartordercomparison = new ApexCharts(
          document.querySelector("#sales-comparison-chart"),
          ordercomparisonchartoptions
        );
        chartordercomparison.render();
  });
});