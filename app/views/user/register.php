<div>
  <h2 class="text-center mb-4">Register</h2>
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
      <form action="<?= baseUrl('user/register') ?>" method="post">
        <div class="mb-3">
          <label for="name" class="form-label">Username *</label>
          <input type="text" class="form-control <?= isset($errors['username']) ? 'is-invalid' : '' ?>"
            id="name" name="username" value="<?= isset($_POST['username']) ? e($_POST['username']) : '' ?>" required />
          <?php if (isset($errors['username'])) : ?>
            <div class="invalid-feedback"><?= $errors['username'] ?></div>
          <?php endif; ?>
        </div>
        <div class="mb-3">
          <label for="email" class="form-label">Email address *</label>
          <input type="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>"
            id="email" name="email" value="<?= isset($_POST['email']) ? e($_POST['email']) : '' ?>" required />
          <?php if (isset($errors['email'])) : ?>
            <div class="invalid-feedback"><?= $errors['email'] ?></div>
          <?php endif; ?>
        </div>
        <div class="mb-3">
          <label for="password" class="form-label">Password *</label>
          <input type="password" class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>"
            id="password" name="password" required />
          <?php if (isset($errors['password'])) : ?>
            <div class="invalid-feedback"><?= $errors['password'] ?></div>
          <?php endif; ?>
        </div>
        <div class="mb-3">
          <label for="confirm-password" class="form-label">Confirm Password *</label>
          <input type="password"
            class="form-control <?= isset($errors['confirm_password']) ? 'is-invalid' : '' ?>"
            id="confirm-password"
            name="confirm_password"
            required />
          <?php if (isset($errors['confirm_password'])) : ?>
            <div class="invalid-feedback"><?= $errors['confirm_password'] ?></div>
          <?php endif; ?>
        </div>
        <button type="submit" class="btn btn-primary w-100">Register</button>
      </form>
      <p class="mt-3 text-center">
        Already have an account? <a href="<?= baseUrl('user/login') ?>">Login here</a>.
      </p>
    </div>
  </div>
</div>
