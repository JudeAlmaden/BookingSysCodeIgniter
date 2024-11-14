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

<div class="container-fluid border border-light border-1 shadow-sm rounded-3 p-5 m-0 ">
<table class="table table-striped table-hoverable w-100">
    <thead>
        <tr class="bg-dark text-light">
            <th scope="col">#</th>
            <th scope="col">Booking Name</th>
            <th scope="col">Status</th>
            <th scope="col">Controls</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($bookings)): ?>
            <?php foreach ($bookings as $booking): ?>
                <tr>
                    <td><?= $booking['id'] ?></td>
                    <td><?= $booking['booking_name'] ?></td>
                    <td><?= $booking['status'] ?></td>
                    <td>
                        <div class="d-flex justify-content-around">
                            <!-- Add your controls here (e.g., View, Edit, Delete) -->
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="4">No pending bookings found</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>
<nav aria-label="Page navigation example">
      <ul class="pagination justify-content-end">
          <li class="page-item <?= ($currentPage == 1) ? 'disabled' : '' ?>">
              <a class="page-link" href="<?= base_url("dashboard/bookings/" . ($currentPage - 1)) ?>" tabindex="-1" aria-disabled="true">Previous</a>
          </li>

          <?php
          $totalPages = ceil($totalBookings / $perPage);
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


<?= $this->endSection()?>