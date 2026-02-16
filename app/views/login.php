<?php
function e($v){ return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); }
function cls_invalid($errors, $field){ return ($errors[$field] ?? '') !== '' ? 'is-invalid' : ''; }

// If the controller doesn't pass them, avoid warnings.
$errors = $errors ?? [];
$values = $values ?? [];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <script src="https://unpkg.com/lucide@latest"></script>
  <style>
    :root {
      --bg-dark: #0f172a;
      --bg-card: #1e293b;
      --bg-input: #0f172a;
      --border-color: #334155;
      --text-primary: #f8fafc;
      --text-secondary: #94a3b8;
      --accent-green: #22c55e;
      --accent-yellow: #eab308;
      --accent-blue: #3b82f6;
    }
    
    body {
      background-color: var(--bg-dark);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
    }
    
    .login-card {
      background-color: var(--bg-card);
      border-radius: 16px;
      padding: 2rem;
      width: 100%;
      max-width: 550px;
      box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
    }
    
    .card-title {
      color: var(--text-primary);
      font-size: 1.25rem;
      font-weight: 600;
      display: flex;
      align-items: center;
      gap: 0.75rem;
      margin-bottom: 1.5rem;
      padding-bottom: 1rem;
      border-bottom: 1px solid var(--border-color);
    }
    
    .card-title .icon {
      color: var(--accent-blue);
    }
    
    .form-row {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 1rem;
    }
    
    .form-group {
      margin-bottom: 1rem;
    }
    
    .form-label {
      color: var(--text-secondary);
      font-size: 0.875rem;
      margin-bottom: 0.5rem;
      display: block;
    }
    
    .input-group {
      background-color: var(--bg-input);
      border: 1px solid var(--border-color);
      border-radius: 8px;
      overflow: hidden;
      display: flex;
      align-items: center;
      transition: border-color 0.2s, box-shadow 0.2s;
    }
    
    .input-group:focus-within {
      border-color: var(--accent-blue);
      box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
    }
    
    .input-group.is-invalid {
      border-color: #ef4444;
    }
    
    .input-group.filled {
      background-color: rgba(234, 179, 8, 0.15);
      border-color: var(--accent-yellow);
    }
    
    .input-icon {
      background: transparent;
      border: none;
      color: var(--text-secondary);
      padding: 0.75rem 1rem;
      display: flex;
      align-items: center;
    }
    
    .input-group.filled .input-icon {
      color: var(--accent-yellow);
    }
    
    .form-control {
      background: transparent;
      border: none;
      color: var(--text-primary);
      padding: 0.75rem 1rem 0.75rem 0;
      font-size: 1rem;
      flex: 1;
      width: 100%;
    }
    
    .form-control::placeholder {
      color: var(--text-secondary);
    }
    
    .form-control:focus {
      background: transparent;
      box-shadow: none;
      outline: none;
      color: var(--text-primary);
    }
    
    .invalid-feedback {
      color: #ef4444;
      font-size: 0.8rem;
      margin-top: 0.25rem;
      display: block;
    }
    
    .btn-login {
      background-color: var(--accent-green);
      border: none;
      color: #000;
      font-weight: 600;
      padding: 0.875rem 1.5rem;
      border-radius: 8px;
      width: auto;
      font-size: 1rem;
      transition: background-color 0.2s, transform 0.1s;
      cursor: pointer;
    }
    
    .btn-login:hover {
      background-color: #16a34a;
      transform: translateY(-1px);
    }
    
    .form-check {
      margin-bottom: 1rem;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }
    
    .form-switch .form-check-input {
      background-color: var(--bg-input);
      border-color: var(--border-color);
      width: 2.5rem;
      height: 1.25rem;
      cursor: pointer;
      margin: 0;
    }
    
    .form-switch .form-check-input:checked {
      background-color: var(--accent-green);
      border-color: var(--accent-green);
    }
    
    .form-check-label {
      color: var(--text-secondary);
      font-size: 0.875rem;
    }
    
    .form-check-label a {
      color: var(--accent-blue);
      text-decoration: none;
    }
    
    .form-check-label a:hover {
      text-decoration: underline;
    }
    
    .alert {
      border-radius: 8px;
      padding: 1rem;
      margin-bottom: 1.5rem;
    }
    
    .alert-danger {
      background-color: rgba(239, 68, 68, 0.1);
      border: 1px solid rgba(239, 68, 68, 0.3);
      color: #fca5a5;
    }
    
    .password-wrapper {
      position: relative;
      display: flex;
      align-items: center;
      flex: 1;
    }
    
    .password-wrapper input {
      flex: 1;
    }
    
    .password-strength {
      width: 40px;
      height: 8px;
      background-color: var(--border-color);
      border-radius: 4px;
      margin-right: 0.75rem;
      overflow: hidden;
    }
    
    .password-strength-bar {
      height: 100%;
      width: 0%;
      background-color: var(--accent-green);
      transition: width 0.3s, background-color 0.3s;
    }
  </style>
