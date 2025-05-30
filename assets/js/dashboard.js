
// document.addEventListener("DOMContentLoaded", function () {
  //Expense chart
  console.log("Loading charts...");
  // fetch('/dashboard/expense/chart')
  //   .then(response => response.json())
  //   .then(data => {
  //     const expenseCtx = document.getElementById("chart-expense").getContext("2d");
  //     const expenseData = {
  //       labels: data.labels,
  //       datasets: [{  
  //         label: "Expense Data",
  //         data: data.data,
  //         backgroundColor: [
  //           "rgb(255, 99, 132)",
  //           "rgb(54, 162, 235)",
  //           "rgb(255, 205, 86)",
  //         ],
  //         hoverOffset: 4,
  //       }],
  //     };
  //     const expenseConfig = {
  //       type: "doughnut",
  //       data: expenseData,
  //     };
  //     new Chart(expenseCtx, expenseConfig);
  //   });

  //Income chart
  fetch('/dashboard/income/chart')
    .then(response => response.json())
    .then(data => {
      const incomeCtx = document.getElementById("chart-income").getContext("2d");
      const incomeData = {
        labels: data.labels,
        datasets: [{  
          label: "Income Data",
          data: data.data,
          backgroundColor: [
            "rgb(255, 99, 132)",
            "rgb(54, 162, 235)",
            "rgb(255, 205, 86)",
          ],
          hoverOffset: 4,
        }],
      };
      const incomeConfig = {
        type: "doughnut",
        data: incomeData,
      };
      new Chart(incomeCtx, incomeConfig);
    });

  //Account chart
  fetch('/dashboard/account/chart')
    .then(response => response.json())
    .then(data => {
      const accountCtx = document.getElementById("chart-account").getContext("2d");
      const accountData = {
        labels: data.labels,
        datasets: [{  
          label: "Account Data",
          data: data.data,
          backgroundColor: [
            "rgb(255, 99, 132)",
            "rgb(54, 162, 235)",
            "rgb(255, 205, 86)",
          ],
          hoverOffset: 4,
        }],
      };
      const accountConfig = {
        type: "doughnut",
        data: accountData,
      };
      new Chart(accountCtx, accountConfig);
    });
// });
  

var win = navigator.platform.indexOf("Win") > -1;
if (win && document.querySelector("#sidenav-scrollbar")) {
  var options = {
    damping: "0.5",
  };
  Scrollbar.init(document.querySelector("#sidenav-scrollbar"), options);
}
