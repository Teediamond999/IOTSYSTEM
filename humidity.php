<?php
include 'header.php';

// Check if user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$email = $_SESSION['email'];
include 'connect.php';
?>

<!-- Weather Icons CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/weather-icons/2.0.12/css/weather-icons.min.css">
<!-- Animate.css for smooth animations -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
<!-- Chart.js for humidity visualization -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
    .humidity-card {
        background: linear-gradient(135deg, #6a85b6 0%, #bac8e0 100%);
        border-radius: 15px;
        color: white;
        padding: 30px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        position: relative;
        overflow: hidden;
        margin-bottom: 30px;
    }
    
    .humidity-card::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 70%);
    }
    
    .humidity-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }
    
    .humidity-location {
        font-size: 24px;
        font-weight: 600;
    }
    
    .humidity-date {
        font-size: 14px;
        opacity: 0.8;
    }
    
    .humidity-main {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 20px;
    }
    
    .humidity-value {
        font-size: 72px;
        font-weight: 300;
        line-height: 1;
    }
    
    .humidity-icon {
        font-size: 80px;
    }
    
    .humidity-visualization {
        background: rgba(255, 255, 255, 0.2);
        padding: 15px;
        border-radius: 10px;
        margin-bottom: 20px;
    }
    
    .humidity-details {
        display: flex;
        justify-content: space-between;
    }
    
    .humidity-detail-item {
        text-align: center;
        flex: 1;
    }
    
    .humidity-detail-item i {
        font-size: 20px;
        margin-bottom: 5px;
    }
    
    .humidity-detail-label {
        font-size: 12px;
        opacity: 0.8;
        margin-bottom: 5px;
    }
    
    .humidity-detail-value {
        font-size: 16px;
        font-weight: 600;
    }
    
    .comfort-level {
        font-size: 14px;
        margin-top: 10px;
        padding: 8px 12px;
        border-radius: 20px;
        background: rgba(255, 255, 255, 0.3);
        display: inline-block;
    }
    
    .loading-humidity {
        text-align: center;
        padding: 40px;
        font-size: 18px;
        color: #555;
    }
    
    .loading-spinner {
        border: 4px solid rgba(0, 0, 0, 0.1);
        border-radius: 50%;
        border-top: 4px solid #3498db;
        width: 40px;
        height: 40px;
        animation: spin 1s linear infinite;
        margin: 0 auto 20px;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    #map-container {
        height: 380px;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        margin-top: 30px;
    }
    
    #humidityChart {
        background: rgba(255, 255, 255, 0.2);
        border-radius: 8px;
        padding: 10px;
    }
</style>

<div class="main-container">
    <div class="pd-ltr-20">
        <div class="col-lg-12 col-md-12 col-sm-12 mb-30">
            <div class="card-box pd-30 pt-10 height-100-p">
                <h2 class="mb-30 h4">Humidity Levels in Offa, Kwara State, Nigeria</h2>
                
                <div id="humidity-display">
                    <div class="loading-humidity">
                        <div class="loading-spinner"></div>
                        Loading humidity data for Offa...
                    </div>
                </div>
                
                <div id="map-container">
                    <div id="browservisit" style="width: 100%; height: 100%;"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Leaflet JS for map -->
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<script>
// Function to fetch weather data with humidity for Offa
function fetchHumidityData() {
    const lat = 8.150; // Offa latitude
    const lon = 4.717; // Offa longitude
    const locationName = 'Offa, Kwara State, Nigeria';
    const apiKey = '2c190b1e325d696dd18cb2234d2463c3';
    const apiUrl = `https://api.openweathermap.org/data/2.5/weather?lat=${lat}&lon=${lon}&appid=${apiKey}&units=metric`;
    
    fetch(apiUrl)
        .then(response => response.json())
        .then(data => {
            if (data.main && data.main.humidity) {
                renderHumidityCard(data, locationName);
                initMap(lat, lon, locationName);
            } else {
                showError('Could not fetch humidity data for Offa.');
            }
        })
        .catch(error => {
            console.error('Error fetching weather:', error);
            showError('Error loading humidity data for Offa. Please try again.');
        });
}

