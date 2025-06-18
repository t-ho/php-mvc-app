<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <ul class="navbar-nav ms-auto">
        <li class="nav-item">
            <a class="nav-link active" aria-current="page" href="<?= baseUrl('/') ?>">Home</a>
        </li>
        <?php if (isLoggedIn()) : ?>
          <li class="nav-item">
              <a class="nav-link" href="<?= baseUrl('dashboard') ?>">Admin</a>
          </li>
        <?php endif; ?>
        <li class="nav-item">
            <a class="nav-link" href="<?= baseUrl('about') ?>">About</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="<?= baseUrl('contact') ?>">Contact</a>
        </li>

        <?php if (!isLoggedIn()) : ?>
          <li class="nav-item">
              <a class="nav-link" href="<?= route('login') ?>">Login</a>
          </li>
          <li class="nav-item">
              <a class="nav-link" href="<?= baseUrl('user/register') ?>">Register</a>
          </li>
        <?php endif; ?>
    </ul>
</nav>
