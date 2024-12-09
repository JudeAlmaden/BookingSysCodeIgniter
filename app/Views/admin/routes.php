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
        <form id="create-route" class="position-relative" action="<?= site_url('dashboard/routes') ?>" method="POST">
          <div class="col-12 text-center ps-5 pe-3">
            <label for="InitialLocation" class="form-label"><b class="h5">Route Name</b></label>
            <input type="text" class="form-control border-top-0 border-start-0 border-end-0  mb-5 p-0 " id="InitialLocation" placeholder="Ex: PITX - Lucena Grand" name="routeName" required> 
          </div>
          
          <div id="points-container" class="position-relative ps-5 pe-3">
          <div class="position-absolute h-100  rounded rounded-3" style="width: 10px; margin-left: -20px; background-image: linear-gradient(120deg, #84fab0 0%, #8fd3f4 100%);"></div>
            <div class="border border-light border-1 shadow-sm rounded-3 p-3">
              <div class="row">
                  <div class="col-8">
                  <label for="InitialLocation" class="form-label"><b>Initial Location</b></label>
                  <input type="text" class="form-control border-top-0 border-start-0 border-end-0 m-0 p-0" id="InitialLocation" placeholder="Point A" name="initial" required> 
                  </div>
              </div>
            </div>

            <div class="fs-2 text-center ellipse">&#8942;</div>

            <div class="border border-light border-1 shadow-sm rounded-3 p-3" id="route-final">
                  <div class="row">
                      <div class="col-12 row">
                          <label for="FinalDestination" class="form-label"><b>Final Destination</b></label>
                          <div class="col-8">
                              <input type="text" class="form-control border-top-0 border-start-0 border-end-0 m-0 p-0 w-" id="FinalDestination" placeholder="Point B" name="stations[]" required>
                          </div>
                          <div class="col-3">
                              <input type="number" class="form-control border-top-0 border-start-0 border-end-0 m-0 p-0 tooltiptext" id="DistanceToFinal" placeholder="Kilometers" name="distance[]" required>

                          </div>
                          <span class="d-inline-block col-1" tabindex="0" data-toggle="tooltip" title="Distance From Previous Point">
                            <i class="fa fa-question-circle "></i>
                          </span>
                      </div>
                  </div>
              </div>
            </div>
        </form>
      </div>

      <div class="d-flex justify-content-center p-3">
        <button class="btn btn-primary" id="add-new-stop">Add stop</button>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary" form="create-route">Create</button>
      </div>
    </div>
  </div>
</div>
<div class="container">
  <div class="row align-items-center">
    <div class="col">
      <h1>Routes</h1>
    </div>
    <div class="col d-flex justify-content-end">
      <button type="button" class="btn btn-primary btn-lg p-3" data-bs-toggle="modal" data-bs-target="#create">
        <i class="bi bi-plus-circle me-2"></i> <span class="fw-bold">Create a new Route</span>
      </button>
    </div>
  </div>

  <!-- Search Bar -->
  <div class="row mt-3">
    <div class="col-12">
      <form action="" method="GET">
        <div class="form-group d-flex align-items-center">
          <input type="text" class="form-control" id="search" name="search" placeholder="Search for a route..." required>
          <button type="submit" class="btn btn-primary ms-2">
            <i class="bi bi-search"></i> Search
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<hr>

<div class="container-fluid border border-light border-1 shadow-sm rounded-3 p-5 m-0 ">
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


<script>
  let post = 1;
  let letter = 'A';

  document.getElementById('add-new-stop').addEventListener('click', function() {
    // Create the new div element
    const newStop = document.createElement('div');
    newStop.className = 'stop-container border border-light border-1 shadow-sm rounded-3 p-3';

    newStop.innerHTML = `
        <div class="row">
            <div class="col-12 row">
                <div class="w-100 d-flex justify-content-between align-items-center mb-3">
                    <label class="form-label"><b>Terminal/Stop</b></label>
                    <button type="button" class="btn-close" aria-label="Close" onclick="deleteStop(this)"></button>
                </div>
                <div class="col-8">
                    <input type="text" class="form-control border-top-0 border-start-0 border-end-0 m-0 p-0" placeholder=" Point ${post}" name="stations[]" required>
                </div>
                <div class="col-4">
                    <span class="d-inline-block" tabindex="0" data-toggle="tooltip" title="Distance From Previous Point">
                        <input type="number" class="form-control border-top-0 border-start-0 border-end-0 m-0 p-0 tooltiptext"  placeholder="Kilometers" name="distance[]" required>
                    </span>
                </div>
            </div>
        </div>
    `;

    const ellipse = document.createElement('div');
    ellipse.className = 'fs-2 text-center ellipse';
    ellipse.innerHTML = '&#8942;'
    // Add the inner HTML structure

    document.getElementById('points-container').insertBefore(newStop,  document.getElementById('route-final'));
    document.getElementById('points-container').insertBefore(ellipse,  document.getElementById('route-final'));

    post++;

    setPlaceholders();
});


function deleteStop(button) {

    //Items to delete
    const container = button.closest('.stop-container'); 
    const ellpise = container.nextElementSibling;

    if (container) {
      container.remove();
    }

    ellpise.remove();
    post--;    

    setPlaceholders();
}

function setPlaceholders(){
      //Fix the placeholders
    let all =  document.getElementById('points-container')
    let containers = all.querySelectorAll(':scope > div')
    let index= 0;

    containers.forEach((container) => {
    // Get the first input field within all child elements of the current sibling
    const firstInput = container.querySelector('input');
    const label = getLabel(index);

    if (firstInput) {
        // Change its placeholder to "Point {index}"
        firstInput.placeholder = `Point ${label}`;
        index++;
    }
});
}


function getLabel(index) {
    let label = '';
    let num = index;

    while (num >= 0) {
        // Calculate the letter and update the label
        label = String.fromCharCode((num % 26) + 65) + label;
        num = Math.floor(num / 26) - 1; // Move to the next "digit" in base-26
    }
    
    return label;
}


</script>
<?= $this->endSection()?>