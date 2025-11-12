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

<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<style>
    #location-status {
        padding: 10px;
        margin-bottom: 15px;
        border-radius: 5px;
    }
    .loading {
        background-color: #fff3cd;
        color: #856404;
    }
    .success {
        background-color: #d4edda;
        color: #155724;
    }
    .error {
        background-color: #f8d7da;
        color: #721c24;
    }
    #location-details {
        margin: 15px 0;
    }
    .detail-item {
        margin-bottom: 8px;
    }
</style>

<div class="main-container">
    <div class="pd-ltr-20">
        <div class="col-lg-12 col-md-12 col-sm-12 mb-30">
            <div class="card-box pd-30 pt-10 height-100-p">
                <h2 class="mb-30 h4">Live Location: Offa, Kwara State, Nigeria</h2>
                
                <div id="location-status" class="loading">
                    <i class="fa fa-spinner fa-spin"></i> Loading location for Offa...
                </div>
                
                <div id="location-details" style="display:none;">
                    <div class="detail-item"><strong>Address:</strong> <span id="address">Loading...</span></div>
                    <div class="detail-item"><strong>Coordinates:</strong> <span id="coordinates"></span></div>
                    <div class="detail-item"><strong>Accuracy:</strong> <span id="accuracy"></span> meters</div>
                    <div class="detail-item"><strong>Last Updated:</strong> <span id="timestamp"></span></div>
                </div>
                
                <div id="browservisit" style="width: 100% !important; height: 380px;"></div>
            </div>
        </div>
    </div>
</div>

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<!-- Font Awesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<script>
    // Initialize map centered on Offa, Kwara State, Nigeria
    let map = L.map('browservisit').setView([8.150, 4.717], 15);
    let userMarker = null;

    // Load OpenStreetMap tiles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    function updateLocationStatus(status, message) {
        const statusDiv = document.getElementById('location-status');
        statusDiv.className = status;
        statusDiv.innerHTML = message;
    }

    function formatTimestamp(timestamp) {
        const date = new Date(timestamp);
        return date.toLocaleString();
    }

    function updateLocationDetails(position) {
        document.getElementById('coordinates').textContent = 
            `${position.coords.latitude.toFixed(6)}, ${position.coords.longitude.toFixed(6)}`;
        document.getElementById('accuracy').textContent = position.coords.accuracy.toFixed(0);
        document.getElementById('timestamp').textContent = formatTimestamp(position.timestamp);
        
        // Show details section
        document.getElementById('location-details').style.display = 'block';
    }

    function displayOffaLocation() {
        const offaPosition = {
            coords: {
                latitude: 8.150,
                longitude: 4.717,
                accuracy: 10 // Simulated accuracy in meters
            },
            timestamp: Date.now()
        };

        const lat = offaPosition.coords.latitude;
        const lon = offaPosition.coords.longitude;

        // Ensure map is centered on Offa
        map.setView([lat, lon], 15);

        // Add or update marker
        if (userMarker) {
            userMarker.setLatLng([lat, lon]);
        } else {
            userMarker = L.marker([lat, lon], {
                icon: L.divIcon({
                    className: 'user-location-marker',
                    html: '<i class="fas fa-map-marker-alt" style="color:red; font-size:24px;"></i>',
                    iconSize: [24, 24],
                    iconAnchor: [12, 24]
                })
            }).addTo(map)
            .bindPopup("Location: Offa, Kwara State, Nigeria")
            .openPopup();
        }

        // Update status
        updateLocationStatus('success', '<i class="fas fa-check-circle"></i> Displaying location for Offa, Kwara State, Nigeria');

        // Update location details
        updateLocationDetails(offaPosition);

        // Hardcoded address for Offa (since we're bypassing geocoding for simulation)
        const offaAddress = "Offa, Kwara State, Nigeria";
        document.getElementById("address").textContent = offaAddress;

        // Update marker popup with address
        userMarker.setPopupContent(`
            <b>Location</b><br>
            ${offaAddress}
        `).openPopup();
    }

    // Simulate location display for Offa
    try {
        displayOffaLocation();
    } catch (err) {
        updateLocationStatus('error', '<i class="fas fa-exclamation-triangle"></i> Error displaying location for Offa.');
    }
</script>

<?php include 'footer.php'; ?>