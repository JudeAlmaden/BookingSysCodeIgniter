<?= $this->extend("layouts/index") ?>

<?= $this->section("body") ?>

<style>
    .logo {
        width: 80px;
        margin: auto;
    }

    .logo img {
        width: 100%;
        height: 80px;
        object-fit: cover;
        border-radius: 50%;
        box-shadow: 0px 0px 3px #5f5f5f,
            0px 0px 0px 5px #ecf0f3,
            8px 8px 15px #a7aaa7,
            -8px -8px 15px #fff;
            background-color: white;
    }

    .shadow{
        border-radius: inherit;
        box-shadow: 12px 12px 12px rgba(0, 0, 0, .5), 
        -10px -10px 10px rgba(0,0,0,0.1) !important;
    }

  #title{
    background: linear-gradient(to right, #12c2e9, #c471ed, #f64f59);
    background-clip: text;
    color:transparent
  }
</style>

<section class="vh-100" style="background-color:#f9f9f9 !important">
  <div class="container py-5 h-100">
    <div class="row d-flex justify-content-center align-items-center h-100">
      <div class="col-12 col-md-8 col-lg-6 col-xl-5">
        <div class="card bg-dark text-white" style="border-radius: 1rem;">
          <div class="card-body p-5 text-center shadow">
            <div class="mb-md-5 mt-md-4 pb-5">        

            <div class="logo">
                <img src="https://www.freepnglogos.com/uploads/logo-chatgpt-png/chatgpt-brand-logo-transparent.png" alt="LogoLangz">
            </div>

              <form class="" action="<?= base_url('') ?>" method="post">
                <h2 class="fw-bold my-2 text-uppercase" id="title">Ride Booking System</h2>
                <p class="text-white-50 mb-5">Please enter your login and password!</p>

                <div data-mdb-input-init class="form-outline form-white mb-4 form-floating">
                    <input type="email" id="typeEmailX" class="form-control form-control-lg " name="email" />
                    <label class="form-label" for="typeEmailX">Email</label>
                </div>

                <div data-mdb-input-init class="form-outline form-white mb-4 form-floating">
                    <input type="password" id="typePasswordX" class="form-control form-control-lg" name="password"/>
                    <label class="form-label" for="typePasswordX" >Password</label>
                </div>

                <p class="small mb-5 pb-lg-2"><a class="text-white-50" href="#!">Forgot password?</a></p>

                <button data-mdb-button-init data-mdb-ripple-init class="btn btn-outline-light btn-lg px-5" type="submit">Login</button>
              </form>

              <div class="d-flex justify-content-center text-center mt-4 pt-1">
                <a href="#!" class="text-white"><i class="fab fa-facebook-f fa-lg"></i></a>
                <a href="#!" class="text-white"><i class="fab fa-twitter fa-lg mx-4 px-2"></i></a>
                <a href="#!" class="text-white"><i class="fab fa-google fa-lg"></i></a>
              </div>

            </div>

            <div>
              <p class="mb-0">Don't have an account? <a href="<?= base_url('register') ?>" class="text-white-50 fw-bold">Sign Up</a>
              </p>
            </div>
          </div>
        </div>

        <?php if (isset($validation)) : ?>
            <div class="col-12 mt-5">
                <div class="alert alert-danger" role="alert">
                    <?= $validation->listErrors() ?>
                </div>
            </div>
        <?php endif; ?>

      </div>
    </div>
  </div>
</section>

<?= $this->endSection() ?>