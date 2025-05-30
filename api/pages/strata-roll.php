<?php
// pages/strata-roll.php
require_once __DIR__ . '/../api/includes/session.php';
require_once __DIR__ . '/../api/database/config.php';

requireLogin(); // Only logged-in users can access strata roll

$currentUser = getCurrentUser();

// Sample owners data (in a real app, this would come from database)
$owners = [
    ['id' => 1, 'unit' => '0101', 'floor' => 1, 'owner_name' => 'John Smith', 'email' => 'john.smith@email.com', 'phone' => '0412 345 678', 'entitlements' => 85, 'type' => 'Owner-Occupier'],
    ['id' => 2, 'unit' => '0102', 'floor' => 1, 'owner_name' => 'Mary Johnson', 'email' => 'mary.j@email.com', 'phone' => '0423 456 789', 'entitlements' => 85, 'type' => 'Owner-Occupier'],
    ['id' => 3, 'unit' => '0201', 'floor' => 2, 'owner_name' => 'Robert Chen', 'email' => 'r.chen@email.com', 'phone' => '0434 567 890', 'entitlements' => 90, 'type' => 'Investor'],
    ['id' => 4, 'unit' => '0202', 'floor' => 2, 'owner_name' => 'Sarah Wilson', 'email' => 'sarah.wilson@email.com', 'phone' => '0445 678 901', 'entitlements' => 90, 'type' => 'Owner-Occupier'],
    ['id' => 5, 'unit' => '0301', 'floor' => 3, 'owner_name' => 'David Thompson', 'email' => 'd.thompson@email.com', 'phone' => '0456 789 012', 'entitlements' => 95, 'type' => 'Committee Member'],
    ['id' => 6, 'unit' => '0302', 'floor' => 3, 'owner_name' => 'Lisa Anderson', 'email' => 'lisa.a@email.com', 'phone' => '0467 890 123', 'entitlements' => 95, 'type' => 'Owner-Occupier'],
    ['id' => 7, 'unit' => '0401', 'floor' => 4, 'owner_name' => 'Michael Brown', 'email' => 'm.brown@email.com', 'phone' => '0478 901 234', 'entitlements' => 100, 'type' => 'Investor'],
    ['id' => 8, 'unit' => '0402', 'floor' => 4, 'owner_name' => 'Jennifer Davis', 'email' => 'jen.davis@email.com', 'phone' => '0489 012 345', 'entitlements' => 100, 'type' => 'Owner-Occupier'],
    ['id' => 9, 'unit' => '1201', 'floor' => 12, 'owner_name' => 'Emma Wilson', 'email' => 'emma.wilson@email.com', 'phone' => '0490 123 456', 'entitlements' => 120, 'type' => 'Committee Member'],
    ['id' => 10, 'unit' => '1204', 'floor' => 12, 'owner_name' => 'Sarah Johnson', 'email' => 'sarah.johnson@email.com', 'phone' => '0401 234 567', 'entitlements' => 130, 'type' => 'Committee Chair'],
];

