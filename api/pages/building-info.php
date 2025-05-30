<?php
// pages/building-info.php
require_once __DIR__ . '/../api/includes/session.php';
require_once __DIR__ . '/../api/database/config.php';

$currentUser = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Building Information - Strata Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="/api/index.php">Strata Management</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="/api/index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="/pages/building-info.php">Building Info</a>
                    </li>
                    <?php if (isLoggedIn()): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/pages/documents.php">Documents</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/pages/maintenance.php">Maintenance</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/pages/levies.php">Levies</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/pages/strata-roll.php">Strata Roll</a>
                        </li>
                    <?php endif; ?>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <?php if (isLoggedIn()): ?>
                        <li class="nav-item">
                            <span class="navbar-text me-3">
                                Welcome, <?php echo htmlspecialchars($currentUser['username']); ?>
                                <span class="badge bg-<?php 
                                    echo $currentUser['role'] === 'admin' ? 'danger' : 
                                        ($currentUser['role'] === 'committee' ? 'warning' : 'primary'); 
                                ?>"><?php echo htmlspecialchars(ucfirst($currentUser['role'])); ?></span>
                            </span>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/api/logout.php">Logout</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/api/pages/login.php">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/api/pages/register.php">Register</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <!-- Building Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h1 class="card-title"><i class="bi bi-building"></i> Harbour View Towers</h1>
                        <p class="card-text lead">123 Harbour Street, Sydney NSW 2000</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Building Overview -->
        <div class="row mb-4">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3><i class="bi bi-info-circle"></i> Building Overview</h3>
                    </div>
                    <div class="card-body">
                        <p>Harbour View Towers is a prestigious 15-story residential strata building completed in 2018. The building features modern amenities and stunning harbour views, making it one of Sydney's premier residential addresses.</p>
                        
                        <h5>Building Features:</h5>
                        <ul>
                            <li>24/7 Concierge Service</li>
                            <li>Gymnasium and Swimming Pool</li>
                            <li>Rooftop Garden and BBQ Area</li>
                            <li>Underground Parking (2 levels)</li>
                            <li>Conference Room and Business Centre</li>
                            <li>Visitor Parking</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="bi bi-graph-up"></i> Building Statistics</h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-6 mb-3">
                                <h4 class="text-primary">120</h4>
                                <small>Total Units</small>
                            </div>
                            <div class="col-6 mb-3">
                                <h4 class="text-success">15</h4>
                                <small>Floors</small>
                            </div>
                            <div class="col-6 mb-3">
                                <h4 class="text-warning">180</h4>
                                <small>Parking Spaces</small>
                            </div>
                            <div class="col-6 mb-3">
                                <h4 class="text-info">2018</h4>
                                <small>Built</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Committee Information -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3><i class="bi bi-people"></i> Strata Committee</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="card border-primary">
                                    <div class="card-body text-center">
                                        <i class="bi bi-person-badge fs-2 text-primary"></i>
                                        <h5 class="mt-2">Chairperson</h5>
                                        <p class="mb-0">Sarah Johnson</p>
                                        <small class="text-muted">Unit 1204</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card border-success">
                                    <div class="card-body text-center">
                                        <i class="bi bi-calculator fs-2 text-success"></i>
                                        <h5 class="mt-2">Treasurer</h5>
                                        <p class="mb-0">Michael Chen</p>
                                        <small class="text-muted">Unit 0805</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card border-warning">
                                    <div class="card-body text-center">
                                        <i class="bi bi-file-earmark-text fs-2 text-warning"></i>
                                        <h5 class="mt-2">Secretary</h5>
                                        <p class="mb-0">Emma Wilson</p>
                                        <small class="text-muted">Unit 0612</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact Information -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="bi bi-building-gear"></i> Strata Manager</h5>
                    </div>
                    <div class="card-body">
                        <h6>Sydney Strata Solutions Pty Ltd</h6>
                        <p class="mb-1"><i class="bi bi-geo-alt"></i> Level 5, 789 George Street, Sydney NSW 2000</p>
                        <p class="mb-1"><i class="bi bi-telephone"></i> (02) 9876 5432</p>
                        <p class="mb-1"><i class="bi bi-envelope"></i> info@sydneystratasolutions.com.au</p>
                        <p class="mb-0"><i class="bi bi-person-contact"></i> Manager: David Thompson</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="bi bi-shield-check"></i> Emergency Contacts</h5>
                    </div>
                    <div class="card-body">
                        <h6>Building Manager (24/7)</h6>
                        <p class="mb-1"><i class="bi bi-telephone-fill text-danger"></i> (02) 9876 5400</p>
                        <h6>Emergency Services</h6>
                        <p class="mb-1"><i class="bi bi-telephone-fill text-danger"></i> 000</p>
                        <h6>Security</h6>
                        <p class="mb-0"><i class="bi bi-telephone"></i> (02) 9876 5401</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Building Rules and Bylaws -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="bi bi-list-check"></i> Important Building Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Building Facilities Hours:</h6>
                                <ul class="list-unstyled">
                                    <li><strong>Gymnasium:</strong> 6:00 AM - 10:00 PM</li>
                                    <li><strong>Swimming Pool:</strong> 6:00 AM - 9:00 PM</li>
                                    <li><strong>Rooftop Garden:</strong> 8:00 AM - 8:00 PM</li>
                                    <li><strong>Conference Room:</strong> 8:00 AM - 6:00 PM (booking required)</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6>Moving Guidelines:</h6>
                                <ul class="list-unstyled">
                                    <li>• Advance booking required</li>
                                    <li>• Moving hours: 9:00 AM - 5:00 PM (weekdays only)</li>
                                    <li>• Service elevator must be used</li>
                                    <li>• Deposit required for protective covering</li>
                                </ul>
                            </div>
                        </div>
                        
                        <?php if (isLoggedIn()): ?>
                        <div class="mt-3">
                            <a href="/pages/documents.php" class="btn btn-primary">
                                <i class="bi bi-file-earmark-pdf"></i> View Full Building Rules & Bylaws
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 