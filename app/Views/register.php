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

            <div class="panel-heading h2">Register</div>
            <p class="text-white-50 pb-3">Setup your account and start booking!</p>
            <div class="panel-body">

                <form class="" action="<?= base_url('register') ?>" method="POST">
                    <div class="form-group form-floating">
                        <input type="text" class="form-control" name="name" id="name" >
                        <label for="name">Name</label>
                    </div>
                    <br>
                    <div class="form-group form-floating">
                        <input type="email" class="form-control" name="email" id="email">
                        <label for="email">Email</label>
                    </div>
                    <br>
                    <div class="form-group form-floating">
                        <input type="text" class="form-control" name="phone_no" id="phone_no">
                        <label for="phone_no">Phone No</label>
                    </div>
                    <br>
                    <div class="form-group form-floating"> 
                        <input type="password" class="form-control" name="password" id="password">
                        <label for="password">Password</label>
                    </div>
                    <br>
                    <div class="form-group form-floating">
                        <input type="password" class="form-control" name="password_confirm" id="password_confirm">
                        <label for="password_confirm">Confirm Password</label>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 my-3 "><h4>Submit</h4></button>
                </form>
            </div>

            </div>

            <div>
              <p class="mb-0">Already Have an account? <a href="<?= base_url('') ?>" class="text-white-50 fw-bold">Sign In</a>
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