<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<h1><?= $title ?></h1>

  <form id="register-form" method="post" action="/register">
    <div class="input-group" style="margin-bottom: 16px;">
      <input type="text" name="name" placeholder="Full Name" required />
    </div>

    <div class="input-group" style="margin-bottom: 16px;">
      <input type="email" name="email" placeholder="Email Address" required />
    </div>

    <div class="input-group" style="margin-bottom: 20px;">
      <input type="password" name="password" placeholder="Password" required />
    </div>

    
    <div class="input-group" style="margin-bottom: 20px;">
      <input type="password" name="password_confirm" placeholder="Repeat password" required />
    </div>

    <?php if (isset($validation)): ?>
    <div class="error text-box">
        <?= $validation->listErrors() ?>
    </div>
    <?php endif; ?>

    <div class="auth-switch">
      <span>Already have an account?</span>
      <a href="/login">Go to login</a>
    </div>


    <button type="submit" class="login-button">Create Account</button>
  </form>


<?= $this->endSection() ?>
