<?= $this->extend("layouts/admin") ?>

<?= $this->section("body") ?>

<div class="container">
    <div class="row align-items-center">
        <div class="col">
            <h1>Route Details</h1>
        </div>
    </div>
</div>

<hr>

<div class="container-fluid border border-light border-1 shadow-sm rounded-3 p-5 m-0 ">
    <?php if ($route): ?>
        <h3><?= esc($route['name']) ?></h3>
        <h5>Total Distance: <?= $totalDistance ?> Kilometers</h5>
        <h5>Number of stops: <?= $totalStops ?></h5>
        <hr>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Stop Name</th>
                    <th>Distance (Km)</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($stops)): ?>
                    <?php foreach ($stops as $stop): ?>
                        <tr>
                            <td><?= esc($stop['name']) ?></td>
                            <td><?= esc($stop['distance']) ?> Km</td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="2" class="text-center">No stops found for this route.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No route found.</p>
    <?php endif; ?>
</div>

<div class="text-end m-5">
    <button class="btn btn-secondary" onclick="window.history.back();">Back</button>
</div>


<?= $this->endSection() ?>