<?= $this->extend('layouts/admin') ?>

<?= $this->section('body') ?>

<div class="container mt-4">
    <h2 class="mb-4">Payment Requests</h2>

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

    <!-- Payment Requests Table -->
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5>Pending Payments</h5>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th class="text-center">Payment ID</th>
                        <th class="text-center">Booking ID</th>
                        <th class="text-center">Amount</th>
                        <th class="text-center">Booking Status</th>
                        <th class="text-center">Payment Status</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($payments) > 0): ?>
                        <?php foreach ($payments as $payment): ?>
                            <tr>
                                <td class="text-center"><?= $payment['id'] ?></td>
                                <td class="text-center"><?= $payment['booking_id'] ?></td>
                                <td class="text-center">₱<?= number_format($payment['amount'], 2) ?></td>
                                <td class="text-center">
                                    <?php if ($payment['booking_status'] === 'Approved'): ?>
                                        <span class="badge bg-success col-12">Approved</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning col-12">Pending</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($payment['status'] === 'Approved'): ?>
                                        <span class="badge bg-primary col-12">Payment Approved</span>
                                    <?php elseif ($payment['status'] === 'Pending'): ?>
                                        <span class="badge bg-warning col-12">Pending</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger col-12">Payment Denied</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                <div class="d-flex justify-content-center gap-1">
                                        <!-- Accept Payment Button -->
                                        <form action="<?= base_url('payment/approve/' . $payment['id']) ?>" method="POST" class="d-inline col-auto">
                                            <button type="submit" class="btn btn-success" style="font-size:12px">
                                                <i class="fas fa-check"></i> Accept
                                            </button>
                                        </form>

                                        <!-- Deny Payment Button -->
                                        <form action="<?= base_url('payment/reject/' . $payment['id']) ?>" method="POST" class="d-inline col-auto">
                                            <button type="submit" class="btn btn-danger w-100" style="font-size:12px">
                                                <i class="fas fa-times"></i> Deny
                                            </button>
                                        </form>

                                        <!-- View Payment Details Button -->
                                        <form action="<?= base_url('dashboard/payment/view/' . $payment['booking_id']) ?>" method="GET" class="d-inline col-auto">
                                            <button type="submit" class="btn btn-primary me-2 w-100" style="font-size:12px">
                                                <i class="fas fa-eye"></i> View 
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center">No pending payments found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <!-- Pagination Links -->
            <div class="d-flex justify-content-between">
                <div>
                    <span>Showing <?= ($currentPage - 1) * $perPage + 1 ?> to <?= min($currentPage * $perPage, $resultCount) ?> of <?= $resultCount ?> payments</span>
                </div>
                <div>
                    <?= $pager->links() ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>