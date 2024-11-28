<?= $this->extend("layouts/customer") ?>

<?= $this->section("body") ?>

<style>
    #banner {
        position: relative;
        height: 60vh;
        overflow: hidden;
    }

    #banner::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-image: url("https://cdn.pixabay.com/video/2022/12/13/142755-780943401_tiny.jpg");
        background-position: center;
        background-repeat: no-repeat;
        background-size: cover;
        filter: blur(5px);
        z-index: 1;
    }

    #banner-content {
        position: relative;
        z-index: 2;
        padding: 20px;
        color: white;
    }

    #banner-content h1{
        background-image: linear-gradient(-20deg, #b721ff 0%, #21d4fd 100%);
        background-clip: text;
        color:transparent;
        font-weight: bold;
        font-size: 5rem;
    }

    #banner-content p{
        background-image: linear-gradient(-225deg, #3D4E81 0%, #5753C9 48%, #6E7FF3 100%);        background-clip: text;
        color:transparent;
        font-weight: bold;
        font-size: 2rem
    }
    
    #form-search{
        margin-top: -50px;
        z-index: 2;
    }

    #content{
        z-index: 2;
        position: relative;
    }

    select:focus {
        outline: none !important;
        box-shadow: none !important;
    }

    select:hover{
        cursor: pointer;
    }
</style>

<div id="banner">
    <div id="banner-content" class="mt-5 p-5 text-center">
        <h1 class="mt-5 pt-5 pb-1">To Wherever you want to go</h1>
        <p>Book a seat now</p>
    </div>
</div>


