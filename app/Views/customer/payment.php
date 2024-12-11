<?= $this->extend('layouts/customer') ?>

<?= $this->section('body') ?>

<div class="container">
    <h2 class="text-center">Payment Checkout</h2>

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

    <div class="row justify-content-center">
        <div class="col-md-8">
            <h4>Booking Details</h4>
            <table class="table table-striped">
                <tr>
                    <td><strong>Booking ID:</strong></td>
                    <td><?= $booking['id'] ?></td>
                </tr>
                <tr>
                    <td><strong>Trip ID:</strong></td>
                    <td><?= $booking['trip_id'] ?></td>
                </tr>
                <tr>
                    <td><strong>Seats:</strong></td>
                    <td><?= $booking['num_seats'] ?></td>
                </tr>
                <tr>
                    <td><strong>Price:</strong></td>
                    <td>₱<?= number_format($booking['price'], 2) ?></td>
                </tr>
                <tr>
                    <td><strong>Status:</strong></td>
                    <td>
                        <?php if ($booking['status'] === 'Approved'): ?>
                            <span class="badge bg-success">Approved</span>
                        <?php else: ?>
                            <span class="badge bg-warning">Pending</span>
                        <?php endif; ?>
                    </td>
                </tr>
            </table>

            <h4>Payment Details</h4>
            <table class="table table-striped">
                <tr>
                    <td><strong>Amount:</strong></td>
                    <td>₱<?= number_format($booking['price'], 2) ?></td>
                </tr>
                <tr>
                    <td><strong>Status:</strong></td>
                    <td>
                        <?php if ($payment && $payment['status'] === 'Approved'): ?>
                            <span class="badge bg-primary">Payment Approved</span>
                        <?php else: ?>
                            <span class="badge bg-warning">Pending</span>
                        <?php endif; ?>
                    </td>
                </tr>
            </table>

            <?php if ($payment && $payment['status'] === 'Approved'): ?>
                <div class="alert alert-success">Your payment has been approved.</div>
            <?php elseif ($booking['status'] === 'Approved'): ?>
                <form action="<?= base_url('payment/processPayment/' . $booking['id']) ?>" method="post" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="transaction_id" class="form-label">Transaction ID</label>
                        <input type="text" class="form-control" id="transaction_id" name="transaction_id" required>
                    </div>

                    <div class="mb-3">
                        <label for="payment_proof" class="form-label">Payment Proof (Image)</label>
                        <input type="file" class="form-control" id="payment_proof" name="payment_proof" accept="image/*" required>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg">Pay Now</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<?= $this->endSection() ?>