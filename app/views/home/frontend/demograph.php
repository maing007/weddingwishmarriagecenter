    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <section id="member-demographics">
    <div class="gender-selection">
        <button id="female-btn">Female</button>
        <button id="male-btn">Male</button>
        <button id="both-btn" class="active">Both</button>
    </div>

    <div class="charts">
        <div class="chart">
            <canvas id="religion-chart"></canvas>
        </div>
        <div class="chart">
            <canvas id="age-group-chart"></canvas>
        </div>
    </div>
</section>

    <style>
   /* Member Demographics Section */
#member-demographics {
    background-color: #fff;
    padding: 40px;
    border-radius: 10px;
    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
    margin: 20px;
}

/* Gender Selection Buttons */
.gender-selection {
    display: flex;
    justify-content: center;
    margin-bottom: 30px;
}

.gender-selection button {
    background-color: #e88cc6;
    color: white;
    border: none;
    padding: 10px 20px;
    font-size: 16px;
    cursor: pointer;
    margin: 0 10px;
    border-radius: 5px;
}

.gender-selection button.active {
    background-color: #d65a9a;
}

/* Chart Containers */
.charts {
    display: flex;
    justify-content: space-between;
    gap: 20px;
    margin-top: 30px;
}

.chart {
    width: 48%; /* To ensure equal distribution of charts */
}


    </style>
    <script>
  // Gender-based data for religion chart
const dataByGender = {
    female: {
        religion: [85, 7, 4, 4], // Female data for religion (Muslim, Christian, Qadiyani, Others)
        ageGroup: [40, 35, 15, 5, 3, 1, 1] // Female data for age groups (18-30, 31-40, 41-50, etc.)
    },
    male: {
        religion: [92, 3, 2, 3], // Male data for religion
        ageGroup: [55, 30, 10, 3, 1, 0.5, 0.5] // Male data for age groups
    },
    both: {
        religion: [89.8, 5, 3, 2.2], // Both data for religion
        ageGroup: [50.9, 40.3, 5, 2, 0.8, 0.5, 0.3] // Both data for age groups
    }
};

// Initial chart data (Both gender)
let currentData = dataByGender.both;

// Create religion and age group charts using Chart.js
const ctxReligion = document.getElementById('religion-chart').getContext('2d');
const ctxAgeGroup = document.getElementById('age-group-chart').getContext('2d');

// Religion chart
const religionChart = new Chart(ctxReligion, {
    type: 'pie',
    data: {
        labels: ['Muslim', 'Christian', 'Qadiyani', 'Others'],
        datasets: [{
            label: 'Members by Religion',
            data: currentData.religion,
            backgroundColor: ['#ff6384', '#36a2eb', '#ffce56', '#ff5733'],
            borderColor: '#fff',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'top',
            },
            tooltip: {
                enabled: true,
            }
        }
    }
});

// Age Group chart
const ageGroupChart = new Chart(ctxAgeGroup, {
    type: 'pie',
    data: {
        labels: ['18-30', '31-40', '41-50', '51-60', '61-70', '71-80', 'Other'],
        datasets: [{
            label: 'Members by Age Group',
            data: currentData.ageGroup,
            backgroundColor: ['#ff6384', '#36a2eb', '#ffce56', '#ff5733', '#f1c40f', '#e67e22', '#2ecc71'],
            borderColor: '#fff',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'top',
            },
            tooltip: {
                enabled: true,
            }
        }
    }
});

// Handle button clicks to change data based on selected gender
document.getElementById('female-btn').addEventListener('click', () => {
    updateCharts('female');
    setActiveButton('female');
});

document.getElementById('male-btn').addEventListener('click', () => {
    updateCharts('male');
    setActiveButton('male');
});

document.getElementById('both-btn').addEventListener('click', () => {
    updateCharts('both');
    setActiveButton('both');
});

// Update charts with new data
function updateCharts(gender) {
    currentData = dataByGender[gender];

    // Update Religion Chart
    religionChart.data.datasets[0].data = currentData.religion;
    religionChart.update();

    // Update Age Group Chart
    ageGroupChart.data.datasets[0].data = currentData.ageGroup;
    ageGroupChart.update();
}

// Set active button style
function setActiveButton(gender) {
    const buttons = document.querySelectorAll('.gender-selection button');
    buttons.forEach(button => {
        if (button.id === `${gender}-btn`) {
            button.classList.add('active');
        } else {
            button.classList.remove('active');
        }
    });
}


    </script>