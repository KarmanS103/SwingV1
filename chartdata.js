$(document).ready(function(){
  $.ajax({
      url: "http://localhost/chartdata.php",
      type: "GET",
      success: function (data) {
          console.log(data);

          var date = [];
          var close = [];

          for (var i in data) {
              date.push(data[i].date);
              close.push(data[i].close);
          }

          var chartdata = {
              labels: date,
              datasets: [
                  {
                      label: false,
                      fill: false,
                      lineTension: .01,
                      backgroundColor: "rgba(59, 89, 152, 0.75)",
                      borderColor: "rgba(59, 89, 152, 1)",
                      pointHoverBackgroundColor: "rgba(59, 89, 152, 1)",
                      pointHoverBorderColor: "rgba(59, 89, 152, 1)",
                      data: close
                  }
              ]
          };

          var ctx = $("#chartdatacanvas");

          var LineGraph = new Chart(ctx, {
              type: 'line',
              data: chartdata,
              options: {
                  legend: {
                      display: false
                  },
                  tooltips: {
                      enabled: true
                  },
                  scales: {
                      xAxes: [{
                          ticks: {
                              display: false
                          },
                          gridLines: {
                              display: false
                          }
                      }]
                  }
              }

          });
      },

      error: function (data) {

      }
  });

  });