<?php
// pages/maintenance.php
require_once __DIR__ . '/../api/includes/session.php';
require_once __DIR__ . '/../api/database/config.php';

requireLogin(); // Only logged-in users can access maintenance

$currentUser = getCurrentUser();

// Handle maintenance request submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'submit_request') {
        $title = sanitizeInput($_POST['title']);
        $description = sanitizeInput($_POST['description']);
        $priority = sanitizeInput($_POST['priority']);
        $category = sanitizeInput($_POST['category']);
        $location = sanitizeInput($_POST['location']);
        
        // In a real application, you would save to database here
        $success = true; // Simulated success
        
        if ($success) {
            $successMessage = "Maintenance request submitted successfully! Ticket #" . rand(1000, 9999);
        } else {
            $errorMessage = "Failed to submit maintenance request. Please try again.";
        }
    }
    
    if ($_POST['action'] === 'update_status' && hasAnyRole(['admin', 'committee'])) {
        $requestId = (int)$_POST['request_id'];
        $newStatus = sanitizeInput($_POST['new_status']);
        $notes = sanitizeInput($_POST['notes']);
        
        $successMessage = "Request status updated successfully!";
    }
}

// Sample maintenance requests data (in a real app, this would come from database)
$requests = [
    ['id' => 1, 'ticket' => 'MNT-1001', 'title' => 'Broken elevator button - Level 5', 'category' => 'Elevator', 'priority' => 'High', 'status' => 'In Progress', 'unit' => '0501', 'submitted_by' => 'John Smith', 'submitted_date' => '2024-03-15', 'assigned_to' => 'ABC Elevator Services', 'estimated_completion' => '2024-03-20'],
    ['id' => 2, 'ticket' => 'MNT-1002', 'title' => 'Gym equipment needs maintenance', 'category' => 'Common Areas', 'priority' => 'Medium', 'status' => 'Pending', 'unit' => 'Committee', 'submitted_by' => 'Building Manager', 'submitted_date' => '2024-03-18', 'assigned_to' => null, 'estimated_completion' => null],
    ['id' => 3, 'ticket' => 'MNT-1003', 'title' => 'Leaking pipe in parking garage', 'category' => 'Plumbing', 'priority' => 'High', 'status' => 'Completed', 'unit' => 'Common', 'submitted_by' => 'Security', 'submitted_date' => '2024-03-10', 'assigned_to' => 'Sydney Plumbing Co.', 'estimated_completion' => '2024-03-12'],
    ['id' => 4, 'ticket' => 'MNT-1004', 'title' => 'Air conditioning not working', 'category' => 'HVAC', 'priority' => 'Medium', 'status' => 'Scheduled', 'unit' => '1204', 'submitted_by' => 'Sarah Johnson', 'submitted_date' => '2024-03-19', 'assigned_to' => 'Climate Control Solutions', 'estimated_completion' => '2024-03-25'],
    ['id' => 5, 'ticket' => 'MNT-1005', 'title' => 'Pool pump making noise', 'category' => 'Pool/Spa', 'priority' => 'Low', 'status' => 'Pending', 'unit' => 'Common', 'submitted_by' => 'Maintenance Team', 'submitted_date' => '2024-03-20', 'assigned_to' => null, 'estimated_completion' => null],
];

