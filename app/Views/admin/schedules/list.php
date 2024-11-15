<?= $this->extend("layouts/admin") ?>

<?= $this->section("body") ?>


<div class="container">
  <div class="row align-items-center">
    <div class="col">
      <h1>Routes</h1>
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
        <td class="">View Full details</td>
      </tr>
    </thead>
    <tbody>
      <?php if(!empty($schedules)): ?>
          <?php foreach($schedules as $schedule): ?>
              <tr>
                  <td class="col-auto"><?= $schedule['trip_id'] ?></td>
                  <td class="col-auto"><?= $schedule['vehicle_name'] ?></td>
                  <td class="col-auto"><?= $schedule['departure']?></td>
                  <td class="col-auto"><?= $schedule['arrival'] ?></td>
                  <td class="col-auto">
                    <a href="<?= base_url('dashboard/schedule/view/' . $schedule['trip_id']) ?>" class="btn btn-primary btn-sm">
                        <i>View</i>
                    </a>
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