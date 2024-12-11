<?= $this->extend("layouts/admin") ?>

<?= $this->section("body") ?>

<div class="container my-5">
    <h1 class="text-center mb-4">Admin Dashboard</h1>

    <div class="row">
        <!-- Total Revenue Card -->
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">Total Revenue for the Current Month</h5>
                </div>
                <div class="card-body text-center">
                    <h2 class="display-4">₱<?= number_format($totalRevenue, 2) ?></h2>
                    <p class="lead">This is the total revenue from all approved payments in the current month.</p>
                </div>
                <div class="card-footer text-muted">
                    <small>Last updated: <?= date('F j, Y') ?></small>
                </div>
            </div>
        </div>

        <!-- Total Accepted Bookings Card -->
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-primary text-white text-center">
                    <h5 class="card-title mb-0">Total Accepted Bookings for Current Month</h5>
                </div>
                <div class="card-body text-center">
                    <?php if ($totalAcceptedBookings > 0): ?>
                        <div class="d-flex justify-content-center align-items-center">
                            <i class="bi bi-check-circle-fill text-success fs-1 me-3"></i>
                            <span class="fs-4 text-success"><?= $totalAcceptedBookings ?> Accepted Bookings</span>
                        </div>
                    <?php else: ?>
                        <div class="d-flex justify-content-center align-items-center">
                            <i class="bi bi-x-circle-fill text-danger fs-1 me-3"></i>
                            <span class="fs-4 text-danger">No accepted bookings for this month</span>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="card-footer bg-light text-center">
                    <small class="text-muted">Updated daily</small>
                </div>
            </div>
        </div>

        <!-- Total Trips This Month Card -->
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-info text-white">
                    <h5 class="card-title mb-0">Total Trips This Month</h5>
                </div>
                <div class="card-body text-center">
                    <h3 class="card-text"><?= number_format($totalTripsThisMonth) ?></h3>
                    <p class="text-muted">Total number of trips for the current month.</p>
                </div>
            </div>
        </div>
    </div>
</div>


<?= $this->endSection() ?>