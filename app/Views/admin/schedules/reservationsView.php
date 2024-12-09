<?= $this->extend("layouts/admin") ?>

<?= $this->section("body") ?>

<a href="javascript:void(0);" onclick="window.history.back();" class="btn btn-secondary mb-3">
    <i class="bi bi-arrow-left"></i> Back
</a>

<div class="container mt-5">
    <h4 class="text-center text-primary mb-4">Booked Passengers for 
        <span class="text-uppercase"><?= esc($stop['stop_name']) ?></span>
    </h4>

    <?php if ($passengers !== null): ?>
        <div class="card shadow-lg">
            <div class="card-body">
                <table class="table table-striped table-bordered table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Stop</th>
                            <th>Passenger</th>
                            <th>Phone Number</th>
                            <th>From</th>
                            <th>To</th>
                            <th>Price</th>
                            <th>Seats</th>
                            <th>Amount Paid</th>
                            <th>Payment Status</th>
                            <th>Passenger Status</th>
                            <th>Remove Passenger</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($passengers as $passenger): ?>
                            <tr>
                                <td><?= esc($passenger['currentStop']) ?></td>
                                <td><?= esc($passenger['passenger']) ?></td>
                                <td><?= esc($passenger['phone_no']) ?></td>
                                <td><?= esc($passenger['from']) ?></td>
                                <td><?= esc($passenger['to']) ?></td>
                                <td><?= esc($passenger['price']) ?></td>
                                <td><?= esc($passenger['num_seats']) ?></td>
                                <td><?= esc($passenger['amount_paid']) ?></td>
                                <td><?= esc($passenger['payment_status']) ?></td>
                                <td><?= esc($passenger['passenger_status']) ?></td>
                                <td>
                                    <a href="<?= site_url('dashboard/cancel-booking/' . esc($passenger['booking_id'])) ?>" class="btn btn-danger">
                                        Cancel Booking
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-warning text-center mt-4">
            <strong>No passengers available for this stop.</strong>
        </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
