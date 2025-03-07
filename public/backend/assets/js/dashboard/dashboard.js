$(function () {
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

  getChartData(0, 0, 0);

    $(".selChangeChart").change(function(e){
        let branch = $("#current_branch").val();
        let month = $("#selChangeMonth").val();
        let year = $("#selChangeYear").val();
        getChartData(branch, month, year);        
    });
});


function getChartData(branch, month, year){
  $.ajax({
    url: '/ajax/chart/sales/comparison/'+branch+'/'+month+'/'+year,
    type: 'GET',
    dataType: 'json',
    async: true,
    cache: false,
    success: function (response) {
      options = {
        labels: ["Collected", "Balance"],
        series: [parseFloat(response[0].advance), parseFloat(response[0].balance)],
        chart: {
          type: "donut",
          height: 400,
        },
        dataLabels: {
          enabled: false,
        },
        legend: {
          position: "bottom",
          fontSize: "14px",
          fontFamily: "Rubik, sans-serif",
          fontWeight: 500,
          labels: {
            colors: ["var(--chart-text-color)"],
          },
          markers: {
            width: 6,
            height: 6,
          },
          itemMargin: {
            horizontal: 7,
            vertical: 0,
          },
        },
        stroke: {
          width: 10,
          colors: ["var(--light2)"],
        },
        plotOptions: {
          pie: {
            expandOnClick: false,
            donut: {
              size: "83%",
              labels: {
                show: true,
                name: {
                  offsetY: 4,
                },
                total: {
                  show: true,
                  fontSize: "20px",
                  fontFamily: "Rubik, sans-serif",
                  fontWeight: 500,
                  label: "₹ "+ response[0].invtot,
                  formatter: () => "Total Sales",
                },
              },
            },
          },
        },
        states: {
          normal: {
            filter: {
              type: "none",
            },
          },
          hover: {
            filter: {
              type: "none",
            },
          },
          active: {
            allowMultipleDataPointsSelection: false,
            filter: {
              type: "none",
            },
          },
        },
        colors: ["#54BA4A", "#FFA941"],
        responsive: [
          {
            breakpoint: 1630,
            options: {
              chart: {
                height: 360,
              },
            },
          },
          {
            breakpoint: 1584,
            options: {
              chart: {
                height: 400,
              },
            },
          },
          {
            breakpoint: 1473,
            options: {
              chart: {
                height: 250,
              },
            },
          },
          {
            breakpoint: 1425,
            options: {
              chart: {
                height: 270,
              },
            },
          },
          {
            breakpoint: 1400,
            options: {
              chart: {
                height: 320,
              },
            },
          },
          {
            breakpoint: 480,
            options: {
              chart: {
                height: 250,
              },
            },
          },
        ],
      };
      var salescomparisonchart = new ApexCharts(
        document.querySelector("#sales-comparison-chart"),
        options
      );
      salescomparisonchart.updateSeries(reset(options));
      salescomparisonchart.render();  
    }
  });
}

$.getJSON('/ajax/chart/branch/performance', function (response) {
  let br = [], bal = [];
  for (const [key, value] of Object.entries(response)) {
    br.push(value.branch)
    bal.push(parseFloat(value.balance).toFixed(2))
  }
  //console.log(response)
var brOptions = {
  chart: {
    height: 400,
    type: "radialBar",
  },
  plotOptions: {
    radialBar: {
      hollow: {
        margin: 5,
        size: "25%"
      }, 
      dataLabels: {
        name: {
          fontSize: "22px",
        },
        value: {
          fontSize: "16px",
        },
        total: {
          show: true,
          label: "Performance",
          formatter: function (w) {
            //return 1000000;
          },
        },
      },
    },
  },
  series: bal,
  labels: br,
  responsive: [
    {
      breakpoint: 480,
      options: {
        chart: {
          height: 250,
        },
        legend: {
          show: false,
        },
        plotOptions: {
          radialBar: {
            dataLabels: {
              name: {
                offsetY: -1,
              },
              value: {
                offsetY: 4,
              },
            },
          },
        },
      },
    },
  ],
  colors: [
    CubaAdminConfig.secondary,
    "#51bb25",
    "#f8d62b",
    CubaAdminConfig.primary,
  ],
};

var chartBrPerf = new ApexCharts(document.querySelector("#brPerformanceChart"), brOptions);

chartBrPerf.render();
});

function reset(options) {
  return options.series
}


  
  