</head>
<body>
<div class="container">
  <div class="row justify-content-center">
    <div class="col-12">
      <div class="login-card mx-auto">
        <div class="card-title">
          <i data-lucide="user-plus" class="icon"></i>
          User Login
        </div>

        <form id="loginForm" method="post" action="/login" enctype="multipart/form-data" novalidate>
          <?php $global = (string)($errors['_global'] ?? ''); ?>
          <?php if ($global !== ''): ?>
          <div id="formStatus" class="alert alert-danger">
            <?= e($global) ?>
          </div>
          <?php endif; ?>

          <div class="form-row">
            <!-- Nom Field -->
            <div class="form-group">
              <label for="nom" class="form-label">Username</label>
              <div class="input-group" id="nomGroup">
                <span class="input-icon">
                  <i data-lucide="user"></i>
                </span>
                <input 
                  type="text" 
                  id="nom" 
                  name="nom" 
                  class="form-control" 
                  placeholder="Enter username"
                  value="<?= e($values['nom'] ?? '') ?>">
              </div>
              <?php if (!empty($errors['nom'])): ?>
                <div class="invalid-feedback"><?= e($errors['nom']) ?></div>
              <?php endif; ?>
            </div>

            <!-- Email Field -->
            <div class="form-group">
              <label for="email" class="form-label">Email</label>
              <div class="input-group" id="emailGroup">
                <span class="input-icon">
                  <i data-lucide="mail"></i>
                </span>
                <input 
                  type="email" 
                  id="email" 
                  name="email" 
                  class="form-control" 
                  placeholder="Enter email"
                  value="<?= e($values['email'] ?? '') ?>">
              </div>
              <?php if (!empty($errors['email'])): ?>
                <div class="invalid-feedback"><?= e($errors['email']) ?></div>
              <?php endif; ?>
            </div>
          </div>

          <div class="form-row">
            <!-- Password Field -->
            <div class="form-group">
              <label for="password" class="form-label">Password</label>
              <div class="input-group" id="passwordGroup">
                <span class="input-icon">
                  <i data-lucide="lock"></i>
                </span>
                <div class="password-wrapper">
                  <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    class="form-control" 
                    placeholder="Enter password">
                  <div class="password-strength">
                    <div class="password-strength-bar" id="strengthBar"></div>
                  </div>
                </div>
              </div>
              <?php if (!empty($errors['password'])): ?>
                <div class="invalid-feedback"><?= e($errors['password']) ?></div>
              <?php endif; ?>
            </div>

            <!-- Confirm Password Field -->
            <div class="form-group">
              <label for="confirm_password" class="form-label">Confirm Password</label>
              <div class="input-group" id="confirmGroup">
                <span class="input-icon">
                  <i data-lucide="lock"></i>
                </span>
                <input 
                  type="password" 
                  id="confirm_password" 
                  name="confirm_password" 
                  class="form-control" 
                  placeholder="Confirm password">
              </div>
            </div>
          </div>

          <!-- Terms Checkbox -->
          <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" id="terms" name="terms">
            <label class="form-check-label" for="terms">
              I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a>
            </label>
          </div>

          <button class="btn btn-login" type="submit">Se connecter</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
  lucide.createIcons();
  
  // Add filled class when input has value
  document.querySelectorAll('.form-control').forEach(input => {
    const group = input.closest('.input-group');
    
    // Check initial value
    if (input.value.trim() !== '') {
      group.classList.add('filled');
    }
    
    input.addEventListener('input', function() {
      if (this.value.trim() !== '') {
        group.classList.add('filled');
      } else {
        group.classList.remove('filled');
      }
    });
  });
  
  // Password strength indicator
  document.getElementById('password').addEventListener('input', function() {
    const password = this.value;
    const strengthBar = document.getElementById('strengthBar');
    let strength = 0;
    
    if (password.length >= 6) strength += 25;
    if (password.length >= 10) strength += 25;
    if (/[A-Z]/.test(password)) strength += 25;
    if (/[0-9]/.test(password) || /[^A-Za-z0-9]/.test(password)) strength += 25;
    
    strengthBar.style.width = strength + '%';
    
    if (strength <= 25) {
      strengthBar.style.backgroundColor = '#ef4444';
    } else if (strength <= 50) {
      strengthBar.style.backgroundColor = '#f97316';
    } else if (strength <= 75) {
      strengthBar.style.backgroundColor = '#eab308';
    } else {
      strengthBar.style.backgroundColor = '#22c55e';
    }
  });
</script>
<script src="/js/validation-ajax.js" defer></script>
</body>
</html>
