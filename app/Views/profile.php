<?= $this->extend("layouts/app") ?>

<?= $this->section("body") ?>


                <h3>Hi, <?= $user['name'] ?></h3>
                <hr>
                <p>Email: <?= $user['email'] ?></p>
                <p>Phone No: <?= $user['phone_no'] ?></p>
 

<?= $this->endSection() ?>