$categories = ['Plumbing', 'Electrical', 'HVAC', 'Elevator', 'Common Areas', 'Pool/Spa', 'Security', 'Cleaning', 'Other'];
$priorities = ['Low', 'Medium', 'High', 'Emergency'];
$statuses = ['Pending', 'Scheduled', 'In Progress', 'Completed', 'On Hold'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance Requests - Strata Management</title>
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
                        <a class="nav-link active" href="/pages/maintenance.php">Maintenance</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/pages/levies.php">Levies</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/pages/strata-roll.php">Strata Roll</a>
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
                <h1><i class="bi bi-tools"></i> Maintenance Requests</h1>
                <p class="lead">Submit and track maintenance requests for building issues.</p>
            </div>
        </div>

        <!-- Success/Error Messages -->
        <?php if (isset($successMessage)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($successMessage); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($errorMessage)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($errorMessage); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Submit New Request -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="bi bi-plus-circle"></i> Submit New Maintenance Request</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <input type="hidden" name="action" value="submit_request">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="title" class="form-label">Issue Title</label>
                                        <input type="text" class="form-control" id="title" name="title" required placeholder="Brief description of the issue">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="category" class="form-label">Category</label>
                                        <select class="form-select" id="category" name="category" required>
                                            <option value="">Select Category</option>
                                            <?php foreach ($categories as $category): ?>
                                                <option value="<?php echo $category; ?>"><?php echo $category; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="priority" class="form-label">Priority</label>
                                        <select class="form-select" id="priority" name="priority" required>
                                            <option value="">Select Priority</option>
                                            <?php foreach ($priorities as $priority): ?>
                                                <option value="<?php echo $priority; ?>"><?php echo $priority; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="location" class="form-label">Location</label>
                                        <input type="text" class="form-control" id="location" name="location" required placeholder="e.g., Unit 0501, Gym, Parking Level B1">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="contact_phone" class="form-label">Contact Phone (Optional)</label>
                                        <input type="tel" class="form-control" id="contact_phone" name="contact_phone" placeholder="For urgent issues">
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Detailed Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3" required placeholder="Please provide as much detail as possible about the issue..."></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="photos" class="form-label">Photos (Optional)</label>
                                <input type="file" class="form-control" id="photos" name="photos[]" multiple accept="image/*">
                                <div class="form-text">Upload photos to help identify the issue (max 5 files, 10MB each)</div>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-send"></i> Submit Request
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Request Statistics -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card text-center border-warning">
                    <div class="card-body">
                        <h4 class="text-warning"><?php echo count(array_filter($requests, fn($r) => $r['status'] === 'Pending')); ?></h4>
                        <small>Pending</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center border-info">
                    <div class="card-body">
                        <h4 class="text-info"><?php echo count(array_filter($requests, fn($r) => $r['status'] === 'Scheduled')); ?></h4>
                        <small>Scheduled</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center border-primary">
                    <div class="card-body">
                        <h4 class="text-primary"><?php echo count(array_filter($requests, fn($r) => $r['status'] === 'In Progress')); ?></h4>
                        <small>In Progress</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center border-success">
                    <div class="card-body">
                        <h4 class="text-success"><?php echo count(array_filter($requests, fn($r) => $r['status'] === 'Completed')); ?></h4>
                        <small>Completed</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter and Search -->
        <div class="row mb-4">
            <div class="col-md-4">
                <select class="form-select" id="statusFilter">
                    <option value="">All Statuses</option>
                    <?php foreach ($statuses as $status): ?>
                        <option value="<?php echo $status; ?>"><?php echo $status; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <select class="form-select" id="priorityFilter">
                    <option value="">All Priorities</option>
                    <?php foreach ($priorities as $priority): ?>
                        <option value="<?php echo $priority; ?>"><?php echo $priority; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control" id="searchInput" placeholder="Search requests...">
                </div>
            </div>
        </div>

        <!-- Requests Table -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="bi bi-list-task"></i> All Maintenance Requests</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover" id="requestsTable">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Ticket #</th>
                                        <th>Title</th>
                                        <th>Category</th>
                                        <th>Priority</th>
                                        <th>Status</th>
                                        <th>Unit/Location</th>
                                        <th>Submitted</th>
                                        <?php if (hasAnyRole(['admin', 'committee'])): ?>
                                        <th>Assigned To</th>
                                        <?php endif; ?>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($requests as $request): ?>
                                    <tr data-status="<?php echo $request['status']; ?>" data-priority="<?php echo $request['priority']; ?>">
                                        <td><strong><?php echo htmlspecialchars($request['ticket']); ?></strong></td>
                                        <td><?php echo htmlspecialchars($request['title']); ?></td>
                                        <td>
                                            <span class="badge bg-secondary"><?php echo htmlspecialchars($request['category']); ?></span>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?php 
                                                echo $request['priority'] === 'Emergency' ? 'danger' : 
                                                    ($request['priority'] === 'High' ? 'warning' : 
                                                    ($request['priority'] === 'Medium' ? 'info' : 'light text-dark')); 
                                            ?>">
                                                <?php echo htmlspecialchars($request['priority']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?php 
                                                echo $request['status'] === 'Completed' ? 'success' : 
                                                    ($request['status'] === 'In Progress' ? 'primary' : 
                                                    ($request['status'] === 'Scheduled' ? 'info' : 
                                                    ($request['status'] === 'On Hold' ? 'secondary' : 'warning'))); 
                                            ?>">
                                                <?php echo htmlspecialchars($request['status']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo htmlspecialchars($request['unit']); ?></td>
                                        <td><?php echo date('M j, Y', strtotime($request['submitted_date'])); ?></td>
                                        <?php if (hasAnyRole(['admin', 'committee'])): ?>
                                        <td>
                                            <?php if ($request['assigned_to']): ?>
                                                <small><?php echo htmlspecialchars($request['assigned_to']); ?></small>
                                            <?php else: ?>
                                                <span class="text-muted">Not assigned</span>
                                            <?php endif; ?>
                                        </td>
                                        <?php endif; ?>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <button type="button" class="btn btn-outline-primary" title="View Details" 
                                                        data-bs-toggle="modal" data-bs-target="#viewModal"
                                                        data-request='<?php echo json_encode($request); ?>'>
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                                <?php if (hasAnyRole(['admin', 'committee'])): ?>
                                                <button type="button" class="btn btn-outline-warning" title="Update Status"
                                                        data-bs-toggle="modal" data-bs-target="#updateModal"
                                                        data-request-id="<?php echo $request['id']; ?>"
                                                        data-current-status="<?php echo $request['status']; ?>">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- View Request Details Modal -->
    <div class="modal fade" id="viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewModalLabel">Request Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="modalContent">
                    <!-- Content will be populated by JavaScript -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Update Status Modal (Admin/Committee Only) -->
    <?php if (hasAnyRole(['admin', 'committee'])): ?>
    <div class="modal fade" id="updateModal" tabindex="-1" aria-labelledby="updateModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateModalLabel">Update Request Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="update_status">
                        <input type="hidden" name="request_id" id="updateRequestId">
                        
                        <div class="mb-3">
                            <label for="new_status" class="form-label">New Status</label>
                            <select class="form-select" id="new_status" name="new_status" required>
                                <?php foreach ($statuses as $status): ?>
                                    <option value="<?php echo $status; ?>"><?php echo $status; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes (Optional)</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Add any notes about this status update..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Status</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Filter functionality
        document.getElementById('statusFilter').addEventListener('change', function() {
            filterTable();
        });
        
        document.getElementById('priorityFilter').addEventListener('change', function() {
            filterTable();
        });
        
        document.getElementById('searchInput').addEventListener('input', function() {
            filterTable();
        });
        
        function filterTable() {
            const statusFilter = document.getElementById('statusFilter').value;
            const priorityFilter = document.getElementById('priorityFilter').value;
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const rows = document.querySelectorAll('#requestsTable tbody tr');
            
            rows.forEach(row => {
                const status = row.dataset.status;
                const priority = row.dataset.priority;
                const text = row.textContent.toLowerCase();
                
                const statusMatch = !statusFilter || status === statusFilter;
                const priorityMatch = !priorityFilter || priority === priorityFilter;
                const searchMatch = !searchTerm || text.includes(searchTerm);
                
                if (statusMatch && priorityMatch && searchMatch) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }
        
        // View modal setup
        const viewModal = document.getElementById('viewModal');
        viewModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const request = JSON.parse(button.getAttribute('data-request'));
            
            const content = `
                <div class="row">
                    <div class="col-md-6">
                        <h6>Request Information</h6>
                        <p><strong>Ticket:</strong> ${request.ticket}</p>
                        <p><strong>Title:</strong> ${request.title}</p>
                        <p><strong>Category:</strong> ${request.category}</p>
                        <p><strong>Priority:</strong> ${request.priority}</p>
                        <p><strong>Status:</strong> ${request.status}</p>
                        <p><strong>Location:</strong> ${request.unit}</p>
                    </div>
                    <div class="col-md-6">
                        <h6>Submission Details</h6>
                        <p><strong>Submitted by:</strong> ${request.submitted_by}</p>
                        <p><strong>Date:</strong> ${request.submitted_date}</p>
                        <p><strong>Assigned to:</strong> ${request.assigned_to || 'Not assigned'}</p>
                        <p><strong>Est. Completion:</strong> ${request.estimated_completion || 'TBD'}</p>
                    </div>
                </div>
                <div class="mt-3">
                    <h6>Description</h6>
                    <p class="bg-light p-3 rounded">This is a sample description. In a real application, this would contain the full detailed description of the maintenance issue.</p>
                </div>
            `;
            
            document.getElementById('modalContent').innerHTML = content;
        });
        
        <?php if (hasAnyRole(['admin', 'committee'])): ?>
        // Update modal setup
        const updateModal = document.getElementById('updateModal');
        updateModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const requestId = button.getAttribute('data-request-id');
            const currentStatus = button.getAttribute('data-current-status');
            
            document.getElementById('updateRequestId').value = requestId;
            document.getElementById('new_status').value = currentStatus;
        });
        <?php endif; ?>
    </script>
</body>
</html> 