$totalEntitlements = array_sum(array_column($owners, 'entitlements'));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Strata Roll - Strata Management</title>
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
                        <a class="nav-link" href="/pages/building-info.php">Building Info</a>
                    </li>
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
                        <a class="nav-link active" href="/pages/strata-roll.php">Strata Roll</a>
                    </li>
                </ul>
                <ul class="navbar-nav ms-auto">
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
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <h1><i class="bi bi-people"></i> Strata Roll</h1>
                <p class="lead">Owner directory and unit entitlements information.</p>
            </div>
        </div>

        <!-- Privacy Notice for Regular Owners -->
        <?php if ($currentUser['role'] === 'owner'): ?>
        <div class="row mb-4">
            <div class="col-12">
                <div class="alert alert-info">
                    <h6><i class="bi bi-info-circle"></i> Privacy Notice</h6>
                    <p class="mb-0">Contact details are restricted to maintain owner privacy. Only basic unit and entitlement information is displayed for general owners.</p>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Summary Statistics -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h4 class="text-primary"><?php echo count($owners); ?></h4>
                        <small>Total Units</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h4 class="text-success"><?php echo $totalEntitlements; ?></h4>
                        <small>Total Entitlements</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h4 class="text-warning"><?php echo count(array_filter($owners, fn($o) => $o['type'] === 'Committee Member' || $o['type'] === 'Committee Chair')); ?></h4>
                        <small>Committee Members</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h4 class="text-info"><?php echo count(array_filter($owners, fn($o) => $o['type'] === 'Investor')); ?></h4>
                        <small>Investment Properties</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search and Filter -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control" id="searchInput" placeholder="Search by unit, name, or floor...">
                </div>
            </div>
            <div class="col-md-6">
                <select class="form-select" id="typeFilter">
                    <option value="">All Owner Types</option>
                    <option value="Owner-Occupier">Owner-Occupier</option>
                    <option value="Investor">Investor</option>
                    <option value="Committee Member">Committee Member</option>
                    <option value="Committee Chair">Committee Chair</option>
                </select>
            </div>
        </div>

        <!-- Owners Table -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5><i class="bi bi-list-ul"></i> Unit Owners</h5>
                        <?php if (hasAnyRole(['admin', 'committee'])): ?>
                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addOwnerModal">
                            <i class="bi bi-plus"></i> Add Owner
                        </button>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover" id="ownersTable">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Unit</th>
                                        <th>Floor</th>
                                        <th>Owner Name</th>
                                        <?php if (hasAnyRole(['admin', 'committee'])): ?>
                                        <th>Contact Email</th>
                                        <th>Phone</th>
                                        <?php endif; ?>
                                        <th>Entitlements</th>
                                        <th>Share %</th>
                                        <th>Type</th>
                                        <?php if (hasAnyRole(['admin', 'committee'])): ?>
                                        <th>Actions</th>
                                        <?php endif; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($owners as $owner): ?>
                                    <tr data-type="<?php echo $owner['type']; ?>">
                                        <td><strong><?php echo htmlspecialchars($owner['unit']); ?></strong></td>
                                        <td><?php echo htmlspecialchars($owner['floor']); ?></td>
                                        <td>
                                            <i class="bi bi-person-circle"></i>
                                            <?php echo htmlspecialchars($owner['owner_name']); ?>
                                        </td>
                                        <?php if (hasAnyRole(['admin', 'committee'])): ?>
                                        <td>
                                            <a href="mailto:<?php echo htmlspecialchars($owner['email']); ?>" class="text-decoration-none">
                                                <?php echo htmlspecialchars($owner['email']); ?>
                                            </a>
                                        </td>
                                        <td>
                                            <a href="tel:<?php echo htmlspecialchars($owner['phone']); ?>" class="text-decoration-none">
                                                <?php echo htmlspecialchars($owner['phone']); ?>
                                            </a>
                                        </td>
                                        <?php endif; ?>
                                        <td><?php echo number_format($owner['entitlements']); ?></td>
                                        <td>
                                            <div class="progress" style="height: 20px;">
                                                <div class="progress-bar" role="progressbar" 
                                                     style="width: <?php echo ($owner['entitlements'] / $totalEntitlements * 100); ?>%"
                                                     aria-valuenow="<?php echo ($owner['entitlements'] / $totalEntitlements * 100); ?>" 
                                                     aria-valuemin="0" aria-valuemax="100">
                                                    <?php echo number_format(($owner['entitlements'] / $totalEntitlements * 100), 2); ?>%
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?php 
                                                echo $owner['type'] === 'Committee Chair' ? 'danger' : 
                                                    ($owner['type'] === 'Committee Member' ? 'warning' : 
                                                    ($owner['type'] === 'Investor' ? 'info' : 'primary')); 
                                            ?>">
                                                <?php echo htmlspecialchars($owner['type']); ?>
                                            </span>
                                        </td>
                                        <?php if (hasAnyRole(['admin', 'committee'])): ?>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <button type="button" class="btn btn-outline-primary" title="Edit">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <button type="button" class="btn btn-outline-info" title="Contact">
                                                    <i class="bi bi-envelope"></i>
                                                </button>
                                                <?php if ($currentUser['role'] === 'admin'): ?>
                                                <button type="button" class="btn btn-outline-danger" title="Remove">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <?php endif; ?>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Entitlements Summary -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="bi bi-pie-chart"></i> Entitlements Distribution</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>By Owner Type:</h6>
                                <ul class="list-unstyled">
                                    <?php
                                    $typeStats = [];
                                    foreach ($owners as $owner) {
                                        if (!isset($typeStats[$owner['type']])) {
                                            $typeStats[$owner['type']] = ['count' => 0, 'entitlements' => 0];
                                        }
                                        $typeStats[$owner['type']]['count']++;
                                        $typeStats[$owner['type']]['entitlements'] += $owner['entitlements'];
                                    }
                                    foreach ($typeStats as $type => $stats):
                                    ?>
                                    <li>
                                        <strong><?php echo htmlspecialchars($type); ?>:</strong>
                                        <?php echo $stats['count']; ?> units, 
                                        <?php echo number_format($stats['entitlements']); ?> entitlements
                                        (<?php echo number_format(($stats['entitlements'] / $totalEntitlements * 100), 1); ?>%)
                                    </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6>Important Notes:</h6>
                                <ul class="list-unstyled">
                                    <li>• Total entitlements determine voting power in AGM</li>
                                    <li>• Levies are calculated based on unit entitlements</li>
                                    <li>• Committee members have additional responsibilities</li>
                                    <li>• Contact details are confidential and protected</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Owner Modal (Admin/Committee Only) -->
    <?php if (hasAnyRole(['admin', 'committee'])): ?>
    <div class="modal fade" id="addOwnerModal" tabindex="-1" aria-labelledby="addOwnerModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addOwnerModalLabel">Add New Owner</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="unit" class="form-label">Unit Number</label>
                                    <input type="text" class="form-control" id="unit" placeholder="e.g., 0501">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="floor" class="form-label">Floor</label>
                                    <input type="number" class="form-control" id="floor" min="1" max="15">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="ownerName" class="form-label">Owner Name</label>
                            <input type="text" class="form-control" id="ownerName">
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Phone</label>
                                    <input type="tel" class="form-control" id="phone">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="entitlements" class="form-label">Entitlements</label>
                                    <input type="number" class="form-control" id="entitlements" min="1">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="ownerType" class="form-label">Owner Type</label>
                                    <select class="form-select" id="ownerType">
                                        <option value="Owner-Occupier">Owner-Occupier</option>
                                        <option value="Investor">Investor</option>
                                        <option value="Committee Member">Committee Member</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary">Add Owner</button>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Search functionality
        document.getElementById('searchInput').addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('#ownersTable tbody tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
        
        // Type filtering
        document.getElementById('typeFilter').addEventListener('change', function() {
            const selectedType = this.value;
            const rows = document.querySelectorAll('#ownersTable tbody tr');
            
            rows.forEach(row => {
                const rowType = row.dataset.type;
                if (selectedType === '' || rowType === selectedType) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html> 