<div class="container-fluid" id="content">
    <div id="form-search" class="bg-light p-3 mb-5 border rounded shadow-lg container">
        <form class=" p-2 pb-3 row" action="<?= base_url('homepage') ?>" method="POST">
            <!-- Left side -->
            <div class="form-group d-flex flex-column col-5">
                <!-- Top row -->
                <div class="d-flex flex-row text-dark mb-2 bold">
                  <label for="fromLocation" class="col-6 fw-bold h3">From</label>
                  <label for="toLocation" class="col-6 fw-bold h3 ps-3">To</label>
                </div>

                <!-- Bottom row -->
                <div class="d-flex flex-row position-relative" style="height: 40px;">
                  <div class="col-6 rounded-start border pe-4" style="height:50px; background-color:white">
                    <input type="text" placeholder="Location A" class="form-control border-0 h-100 w-100" id="fromLocation" name="fromLocation" style="background-color:transparent" 
                    value="<?php echo isset($from) && !empty($from) ? $from : ''; ?>" required>
                    <div id="suggestionsFromLocation" class="list-group" style="position: absolute; z-index: 1000;"></div>
                  </div>

                  <div class="border rounded-circle bg-white d-flex align-items-center justify-content-center" style="position:absolute; width:50px; height:50px; left:calc(50% - 50px / 2); bottom:calc(-25%)">
                    <i class="fa fa-exchange bg-transparent" aria-hidden="true"></i>
                  </div>

                  <div class="col-6 rounded-end border ps-5" style="height:50px; background-color:white">
                    <input type="text" placeholder="Location A" class="form-control border-0 h-100 w-100" id="toLocation" name="toLocation" style="background-color:transparent" 
                    value="<?php echo isset($to) && !empty($to) ? $to : ''; ?>" required>
                    <div id="suggestionsToLocation" class="list-group" style="position: absolute; z-index: 1000;"></div>
                  </div>
                </div>
            </div>

            <!-- Center side -->
            <div class="col-4 d-flex flex-column text-center border-start border-dark px-3">
                <!-- Center top -->
                <div class="d-flex flex-row text-dark mb-2 bold text-start">
                  <label for="vehicleType" class="col-9 fw-bold h3">Vehicle</label>
                  <label for="seats" class="col-3 fw-bold h3 ">Seats</label>
                </div>

                <!-- Center Bottom -->
                <div class="d-flex flex-row position-relative" style="height: 40px;">
                <div class="col-9 rounded-end border" style="height:50px; background-color:white">
                  <div class="col-12 rounded-end border" style="height:50px; background-color:white">
                    <input type="text" placeholder="Any" class="form-control border-0 h-100 w-100" id="type" name="type" style="background-color:transparent" v
                    value="<?php echo isset($type) && !empty($type) ? $type : 'Any'; ?>" required>
                    <div id="suggestionType" class="list-group" style="position: absolute; z-index: 1000;"></div>
                  </div>
                  </div>
                  <div class="col-3 rounded-end border" style="height:50px; background-color:white">
                    <input type="number" class="form-select border-0 h-100 w-100" name="numSeats"  style="background-color:transparent" 
                    value="<?php echo isset($seats) && !empty($seats) ? $seats : 1; ?>">
                  </div>
                </div>
            </div>

            <!-- Right side -->
            <div class="col-3 d-flex flex-column text-center border-start border-dark  px-3">        
              <div class="d-flex flex-row text-dark mb-2 bold text-start">
                    <label for="vehicleType" class="col-12 fw-bold h3">Date</label>
              </div>
              <div class="d-flex flex-row text-dark mb-2 bold text-start">
                  <input type="date" class="form-select border-0 h-100 w-100" name="date" id="dateInput"
                  value="<?php echo isset($date) && !empty($date) ? $date : ""; ?>">
              </div>
            </div>

            <div class="col-12 mt-3">
              <button class="btn btn-primary h-100 w-100"><i class="fas fa-search"></i> Search</button>
            </div>
        </form>
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

    <!-- Table -->
    <div class="m-5 px-5 pb-5">
      <?php if(!empty($schedules)&& isset($schedules)) : ?>
        <table class="table table-striped table-hoverable w-100 border rouned">
          <thead>
            <tr class="bg-dark text-light">
              <th class="col text-center" scope="">ID</th>
              <th class="col" scope="">Vehicle</th>
              <th class="col" scope="">Pricing(Seat)</th>
              <th class="col text-center" scope="">Schedule </th>              
              <th class="col text-center" scope="">Reserve</th>
            </tr>
          </thead>
          <tbody>
              <?php foreach($schedules as $schedule): ?>
                <?php $totalFare = $schedule['base_fare'] + ($schedule['per_kilometer'] * $schedule['total_distance']);?>
                <tr>
                  <td class="col text-center"><?= $schedule['trip_id'] ?></td>
                  
                  <!-- Vehicle Details -->
                  <td class="col">
                      <div class="d-flex flex-column">
                          <div class="fw-bold"><?= $schedule['vehicle_name'] ?></div>
                          <div class="text-muted"><?= $schedule['type'] ?>, Seats: <?= $schedule['capacity'] ?></div>
                          <div class="text-muted">Available Seats: <?= $schedule['available_seats'] ?></div>
                      </div>
                  </td>
                  
                  <!-- Fare and Distance Information -->
                  <td class="col">
                      <div class="d-flex flex-column">
                        <div>Base Fare: <span class="fw-bold"><?= $schedule['base_fare'] ?></span></div>
                        <div>Per Kilometer: <span class="fw-bold"><?= $schedule['per_kilometer'] ?></span></div>
                        <div>Total Distance: <span class="fw-bold"><?= $schedule['total_distance'] ?> km</span></div>
                        <div>Total Price: <span class="fw-bold"><?= number_format($totalFare, 2) ?> PHP</span></div>
                      </div>
                  </td>
                  
                  <!-- Departure and Arrival Times -->
                  <td class="col">
                      <div class="d-flex flex-column">
                          <div>Departure from <?= $from ?>: 
                              <span class="fw-bold"><?= date("F j, Y g:i A", strtotime($schedule['departure'])) ?></span>
                          </div>
                          <div>Arrival at <?= $to ?>: 
                              <span class="fw-bold"><?= date("F j, Y g:i A", strtotime($schedule['arrival'])) ?></span>
                          </div>
                      </div>
                  </td>
                  
                  <!-- Action Button -->
                  <td class="col text-center">
                  <button type="button" class="btn btn-primary" 
                            data-bs-toggle="modal" 
                            data-bs-target="#modalBook" 
                            data-bs-tag="<?= $schedule['vehicle_name'] ?>"
                            data-bs-trip_id="<?= $schedule['trip_id'] ?>"
                            data-bs-totalFare="<?= number_format($schedule['base_fare'] + ($schedule['per_kilometer'] * $schedule['total_distance']), 2) ?>"
                            data-bs-distance="<?= $schedule['total_distance'] ?>"
                            data-bs-seats="<?= $seats ?>"
                            data-bs-from="<?= $from ?>"
                            data-bs-to="<?= $to ?>"
                            data-bs-available="<?= $schedule['available_seats'] ?>"
                            >
                        Book <?= $schedule['vehicle_name'] ?>
                    </button>
                  </td>
              </tr>
              <?php endforeach; ?>
              <?php elseif (empty($schedule) && isset($schedules)) : ?>
              <tr>
                  <td colspan="12">Nothing has been scheduled yet :(</td>
              </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
</div>      


<!-- Modal -->
<?php if(!empty($schedules)): ?>
<div class="modal fade" id="modalBook" aria-labelledby="modalBookLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalBookLabel">Booking Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="<?= site_url('homepage/book') ?>" id="bookingForm">
                    <!-- Vehicle Name and Trip ID -->
                    <div class="mb-3">
                        <label for="trip_id" class="form-label">Vehicle Name and Trip ID</label>
                        <div class="input-group">
                            <span id="modalVehicleName" class="input-group-text">Vehicle</span>
                            <input type="number" class="form-control" id="trip_id" name="trip_id" readonly>
                        </div>
                    </div>

                    <!-- From and To Locations -->
                    <div class="mb-3">
                        <div class="row">
                            <div class="col-md-6">
                                <label for="modalFrom" class="form-label">From:</label>
                                <input type="text" class="form-control" id="modalFrom" name="from" readonly>
                            </div>
                            <div class="col-md-6">
                                <label for="modalTo" class="form-label">To:</label>
                                <input type="text" class="form-control" id="modalTo" name="to" readonly>
                            </div>
                        </div>
                    </div>

                    <!-- Distance -->
                    <div class="mb-3">
                        <label for="modalDistance" class="form-label">Distance</label>
                        <input type="text" class="form-control" id="modalDistance" readonly>
                    </div>

                    <!-- Fare and Seats Input -->
                    <div class="mb-3">
                        <label for="modalFare" class="form-label">Fare (per Seat)</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="modalFare" readonly>
                            <span class="input-group-text">PHP</span>
                        </div>
                    </div>

                    <!-- Seats Selection -->
                    <div class="mb-3">
                        <label for="modalSeats" class="form-label">Seats</label>
                        <input type="number" class="form-control" id="modalSeats" value="1" min="1" name="seats">
                    </div>

                    <!-- Total Price Display -->
                    <div class="mb-3">
                        <label for="totalPrice" class="form-label">Total Price</label>
                        <div id="totalPrice" class="h5">PHP 0.00</div>
                    </div>

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="submitBookingForm()">Confirm Booking</button>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>


<script>
  let isQuerying = false;
  const today = new Date();
  today.setDate(today.getDate() + 1);
  const tomorrow = today.toISOString().split('T')[0];
  document.getElementById('dateInput').setAttribute('min', tomorrow);

  function submitBookingForm() {
      // Submit the form by its id
      document.getElementById("bookingForm").submit();
  }

  $(document).ready(function() {
    //From Locations

    $('#fromLocation').on('input', function() {
        var query = $(this).val();
        //Clear values
        if (query.length > 0 && !isQuerying) {
          let url = `<?= site_url("stops/search/") ?>${encodeURIComponent(query)}`;

          isQuerying = true;
          $.ajax({
              url: url, 
              type: 'GET',
              dataType: 'json',
              complete:function(){
                isQuerying = false;
              },
              success: function(data) {
                $('#suggestionsFromLocation').empty(); 
                if (data.length > 0) {
                    $.each(data, function(index, location) {
                        $('#suggestionsFromLocation').append(
                            `<a href="#" class="list-group-item-from-location list-group-item list-group-item-action" data-id="${location.id}">${location.name}</a>`
                        );
                    });
                } else {
                    $('#suggestionsFromLocation').append('<div class="list-group-item-from-location list-group-item">No results found</div>');
                }
              },
            error: function() {
              $('#suggestionsFromLocation').empty(); 
                $('#suggestionsFromLocation').append('<div class="list-group-item-from-location list-group-item">Error fetching results</div>');
            }
          });
        } 
    });  
    $(document).on('click', '.list-group-item-from-location', function(e) {
      e.preventDefault();
      
      var fromLocation = $(this).text();
    
      $('#fromLocation').val(fromLocation); 
      $('#suggestionsToLocation').empty();

    });

    //To locations
    $('#toLocation').on('input', function() {
          var query = $(this).val();

          //Clear values
          $('#suggestionsToLocation').empty(); 

            
          if (query.length > 0 && !isQuerying) {

            let url = `<?= site_url("stops/search/") ?>${encodeURIComponent(query)}`;
            isQuerying= true;

            $.ajax({
                url: url, 
                type: 'GET',
                dataType: 'json',
                complete:function(){
                    isQuerying = false;
                },
                success: function(data) {
                  $('#suggestionsFromLocation').empty(); 
                  if (data.length > 0) {
                      $.each(data, function(index, location) {
                          $('#suggestionsToLocation').append(
                              `<a href="#" class="list-group-item-to-location list-group-item list-group-item-action" data-id="${location.id}">${location.name}</a>`
                          );
                      });
                  } else {
                      $('#suggestionsToLocation').append('<div class="list-group-item-to-location list-group-item">No results found</div>');
                  }
                },
              error: function() {
                $('#suggestionsFromLocation').empty(); 
                $('#suggestionsToLocation').append('<div class="list-group-item-to-location list-group-item">Error fetching results</div>');
              }
            });
          } 
      });
    $(document).on('click', '.list-group-item-to-location', function(e) {
      e.preventDefault();
      
      var toLocation = $(this).text();
      
      $('#toLocation').val(toLocation); 
      $('#suggestionsToLocation').empty();

    });


    //Vehicle type
    $('#type').on('input', function() {
          var query = $(this).val();

          //Clear values
          $('#suggestionType').empty(); 

          if (query.length > 0 && !isQuerying) {
            let url = `<?= site_url("vehicles/type/get/") ?>${encodeURIComponent(query)}`;
            isQuerying=true;

            $.ajax({
                url: url, 
                type: 'GET',
                dataType: 'json',              
                complete:function(){
                    isQuerying = false;
                },
                success: function(data) {
                  $('#suggestionsFromLocation').empty(); 

                  if (data.length > 0) {
                      $.each(data, function(index, vehicle) {
                          $('#suggestionType').append(
                              `<a href="#" class="list-group-item-type list-group-item list-group-item-action" data-id="${vehicle.type}">${vehicle.type}</a>`
                          );
                      });
                  } else {
                      $('#suggestionType').append('<div class="list-group-item-type list-group-item">No results found</div>');
                  }
                },
              error: function() {
                $('#suggestionsFromLocation').empty();   
                $('#suggestionType').append('<div class="list-group-item-type list-group-item">Error fetching results</div>');
              }
            });
          } 
      });   
    $(document).on('click', '.list-group-item-type', function(e) {
      e.preventDefault();
      
      var type = $(this).text();
      
      $('#type').val(type); 
      $('#suggestionType').empty();
    });


    //Clear
    $(document).on('click', function(e) {

      //If clicked element is not inside list group
      if (!$(e.target).closest('.list-group').length) {

        $('#suggestionsFromLocation').empty();
        $('#suggestionsToLocation').empty();
        $('#suggestionType').empty();  

      }
    });


    //For Modals
    const modalBook = document.getElementById('modalBook');

    modalBook.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;

        // Retrieve data attributes from the button
        const vehicleName = button.getAttribute('data-bs-tag');
        const tripId = button.getAttribute('data-bs-trip_id');
        const fromLocation = button.getAttribute('data-bs-from');
        const toLocation = button.getAttribute('data-bs-to');
        const distance = button.getAttribute('data-bs-distance');
        const totalFare = parseFloat(button.getAttribute('data-bs-totalFare'));  // Parse as a number
        const seats = button.getAttribute('data-bs-seats');
        const maxSeats = button.getAttribute('data-bs-available');
        
        // Populate modal fields
        document.getElementById('modalVehicleName').textContent = vehicleName;
        document.getElementById('modalFrom').value = fromLocation;
        document.getElementById('modalTo').value = toLocation;
        document.getElementById('trip_id').value = tripId;
        document.getElementById('modalDistance').value = `${distance} km`;
        document.getElementById('modalFare').value = `PHP ${totalFare.toFixed(2)}`;
        document.getElementById('modalSeats').value = seats;
        document.getElementById("modalSeats").setAttribute("max", maxSeats);

        // Update total price display
        updateTotalPrice(seats, totalFare);
    });

    $('#modalSeats').on('input', function() {
        const seats = $(this).val();
        const fare = parseFloat($('#modalFare').val().replace('PHP ', '').replace(',', ''));

        // Check if fare is valid before calculating
        if (!isNaN(fare)) {
            updateTotalPrice(seats, fare);
        } else {
            $('#totalPrice').text('Invalid Fare');
        }
    });

    // Function to update the total price
    function updateTotalPrice(seats, fare) {
        const totalPrice = seats * fare;
        $('#totalPrice').text('PHP ' + totalPrice.toFixed(2));  // Format with two decimals
    }
});


</script>
<?= $this->endSection()?>