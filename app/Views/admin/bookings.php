<?= $this->extend("layouts/admin") ?>

<?= $this->section("body") ?>

<div class="container">
    <div class="row align-items-center">
        <div class="col">
            <h1>Pending Bookings</h1>
        </div>
    </div>
</div>

<hr>

<div class="container-fluid border border-light border-1 shadow-sm rounded-3 p-5 m-0">
    <table class="table table-bordered table-hover align-middle">
        <thead>
            <tr class="bg-dark text-light text-center">
                <th scope="col">#</th>
                <th scope="col">Booking Details</th>
                <th scope="col">Trip Details</th>
                <th scope="col">Capacity</th> <!-- New Column for Capacity -->
                <th scope="col">Status</th>
                <th scope="col">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($bookings)): ?>
                <?php foreach ($bookings as $index => $booking): ?>
                    <tr>
                        <!-- Sequential index -->
                        <td class="text-center"><?= $index + 1 ?></td>

                        <!-- Booking Details -->
                        <td>
                            <strong>User:</strong> <?= $booking['name'] ?><br>
                            <strong>Seats:</strong> <?= $booking['num_seats'] ?><br>
                            <strong>Price:</strong> ₱<?= number_format($booking['price'], 2) ?><br>
                            <small class="text-muted">Created: <?= date('Y-m-d H:i:s', strtotime($booking['created_at'])) ?></small>
                        </td>

                        <!-- Trip Details -->
                        <td>
                            <strong>From:</strong> <?= $booking['from'] ?><br>
                            <strong>To:</strong> <?= $booking['to'] ?><br>
                            <strong>Distance:</strong> <?= $booking['distance'] ?> km
                        </td>

                        <!-- Capacity (Current Capacity and Seats Booked) -->
                        <td class="text-center">
                            <div class="capacity-details">
                                <div class="mb-2">
                                    <strong>Vehicle Capacity:</strong> <span class="badge bg-info"><?= $booking['vehicle_capacity'] ?> seats</span>
                                </div>
                                <div class="mb-2">
                                    <strong>Available:</strong> <span class="badge bg-success"><?= $booking['current_capacity'] ?> seats</span>
                                </div>
                                <div class="mb-2">
                                    <strong>Request:</strong> <span class="badge bg-warning"><?= $booking['num_seats'] ?> seats</span>
                                </div>
                            </div>
                        </td>


                        <!-- Status -->
                        <td class="text-center">
                            <?php if ($booking['status'] === 'Pending'): ?>
                                <span class="badge bg-warning text-dark">Pending</span>
                            <?php elseif ($booking['status'] === 'Confirmed'): ?>
                                <span class="badge bg-success">Confirmed</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Other</span>
                            <?php endif; ?>
                        </td>

                        <!-- Actions -->
                        <td class="text-center">
                            <div class="btn-group-vertical w-100">
                                <!-- View Button -->
                                <a href="<?= site_url('dashboard/bookings/approve/' . $booking['id']) ?>"
                                    class="btn btn-primary w-100 mb-2" title="Approve">
                                    <i class="bi bi-eye"></i> Approve
                                </a>

                                <!-- Edit Button -->
                                <a href="<?= site_url('dashboard/bookings/decline/' . $booking['id']) ?>"
                                    class="btn btn-danger w-100 mb-2" title="Decline" >
                                    <i class="bi bi-pencil-square"></i> Decline
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="text-center">No bookings found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>




<nav aria-label="Page navigation example">
    <ul class="pagination justify-content-end">
        <li class="page-item <?= ($currentPage == 1) ? 'disabled' : '' ?>">
            <a class="page-link" href="<?= base_url("dashboard/bookings/" . ($currentPage - 1)) ?>" tabindex="-1" aria-disabled="true">Previous</a>
        </li>

        <?php
        $totalPages = ceil($resultCount / $perPage);
        for ($i = 1; $i <= $totalPages; $i++): ?>
            <li class="page-item <?= ($currentPage == $i) ? 'active' : '' ?>">
                <a class="page-link" href="<?= base_url("dashboard/bookings/$i") ?>"><?= $i ?></a>
            </li>
        <?php endfor; ?>

        <li class="page-item <?= ($currentPage == $totalPages) ? 'disabled' : '' ?>">
            <a class="page-link" href="<?= base_url("dashboard/bookings/" . ($currentPage + 1)) ?>">Next</a>
        </li>
    </ul>
</nav>
</div>

<?php if (session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger">
        <ul>
            <?php
            $errors = session()->getFlashdata('errors');
            if (is_array($errors)) {
                foreach ($errors as $error) {
                    echo '<li>' . esc($error) . '</li>';
                }
            } else {
                // If it's a string, display it as a single item
                echo '<li>' . esc($errors) . '</li>';
            }
            ?>
        </ul>
    </div>
<?php endif; ?>


<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success">
        <ul>
            <?php
            $success = session()->getFlashdata('success');
            if (is_array($success)) {
                foreach ($success as $succ) {
                    echo '<li>' . esc($succ) . '</li>';
                }
            } else {
                // If it's a string, display it as a single item
                echo '<li>' . esc($success) . '</li>';
            }
            ?>
        </ul>
    </div>
<?php endif; ?>


<?= $this->endSection() ?>