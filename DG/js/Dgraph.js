document.addEventListener('DOMContentLoaded', function () {
    fetch('load_dashboard.php')
      .then(response => response.json())
      .then(data => {
        // Product Chart
        const productCtx = document.getElementById('productChart').getContext('2d');
        const productChart = new Chart(productCtx, {
          type: 'bar',
          data: {
            labels: ['Products'],
            datasets: [{
              label: '# of Products',
              data: [data.number_of_products],
              backgroundColor: 'rgba(75, 192, 192, 0.2)',
              borderColor: 'rgba(75, 192, 192, 1)',
              borderWidth: 1
            }]
          },
          options: {
            scales: {
              y: {
                beginAtZero: true
              }
            }
          }
        });
  
        // User Chart
        const userCtx = document.getElementById('userChart').getContext('2d');
        const userChart = new Chart(userCtx, {
          type: 'bar',
          data: {
            labels: ['User Accounts'],
            datasets: [{
              label: '# of Users',
              data: [data.number_of_users],
              backgroundColor: 'rgba(54, 162, 235, 0.2)',
              borderColor: 'rgba(54, 162, 235, 1)',
              borderWidth: 1
            }]
          },
          options: {
            scales: {
              y: {
                beginAtZero: true
              }
            }
          }
        });
  
        // Reserve Chart
        const reserveCtx = document.getElementById('reserveChart').getContext('2d');
        const reserveChart = new Chart(reserveCtx, {
          type: 'bar',
          data: {
            labels: ['Reserve'],
            datasets: [{
              label: '# of Reserves',
              data: [data.reserve_count],
              backgroundColor: 'rgba(255, 206, 86, 0.2)',
              borderColor: 'rgba(255, 206, 86, 1)',
              borderWidth: 1
            }]
          },
          options: {
            scales: {
              y: {
                beginAtZero: true
              }
            }
          }
        });
      });
  });
  