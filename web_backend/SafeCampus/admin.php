<?php
session_start();

// --- SECURITY GUARD (Kod Baru) ---
// Kalau user belum login, tendang balik ke page login
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit();
}

// Logic Logout (Bila tekan button logout)
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: admin_login.php");
    exit();
}

// --- CODING LAMA (Kekal Sama) ---
include 'db.php';

// 1. Kalau Admin tekan butang "Add Location"
if (isset($_POST['add_location'])) {
    $name = $_POST['name'];
    $type = $_POST['type'];
    $lat = $_POST['lat'];
    $lng = $_POST['lng'];

    $stmt = $conn->prepare("INSERT INTO locations (location_name, location_type, latitude, longitude) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssdd", $name, $type, $lat, $lng);
    $stmt->execute();
    $stmt->execute();
    header("Location: admin.php"); // Refresh to clear form re-submission
    exit();
}

// 2. Kalau Admin tekan butang "Edit Location" (Update)
if (isset($_POST['update_location'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $type = $_POST['type'];
    $lat = $_POST['lat'];
    $lng = $_POST['lng'];

    $stmt = $conn->prepare("UPDATE locations SET location_name=?, location_type=?, latitude=?, longitude=? WHERE id=?");
    $stmt->bind_param("ssddi", $name, $type, $lat, $lng, $id);
    $stmt->execute();
    header("Location: admin.php");
    exit();
}

// 3. Kalau Admin tekan butang "Delete"
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM locations WHERE id=$id");
    header("Location: admin.php");
}

// 3. Kalau Admin tekan butang "Delete Report"
if (isset($_GET['delete_report'])) {
    $id = $_GET['delete_report'];
    $conn->query("DELETE FROM incidents WHERE id=$id");
    header("Location: admin.php");
    exit();
}

// 4. Tarik Data dari Database
$reports = $conn->query("SELECT * FROM incidents ORDER BY report_time DESC");
$locations = $conn->query("SELECT * FROM locations");

// Kira Statistik Ringkas
$total_reports = $reports->num_rows;
$total_locations = $locations->num_rows;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SafeCampus Admin | UiTM Jasin</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        :root {
            --uitm-blue: #003366;
            --uitm-yellow: #F7C948;
            --glass-bg: rgba(255, 255, 255, 0.15);
            --glass-border: rgba(255, 255, 255, 0.2);
            --shadow-soft: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
            --text-white: #ffffff;
            --text-muted: rgba(255, 255, 255, 0.7);
        }

        body {
            background: linear-gradient(135deg, #0f2027 0%, #203a43 50%, #2c5364 100%);
            background-attachment: fixed;
            font-family: 'Poppins', sans-serif;
            color: var(--text-white);
            min-height: 100vh;
        }

        /* Glassmorphism Classes */
        .glass-navbar {
            background: rgba(0, 51, 102, 0.7) !important;
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--glass-border);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
        }

        .glass-card {
            background: var(--glass-bg);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            box-shadow: var(--shadow-soft);
            margin-bottom: 24px;
            color: var(--text-white);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .glass-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 40px 0 rgba(31, 38, 135, 0.45);
        }

        .navbar-brand {
            font-weight: 700;
            color: var(--uitm-yellow) !important;
            letter-spacing: 1.5px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
            display: flex;
            align-items: center;
        }

        .logo-nav {
            height: 35px;
            width: auto;
            margin-right: 10px;
            filter: drop-shadow(0 2px 2px rgba(0, 0, 0, 0.3));
        }

        /* Header Customization */
        .card-header-glass {
            background: rgba(0, 51, 102, 0.6);
            color: var(--uitm-yellow);
            font-weight: 600;
            border-top-left-radius: 20px !important;
            border-top-right-radius: 20px !important;
            padding: 20px 25px;
            border-bottom: 1px solid var(--glass-border);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .card-header-warning-glass {
            background: rgba(247, 201, 72, 0.2);
            color: var(--uitm-yellow);
            font-weight: 700;
            border-top-left-radius: 20px !important;
            border-top-right-radius: 20px !important;
            padding: 20px 25px;
            border-bottom: 1px solid var(--glass-border);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Stats Cards */
        .stat-card-glass {
            border: none;
            border-radius: 20px;
            position: relative;
            overflow: hidden;
        }

        .stat-card-blue {
            background: linear-gradient(135deg, rgba(0, 51, 102, 0.8) 0%, rgba(0, 85, 170, 0.8) 100%);
            border: 1px solid var(--glass-border);
            color: white !important;
        }

        .stat-card-yellow {
            background: linear-gradient(135deg, rgba(200, 150, 0, 0.8) 0%, rgba(247, 201, 72, 0.6) 100%);
            border: 1px solid var(--glass-border);
            color: white !important;
        }

        .stat-icon-wrapper {
            background: rgba(255, 255, 255, 0.2);
            padding: 15px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white !important;
            /* Force icon white */
        }

        .stat-label {
            font-size: 0.85rem;
            letter-spacing: 1px;
            opacity: 0.9;
            color: white !important;
        }

        .stat-value {
            color: white !important;
        }

        /* Tables & Lists */
        .table-glass {
            background: transparent !important;
            color: var(--text-white);
        }

        .table-glass th {
            background-color: rgba(0, 0, 0, 0.2);
            color: var(--uitm-yellow);
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            border-bottom: 1px solid var(--glass-border);
            border-top: none;
        }

        .table-glass td {
            color: var(--text-white);
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .table-hover tbody tr:hover {
            background-color: rgba(255, 255, 255, 0.05);
            color: white;
        }

        .list-group-glass .list-group-item {
            background: transparent;
            border-color: rgba(255, 255, 255, 0.05);
            padding: 15px 25px;
            color: white;
        }

        .list-group-glass .list-group-item:hover {
            background-color: rgba(255, 255, 255, 0.05);
        }

        /* Forms & Inputs */
        .form-control,
        .form-select {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            color: white;
            padding: 12px 15px;
        }

        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.4);
        }

        .form-control:focus,
        .form-select:focus {
            background: rgba(255, 255, 255, 0.2);
            box-shadow: 0 0 0 4px rgba(247, 201, 72, 0.2);
            border-color: var(--uitm-yellow);
            color: white;
        }

        /* Dropdown options need dark background */
        .form-select option {
            background-color: #2c5364;
            color: white;
        }

        .btn-custom {
            background: linear-gradient(135deg, #F7C948 0%, #e0b020 100%);
            color: #003366;
            font-weight: 700;
            border: none;
            border-radius: 10px;
            padding: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
            transition: all 0.3s;
        }

        .btn-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(247, 201, 72, 0.4);
            color: #002244;
            background: linear-gradient(135deg, #ffdb70 0%, #f0c040 100%);
        }

        /* Modal Customization */
        .modal-content.glass-modal {
            background: rgba(30, 40, 50, 0.95);
            backdrop-filter: blur(15px);
            border: 1px solid var(--glass-border);
            color: white;
        }

        .modal-header {
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .modal-footer {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .btn-close-white {
            filter: invert(1) grayscale(100%) brightness(200%);
        }

        /* Badges */
        .badge {
            padding: 6px 10px;
            border-radius: 6px;
            font-weight: 500;
            letter-spacing: 0.5px;
        }

        .text-muted-glass {
            color: var(--text-muted) !important;
        }

        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.1);
        }

        ::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.4);
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-dark glass-navbar py-3 sticky-top">
        <div class="container">
            <a class="navbar-brand" href="#">
                <img src="logo.png" alt="SafeCampus" class="logo-nav">
                SafeCampus <span
                    style="font-size:0.8em; font-weight:300; opacity:0.8; margin-left: 8px; color:white;">Admin
                    Panel</span>
            </a>

            <div class="d-flex align-items-center">
                <span class="text-white me-3 d-none d-md-block" style="font-weight:500;">Welcome,
                    <strong>Admin</strong></span>
                <a href="?logout=true" class="btn btn-sm btn-danger fw-bold border-0 shadow-sm"
                    style="background: #ff4757; border-radius: 8px; padding: 8px 16px;">
                    <i class="fas fa-sign-out-alt me-1"></i> LOGOUT
                </a>
            </div>
        </div>
    </nav>

    <div class="container mt-5">

        <div class="row mb-5">
            <div class="col-md-6 mb-3 mb-md-0">
                <div class="card stat-card-glass stat-card-blue p-4 shadow-lg">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase mb-2 stat-label">Total Reports</h6>
                            <h2 class="mb-0 fw-bold display-5 stat-value"><?php echo $total_reports; ?></h2>
                        </div>
                        <div class="stat-icon-wrapper">
                            <i class="fas fa-file-alt fa-2x text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card stat-card-glass stat-card-yellow p-4 shadow-lg">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase mb-2 stat-label">Active Locations</h6>
                            <h2 class="mb-0 fw-bold display-5 stat-value"><?php echo $total_locations; ?></h2>
                        </div>
                        <div class="stat-icon-wrapper">
                            <i class="fas fa-map-marker-alt fa-2x text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-4">
                <!-- Add Marker Card -->
                <div class="card glass-card">
                    <div class="card-header card-header-glass">
                        <i class="fas fa-plus-circle me-2"></i> Add New Map Marker
                    </div>
                    <div class="card-body p-4">
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label text-muted-glass small fw-bold">LOCATION NAME</label>
                                <input type="text" name="name" class="form-control" placeholder="e.g. Pondok Polis"
                                    required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted-glass small fw-bold">TYPE</label>
                                <select name="type" class="form-select">
                                    <option value="Security">👮‍♂️ Security Post</option>
                                    <option value="Clinic">🏥 Clinic</option>
                                    <option value="Emergency">🔥 Emergency Point</option>
                                </select>
                            </div>
                            <div class="row">
                                <div class="col mb-3">
                                    <input type="text" name="lat" class="form-control" placeholder="Latitude" required>
                                </div>
                                <div class="col mb-3">
                                    <input type="text" name="lng" class="form-control" placeholder="Longitude" required>
                                </div>
                            </div>
                            <button type="submit" name="add_location" class="btn btn-custom w-100 mt-2">
                                <i class="fas fa-paper-plane me-1"></i> ADD MARKER
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Current Locations List -->
                <div class="card glass-card">
                    <div class="card-header bg-transparent fw-bold border-bottom"
                        style="border-bottom-color: rgba(255,255,255,0.1) !important;">
                        <i class="fas fa-list me-2 text-warning"></i> Current Locations
                    </div>
                    <ul class="list-group list-group-flush list-group-glass">
                        <?php while ($row = $locations->fetch_assoc()): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong class="text-white"><?php echo $row['location_name']; ?></strong><br>
                                    <span class="badge bg-light text-dark border-0 mt-1"
                                        style="opacity: 0.8"><?php echo $row['location_type']; ?></span>
                                </div>
                                <div class="btn-group" role="group">
                                    <!-- View Button -->
                                    <button type="button" class="btn btn-sm btn-outline-warning" onclick="viewLocation('<?php echo htmlspecialchars($row['location_name'], ENT_QUOTES); ?>', 
                                                              '<?php echo htmlspecialchars($row['location_type'], ENT_QUOTES); ?>', 
                                                              '<?php echo $row['latitude']; ?>', 
                                                              '<?php echo $row['longitude']; ?>')">
                                        <i class="fas fa-eye"></i>
                                    </button>

                                    <!-- Edit Button -->
                                    <button type="button" class="btn btn-sm btn-outline-success" onclick="editLocation('<?php echo $row['id']; ?>', 
                                                              '<?php echo htmlspecialchars($row['location_name'], ENT_QUOTES); ?>', 
                                                              '<?php echo htmlspecialchars($row['location_type'], ENT_QUOTES); ?>', 
                                                              '<?php echo $row['latitude']; ?>', 
                                                              '<?php echo $row['longitude']; ?>')">
                                        <i class="fas fa-edit"></i>
                                    </button>

                                    <!-- Map Button -->
                                    <a href="https://www.google.com/maps/search/?api=1&query=<?php echo $row['latitude']; ?>,<?php echo $row['longitude']; ?>"
                                        target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-map-marker-alt"></i>
                                    </a>

                                    <!-- Delete Button -->
                                    <a href="?delete=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-danger"
                                        onclick="return confirm('Delete this marker?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                </div>
            </div>

            <div class="col-lg-8">
                <!-- Incident Reports Table -->
                <div class="card glass-card">
                    <div class="card-header card-header-warning-glass">
                        <i class="fas fa-bell me-2"></i> Incoming Incident Reports
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush list-group-glass">
                            <?php if ($total_reports > 0): ?>
                                <?php
                                $reports->data_seek(0);
                                while ($row = $reports->fetch_assoc()):
                                    ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <div class="d-flex flex-column" style="max-width: 70%;">
                                            <div class="mb-1">
                                                <span class="badge me-2 
                                            <?php
                                            if ($row['incident_type'] == 'Road Accident')
                                                echo 'bg-danger text-white';
                                            elseif ($row['incident_type'] == 'Infrastructure Damage')
                                                echo 'bg-warning text-dark';
                                            else
                                                echo 'bg-info text-dark';
                                            ?>">
                                                    <?php echo $row['incident_type']; ?>
                                                </span>
                                                <strong class="text-white"><?php echo $row['user_name']; ?></strong>
                                            </div>
                                            <small class="text-muted-glass text-truncate"
                                                style="max-width: 100%; display: block;">
                                                <i class="far fa-clock me-1"></i>
                                                <?php echo date('d M, H:i', strtotime($row['report_time'])); ?>
                                                <span class="mx-1">|</span>
                                                <?php echo $row['description']; ?>
                                            </small>
                                        </div>

                                        <div class="btn-group" role="group">
                                            <!-- View Button -->
                                            <button type="button" class="btn btn-sm btn-outline-warning" onclick="viewReport('<?php echo date('d M Y, h:i A', strtotime($row['report_time'])); ?>', 
                                                                '<?php echo htmlspecialchars($row['user_name'], ENT_QUOTES); ?>', 
                                                                '<?php echo htmlspecialchars($row['incident_type'], ENT_QUOTES); ?>', 
                                                                '<?php echo htmlspecialchars(str_replace(array('\r', '\n'), ' ', $row['description']), ENT_QUOTES); ?>', 
                                                                '<?php echo $row['latitude']; ?>', 
                                                                '<?php echo $row['longitude']; ?>')">
                                                <i class="fas fa-eye"></i>
                                            </button>

                                            <!-- Map Button -->
                                            <a href="https://www.google.com/maps/search/?api=1&query=<?php echo $row['latitude']; ?>,<?php echo $row['longitude']; ?>"
                                                target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-map-marker-alt"></i>
                                            </a>

                                            <!-- Delete Button -->
                                            <a href="?delete_report=<?php echo $row['id']; ?>"
                                                class="btn btn-sm btn-outline-danger"
                                                onclick="return confirm('Are you sure you want to delete report by <?php echo htmlspecialchars($row['user_name'], ENT_QUOTES); ?>?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </li>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <li class="list-group-item text-center py-5 text-muted-glass">
                                    <div style="opacity: 0.5;">
                                        <i class="fas fa-clipboard-check fa-3x mb-3"></i><br>
                                        <span class="fs-5">No New Incidents</span><br>
                                        <small>Everything is safe and sound!</small>
                                    </div>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="text-center py-4 mt-5 small text-white-50">
        <p class="mb-0">© 2026 SafeCampus Admin Panel | Universiti Teknologi MARA</p>
        <p style="font-size: 0.75em; opacity: 0.5;">Designed with <i class="fas fa-heart text-danger"></i> for UiTM
            Jasin</p>
    </footer>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Modal View Report -->
    <div class="modal fade" id="viewReportModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content glass-modal">
                <div class="modal-header">
                    <h5 class="modal-title">Incident Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p><strong>Time:</strong> <span id="modalTime"></span></p>
                    <p><strong>Student:</strong> <span id="modalStudent"></span></p>
                    <p><strong>Type:</strong> <span id="modalType"></span></p>
                    <p><strong>Description:</strong> <span id="modalDescription"></span></p>
                    <p><strong>Location:</strong> <span id="modalLocation"></span></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <a href="#" id="modalMapBtn" target="_blank" class="btn btn-primary">Open in Map</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        function viewReport(time, name, type, desc, lat, lng) {
            document.getElementById('modalTime').innerText = time;
            document.getElementById('modalStudent').innerText = name;
            document.getElementById('modalType').innerText = type;
            document.getElementById('modalDescription').innerText = desc;
            document.getElementById('modalLocation').innerText = lat + ', ' + lng;
            document.getElementById('modalMapBtn').href = "https://www.google.com/maps/search/?api=1&query=" + lat + "," + lng;

            var myModal = new bootstrap.Modal(document.getElementById('viewReportModal'));
            myModal.show();
        }

        function viewLocation(name, type, lat, lng) {
            document.getElementById('locName').innerText = name;
            document.getElementById('locType').innerText = type;
            document.getElementById('locCoords').innerText = lat + ', ' + lng;
            document.getElementById('locMapBtn').href = "https://www.google.com/maps/search/?api=1&query=" + lat + "," + lng;

            var locModal = new bootstrap.Modal(document.getElementById('viewLocationModal'));
            locModal.show();
        }

        function editLocation(id, name, type, lat, lng) {
            document.getElementById('editId').value = id;
            document.getElementById('editName').value = name;
            document.getElementById('editType').value = type;
            document.getElementById('editLat').value = lat;
            document.getElementById('editLng').value = lng;

            var editModal = new bootstrap.Modal(document.getElementById('editLocationModal'));
            editModal.show();
        }
    </script>

    <!-- Modal View Location -->
    <div class="modal fade" id="viewLocationModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content glass-modal">
                <div class="modal-header">
                    <h5 class="modal-title text-warning">Location Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p><strong>Name:</strong> <span id="locName"></span></p>
                    <p><strong>Type:</strong> <span id="locType"></span></p>
                    <p><strong>Coordinates:</strong> <span id="locCoords"></span></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <a href="#" id="locMapBtn" target="_blank" class="btn btn-primary">Open in Map</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Edit Location -->
    <div class="modal fade" id="editLocationModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content glass-modal">
                <div class="modal-header">
                    <h5 class="modal-title text-success">Edit Location</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="id" id="editId">
                        <div class="mb-3">
                            <label class="form-label text-muted-glass small fw-bold">LOCATION NAME</label>
                            <input type="text" name="name" id="editName" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted-glass small fw-bold">TYPE</label>
                            <select name="type" id="editType" class="form-select">
                                <option value="Security">👮‍♂️ Security Post</option>
                                <option value="Clinic">🏥 Clinic</option>
                                <option value="Emergency">🔥 Emergency Point</option>
                            </select>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label class="form-label text-muted-glass small fw-bold">LATITUDE</label>
                                <input type="text" name="lat" id="editLat" class="form-control" required>
                            </div>
                            <div class="col mb-3">
                                <label class="form-label text-muted-glass small fw-bold">LONGITUDE</label>
                                <input type="text" name="lng" id="editLng" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="update_location" class="btn btn-success">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>