<?= $this->extend("layouts/admin") ?>

<?= $this->section("body") ?>

<!-- Modal -->
<div  class="modal fade" id="create" tabindex="-1"  aria-hidden="true" ata-bs-target="#staticBackdrop">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header text-center ">
        <h4 class="modal-title w-auto">Create a new route</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <form id="add-vehicle" class="position-relative" action="<?= site_url('dashboard/vehicles') ?>" method="POST">
          <div class="form-group">
            <label for="vehicleID">Vehicle Info</label>
            <input type="text" class="form-control" id="vehicleID" placeholder="Vehicle tag or ID" required name="tag">
            <small id="tag" class="form-text text-muted">Example: Lucena Lines No. 1238</small>
            <input type="number" class="form-control" id="numberOfSeats" placeholder="Max Seats" required name="number_seats">
            <small id="tag" class="form-text text-muted">Example: 26</small>
            <input type="text" class="form-control" id="vehicleType" placeholder="Type of vehicle" required name="type">
            <small id="tag" class="form-text text-muted">Example: Bus, Taxi, Ferry Boat</small>
            <br>
            <label for="vehivehicleDescriptioncleID">Vehicle Description</label>
            <textarea class="form-control" id="vehicleDescription" rows="3" required name="description"></textarea>
          </div>

          <hr>

          <div class="form-group">
            <label for="vehicleID">Fare information</label>
            <input type="number" class="form-control mb-2" id="baseFare" placeholder="Base Fare"required  name="base_fare">
            <input type="number" class="form-control" id="baseFare" placeholder="Per Kilometer" required name="per_kilometer">
          </div>
        </form>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary" form="add-vehicle">Add</button>
      </div>
    </div>
  </div>
</div>

<div class="container">
  <div class="row align-items-center">
    <div class="col">
      <h1>Your Vehicles</h1>
    </div>
  </div>
</div>

<hr>

<div class="container-fluid border border-light border-1 shadow-sm rounded-3 p-5 m-0 ">
  <button type="button" class="btn btn-primary p-2 mb-3" data-bs-toggle="modal" data-bs-target="#create">
    Add Vehicle
  </button>
  <!-- Button trigger modal -->
  <table class="table table-striped table-hoverable w-100">
    <thead>
      <tr class="bg-dark text-light">
        <th >#</th>
        <th >Tag</th>
        <th >Description</th>
        <th >Description</th>
        <th >Number of Seats</th>
        <th >Base Fare</th>
        <th >Per kilimeter</th>
        <th >Controls</th>
      </tr>
    </thead>
    <tbody>
      <?php if(!empty($vehicles)): ?>
          <?php foreach($vehicles as $vehicle): ?>
              <tr>
                  <td ><?= $vehicle['id'] ?></td>
                  <td ><?= $vehicle['tag'] ?></td>
                  <td ><?= $vehicle['type'] ?></td>
                  <td ><?= $vehicle['description'] ?></td>
                  <td ><?= $vehicle['number_seats'] ?></td>
                  <td ><?= $vehicle['base_fare'] ?></td>
                  <td ><?= $vehicle['per_kilometer'] ?></td>
                  <td >Edit</td>
              </tr>
          <?php endforeach; ?>
      <?php else: ?>
        <tr>
            <td colspan="3">No vehicles</td>
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