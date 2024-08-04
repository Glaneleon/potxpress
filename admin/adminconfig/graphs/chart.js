const ctx = document.getElementById("OrdersChart").getContext("2d");

// Fetch data using AJAX
fetch("./adminconfig/get_orders.php")
  .then((response) => response.json())
  .then((data) => {
    const allMonths = [
      "January",
      "February",
      "March",
      "April",
      "May",
      "June",
      "July",
      "August",
      "September",
      "October",
      "November",
      "December",
    ];
    const chartData = {
      labels: allMonths,
      datasets: [
        {
          label: "Delivered",
          data: new Array(allMonths.length).fill(0), // Initialize with zeros
          backgroundColor: "#198754",
          stack: "orderStatus",
        },
        {
          label: "In Transit",
          data: new Array(allMonths.length).fill(0), // Initialize with zeros
          backgroundColor: "#ffc107",
          stack: "orderStatus",
        },
        {
          label: "Order Confirmed",
          data: new Array(allMonths.length).fill(0), // Initialize with zeros
          backgroundColor: "#FFA500",
          stack: "orderStatus",
        },
        {
          label: "Order Placed",
          data: new Array(allMonths.length).fill(0), // Initialize with zeros
          backgroundColor: "#0d6efd",
          stack: "orderStatus",
        },
      ],
    };

    // Process data and update chartData
    for (const order of data) {
      const month = new Date(order.date).getMonth();
      const monthLabel = allMonths[month];

      if (order.status === "4") {
        chartData.datasets[0].data[month]++;
      } else if (order.status === "3") {
        chartData.datasets[1].data[month]++;
      } 
      else if (order.status === "2") {
        chartData.datasets[2].data[month]++;
      }else if (order.status === "1") {
        chartData.datasets[3].data[month]++;
      }
    }

    // Define options for y-axis scaling
    const yAxisOptions = {
      ticks: {
        beginAtZero: true,
        suggestedMin: 0,
        suggestedMax: 10,
        callback: function (value, index, values) {
          if (value % 10 === 0) {
            return value;
          }
        },
      },
    };

    const myChart = new Chart(ctx, {
      type: "bar",
      data: chartData,
      options: {
        scales: {
          yAxes: [yAxisOptions],
        },
      },
    });
  });
