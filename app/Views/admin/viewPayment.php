<?= $this->extend('layouts/admin') ?>

<?= $this->section('body') ?>

<a href="javascript:void(0);" onclick="window.history.back();" class="btn btn-secondary mb-3">
    <i class="bi bi-arrow-left"></i> Back
</a>

<div class="container mt-4">
    <h2 class="mb-4">Payment Confirmation</h2>

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

    <div class="row">
        <div class="col-md-8 offset-md-2">
            <!-- Booking Details Card -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5>Booking Details</h5>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <td><strong>Booking ID:</strong></td>
                            <td><?= $booking['id'] ?></td>
                        </tr>
                        <tr>
                            <td><strong>Trip ID:</strong></td>
                            <td><?= $booking['trip_id'] ?></td>
                        </tr>
                        <tr>
                            <td><strong>From</strong></td>
                            <td><?= $booking['from'] ?></td>
                        </tr>
                        <tr>
                            <td><strong>To</strong></td>
                            <td><?= $booking['to'] ?></td>
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
                                <?php echo ($booking['status']) ?>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Payment Status Card -->
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h5>Payment Status</h5>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <td><strong>Amount:</strong></td>
                            <td>₱<?= number_format($booking['price'], 2) ?></td>
                        </tr>
                        <tr>
                            <td><strong>Status:</strong></td>
                            <td>
                                <?php echo isset($payment['status']) ? $payment['status'] : 'No Payment Yet'; ?>
                            </td>
                        </tr>
                    </table>

                    <?php if ($payment && $payment['status'] === 'Pending'): ?>
                        <div class="alert alert-warning">Your payment is awaiting confirmation.</div>
                    <?php elseif ($payment && $payment['status'] === 'Approved'): ?>
                        <div class="alert alert-success">Your payment has been confirmed.</div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Payment Proof Card -->
            <?php if ($payment && $payment['payment_proof']): ?>
                <div class="card mb-4">
                    <div class="card-header bg-success text-white">
                        <h5>Payment Proof</h5>
                    </div>
                    <div class="card-body">
                        <p>Your payment proof has been submitted.</p>

                        <!-- Convert BLOB to base64 for displaying as an image -->
                        <?php
                        // Base64 encode the payment proof BLOB
                        $paymentProof = base64_encode($payment['payment_proof']);

                        // Assuming the file is an image (you can adjust this depending on your image type)
                        echo '<img src="data:image/png;base64,' . $paymentProof . '" alt="Payment Proof" class="img-fluid mb-3" />';
                        ?>

                        <br>
                        <!-- Provide the option to download the proof -->
                        <a href="<?= base_url('payment/downloadProof/' . $payment['id']) ?>" class="btn btn-secondary">
                            <i class="fas fa-download"></i> Download Payment Proof
                        </a>

                        <!-- Admin Actions (Accept/Reject) -->
                        <div class="mt-3">
                            <?php if ($payment['status'] === 'Pending'): ?>
                                <form action="<?= base_url('payment/approve/' . $payment['id']) ?>" method="POST" class="d-inline">
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-check"></i> Accept Payment
                                    </button>
                                </form>
                                <form action="<?= base_url('payment/reject/' . $payment['id']) ?>" method="POST" class="d-inline">
                                    <button type="submit" class="btn btn-danger">
                                        <i class="fas fa-times"></i> Deny Payment
                                    </button>
                                </form>
                            <?php endif; ?>

                            <?php if ($payment['status'] === 'Waiting for refund'): ?>
                                <form action="<?= base_url('refund/complete/' . $payment['id']) ?>" method="POST" class="d-inline">
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-check"></i> Complete Refund
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="alert alert-danger">
                    <p>No payment proof uploaded yet.</p>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>



<?= $this->endSection() ?>