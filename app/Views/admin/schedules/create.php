<?= $this->extend("layouts/admin") ?>

<?= $this->section("body") ?>

<a href="javascript:void(0);" onclick="window.history.back();" class="btn btn-secondary mb-3">
    <i class="bi bi-arrow-left"></i> Back
</a>

<style>
  #suggestions-vehicles,
  #suggestions-route {
      max-height: 200px; /* Limit height */
      overflow-y: auto; /* Enable scroll if content exceeds height */
  }

  .list-group-item {
      cursor: pointer; /* Change cursor on hover for better UX */
  }

  .list-group-item:hover {
      background-color: #f8f9fa; /* Light background on hover */
  }
</style>

<div class="contaiener">
  <form method="POST" action="<?= site_url('dashboard/schedules/create') ?>">
    <div class="container mt-4">
      <h2 class="mb-4">Select Vehicle and Route</h2>
        <!-- Vehicle Input -->
        <div class="mb-3">
            <label for="vehicle-input" class="form-label">Select Vehicle</label>
            <input id="vehicle-input" class="form-control form-control-lg" type="text" placeholder="Select Vehicle" aria-label=".form-control-lg" autocomplete="off" >
            ID:<input type="number" id="selected-vehicle-id" class="bg-transparent border-0 m-1" name="vehicle_id" placeholder="--"  hidden></input>
            <div id="suggestions-vehicles" class="list-group" style="position: absolute; z-index: 1000;"></div>
        </div>

        <!-- Route Input -->
        <div class="mb-3">
            <label for="route-input" class="form-label">Select Route</label>
            <input id="route-input" class="form-control form-control-lg" type="text" placeholder="Select Route" aria-label=".form-control-lg" autocomplete="off" >
            ID:<input type="number" class="bg-transparent border-0 m-1" id="selected-route-id" name="route_id" placeholder="--" hidden ></input>
            <div id="suggestions-route" class="list-group" style="position: absolute; z-index: 1000;"></div>
        </div>

         <!-- Stops Container -->
        <h3 class="mt-4">Stops</h3>
        <div class="stops-container">
            <!-- Dynamically generated stops will be appended here -->
        </div>
        <hr>
      <button type="submit" class="btn btn-primary col-12">Submit</button>
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

