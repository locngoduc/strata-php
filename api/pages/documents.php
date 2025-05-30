<?php
// pages/documents.php
require_once __DIR__ . '/../api/includes/session.php';
require_once __DIR__ . '/../api/database/config.php';

requireLogin(); // Only logged-in users can access documents

$currentUser = getCurrentUser();

// Handle document upload (for admin/committee only)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && hasAnyRole(['admin', 'committee'])) {
    if (isset($_POST['action']) && $_POST['action'] === 'upload') {
        $title = sanitizeInput($_POST['title']);
        $description = sanitizeInput($_POST['description']);
        $category = sanitizeInput($_POST['category']);
        
        // In a real application, you would handle file upload here
        $success = true; // Simulated success
        
        if ($success) {
            $successMessage = "Document uploaded successfully!";
        } else {
            $errorMessage = "Failed to upload document.";
        }
    }
}

// Sample documents data (in a real app, this would come from database)
$documents = [
    ['id' => 1, 'title' => 'Building Insurance Certificate 2024', 'category' => 'Insurance', 'date' => '2024-01-15', 'size' => '2.4 MB', 'type' => 'PDF'],
    ['id' => 2, 'title' => 'Annual Financial Report 2023', 'category' => 'Financial', 'date' => '2024-03-01', 'size' => '1.8 MB', 'type' => 'PDF'],
    ['id' => 3, 'title' => 'By-Laws and Building Rules', 'category' => 'Legal', 'date' => '2023-12-01', 'size' => '956 KB', 'type' => 'PDF'],
    ['id' => 4, 'title' => 'AGM Minutes - March 2024', 'category' => 'Minutes', 'date' => '2024-03-15', 'size' => '654 KB', 'type' => 'PDF'],
    ['id' => 5, 'title' => 'Budget 2024-2025', 'category' => 'Financial', 'date' => '2024-03-20', 'size' => '1.2 MB', 'type' => 'Excel'],
    ['id' => 6, 'title' => 'Building Maintenance Schedule', 'category' => 'Maintenance', 'date' => '2024-01-10', 'size' => '758 KB', 'type' => 'PDF'],
];

$categories = ['All', 'Insurance', 'Financial', 'Legal', 'Minutes', 'Maintenance'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documents - Strata Management</title>
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
                        <a class="nav-link active" href="/pages/documents.php">Documents</a>
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
                <h1><i class="bi bi-folder2-open"></i> Document Library</h1>
                <p class="lead">Access important building documents, reports, and notices.</p>
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

        <!-- Upload Document (Admin/Committee Only) -->
        <?php if (hasAnyRole(['admin', 'committee'])): ?>
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="bi bi-cloud-upload"></i> Upload Document</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="action" value="upload">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="title" class="form-label">Document Title</label>
                                        <input type="text" class="form-control" id="title" name="title" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="category" class="form-label">Category</label>
                                        <select class="form-select" id="category" name="category" required>
                                            <option value="">Select Category</option>
                                            <option value="Insurance">Insurance</option>
                                            <option value="Financial">Financial</option>
                                            <option value="Legal">Legal</option>
                                            <option value="Minutes">Minutes</option>
                                            <option value="Maintenance">Maintenance</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="file" class="form-label">Document File</label>
                                        <input type="file" class="form-control" id="file" name="file" accept=".pdf,.doc,.docx,.xls,.xlsx" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="description" class="form-label">Description</label>
                                        <textarea class="form-control" id="description" name="description" rows="2" placeholder="Brief description of the document"></textarea>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-upload"></i> Upload Document
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Filter and Search -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="btn-group" role="group" aria-label="Document categories">
                    <?php foreach ($categories as $category): ?>
                        <button type="button" class="btn btn-outline-primary category-filter <?php echo $category === 'All' ? 'active' : ''; ?>" 
                                data-category="<?php echo $category; ?>">
                            <?php echo $category; ?>
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control" id="searchInput" placeholder="Search documents...">
                </div>
            </div>
        </div>

        <!-- Documents Table -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="bi bi-file-earmark-text"></i> Available Documents</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover" id="documentsTable">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Document</th>
                                        <th>Category</th>
                                        <th>Date</th>
                                        <th>Size</th>
                                        <th>Type</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($documents as $doc): ?>
                                    <tr data-category="<?php echo $doc['category']; ?>">
                                        <td>
                                            <i class="bi bi-file-earmark-<?php echo strtolower($doc['type']); ?>"></i>
                                            <strong><?php echo htmlspecialchars($doc['title']); ?></strong>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?php 
                                                echo $doc['category'] === 'Insurance' ? 'success' : 
                                                    ($doc['category'] === 'Financial' ? 'warning' : 
                                                    ($doc['category'] === 'Legal' ? 'danger' : 
                                                    ($doc['category'] === 'Minutes' ? 'info' : 'secondary'))); 
                                            ?>">
                                                <?php echo htmlspecialchars($doc['category']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('M j, Y', strtotime($doc['date'])); ?></td>
                                        <td><?php echo htmlspecialchars($doc['size']); ?></td>
                                        <td>
                                            <span class="badge bg-light text-dark"><?php echo htmlspecialchars($doc['type']); ?></span>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <button type="button" class="btn btn-outline-primary" title="View">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                                <button type="button" class="btn btn-outline-success" title="Download">
                                                    <i class="bi bi-download"></i>
                                                </button>
                                                <?php if (hasAnyRole(['admin', 'committee'])): ?>
                                                <button type="button" class="btn btn-outline-danger" title="Delete">
                                                    <i class="bi bi-trash"></i>
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

        <!-- Document Statistics -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-md-2">
                                <h4 class="text-primary"><?php echo count($documents); ?></h4>
                                <small>Total Documents</small>
                            </div>
                            <div class="col-md-2">
                                <h4 class="text-success"><?php echo count(array_filter($documents, fn($d) => $d['category'] === 'Insurance')); ?></h4>
                                <small>Insurance</small>
                            </div>
                            <div class="col-md-2">
                                <h4 class="text-warning"><?php echo count(array_filter($documents, fn($d) => $d['category'] === 'Financial')); ?></h4>
                                <small>Financial</small>
                            </div>
                            <div class="col-md-2">
                                <h4 class="text-danger"><?php echo count(array_filter($documents, fn($d) => $d['category'] === 'Legal')); ?></h4>
                                <small>Legal</small>
                            </div>
                            <div class="col-md-2">
                                <h4 class="text-info"><?php echo count(array_filter($documents, fn($d) => $d['category'] === 'Minutes')); ?></h4>
                                <small>Minutes</small>
                            </div>
                            <div class="col-md-2">
                                <h4 class="text-secondary"><?php echo count(array_filter($documents, fn($d) => $d['category'] === 'Maintenance')); ?></h4>
                                <small>Maintenance</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Category filtering
        document.querySelectorAll('.category-filter').forEach(button => {
            button.addEventListener('click', function() {
                // Update active button
                document.querySelectorAll('.category-filter').forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');
                
                const category = this.dataset.category;
                const rows = document.querySelectorAll('#documentsTable tbody tr');
                
                rows.forEach(row => {
                    if (category === 'All' || row.dataset.category === category) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        });
        
        // Search functionality
        document.getElementById('searchInput').addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('#documentsTable tbody tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html> 