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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <style>
        #title{
            background: linear-gradient(to right, #12c2e9, #c471ed, #f64f59);
            background-clip: text;
            color:transparent
        }
   
        /* Custom scrollbar for WebKit browsers (Chrome, Safari, Edge) */
        ::-webkit-scrollbar {
            width: 10px;  /* Width of the scrollbar */
        }

        ::-webkit-scrollbar-track {
            background-color: #e0e0e0;  /* Track color */
        }

        ::-webkit-scrollbar-thumb {
            background-color: #555;  /* Scrollbar thumb color */
            border: none;  /* No border for thumb */
        }

        ::-webkit-scrollbar-thumb:hover {
            background-color: #333;  /* Scrollbar thumb color on hover */
        }

        /* Custom scrollbar for Firefox */
        html {
            scrollbar-width: thin;  /* Thin scrollbar */
            scrollbar-color: #555 #e0e0e0;  /* Thumb color and track color */
        }

    </style>
</head>
<body style="height:100vh">
        
<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
    <a class="navbar-brand ms-2" id="title" href="<?= base_url('homepage') ?>">Booking.IO</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav w-100">
            <li class="nav-item active">
                <a class="nav-link" href="<?= base_url('homepage') ?>">Home <span class="sr-only">(current)</span></a>
            </li>
            <li class="nav-item ">
                <a class="nav-link" href="<?= base_url('homepage/bookings') ?>">Bookings & Payments</a>
            </li>
            <li class="nav-item m-auto me-5">  
              <a class="nav-link" href="#">Logout</a>
            </li>
        </ul>
    </div>
</nav>

<div class="col-12 min-vh-100">
    <?= $this->renderSection('body') ?>
</div>


<div class="d-flex flex-column flex-md-row text-center text-md-start justify-content-between py-4 px-4 px-xl-5 bg-dark">
    <div class="text-white mb-3 mb-md-0">
    Copyright © 2024. All rights reserved. 
    </div>
</div>

</body>
</html>
