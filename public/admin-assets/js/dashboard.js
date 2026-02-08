/**
 * Admin Dashboard Script
 * Loads statistics, dynamic chart, and recent activity
 */

let dashboardChart = null;

$(document).ready(function () {
    loadDashboardStats();
});

async function loadDashboardStats() {
    try {
        const response = await fetch(API_BASE_URL + '/api/stats/dashboard');
        const result = await response.json();

        if (result.status === 'success') {
            const stats = result.data;

            // Update counters with animation
            animateCounter('#total-users-count', stats.total_users);
            animateCounter('#total-students-count', stats.total_students);
            animateCounter('#total-teachers-count', stats.total_teachers);
            animateCounter('#total-courses-count', stats.total_courses);

            // Render Recent Lists
            renderRecentUsers(stats.recent_users);
            renderRecentCourses(stats.recent_courses);

            // Update Chart
            if (stats.chart) {
                updateDashboardChart(stats.chart.labels, stats.chart.data);
            }
        }
    } catch (error) {
        console.error('Error loading dashboard stats:', error);
    }
}

/**
 * Animate numbers
 */
function animateCounter(selector, finalValue) {
    const el = $(selector);
    const value = parseInt(finalValue) || 0;

    // Set initial text to 0 if it's currently non-numeric
    if (isNaN(parseInt(el.text()))) {
        el.text(0);
    }

    // Use jQuery animate to count up
    $({ countNum: el.text() }).animate({
        countNum: value
    }, {
        duration: 1500,
        easing: 'swing',
        step: function () {
            el.text(Math.floor(this.countNum));
        },
        complete: function () {
            el.text(this.countNum);
        }
    });
}

/**
 * Update the registration chart with dynamic data
 */
function updateDashboardChart(labels, data) {
    const ctx = document.getElementById('chart');
    if (!ctx) return;

    if (dashboardChart) {
        dashboardChart.destroy();
    }

    // Default styling from template
    Chart.defaults.global.defaultFontFamily = "rubik";
    Chart.defaults.global.defaultFontColor = '#999';
    Chart.defaults.global.defaultFontSize = '12';

    dashboardChart = new Chart(ctx.getContext('2d'), {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: "Registrations",
                backgroundColor: 'rgba(76, 24, 100, 0.05)',
                borderColor: '#4c1864',
                borderWidth: "3",
                data: data,
                pointRadius: 4,
                pointHoverRadius: 4,
                pointHitRadius: 10,
                pointBackgroundColor: "#fff",
                pointHoverBackgroundColor: "#fff",
                pointBorderWidth: "3",
            }]
        },
        options: {
            layout: { padding: 0 },
            legend: { display: false },
            title: { display: false },
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true,
                        stepSize: 1
                    },
                    gridLines: {
                        borderDash: [6, 6],
                        color: "#ebebeb",
                        lineWidth: 1,
                    },
                }],
                xAxes: [{
                    gridLines: { display: false },
                }],
            },
            tooltips: {
                backgroundColor: '#333',
                titleFontSize: 12,
                titleFontColor: '#fff',
                bodyFontColor: '#fff',
                bodyFontSize: 12,
                displayColors: false,
                xPadding: 10,
                yPadding: 10,
                intersect: false
            }
        }
    });
}

function renderRecentUsers(users) {
    const listContainer = $('#new-users-list');
    listContainer.empty();

    if (!users || users.length === 0) {
        listContainer.html('<li><span class="new-users-text">No recent users found</span></li>');
        return;
    }

    users.forEach(user => {
        const randomPic = Math.floor(Math.random() * 3) + 1;
        const row = `
            <li>
                <span class="new-users-pic">
                    <img src="assets/images/testimonials/pic${randomPic}.jpg" alt="" />
                </span>
                <span class="new-users-text">
                    <a href="user-profile.html?id=${user.id}" class="new-users-name">${user.prenom} ${user.nom}</a>
                    <span class="new-users-info">${capitalize(user.role)} - ${new Date(user.created_at).toLocaleDateString()}</span>
                </span>
                <span class="new-users-btn">
                    <a href="user-profile.html?id=${user.id}" class="btn button-sm outline">View</a>
                </span>
            </li>
        `;
        listContainer.append(row);
    });
}

function renderRecentCourses(courses) {
    const listContainer = $('#new-courses-list');
    listContainer.empty();

    if (!courses || courses.length === 0) {
        listContainer.html('<li><span class="new-users-text">No recent courses found</span></li>');
        return;
    }

    courses.forEach(course => {
        const row = `
            <li>
                <span class="new-users-text">
                    <a href="courses.html" class="new-users-name">${course.titre}</a>
                    <span class="new-users-info">$${course.prix || 'Free'} - ${capitalize(course.statut || '')}</span>
                </span>
                <span class="new-users-btn">
                    <a href="courses.html" class="btn button-sm outline">View</a>
                </span>
            </li>
        `;
        listContainer.append(row);
    });
}

function capitalize(s) {
    if (typeof s !== 'string') return '';
    return s.charAt(0).toUpperCase() + s.slice(1);
}
