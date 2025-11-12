<?php
include 'header.php';

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$email = $_SESSION['email'];
include 'connect.php';
?>

<!-- Weather Icons & Animate CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/weather-icons/2.0.12/css/weather-icons.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
    .pollution-card {
        background: linear-gradient(135deg, #3a6186, #89253e);
        border-radius: 15px;
        color: white;
        padding: 30px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        margin-bottom: 30px;
    }
    .pollution-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }
    .pollution-location {
        font-size: 24px;
        font-weight: 600;
    }
    .pollution-date {
        font-size: 14px;
        opacity: 0.8;
    }
    .aqi-value {
        font-size: 60px;
        font-weight: bold;
    }
    .aqi-description {
        font-size: 18px;
        margin-top: 10px;
    }
    #map-container {
        height: 380px;
        border-radius: 15px;
        overflow: hidden;
        margin-top: 30px;
    }
</style>

<div class="main-container">
    <div class="pd-ltr-20">
        <div class="col-lg-12 col-md-12 col-sm-12 mb-30">
            <div class="card-box pd-30 pt-10 height-100-p">
                <h2 class="mb-30 h4">Air Pollution Level (AQI)</h2>
                <div id="pollution-display">
                    <div class="loading-humidity">
                        <div class="loading-spinner"></div>
                        Fetching pollution data from your location...
                    </div>
                </div>
                <div id="map-container">
                    <div id="browservisit" style="width: 100%; height: 100%;"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<script>
const apiKey = '2c190b1e325d696dd18cb2234d2463c3';

function getAQILevel(aqi) {
    const levels = [
        { max: 1, text: "Good", color: "#2ecc71", icon: "wi-day-sunny" },
        { max: 2, text: "Fair", color: "#f1c40f", icon: "wi-day-cloudy" },
        { max: 3, text: "Moderate", color: "#e67e22", icon: "wi-cloudy" },
        { max: 4, text: "Poor", color: "#e74c3c", icon: "wi-dust" },
        { max: 5, text: "Very Poor", color: "#8e44ad", icon: "wi-smog" }
    ];
    return levels.find(l => aqi <= l.max) || levels[4];
}

function fetchPollution(lat, lon) {
    const pollutionUrl = `https://api.openweathermap.org/data/2.5/air_pollution?lat=${lat}&lon=${lon}&appid=${apiKey}`;

    fetch(pollutionUrl)
        .then(res => res.json())
        .then(data => {
            if (data.list && data.list.length > 0) {
                const aqi = data.list[0].main.aqi;
                renderPollutionCard(aqi, lat, lon);
            } else {
                showError("Pollution data not available.");
            }
        })
        .catch(err => {
            console.error(err);
            showError("Failed to fetch pollution data.");
        });
}

function renderPollutionCard(aqi, lat, lon) {
    const info = getAQILevel(aqi);
    const now = new Date();
    const dateString = now.toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });

    const html = `
        <div class="pollution-card animate__animated animate__fadeIn">
            <div class="pollution-header">
                <div class="pollution-location">Your Location</div>
                <div class="pollution-date">${dateString}</div>
            </div>
            <div style="text-align: center;">
                <i class="wi ${info.icon}" style="font-size: 80px;"></i>
                <div class="aqi-value">${aqi}</div>
                <div class="aqi-description" style="background:${info.color}; padding:8px 16px; border-radius:20px; display:inline-block;">
                    ${info.text} Air Quality
                </div>
            </div>
        </div>
    `;
    document.getElementById('pollution-display').innerHTML = html;
    initMap(lat, lon);
}

function showError(msg) {
    document.getElementById('pollution-display').innerHTML = `
        <div class="pollution-card" style="background:#e74c3c;">
            <div style="text-align:center; padding:30px;">
                <i class="wi wi-alert" style="font-size:50px; margin-bottom:20px;"></i>
                <h3>Error</h3>
                <p>${msg}</p>
            </div>
        </div>
    `;
}

function initMap(lat, lon) {
    const map = L.map('browservisit').setView([lat, lon], 12);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    L.marker([lat, lon]).addTo(map)
        .bindPopup('Your location')
        .openPopup();
}

// Get location and fetch AQI
if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(
        pos => fetchPollution(pos.coords.latitude, pos.coords.longitude),
        err => {
            console.warn("Geolocation error:", err);
            showError("Couldn't get location. Using fallback.");
            fetchPollution(6.5244, 3.3792); // Lagos as fallback
        }
    );
} else {
    showError("Geolocation not supported.");
    fetchPollution(6.5244, 3.3792); // Lagos fallback
}
</script>

<?php
include 'footer.php';
?>
