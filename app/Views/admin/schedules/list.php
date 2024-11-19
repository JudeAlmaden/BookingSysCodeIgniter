<?= $this->extend("layouts/admin") ?>

<?= $this->section("body") ?>


<div class="container">
  <div class="row align-items-center">
    <div class="col">
      <h1>Schedules</h1>
    </div>
    <div class="col d-flex justify-content-end">
    <form method="GET" action="<?= site_url('dashboard/schedules/create/') ?>">
        <button type="submit" class="btn btn-primary btn-lg p-3" data-bs-toggle="modal" data-bs-target="#create">
            <i class="bi bi-plus-circle me-2"></i> <span class="fw-bold">Schedule a Trip</span>
        </button>
    </form>
    </div>
  </div>
</div>

<hr>

<div class="container-fluid border border-light border-1 shadow-sm rounded-3 p-5 m-0 ">
  <table class="table table-striped table-hoverable w-100">
    <thead>
      <tr class="bg-dark text-light">
        <th scope="">#</th>
        <th scope="">Vehicle Name</th>
        <th scope="">Initial Departure</th>
        <th scope="">Final stop</th>
        <th scope="">Status</th>
        <td class="">Actions</td>
      </tr>
    </thead>
    <tbody>
      <?php if(!empty($schedules)): ?>
          <?php foreach($schedules as $schedule): ?>
              <tr>
                <td class="col-auto"><?= $schedule['trip_id'] ?></td>
                <td class="col-auto"><?= $schedule['vehicle_name'] ?></td>
                <td class="col-auto">
                    <?= (new DateTime($schedule['departure']))->format('F j, Y h:i A') ?>
                </td>
                <td class="col-auto">
                    <?= (new DateTime($schedule['arrival']))->format('F j, Y h:i A') ?>
                </td>
                <td class="col-auto">
                    <?=$schedule['trip_status']?>
                </td>
                <td class="col-auto">
                    <a href="<?= base_url('dashboard/schedule/view/' . $schedule['trip_id']) ?>" class="btn btn-primary btn-sm">
                        <i>View</i>
                    </a>
                
                    <!-- Check if the status is "Not Available" and disable buttons -->
                    <?php if ($schedule['trip_status'] == 'Available'): ?>
                        <a href="<?= base_url('/dashboard/schedules/complete/' . $schedule['trip_id']) ?>" class="btn btn-success btn-sm">
                            Mark as Completed
                        </a>

                        <a href="<?= base_url('/dashboard/schedules/cancel/' . $schedule['trip_id']) ?>" class="btn btn-danger btn-sm">
                            Cancel
                        </a>
                    <?php else: ?>
                        <!-- Disabled Mark as Completed and Cancel buttons -->
                        <button class="btn btn-success btn-sm" disabled>Mark as Completed</button>
                        <button class="btn btn-danger btn-sm" disabled>Cancel</button>
                    <?php endif; ?>
                </td>

              </tr>
          <?php endforeach; ?>
      <?php else: ?>
        <tr>
            <td colspan="12">Nothing has been scheduled yet :(</td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>
  <nav aria-label="Page navigation example">
      <ul class="pagination justify-content-end">
          <li class="page-item <?= ($currentPage == 1) ? 'disabled' : '' ?>">
              <a class="page-link" href="<?= site_url("dashboard/routes/" . ($currentPage - 1)) ?>" tabindex="-1" aria-disabled="true">Previous</a>
          </li>

          <?php
          $totalPages = ceil($totalRoutes / $perPage);
          for ($i = 1; $i <= $totalPages; $i++): ?>
              <li class="page-item <?= ($currentPage == $i) ? 'active' : '' ?>">
                  <a class="page-link" href="<?= site_url("dashboard/routes/$i") ?>"><?= $i ?></a>
              </li>
          <?php endfor; ?>

          <li class="page-item <?= ($currentPage == $totalPages) ? 'disabled' : '' ?>">
              <a class="page-link" href="<?= site_url("dashboard/routes/" . ($currentPage + 1)) ?>">Next</a>
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