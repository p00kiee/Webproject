<?php
session_start();
require_once 'config/database.php';

$error = '';
$success = isset($_GET['registered']) ? 'Registration successful. Please login.' : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   $email = trim($_POST['email']);
   $password = trim($_POST['password']);

   if ($email && $password) {
       $stmt = $pdo->prepare("SELECT * FROM customers WHERE email = :email");
       $stmt->execute([':email' => $email]);
       $customer = $stmt->fetch();

       if ($customer && password_verify($password, $customer['password'])) {
           $_SESSION['customer_id'] = $customer['id'];
           $_SESSION['customer_name'] = $customer['name'];
           header('Location: index.php');
           exit;
       } else {
           $error = 'Invalid email or password.';
       }
   } else {
       $error = 'Please fill in both fields.';
   }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Customer Login | SS Surgical</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <style>
        /* Modern Login Design */
        :root {
            --primary-color: #2563eb;
            --primary-hover: #1d4ed8;
            --primary-light: #e0f2fe;
            --primary-dark: #1e40af;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-300: #d1d5db;
            --gray-500: #6b7280;
            --gray-700: #374151;
            --gray-900: #111827;
            --red-50: #fef2f2;
            --red-600: #dc2626;
            --green-50: #f0fdf4;
            --green-600: #16a34a;
            --font-sans: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: var(--font-sans);
            background-image: linear-gradient(135deg, #f0f7ff 0%, #e0f2fe 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 1.5rem;
            color: var(--gray-700);
            line-height: 1.6;
        }

        .login-container {
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
            width: 100%;
            max-width: 420px;
            padding: 2.5rem;
            position: relative;
            overflow: hidden;
            isolation: isolate;
        }

        .login-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 6px;
            background: linear-gradient(90deg, var(--primary-color), var(--primary-light));
            z-index: -1;
        }

        .logo {
            display: flex;
            justify-content: center;
            margin-bottom: 1.5rem;
        }

        .logo-text {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--primary-color);
            letter-spacing: -0.025em;
        }

        h1 {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--gray-900);
            margin: 0 0 0.5rem 0;
            text-align: center;
            letter-spacing: -0.025em;
        }

        .subtitle {
            font-size: 0.95rem;
            color: var(--gray-500);
            text-align: center;
            margin-bottom: 2rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
            position: relative;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--gray-700);
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .input-container {
            position: relative;
        }

        .input-container i {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            left: 1rem;
            color: var(--gray-500);
            transition: all 0.2s ease;
            pointer-events: none;
        }

        input[type="email"],
        input[type="password"],
        input[type="text"] {
            width: 100%;
            padding: 0.75rem 1rem 0.75rem 2.75rem;
            border: 1px solid var(--gray-300);
            border-radius: 10px;
            font-size: 0.95rem;
            transition: all 0.2s ease;
            background: var(--gray-50);
            color: var(--gray-900);
        }

        input:focus {
            outline: none;
            border-color: var(--primary-color);
            background: white;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
        }

        input:focus + i {
            color: var(--primary-color);
        }

        .password-toggle {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--gray-500);
            cursor: pointer;
            font-size: 0.9rem;
            transition: color 0.2s ease;
            z-index: 2;
        }

        .password-toggle:hover {
            color: var(--primary-color);
        }

        .remember-me {
            display: flex;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .remember-me input[type="checkbox"] {
            width: 18px;
            height: 18px;
            margin-right: 0.5rem;
            accent-color: var(--primary-color);
        }

        .remember-me label {
            font-size: 0.9rem;
            color: var(--gray-700);
            cursor: pointer;
        }

        .login-btn {
            width: 100%;
            padding: 0.9rem;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 0.5rem;
            position: relative;
            overflow: hidden;
        }

        .login-btn::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 0;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.1);
            transition: width 0.3s ease;
            z-index: 1;
        }

        .login-btn:hover::before {
            width: 100%;
        }

        .login-btn:hover {
            background: var(--primary-hover);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
        }

        .login-btn:active {
            transform: translateY(0);
        }

        .login-btn i {
            font-size: 1rem;
            position: relative;
            z-index: 2;
        }

        .login-btn span {
            position: relative;
            z-index: 2;
        }

        .register-link {
            display: block;
            text-align: center;
            color: var(--gray-700);
            font-size: 0.9rem;
            margin-top: 1.5rem;
        }

        .register-link a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.2s ease;
            padding: 0.25rem 0.5rem;
            border-radius: 6px;
        }

        .register-link a:hover {
            background-color: var(--primary-light);
            color: var(--primary-dark);
        }

        .error {
            background: var(--red-50);
            color: var(--red-600);
            padding: 0.9rem;
            border-radius: 10px;
            font-size: 0.9rem;
            margin-bottom: 1.5rem;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .success {
            background: var(--green-50);
            color: var(--green-600);
            padding: 0.9rem;
            border-radius: 10px;
            font-size: 0.9rem;
            margin-bottom: 1.5rem;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .forgot-password {
            display: block;
            text-align: right;
            margin-top: 0.5rem;
            color: var(--primary-color);
            font-size: 0.85rem;
            text-decoration: none;
            transition: color 0.2s ease;
        }

        .forgot-password:hover {
            color: var(--primary-hover);
            text-decoration: underline;
        }

        .separator {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 1.5rem 0;
            color: var(--gray-500);
            font-size: 0.85rem;
        }

        .separator::before,
        .separator::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid var(--gray-200);
        }

        .separator::before {
            margin-right: 0.75rem;
        }

        .separator::after {
            margin-left: 0.75rem;
        }

        /* Animation */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-container {
            animation: fadeIn 0.5s ease-out;
        }

        /* Responsive */
        @media (max-width: 480px) {
            .login-container {
                padding: 2rem 1.5rem;
            }
        }
   </style>
</head>
<body>
   <div class="login-container">
        <div class="logo">
            <span class="logo-text">SS Surgical</span>
        </div>
        <h1>Customer Login</h1>
        <p class="subtitle">Login to your account to access your orders and profile</p>
        
        <!-- Success message -->
        <?php if ($success): ?>
            <div class="success">
                <i class="fas fa-check-circle"></i>
                <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>
        
        <!-- Error message -->
        <?php if ($error): ?>
            <div class="error">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <div id="errorMessage" class="error" style="display: none;">
            <i class="fas fa-exclamation-circle"></i>
            <span id="errorText"></span>
        </div>
       
        <form action="login.php" method="POST" id="loginForm">
            <div class="form-group">
                <label for="email">Email</label>
                <div class="input-container">
                    <input type="email" id="email" name="email" placeholder="Enter your email" required>
                    <i class="fas fa-envelope"></i>
                </div>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <div class="input-container">
                    <input type="password" id="password" name="password" placeholder="Enter your password" required>
                    <i class="fas fa-lock"></i>
                    <button type="button" class="password-toggle" id="togglePassword">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                <a href="#" class="forgot-password">Forgot password?</a>
            </div>
            
            <div class="remember-me">
                <input type="checkbox" id="remember" name="remember">
                <label for="remember">Remember me</label>
            </div>
            
            <button type="submit" class="login-btn">
                <i class="fas fa-sign-in-alt"></i>
                <span>Login</span>
            </button>
        </form>

        <p class="register-link">Don't have an account? <a href="registration.php">Register here</a></p>
   </div>

   <script>
   // Email validation function
   function isValidEmail(email) {
       const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
       return re.test(String(email).toLowerCase());
   }

   // Password visibility toggle
   const togglePassword = document.getElementById('togglePassword');
   const password = document.getElementById('password');
   
   togglePassword.addEventListener('click', function() {
       // Toggle the password field type
       const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
       password.setAttribute('type', type);
       
       // Toggle the eye icon
       this.querySelector('i').classList.toggle('fa-eye');
       this.querySelector('i').classList.toggle('fa-eye-slash');
   });

   // Form validation
   document.getElementById('loginForm').addEventListener('submit', function(e) {
       e.preventDefault();
       
       const email = document.getElementById('email').value.trim();
       const password = document.getElementById('password').value.trim();
       const errorDiv = document.getElementById('errorMessage');
       const errorText = document.getElementById('errorText');
       let error = false;
       
       // Reset styles
       errorDiv.style.display = 'none';
       document.getElementById('email').style.borderColor = 'var(--gray-300)';
       document.getElementById('password').style.borderColor = 'var(--gray-300)';
       
       // Validate email
       if (!email) {
           document.getElementById('email').style.borderColor = 'var(--red-600)';
           error = true;
       } else if (!isValidEmail(email)) {
           document.getElementById('email').style.borderColor = 'var(--red-600)';
           errorText.textContent = 'Please enter a valid email address';
           errorDiv.style.display = 'flex';
           return;
       }
       
       // Validate password
       if (!password) {
           document.getElementById('password').style.borderColor = 'var(--red-600)';
           error = true;
       }
       
       if (error) {
           errorText.textContent = 'Please fill in all required fields';
           errorDiv.style.display = 'flex';
           return;
       }
       
       this.submit();
   });

   // Clear error on input
   document.querySelectorAll('input').forEach(input => {
       input.addEventListener('input', function() {
           this.style.borderColor = 'var(--gray-300)';
           document.getElementById('errorMessage').style.display = 'none';
       });
   });
   </script>
</body>
</html>