<?= $this->extend('layouts/customer') ?>

<?= $this->section('body') ?>

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
                                <?php if ($booking['status'] === 'Pending' ): ?>
                                    <span class="badge bg-warning text-dark col-12">Pending</span>
                                <?php elseif (($booking['status'] === 'Approved') OR  ($booking['status'] === 'Confirmed')): ?>
                                    <span class="badge bg-success col-12">Approved</span>
                                <?php else: ?>
                                    <span class="badge bg-danger col-12">Cancelled</span>
                                <?php endif; ?>
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
                                <?php if ($payment && $payment['status'] === 'Approved'): ?>
                                    <span class="badge bg-primary col-12">Payment Approved</span>
                                <?php elseif ($payment && $payment['status'] === 'Pending'): ?>
                                    <span class="badge bg-warning col-12">Waiting for Confirmation</span>
                                <?php elseif ($payment && $payment['status'] === 'Confirmed'): ?>
                                    <span class="badge bg-success col-12">Your booking has been confirmed</span>
                                <?php elseif (($payment && $payment['status'] === 'Confirmed')): ?>
                                    <span class="badge bg-success col-12">Your booking has been confirmed</span>
                                <?php else: ?>
                                    <span class="badge bg-danger col-12">Payment Pending</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    </table>

                    <?php if ($payment && $payment['status'] === 'Pending'): ?>
                        <div class="alert alert-warning">Your payment is awaiting confirmation.</div>
                    <?php elseif ($payment && $payment['status'] === 'Approved'): ?>
                        <div class="alert alert-success">Your payment has been confirmed.</div>
                    <?php elseif ($payment && $payment['status'] === 'Approved'): ?>
                        <div class="alert alert-success">Your payment has been confirmed.</div>
                    <?php elseif (($payment && $payment['status'] === 'Confirmed') && ($booking['status'] === 'Cancelled')): ?>
                        <span class="badge bg-danger col-12">Schedule trip has been cancelled. Show this to claim a refund from the nearest terminal.</span>
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
                    </div>
                </div>
            <?php else: ?>
                <div class="alert alert-danger">
                    <p>No payment proof uploaded yet. Send it to GCASH: 09919263124</p>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>


<?= $this->endSection() ?>
