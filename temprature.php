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

<style>
    .weather-card {
        background: linear-gradient(135deg, #72b8ff 0%, #3a7bd5 100%);
        border-radius: 15px;
        color: white;
        padding: 30px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        position: relative;
        overflow: hidden;
        margin-bottom: 30px;
    }
    
    .weather-card::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 70%);
    }
    
    .weather-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }
    
    .weather-location {
        font-size: 24px;
        font-weight: 600;
    }
    
    .weather-date {
        font-size: 14px;
        opacity: 0.8;
    }
    
    .weather-main {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 20px;
    }
    
    .weather-temperature {
        font-size: 72px;
        font-weight: 300;
        line-height: 1;
    }
    
    .weather-icon {
        font-size: 80px;
    }
    
    .weather-details {
        display: flex;
        justify-content: space-between;
        background: rgba(255, 255, 255, 0.2);
        padding: 15px;
        border-radius: 10px;
    }
    
    .weather-detail-item {
        text-align: center;
    }
    
    .weather-detail-item i {
        font-size: 20px;
        margin-bottom: 5px;
    }
    
    .weather-detail-label {
        font-size: 12px;
        opacity: 0.8;
        margin-bottom: 5px;
    }
    
    .weather-detail-value {
        font-size: 16px;
        font-weight: 600;
    }
    
    .loading-weather {
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
</style>

<div class="main-container">
    <div class="pd-ltr-20">
        <div class="col-lg-12 col-md-12 col-sm-12 mb-30">
            <div class="card-box pd-30 pt-10 height-100-p">
                <h2 class="mb-30 h4">Weather in Offa, Kwara State, Nigeria</h2>
                
                <div id="weather-display">
                    <div class="loading-weather">
                        <div class="loading-spinner"></div>
                        Loading weather data for Offa...
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
// Function to fetch weather data for Offa
function fetchWeather() {
    const lat = 8.150; // Offa latitude
    const lon = 4.717; // Offa longitude
    const locationName = 'Offa, Kwara State, Nigeria';
    const apiKey = '2c190b1e325d696dd18cb2234d2463c3';
    const apiUrl = `https://api.openweathermap.org/data/2.5/weather?lat=${lat}&lon=${lon}&appid=${apiKey}&units=metric`;
    
    fetch(apiUrl)
        .then(response => response.json())
        .then(data => {
            if (data.main && data.main.temp) {
                renderWeatherCard(data, locationName);
                initMap(lat, lon, locationName);
            } else {
                showError('Could not fetch weather data for Offa.');
            }
        })
        .catch(error => {
            console.error('Error fetching weather:', error);
            showError('Error loading weather data for Offa. Please try again.');
        });
}

// Function to render weather card
function renderWeatherCard(data, locationName) {
    const weather = data.weather[0];
    const main = data.main;
    const wind = data.wind;
    const clouds = data.clouds;
    
    // Get weather icon class
    const iconClass = getWeatherIconClass(weather.icon);
    
    // Format date
    const now = new Date();
    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    const dateString = now.toLocaleDateString('en-US', options);
    
    // Create weather card HTML
    const weatherHTML = `
        <div class="weather-card animate__animated animate__fadeIn">
            <div class="weather-header">
                <div class="weather-location">${locationName}</div>
                <div class="weather-date">${dateString}</div>
            </div>
            
            <div class="weather-main">
                <div class="weather-temperature">${Math.round(main.temp)}°C</div>
                <div>
                    <i class="weather-icon ${iconClass}"></i>
                    <div style="text-align: center;">${weather.description}</div>
                </div>
            </div>
            
            <div class="weather-details">
                <div class="weather-detail-item">
                    <i class="wi wi-humidity"></i>
                    <div class="weather-detail-label">HUMIDITY</div>
                    <div class="weather-detail-value">${main.humidity}%</div>
                </div>
                
                <div class="weather-detail-item">
                    <i class="wi wi-strong-wind"></i>
                    <div class="weather-detail-label">WIND</div>
                    <div class="weather-detail-value">${wind.speed} m/s</div>
                </div>
                
                <div class="weather-detail-item">
                    <i class="wi wi-barometer"></i>
                    <div class="weather-detail-label">PRESSURE</div>
                    <div class="weather-detail-value">${main.pressure} hPa</div>
                </div>
                
                <div class="weather-detail-item">
                    <i class="wi wi-cloud"></i>
                    <div class="weather-detail-label">CLOUDS</div>
                    <div class="weather-detail-value">${clouds.all}%</div>
                </div>
            </div>
        </div>
    `;
    
    document.getElementById('weather-display').innerHTML = weatherHTML;
}

// Function to get weather icon class
function getWeatherIconClass(iconCode) {
    const iconMap = {
        '01d': 'wi-day-sunny',
        '01n': 'wi-night-clear',
        '02d': 'wi-day-cloudy',
        '02n': 'wi-night-alt-cloudy',
        '03d': 'wi-cloud',
        '03n': 'wi-cloud',
        '04d': 'wi-cloudy',
        '04n': 'wi-cloudy',
        '09d': 'wi-rain',
        '09n': 'wi-rain',
        '10d': 'wi-day-rain',
        '10n': 'wi-night-alt-rain',
        '11d': 'wi-thunderstorm',
        '11n': 'wi-thunderstorm',
        '13d': 'wi-snow',
        '13n': 'wi-snow',
        '50d': 'wi-fog',
        '50n': 'wi-fog'
    };
    
    return iconMap[iconCode] || 'wi-day-sunny';
}

// Function to initialize map
function initMap(lat, lon, locationName) {
    const map = L.map('browservisit').setView([lat, lon], 12);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);
    
    const marker = L.marker([lat, lon]).addTo(map)
        .bindPopup(`<b>${locationName}</b><br>Weather measurement location`)
        .openPopup();
}

// Function to show error
function showError(message) {
    document.getElementById('weather-display').innerHTML = `
        <div class="weather-card" style="background: linear-gradient(135deg, #ff7676 0%, #d63031 100%);">
            <div style="text-align: center; padding: 30px;">
                <i class="wi wi-alien" style="font-size: 50px; margin-bottom: 20px;"></i>
                <h3 style="margin-bottom: 10px;">Oops!</h3>
                <p>${message}</p>
            </div>
        </div>
    `;
}

// Fetch weather data for Offa
fetchWeather();
</script>

<?php
include 'footer.php';
?>