<script>
$(document).ready(function() {
    let minimumETA = formatDate(addDays(new Date(), 1)); 

    $('#route-input').on('input', function() {
        var query = $(this).val();
        $('#selected-route-id').val(""); 
        $('.stops-container').empty();
        
        if (query.length > 0) {
          let url = `<?= site_url("routes/get/") ?>${encodeURIComponent(query)}`;
            $.ajax({
                url: url, // Your API endpoint
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    $('#suggestions-route').empty(); // Clear previous suggestions
                    if (data.length > 0) {
                        $.each(data, function(index, route) {
                            $('#suggestions-route').append(
                                `<a href="#" class="list-group-item-routes list-group-item list-group-item-action" data-id="${route.id}">${route.name}</a>`
                            );
                        });
                    } else {
                        $('#suggestions-route').append('<div class="list-group-item-routes list-group-item">No results found</div>');
                    }
                },
                error: function() {
                    $('#suggestions-route').empty();
                    $('#suggestions-route').append('<div class="list-group-item-routes list-group-item">Error fetching results</div>');
                }
            });
        } else {
            $('#suggestions-route').empty(); // Clear suggestions if input is empty
        }
    });
   
    $('#vehicle-input').on('input', function() {
        var query = $(this).val();
        $('#selected-vehicle-id').val("");

        $('.stops-container').empty();
        if (query.length > 0) {
            let url = `<?= site_url("vehicles/get/")?>${encodeURIComponent(query)}`;
            $.ajax({
                url: url,
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    $('#suggestions-vehicles').empty();
                    if (data.length > 0) {
                        data.forEach(function(vehicle) {
                            $('#suggestions-vehicles').append(`
                                <a href="#" class="list-group-item-vehicles list-group-item list-group-item-action" data-id="${vehicle.id}">${vehicle.tag}</a>
                            `);
                        });
                    } else {
                        $('#suggestions-vehicles').append('<div class="list-group-item-vehicles list-group-item">No vehicles found</div>');
                    }
                },
                error: function() {
                    $('#suggestions-vehicles').empty();
                    $('#suggestions-vehicles').append('<div class="list-group-item-vehicles list-group-item">Error fetching results</div>');
                }
            });
        } else {
            $('#suggestions-vehicles').empty(); // Clear suggestions if input is empty
        }
    });

    $(document).on('click', '.list-group-item-vehicles', function(e) {
      e.preventDefault();
      
      var vehicleId = $(this).data('id');
      var vehicleTag = $(this).text();
      
      $('#vehicle-input').val(vehicleTag); 
      $('#selected-vehicle-id').val(vehicleId); 
      $('suggestions-vehicles').empty();

      console.log( $('#selected-vehicle-id').val());

    });
    
    $(document).on('click', '.list-group-item-routes', function(e) {
      e.preventDefault();

      var routeId = $(this).data('id');
      var routeName = $(this).text();
      
      $('#route-input').val(routeName); 
      $('#selected-route-id').val(routeId); 
      $('#suggestions-route').empty(); 

      let url = `<?= site_url("stops/get/") ?>${encodeURIComponent(routeId)}`;
        $.ajax({
            url: url, // Your API endpoint
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                $('.stops-container').empty(); 

                if (data.length > 0) {
                    $index = 1;
                    $.each(data, function(index, stop) {

                    // Input fields are name, distance, eta
                      $('.stops-container').append(
                        `<div class="mb-3 border p-3 rounded bg-light">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="font-weight-bold">Stop ${index + 1} at <strong>${stop.distance}km </strong> from previous (${stop.name})</span>
                                <input type="text" class="text-muted" name="stop_id[]" placeholder="ID: ${stop.id}" value="${stop.id}" hidden>
                            </div>
                            <div class="mt-2">
                                <input type="text" class="text-muted" name="name[]" value="${stop.name}" hidden>
                                <input type="text" class="text-muted" name="distance[]"value="${stop.distance}" hidden>
                                <label for="eta-${index}" class="form-label">ETA:</label>
                                <input type="datetime-local" class="eta" name="eta[]" class="form-control" required>
                            </div>
                        </div>`
                    );

                        $index++
                    });
                } else {
                    $('.stops-container').append('<div class="list-group-item-routes">Contains 0 stops</div>');
                }
                validateAndSetMinETA();
            },
            error: function() {
                $('.stops-container').empty();
                $('.stops-container').append('<div>No Stops</div>');
            }
        });
    });

    $(document).on('click', function(e) {
        $('#suggestions-route').empty();
        $('#suggestions-vehicles').empty();
    });
    
    $(document).on('input', '.stops-container input[type="datetime-local"]', function(e) {
        validateAndSetMinETA();
    });
    
    function addDays(date, days) {
        var result = new Date(date);
        result.setDate(result.getDate() + days);
        // Round down to the nearest day by setting the time to midnight
        result.setHours(0, 0, 0, 0);
        return result;
    }

    function formatDate(date) {
        var year = date.getFullYear();
        var month = ('0' + (date.getMonth() + 1)).slice(-2);
        var day = ('0' + date.getDate()).slice(-2);
        var hours = ('0' + date.getHours()).slice(-2);
        var minutes = ('0' + date.getMinutes()).slice(-2);
        return year + '-' + month + '-' + day + 'T' + hours + ':' + minutes;
    }

    function validateAndSetMinETA() {

        var currentETA = minimumETA;
        //Get every stop sched input
        $('.stops-container input[type="datetime-local"]').each(function(index, element) {
            //Set the element's minimum eta to be 2 days from now
            var currentValue = $(this).val();
            $(element).attr('min', currentETA);
            if(currentValue>=currentETA && currentValue!=""){
                currentETA = currentValue;
            }else{
                $(this).val("");
            }
        });
    }

});
</script>


<?= $this->endSection()?>