// Function to render humidity card
function renderHumidityCard(data, locationName) {
    const main = data.main;
    const weather = data.weather[0];
    const humidity = main.humidity;
    
    // Get comfort level
    const comfort = getComfortLevel(humidity);
    
    // Format date
    const now = new Date();
    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    const dateString = now.toLocaleDateString('en-US', options);
    
    // Create humidity card HTML
    const humidityHTML = `
        <div class="humidity-card animate__animated animate__fadeIn">
            <div class="humidity-header">
                <div class="humidity-location">${locationName}</div>
                <div class="humidity-date">${dateString}</div>
            </div>
            
            <div class="humidity-main">
                <div>
                    <div class="humidity-value">${humidity}%</div>
                    <div class="comfort-level" style="background: ${comfort.color}">
                        <i class="wi ${comfort.icon}"></i> ${comfort.text}
                    </div>
                </div>
                <div>
                    <i class="humidity-icon wi wi-humidity"></i>
                    <div style="text-align: center;">${weather.description}</div>
                </div>
            </div>
            
            <div class="humidity-visualization">
                <canvas id="humidityChart"></canvas>
            </div>
            
            <div class="humidity-details">
                <div class="humidity-detail-item">
                    <i class="wi wi-thermometer"></i>
                    <div class="humidity-detail-label">FEELS LIKE</div>
                    <div class="humidity-detail-value">${Math.round(main.feels_like)}°C</div>
                </div>
                
                <div class="humidity-detail-item">
                    <i class="wi wi-barometer"></i>
                    <div class="humidity-detail-label">PRESSURE</div>
                    <div class="humidity-detail-value">${main.pressure} hPa</div>
                </div>
                
                <div class="humidity-detail-item">
                    <i class="wi wi-sunrise"></i>
                    <div class="humidity-detail-label">DEW POINT</div>
                    <div class="humidity-detail-value">${calculateDewPoint(main.temp, humidity).toFixed(1)}°C</div>
                </div>
            </div>
        </div>
    `;
    
    document.getElementById('humidity-display').innerHTML = humidityHTML;
    
    // Render humidity chart
    renderHumidityChart(humidity, comfort);
}

// Function to calculate dew point
function calculateDewPoint(temp, humidity) {
    // Magnus formula for dew point calculation
    const a = 17.27;
    const b = 237.7;
    const alpha = ((a * temp) / (b + temp)) + Math.log(humidity/100);
    return (b * alpha) / (a - alpha);
}

// Function to get comfort level
function getComfortLevel(humidity) {
    if (humidity < 30) {
        return {
            text: "Dry - May cause discomfort",
            icon: "wi-sandstorm",
            color: "rgba(210, 180, 140, 0.7)"
        };
    } else if (humidity < 50) {
        return {
            text: "Comfortable - Ideal conditions",
            icon: "wi-day-sunny",
            color: "rgba(144, 238, 144, 0.7)"
        };
    } else if (humidity < 70) {
        return {
            text: "Moderate - Slightly humid",
            icon: "wi-humidity",
            color: "rgba(173, 216, 230, 0.7)"
        };
    } else {
        return {
            text: "High - Uncomfortable",
            icon: "wi-rain",
            color: "rgba(70, 130, 180, 0.7)"
        };
    }
}

// Function to render humidity chart
function renderHumidityChart(humidity, comfort) {
    const ctx = document.getElementById('humidityChart').getContext('2d');
    
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Current Humidity', 'Remaining'],
            datasets: [{
                data: [humidity, 100 - humidity],
                backgroundColor: [
                    comfort.color,
                    'rgba(255, 255, 255, 0.2)'
                ],
                borderWidth: 0
            }]
        },
        options: {
            cutout: '70%',
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    enabled: false
                }
            },
            animation: {
                animateScale: true,
                animateRotate: true
            }
        }
    });
}

// Function to initialize map
function initMap(lat, lon, locationName) {
    const map = L.map('browservisit').setView([lat, lon], 12);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);
    
    const marker = L.marker([lat, lon]).addTo(map)
        .bindPopup(`<b>${locationName}</b><br>Humidity measurement location`)
        .openPopup();
}

// Function to show error
function showError(message) {
    document.getElementById('humidity-display').innerHTML = `
        <div class="humidity-card" style="background: linear-gradient(135deg, #ff7676 0%, #d63031 100%);">
            <div style="text-align: center; padding: 30px;">
                <i class="wi wi-rain-mix" style="font-size: 50px; margin-bottom: 20px;"></i>
                <h3 style="margin-bottom: 10px;">Humidity Data Unavailable</h3>
                <p>${message}</p>
            </div>
        </div>
    `;
}

// Fetch humidity data for Offa
fetchHumidityData();
</script>

<?php
include 'footer.php';
?>