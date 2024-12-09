<?= $this->extend('layouts/admin') ?>

<?= $this->section('body') ?>

<div class="container mt-4">
    <h2 class="mb-4">Refund Requests</h2>

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
            <h5>Pending Refunds</h5>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th class="text-center">Payment ID</th>
                        <th class="text-center">Booking ID</th>
                        <th class="text-center">Amount</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($payments) > 0): ?>
                        <?php foreach ($payments as $payment): ?>
                            <tr class="text-center">
                                <td><?= $payment['id'] ?></td>
                                <td><?= $payment['booking_id'] ?></td>
                                <td>₱<?= number_format($payment['amount'], 2) ?></td>
                                <td>
                                    <div class="d-flex justify-content-start">
                                        <form action="<?= base_url('dashboard/payment/view/' . $payment['booking_id']) ?>" method="GET" class="d-inline col-12">
                                            <button type="submit" class="btn btn-primary me-2 w-100">
                                                <i class="fas fa-eye"></i> View Details
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
