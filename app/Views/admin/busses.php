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
        <form id="create-route" class="position-relative" action="<?= site_url('dashboard/busses') ?>" method="POST">
          <div class="form-group">
            <label for="exampleInputEmail1">Email address</label>
            <input type="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Enter email">
            <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small>
          </div>
          <div class="form-group">
            <label for="exampleInputPassword1">Password</label>
            <input type="password" class="form-control" id="exampleInputPassword1" placeholder="Password">
          </div>
          <div class="form-check">
            <input type="checkbox" class="form-check-input" id="exampleCheck1">
            <label class="form-check-label" for="exampleCheck1">Check me out</label>
          </div>
          <button type="submit" class="btn btn-primary">Submit</button>
        </form>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary" form="create-route">Add</button>
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
        <th scope="col-1">#</th>
        <th scope="col-9">Route Name</th>
        <th scope="col-2">Controls</th>
      </tr>
    </thead>
    <tbody>
      <?php if(!empty($routes)): ?>
          <?php foreach($routes as $route): ?>
              <tr>
                  <td class="col-1"><?= $route['id'] ?></td>
                  <td class="col-9"><?= $route['name'] ?></td>
                  <td class="col-2">
                    <div class="row">
                      <div class="col-6">
                        <form action="<?= site_url('dashboard/routes/view/' . $route['id']) ?>" method="GET"><button type="submit" class="btn btn-primary w-100" >View</input></form>
                      </div>
                      <div class="col-6">
                        <form action="<?= site_url('dashboard/routes/delete/' . $route['id']) ?>" method="POST"><button type="submit" class="btn btn-danger w-100">Delete</input></form>
                      </div>
                    </div>
                  </td>
              </tr>
          <?php endforeach; ?>
      <?php else: ?>
        <tr>
            <td colspan="3">No routes found</td>
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
          $totalPages = ceil($totalRoutes / $routesPerPage);
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