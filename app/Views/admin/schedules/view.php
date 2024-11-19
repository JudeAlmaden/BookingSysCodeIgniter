<?= $this->extend("layouts/admin") ?>

<?= $this->section("body") ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger">
        <?= session()->getFlashdata('error') ?>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success">
        <?= session()->getFlashdata('success') ?>
    </div>
<?php endif; ?>

<a href="javascript:void(0);" onclick="window.history.back();" class="btn btn-secondary mb-3">
    <i class="bi bi-arrow-left"></i> Back
</a>


<div class="card mb-4">
    <div class="card-header">
        <h5>Vehicle Information</h5>
    </div>
    <div class="card-body">
        <p><strong>Tag:</strong> <?= $vehicle['tag'] ?></p>
        <p><strong>Type:</strong> <?= $vehicle['type'] ?></p>
        <p><strong>Number of Seats:</strong> <?= $vehicle['number_seats'] ?></p>

        <?php if (isset($schedules[0]['status']) && $schedules[0]['status'] == "Available"): ?>
            <!-- Mark as Completed Button -->
            <a href="<?= base_url('/dashboard/schedules/complete/' . $id) ?>" class="btn btn-primary">
                Mark as Completed
            </a>
            <!-- Cancel Button -->
            <a href="<?= base_url('/dashboard/schedules/cancel/' . $id) ?>" class="btn btn-danger" 
            onclick="return confirm('Are you sure you want to cancel this trip?');">
                Cancel the trip
            </a>
        <?php endif; ?>
    </div>
</div>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>ETA</th>
            <th>Stop Index</th>
            <th>Reservations</th>
            <th>Status</th>
            <th>Stop Name</th>
            <th>Distance</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($schedules as $schedule): ?>
            <tr>
                <td><?= $schedule['ETA'] ?></td>
                <td><?= $schedule['stop_index'] ?></td>
                <td><?= $schedule['reservations'] ?></td>
                <td><?= $schedule['status'] ?></td>
                <td><?= $schedule['stop_name'] ?></td>
                <td><?= $schedule['distance'] ?></td>
                <td>
                    <a href="/dashboard/schedules/reservations/<?= $schedule['id'] ?>" class="btn btn-primary btn-sm">View Reservations</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>


<?= $this->endSection()?>