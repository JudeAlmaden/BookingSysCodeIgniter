<?= $this->extend("layouts/admin") ?>
<?= $this->section("body") ?>

<div class="container">
  <div class="row align-items-center">
    <div class="col">
      <h1>Vehicles</h1>
    </div>
    <div class="col d-flex justify-content-end">
      <button type="button" class="btn btn-primary btn-lg p-3" data-bs-toggle="modal" data-bs-target="#create">
        <i class="bi bi-plus-circle me-2"></i> <span class="fw-bold">Add Vehicle</span>
      </button>
    </div>
  </div>
  <!-- Search Bar -->
  <div class="row mt-3">
    <div class="col-12">
      <form action="" method="GET">
        <div class="form-group d-flex align-items-center">
          <input type="text" class="form-control" id="search" name="search" placeholder="Search for a vehicle..." required>
          <button type="submit" class="btn btn-primary ms-2">
            <i class="bi bi-search"></i> Search
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<hr>

<!-- Modal -->
<div class="modal fade" id="create" tabindex="-1" aria-hidden="true" ata-bs-target="#staticBackdrop">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header text-center">
        <h4 class="modal-title w-auto" id="modal-title">Create a new route</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <form id="operation-vehicle" class="position-relative" action="<?= site_url('dashboard/vehicles') ?>" method="POST">
          <input type="hidden" name="id" id="vehicle-id">
          <div class="form-group">
            <label for="vehicleID">Vehicle Info</label>
            <input type="text" class="form-control" id="vehicleID" placeholder="Vehicle tag or ID" required name="tag">
            <small id="tag-help" class="form-text text-muted">Example: Lucena Lines No. 1238</small>
            <input type="number" class="form-control" id="numberOfSeats" placeholder="Max Seats" required name="number_seats">
            <small class="form-text text-muted">Example: 26</small>
            <input type="text" class="form-control" id="vehicleType" placeholder="Type of vehicle" required name="type">
            <small class="form-text text-muted">Example: Bus, Taxi, Ferry Boat</small>
            <br>
            <label for="vehicleDescription">Vehicle Description</label>
            <textarea class="form-control" id="vehicleDescription" rows="3" required name="description"></textarea>
          </div>

          <hr><br>

          <div class="form-group">
            <label for="baseFare">Fare information</label>
            <input type="number" class="form-control mb-2" id="baseFare" placeholder="Base Fare" required name="base_fare">
            <input type="number" class="form-control" id="perKilometer" placeholder="Per Kilometer" required name="per_kilometer">
          </div>
        </form>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary" form="operation-vehicle">Save</button>
      </div>
    </div>
  </div>
</div>

<!-- Vehicle Table -->
<table class="table table-striped table-hoverable w-100">
  <thead>
    <tr class="bg-dark text-light">
      <th>#</th>
      <th>Tag</th>
      <th>Type</th>
      <th>Description</th>
      <th>Number of Seats</th>
      <th>Base Fare</th>
      <th>Per kilometer</th>
      <th>Current Status</th>
      <th>Actions</th>
    </tr>
  </thead>
  <tbody>
    <?php if (!empty($vehicles)): ?>
      <?php foreach ($vehicles as $vehicle): ?>
        <tr>
          <td><?= $vehicle['id'] ?></td>
          <td><?= $vehicle['tag'] ?></td>
          <td><?= $vehicle['type'] ?></td>
          <td><?= $vehicle['description'] ?></td>
          <td><?= $vehicle['number_seats'] ?></td>
          <td><?= $vehicle['base_fare'] ?></td>
          <td><?= $vehicle['per_kilometer'] ?></td>
          <td><?= $vehicle['status'] ?></td>
          <td>
            <a href="<?= site_url('dashboard/vehicles/toggle/' . $vehicle['id']) ?>"
              class="btn btn-warning col-5 mb-2 col-md-12"
              style="font-size:12px"
              onclick="return confirm('Are you sure you want to change the status of this vehicle? This will cancel all bookings and trips associated');">
              <i class="bi bi-pencil-square"></i>Toggle
            </a>
            <button class="btn btn-primary col-5 mb-2 col-md-12"
              style="font-size:12px"
              data-id="<?= $vehicle['id'] ?>"
              data-tag="<?= $vehicle['tag'] ?>"
              data-type="<?= $vehicle['type'] ?>"
              data-description="<?= $vehicle['description'] ?>"
              data-number_seats="<?= $vehicle['number_seats'] ?>"
              data-base_fare="<?= $vehicle['base_fare'] ?>"
              data-per_kilometer="<?= $vehicle['per_kilometer'] ?>">
              <i class="bi bi-pencil-square"></i> Edit
            </button>
          </td>
        </tr>
      <?php endforeach; ?>
    <?php else: ?>
      <tr>
        <td colspan="9">No vehicles</td>
      </tr>
    <?php endif; ?>
  </tbody>
</table>

<script>
  // JavaScript to handle Edit button clicks
  document.querySelectorAll('.edit-btn').forEach(button => {
    button.addEventListener('click', () => {
      // Populate modal fields with the vehicle data
      document.getElementById('vehicle-id').value = button.dataset.id;
      document.getElementById('vehicleID').value = button.dataset.tag;
      document.getElementById('vehicleType').value = button.dataset.type;
      document.getElementById('vehicleDescription').value = button.dataset.description;
      document.getElementById('numberOfSeats').value = button.dataset.number_seats;
      document.getElementById('baseFare').value = button.dataset.base_fare;
      document.getElementById('perKilometer').value = button.dataset.per_kilometer;

      // Update modal title and form action for editing
      document.getElementById('modal-title').innerText = 'Edit Vehicle';
      document.getElementById('operation-vehicle').action = "<?= site_url('dashboard/vehicles/update/') ?>";

      // Show modal
      new bootstrap.Modal(document.getElementById('create')).show();
    });
  });

  // Clear modal fields when opened for adding a new vehicle
  document.querySelector('[data-bs-target="#create"]').addEventListener('click', () => {
    document.getElementById('operation-vehicle').reset();
    document.getElementById('modal-title').innerText = 'Create a new route';
    document.getElementById('operation-vehicle').action = "<?= site_url('dashboard/vehicles') ?>";
  });
</script>


<nav aria-label="Page navigation example">
  <ul class="pagination justify-content-end">
    <li class="page-item <?= ($currentPage == 1) ? 'disabled' : '' ?>">
      <a class="page-link" href="<?= site_url("dashboard/routes/" . ($currentPage - 1)) ?>" tabindex="-1" aria-disabled="true">Previous</a>
    </li>

    <?php
    $totalPages = ceil($resultCount / $perPage);
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
<?= $this->endSection() ?>