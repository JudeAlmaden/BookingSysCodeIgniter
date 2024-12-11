<?= $this->extend("layouts/customer") ?>

<?= $this->section("body") ?>
<div class="container mt-5">
    <h1 class="mb-4">Your Bookings</h1>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger">
            <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($bookings) && is_array($bookings)): ?>
        <table class="table table-bordered table-striped">
            <thead class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>Trip</th>
                    <th>Distance</th>
                    <th>Seats</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th>Payment Status</th>
                    <th>From</th>
                    <th>To</th>
                    <th>Booked At</th>
                    <th>Payment</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($bookings as $booking): ?>
                    <tr>
                        <td><?= $booking['id'] ?></td>
                        <td><?= $booking['trip_id'] ?></td>
                        <td><?= $booking['distance'] ?> km</td>
                        <td><?= $booking['num_seats'] ?></td>
                        <td>₱<?= number_format($booking['price'], 2) ?></td>
                        <td>
                            <?php if ($booking['status'] === 'Pending'): ?>
                                <span class="badge bg-warning text-dark col-12">Pending</span>
                            <?php elseif (($booking['status'] === 'Approved') or  ($booking['status'] === 'Confirmed')): ?>
                                <span class="badge bg-success col-12">Approved</span>
                            <?php else: ?>
                                <span class="badge bg-danger col-12">Cancelled</span>
                            <?php endif; ?>
                        </td>
                        <td><?= !empty($booking['payment_status']) ? esc($booking['payment_status']) : 'None' ?></td>
                        <td><?= $booking['from'] ?></td>
                        <td><?= $booking['to'] ?></td>
                        <td><?= date('F j, Y, g:i a', strtotime($booking['created_at'])) ?></td>
                        <td>
                            <?php if (!empty($booking['payment_status'])): ?>
                                <a href="<?= base_url('payment/view/' . $booking['id']) ?>" class="btn btn-primary btn-sm col-12">View Payment</a>
                            <?php elseif ($booking['status'] === 'Approved' && empty($booking['payment_status'])): ?>
                                <a href="<?= base_url('payment/checkout/' . $booking['id']) ?>" class="btn btn-primary btn-sm col-12">Make Payment</a>
                            <?php elseif ($booking['payment_status'] === 'Pending'): ?>
                                <a href="<?= base_url('payment/view/' . $booking['id']) ?>" class="btn btn-primary btn-sm col-12">Waiting for your payment to be approved</a>
                            <?php else: ?>
                                <span class="text-muted">No payment action required</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    <?php else: ?>
        <div class="alert alert-info">You have no bookings yet.</div>
    <?php endif; ?>

</div>

<?= $this->endSection() ?>