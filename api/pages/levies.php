<?php
// pages/levies.php
require_once __DIR__ . '/../api/includes/session.php';
require_once __DIR__ . '/../api/database/config.php';

requireLogin(); // Only logged-in users can access levies

$currentUser = getCurrentUser();

// Handle levy payment processing (simulation)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'pay_levy') {
        $levyId = (int)$_POST['levy_id'];
        $amount = (float)$_POST['amount'];
        
        // In a real application, you would process payment here
        $success = true; // Simulated success
        
        if ($success) {
            $successMessage = "Payment of $" . number_format($amount, 2) . " processed successfully!";
        } else {
            $errorMessage = "Payment processing failed. Please try again.";
        }
    }
    
    if ($_POST['action'] === 'generate_notice' && hasAnyRole(['admin', 'committee'])) {
        $noticeType = sanitizeInput($_POST['notice_type']);
        $dueDate = sanitizeInput($_POST['due_date']);
        
        $successMessage = "Levy notices generated successfully for " . $noticeType;
    }
}

// Sample levy data (in a real app, this would come from database)
$levies = [
    ['id' => 1, 'unit' => '0101', 'type' => 'Administrative Fund', 'amount' => 285.50, 'due_date' => '2024-04-15', 'status' => 'Paid', 'paid_date' => '2024-04-10'],
    ['id' => 2, 'unit' => '0101', 'type' => 'Capital Works Fund', 'amount' => 142.75, 'due_date' => '2024-04-15', 'status' => 'Outstanding', 'paid_date' => null],
    ['id' => 3, 'unit' => '0102', 'type' => 'Administrative Fund', 'amount' => 285.50, 'due_date' => '2024-04-15', 'status' => 'Overdue', 'paid_date' => null],
    ['id' => 4, 'unit' => '0102', 'type' => 'Capital Works Fund', 'amount' => 142.75, 'due_date' => '2024-04-15', 'status' => 'Outstanding', 'paid_date' => null],
    ['id' => 5, 'unit' => '0201', 'type' => 'Special Levy - Lift Upgrade', 'amount' => 450.00, 'due_date' => '2024-05-01', 'status' => 'Outstanding', 'paid_date' => null],
];

// Budget information
$budget = [
    'admin_fund' => ['collected' => 34260, 'budgeted' => 45000, 'remaining' => 10740],
    'capital_fund' => ['collected' => 17130, 'budgeted' => 22500, 'remaining' => 5370],
    'special_levy' => ['collected' => 13500, 'budgeted' => 54000, 'remaining' => 40500],
];

