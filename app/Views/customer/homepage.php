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
                    <input type="text" placeholder="Location A" class="form-control border-0 h-100 w-100" id="fromLocation" name="fromLocation" style="background-color:transparent" required>
                    <div id="suggestionsFromLocation" class="list-group" style="position: absolute; z-index: 1000;"></div>
                  </div>

                  <div class="border rounded-circle bg-white d-flex align-items-center justify-content-center" style="position:absolute; width:50px; height:50px; left:calc(50% - 50px / 2); bottom:calc(-25%)">
                    <i class="fa fa-exchange bg-transparent" aria-hidden="true"></i>
                  </div>

                  <div class="col-6 rounded-end border ps-5" style="height:50px; background-color:white">
                    <input type="text" placeholder="Location A" class="form-control border-0 h-100 w-100" id="toLocation" name="toLocation" style="background-color:transparent" required>
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
                    <input type="text" placeholder="Any" class="form-control border-0 h-100 w-100" id="type" name="type" style="background-color:transparent" value="Any" required>
                    <div id="suggestionType" class="list-group" style="position: absolute; z-index: 1000;"></div>
                  </div>
                  </div>
                  <div class="col-3 rounded-end border" style="height:50px; background-color:white">
                    <input type="number" class="form-select border-0 h-100 w-100" name="numSeats"  style="background-color:transparent" value="1">
                  </div>
                </div>
            </div>

            <!-- Right side -->
            <div class="col-3 d-flex flex-column text-center border-start border-dark  px-3">
                <button class="btn btn-primary h-100"><i class="fas fa-search"></i> Search</button>
            </div>
        </form>
    </div>
    <div class="m-5 p-5">
        <hr>
        
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

    </div>
</div>

<script>
  let isQuerying = false;

  $(document).ready(function() {
    //From Locations
    $('#fromLocation').on('input', function() {
        var query = $(this).val();
        //Clear values
        if (query.length > 0) {
          let url = `<?= site_url("stops/search/") ?>${encodeURIComponent(query)}`;

          $.ajax({
              url: url, 
              type: 'GET',
              dataType: 'json',
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
      $('.suggestionsToLocation').empty();

    });

    //To locations
    $('#toLocation').on('input', function() {
          var query = $(this).val();

          //Clear values
          $('#suggestionsToLocation').empty(); 

          if (query.length > 0) {
            let url = `<?= site_url("stops/search/") ?>${encodeURIComponent(query)}`;

            $.ajax({
                url: url, 
                type: 'GET',
                dataType: 'json',
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
      $('.suggestionsToLocation').empty();

    });


    //Vehicle type
    $('#type').on('input', function() {
          var query = $(this).val();

          //Clear values
          $('#suggestionType').empty(); 

          if (query.length > 0) {
            let url = `<?= site_url("stops/search/") ?>${encodeURIComponent(query)}`;

            $.ajax({
                url: url, 
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                  $('#suggestionsFromLocation').empty(); 

                  if (data.length > 0) {
                      $.each(data, function(index, location) {
                          $('#suggestionType').append(
                              `<a href="#" class="list-group-item-type list-group-item list-group-item-action" data-id="${location.id}">${location.name}</a>`
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

        const inputId = lastFocusedElement.attr('id');
      }
    });
  });
</script>
<?= $this->endSection()?>