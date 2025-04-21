<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<h1><?= $title ?></h1>
  <?php if (session()->get('success')): ?>
    <div class="success text-box">
        <?= session()->get('success') ?>
    </div>
    <?php endif; ?>

  <form id="login-form" method="post" action="/login">
   
    <div class="input-group" style="margin-bottom: 16px;">
      <input type="email" name="email" placeholder="Email Address" required />
    </div>

    <div class="input-group" style="margin-bottom: 20px;">
      <input type="password" name="password" placeholder="Password" required />
    </div>

    <?php if (isset($validation)): ?>
    <div class="error text-box">
        <?= $validation->listErrors() ?>
    </div>
    <?php endif; ?>

    <div class="auth-switch">
      <span>Need an account?</span>
      <a href="/register">Go to register</a>
    </div>

    <button type="submit" class="login-button">Log in</button>
  </form>


<?= $this->endSection() ?>