$totalCollected = array_sum(array_column($budget, 'collected'));
$totalBudgeted = array_sum(array_column($budget, 'budgeted'));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Levies & Budget - Strata Management</title>
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
                        <a class="nav-link active" href="/pages/levies.php">Levies</a>
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
                <h1><i class="bi bi-cash-coin"></i> Levies & Budget Management</h1>
                <p class="lead">Manage levy payments and building budget information.</p>
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

        <!-- Budget Overview (Admin/Committee) -->
        <?php if (hasAnyRole(['admin', 'committee'])): ?>
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="bi bi-bar-chart"></i> Budget Overview 2024-2025</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="card border-primary">
                                    <div class="card-body text-center">
                                        <h6 class="card-title text-primary">Administrative Fund</h6>
                                        <h4>${{ number_format($budget['admin_fund']['collected']) }}</h4>
                                        <div class="progress mb-2">
                                            <div class="progress-bar bg-primary" style="width: <?php echo ($budget['admin_fund']['collected'] / $budget['admin_fund']['budgeted'] * 100); ?>%"></div>
                                        </div>
                                        <small class="text-muted">
                                            <?php echo number_format(($budget['admin_fund']['collected'] / $budget['admin_fund']['budgeted'] * 100), 1); ?>% 
                                            of $<?php echo number_format($budget['admin_fund']['budgeted']); ?>
                                        </small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card border-success">
                                    <div class="card-body text-center">
                                        <h6 class="card-title text-success">Capital Works Fund</h6>
                                        <h4>${{ number_format($budget['capital_fund']['collected']) }}</h4>
                                        <div class="progress mb-2">
                                            <div class="progress-bar bg-success" style="width: <?php echo ($budget['capital_fund']['collected'] / $budget['capital_fund']['budgeted'] * 100); ?>%"></div>
                                        </div>
                                        <small class="text-muted">
                                            <?php echo number_format(($budget['capital_fund']['collected'] / $budget['capital_fund']['budgeted'] * 100), 1); ?>% 
                                            of $<?php echo number_format($budget['capital_fund']['budgeted']); ?>
                                        </small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card border-warning">
                                    <div class="card-body text-center">
                                        <h6 class="card-title text-warning">Special Levy</h6>
                                        <h4>${{ number_format($budget['special_levy']['collected']) }}</h4>
                                        <div class="progress mb-2">
                                            <div class="progress-bar bg-warning" style="width: <?php echo ($budget['special_levy']['collected'] / $budget['special_levy']['budgeted'] * 100); ?>%"></div>
                                        </div>
                                        <small class="text-muted">
                                            <?php echo number_format(($budget['special_levy']['collected'] / $budget['special_levy']['budgeted'] * 100), 1); ?>% 
                                            of $<?php echo number_format($budget['special_levy']['budgeted']); ?>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-12 text-center">
                                <h5>Total Collected: $<?php echo number_format($totalCollected); ?> of $<?php echo number_format($totalBudgeted); ?> 
                                    <span class="badge bg-info"><?php echo number_format(($totalCollected / $totalBudgeted * 100), 1); ?>%</span>
                                </h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Generate Levy Notices (Admin/Committee Only) -->
        <?php if (hasAnyRole(['admin', 'committee'])): ?>
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="bi bi-file-earmark-plus"></i> Generate Levy Notices</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <input type="hidden" name="action" value="generate_notice">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="notice_type" class="form-label">Levy Type</label>
                                        <select class="form-select" id="notice_type" name="notice_type" required>
                                            <option value="">Select Levy Type</option>
                                            <option value="Quarterly Admin Levy">Quarterly Admin Levy</option>
                                            <option value="Quarterly Capital Works">Quarterly Capital Works</option>
                                            <option value="Special Levy">Special Levy</option>
                                            <option value="All Current Levies">All Current Levies</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="due_date" class="form-label">Due Date</label>
                                        <input type="date" class="form-control" id="due_date" name="due_date" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">&nbsp;</label>
                                        <div>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="bi bi-printer"></i> Generate Notices
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Levy Status Filter -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-outline-primary status-filter active" data-status="all">All Levies</button>
                    <button type="button" class="btn btn-outline-success status-filter" data-status="Paid">Paid</button>
                    <button type="button" class="btn btn-outline-warning status-filter" data-status="Outstanding">Outstanding</button>
                    <button type="button" class="btn btn-outline-danger status-filter" data-status="Overdue">Overdue</button>
                </div>
            </div>
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control" id="searchInput" placeholder="Search by unit or levy type...">
                </div>
            </div>
        </div>

        <!-- Levies Table -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="bi bi-list-check"></i> Levy Status</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover" id="leviesTable">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Unit</th>
                                        <th>Levy Type</th>
                                        <th>Amount</th>
                                        <th>Due Date</th>
                                        <th>Status</th>
                                        <th>Paid Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($levies as $levy): ?>
                                    <tr data-status="<?php echo $levy['status']; ?>">
                                        <td><strong><?php echo htmlspecialchars($levy['unit']); ?></strong></td>
                                        <td><?php echo htmlspecialchars($levy['type']); ?></td>
                                        <td><strong>$<?php echo number_format($levy['amount'], 2); ?></strong></td>
                                        <td><?php echo date('M j, Y', strtotime($levy['due_date'])); ?></td>
                                        <td>
                                            <span class="badge bg-<?php 
                                                echo $levy['status'] === 'Paid' ? 'success' : 
                                                    ($levy['status'] === 'Outstanding' ? 'warning' : 'danger'); 
                                            ?>">
                                                <?php echo htmlspecialchars($levy['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($levy['paid_date']): ?>
                                                <?php echo date('M j, Y', strtotime($levy['paid_date'])); ?>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <?php if ($levy['status'] !== 'Paid'): ?>
                                                <button type="button" class="btn btn-outline-success" 
                                                        data-bs-toggle="modal" data-bs-target="#paymentModal"
                                                        data-levy-id="<?php echo $levy['id']; ?>"
                                                        data-amount="<?php echo $levy['amount']; ?>"
                                                        data-type="<?php echo htmlspecialchars($levy['type']); ?>"
                                                        data-unit="<?php echo htmlspecialchars($levy['unit']); ?>"
                                                        title="Pay Levy">
                                                    <i class="bi bi-credit-card"></i> Pay
                                                </button>
                                                <?php endif; ?>
                                                <button type="button" class="btn btn-outline-primary" title="View Details">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                                <button type="button" class="btn btn-outline-info" title="Download Invoice">
                                                    <i class="bi bi-download"></i>
                                                </button>
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

        <!-- Summary Statistics -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-md-3">
                                <h4 class="text-primary"><?php echo count($levies); ?></h4>
                                <small>Total Levies</small>
                            </div>
                            <div class="col-md-3">
                                <h4 class="text-success"><?php echo count(array_filter($levies, fn($l) => $l['status'] === 'Paid')); ?></h4>
                                <small>Paid</small>
                            </div>
                            <div class="col-md-3">
                                <h4 class="text-warning"><?php echo count(array_filter($levies, fn($l) => $l['status'] === 'Outstanding')); ?></h4>
                                <small>Outstanding</small>
                            </div>
                            <div class="col-md-3">
                                <h4 class="text-danger"><?php echo count(array_filter($levies, fn($l) => $l['status'] === 'Overdue')); ?></h4>
                                <small>Overdue</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Modal -->
    <div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="paymentModalLabel">Pay Levy</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="pay_levy">
                        <input type="hidden" name="levy_id" id="modalLevyId">
                        <input type="hidden" name="amount" id="modalAmount">
                        
                        <div class="mb-3">
                            <label class="form-label">Levy Details:</label>
                            <div class="card bg-light">
                                <div class="card-body">
                                    <p class="mb-1"><strong>Unit:</strong> <span id="modalUnit"></span></p>
                                    <p class="mb-1"><strong>Type:</strong> <span id="modalType"></span></p>
                                    <p class="mb-0"><strong>Amount:</strong> $<span id="modalAmountDisplay"></span></p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="payment_method" class="form-label">Payment Method</label>
                            <select class="form-select" id="payment_method" name="payment_method" required>
                                <option value="">Select Payment Method</option>
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="credit_card">Credit Card</option>
                                <option value="bpay">BPAY</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="reference" class="form-label">Payment Reference</label>
                            <input type="text" class="form-control" id="reference" name="reference" placeholder="Optional reference number">
                        </div>
                        
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> 
                            <strong>Note:</strong> Payment processing may take 1-3 business days to reflect in your account.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-credit-card"></i> Process Payment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Status filtering
        document.querySelectorAll('.status-filter').forEach(button => {
            button.addEventListener('click', function() {
                document.querySelectorAll('.status-filter').forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');
                
                const status = this.dataset.status;
                const rows = document.querySelectorAll('#leviesTable tbody tr');
                
                rows.forEach(row => {
                    if (status === 'all' || row.dataset.status === status) {
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
            const rows = document.querySelectorAll('#leviesTable tbody tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
        
        // Payment modal setup
        const paymentModal = document.getElementById('paymentModal');
        paymentModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const levyId = button.getAttribute('data-levy-id');
            const amount = button.getAttribute('data-amount');
            const type = button.getAttribute('data-type');
            const unit = button.getAttribute('data-unit');
            
            document.getElementById('modalLevyId').value = levyId;
            document.getElementById('modalAmount').value = amount;
            document.getElementById('modalUnit').textContent = unit;
            document.getElementById('modalType').textContent = type;
            document.getElementById('modalAmountDisplay').textContent = parseFloat(amount).toFixed(2);
        });
    </script>
</body>
</html> 