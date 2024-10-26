<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->renderSection('title') ?></title>
    <link rel="stylesheet" href="<?= base_url('/public/css/style.css') ?>">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
   
</head>
<body style="height:100vh">
    <div class="d-flex flex-column flex-shrink-0 p-3 text-white bg-dark h-100 position-fixed" style="width: 280px;">
        <a href="/dashboard" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
        <svg class="bi me-2" width="40" height="32"><use xlink:href="#bootstrap"></use></svg>
        <span class="fs-4">Seat Booking System</span>
        </a>
        <hr>
        <ul class="nav nav-pills flex-column mb-auto">           
        <li class="nav-item">
            <a href="<?= site_url('dashboard') ?>" class="nav-link text-white <?= (current_url() == site_url('dashboard')) ? 'active' : '' ?>" >
            <svg class="bi me-2" width="16" height="16"><use xlink:href="#home"></use></svg>
            Home
            </a>
        </li>

        
        <li>
            <a href="<?= site_url('dashboard/routes') ?>" class="nav-link text-white <?= (strpos(current_url(), site_url('dashboard/routes')) !== false) ? 'active' : '' ?>">
            <svg class="bi me-2" width="16" height="16"><use xlink:href="#table"></use></svg>
            Routes
            </a>
        </li>
        <li>
            <a href="<?= site_url('dashboard/vehicles') ?>" class="nav-link text-white <?= (strpos(current_url(), site_url('dashboard/vehicles')) !== false) ? 'active' : '' ?>" >
            <svg class="bi me-2" width="16" height="16"><use xlink:href="#speedometer2"></use></svg>
            Vehicles
            </a>
        </li>
        <li>
            <a href="<?= site_url('dashboard/schedules') ?>" class="nav-link text-white <?= (strpos(current_url(), site_url('dashboard/schedules')) !== false) ? 'active' : '' ?>" >
            <svg class="bi me-2" width="16" height="16"><use xlink:href="#table"></use></svg>
            Schedules
            </a>
        </li>
        <li>
            <a href="<?= site_url('dashboard/bookings') ?>" class="nav-link text-white <?= (strpos(current_url(), site_url('dashboard/bookings')) !== false) ? 'active' : '' ?>" >
            <svg class="bi me-2" width="16" height="16"><use xlink:href="#table"></use></svg>
            Bookings
            </a>
        </li>
        </ul>
        <hr>
        <div class="dropdown">
        <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
            <img src="https://t4.ftcdn.net/jpg/02/29/75/83/360_F_229758328_7x8jwCwjtBMmC6rgFzLFhZoEpLobB6L8.jpg" alt="" width="32" height="32" class="rounded-circle me-2">
            <strong>Admin</strong>
        </a>
        <ul class="dropdown-menu dropdown-menu-dark text-small shadow" aria-labelledby="dropdownUser1">
            <!-- <li><a class="dropdown-item" href="#">New project...</a></li>
            <li><a class="dropdown-item" href="#">Settings</a></li>
            <li><a class="dropdown-item" href="#">Profile</a></li> -->
            <!-- <li><hr class="dropdown-divider"></li> -->
            <form action="<?= base_url('logout') ?>" method="GET" style="display: inline;">
                <button type="submit" class="dropdown-item" style="border: none; background: none; color: inherit; padding: 0 0 0 10px; margin: 0; font: inherit;">Sign out</button>
            </form>
        </ul>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="content"style="margin-left: 280px;">
        <div class="bg-dark text-secondary p-3" style="box-shadow: inset 5px -1px 5px #1c1c1c;">Admin Dashboard</div>
        <div style="padding: 20px">
            <?= $this->renderSection('body') ?>
        </div>
    </div>
</body>
</html>
