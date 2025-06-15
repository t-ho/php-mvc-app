<div class="container my-5">
  <h2 class="text-center mb-4">Login</h2>
  <div class="row justify-content-center">
    <div class="col-md-6">
      <?php if (isset($errors) && !empty($errors)) : ?>
        <div class="alert alert-danger">
          <ul class="mb-0">
            <?php foreach ($errors as $error) : ?>
              <li><?= $error ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>
      <form action="<?= baseUrl('user/login') ?>" method="post">
        <div class="mb-3">
          <label for="email" class="form-label">Email Address or Username *</label>
          <input name="emailOrUsername" type="text" class="form-control" id="email" required />
        </div>
        <div class="mb-3">
          <label for="password" class="form-label">Password *</label>
          <input name="password" type="password" class="form-control" id="password" required />
        </div>
        <button type="submit" class="btn btn-primary w-100">Login</button>
      </form>
      <p class="mt-3 text-center">
        Don't have an account? <a href="<?= baseUrl('user/register') ?>">Register here</a>.
      </p>
    </div>
  </div>
</div>
