<?php
require_once 'config/database.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    if ($password !== $confirm_password) {
        $error = 'Passwords do not match.';
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("INSERT INTO customers (name, email, password) VALUES (:name, :email, :password)");
        try {
            $stmt->execute([
                ':name' => $name,
                ':email' => $email,
                ':password' => $hashed_password,
            ]);

            // Redirect to login page
            header('Location: login.php?registered=true');
            exit;
        } catch (PDOException $e) {
            if ($e->errorInfo[1] == 1062) {
                $error = 'Email already exists.';
            } else {
                $error = 'An error occurred. Please try again.';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account | SS Surgical</title>
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

        .registration-container {
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
            width: 100%;
            max-width: 460px;
            padding: 2.5rem;
            position: relative;
            overflow: hidden;
            isolation: isolate;
        }

        .registration-container::before {
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

        input[type="text"],
        input[type="email"],
        input[type="password"] {
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

        .register-btn {
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
            margin-top: 0.5rem;
        }

        .register-btn::before {
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

        .register-btn:hover::before {
            width: 100%;
        }

        .register-btn:hover {
            background: var(--primary-hover);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
        }

        .register-btn:active {
            transform: translateY(0);
        }

        .register-btn i {
            font-size: 1rem;
            position: relative;
            z-index: 2;
        }

        .register-btn span {
            position: relative;
            z-index: 2;
        }

        .login-link {
            display: block;
            text-align: center;
            color: var(--gray-700);
            font-size: 0.9rem;
            margin-top: 1.5rem;
        }

        .login-link a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.2s ease;
            padding: 0.25rem 0.5rem;
            border-radius: 6px;
        }

        .login-link a:hover {
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

        .password-strength {
            margin-top: 0.5rem;
            font-size: 0.85rem;
            color: var(--gray-500);
        }

        .strength-meter {
            height: 4px;
            border-radius: 2px;
            background-color: var(--gray-200);
            margin-top: 0.25rem;
            overflow: hidden;
        }

        .strength-meter-fill {
            height: 100%;
            width: 0;
            transition: width 0.3s ease, background-color 0.3s ease;
        }

        .strength-text {
            font-size: 0.8rem;
            margin-top: 0.25rem;
            text-align: right;
        }

        .policy-agreement {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            font-size: 0.85rem;
            color: var(--gray-500);
            margin-top: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .policy-agreement input[type="checkbox"] {
            margin-top: 3px;
            width: 16px;
            height: 16px;
            accent-color: var(--primary-color);
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

        .registration-container {
            animation: fadeIn 0.5s ease-out;
        }

        /* Responsive */
        @media (max-width: 480px) {
            .registration-container {
                padding: 2rem 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="registration-container">
        <div class="logo">
            <span class="logo-text">SS Surgical</span>
        </div>
        <h1>Create Your Account</h1>
        <p class="subtitle">Join us to access exclusive features and track your orders</p>
        
        <?php if ($error): ?>
            <div class="error">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <div id="error-message" class="error" style="display: none;">
            <i class="fas fa-exclamation-circle"></i>
            <span id="error-text"></span>
        </div>
        
        <form action="registration.php" method="POST" id="registration-form">
            <div class="form-group">
                <label for="name">Full Name</label>
                <div class="input-container">
                    <input type="text" id="name" name="name" placeholder="Enter your full name" required>
                    <i class="fas fa-user"></i>
                </div>
            </div>
            
            <div class="form-group">
                <label for="email">Email Address</label>
                <div class="input-container">
                    <input type="email" id="email" name="email" placeholder="Enter your email address" required>
                    <i class="fas fa-envelope"></i>
                </div>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <div class="input-container">
                    <input type="password" id="password" name="password" placeholder="Create a password" required>
                    <i class="fas fa-lock"></i>
                    <button type="button" class="password-toggle" id="toggle-password">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                <div class="password-strength">
                    <div class="strength-meter">
                        <div class="strength-meter-fill" id="strength-meter-fill"></div>
                    </div>
                    <div class="strength-text" id="strength-text">Password strength</div>
                </div>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <div class="input-container">
                    <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm your password" required>
                    <i class="fas fa-lock"></i>
                    <button type="button" class="password-toggle" id="toggle-confirm-password">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>
            
            <div class="policy-agreement">
                <input type="checkbox" id="agree-terms" name="agree_terms" required>
                <label for="agree-terms">I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a></label>
            </div>
            
            <button type="submit" class="register-btn">
                <i class="fas fa-user-plus"></i>
                <span>Create Account</span>
            </button>
        </form>
        
        <p class="login-link">Already have an account? <a href="login.php">Login here</a></p>
    </div>

    <script>
        // Password toggle visibility
        function setupPasswordToggle(passwordId, toggleId) {
            const passwordInput = document.getElementById(passwordId);
            const toggleButton = document.getElementById(toggleId);
            
            toggleButton.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                
                // Toggle eye icon
                this.querySelector('i').classList.toggle('fa-eye');
                this.querySelector('i').classList.toggle('fa-eye-slash');
            });
        }
        
        // Set up both password fields
        setupPasswordToggle('password', 'toggle-password');
        setupPasswordToggle('confirm_password', 'toggle-confirm-password');
        
        // Password strength meter
        const passwordInput = document.getElementById('password');
        const strengthMeter = document.getElementById('strength-meter-fill');
        const strengthText = document.getElementById('strength-text');
        
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            let strength = 0;
            let strengthLabel = '';
            
            // Calculate password strength
            if (password.length >= 8) strength += 25;
            if (password.match(/[a-z]/)) strength += 25;
            if (password.match(/[A-Z]/)) strength += 25;
            if (password.match(/[0-9]/)) strength += 15;
            if (password.match(/[^a-zA-Z0-9]/)) strength += 10;
            
            // Update strength meter
            strengthMeter.style.width = strength + '%';
            
            // Set color based on strength
            if (strength < 30) {
                strengthMeter.style.backgroundColor = '#ef4444'; // red
                strengthLabel = 'Weak';
            } else if (strength < 60) {
                strengthMeter.style.backgroundColor = '#f97316'; // orange
                strengthLabel = 'Fair';
            } else if (strength < 80) {
                strengthMeter.style.backgroundColor = '#facc15'; // yellow
                strengthLabel = 'Good';
            } else {
                strengthMeter.style.backgroundColor = '#10b981'; // green
                strengthLabel = 'Strong';
            }
            
            strengthText.textContent = password ? strengthLabel : 'Password strength';
        });
        
        // Form validation
        document.getElementById('registration-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const name = document.getElementById('name').value.trim();
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value.trim();
            const confirmPassword = document.getElementById('confirm_password').value.trim();
            const agreeTerms = document.getElementById('agree-terms').checked;
            
            const errorMessageDiv = document.getElementById('error-message');
            const errorText = document.getElementById('error-text');
            
            // Reset styles
            document.querySelectorAll('input').forEach(input => {
                input.style.borderColor = 'var(--gray-300)';
            });
            errorMessageDiv.style.display = 'none';
            
            // Validate fields
            if (!name) {
                highlightError('name', 'Please enter your name');
                return;
            }
            
            if (!email) {
                highlightError('email', 'Please enter your email address');
                return;
            }
            
            if (!isValidEmail(email)) {
                highlightError('email', 'Please enter a valid email address');
                return;
            }
            
            if (!password) {
                highlightError('password', 'Please create a password');
                return;
            }
            
            if (password.length < 6) {
                highlightError('password', 'Password must be at least 6 characters');
                return;
            }
            
            if (!confirmPassword) {
                highlightError('confirm_password', 'Please confirm your password');
                return;
            }
            
            if (password !== confirmPassword) {
                highlightError('confirm_password', 'Passwords do not match');
                document.getElementById('password').style.borderColor = 'var(--red-600)';
                return;
            }
            
            if (!agreeTerms) {
                errorText.textContent = 'You must agree to the Terms of Service and Privacy Policy';
                errorMessageDiv.style.display = 'flex';
                return;
            }
            
            // If all validations pass, submit the form
            this.submit();
        });
        
        function highlightError(fieldId, message) {
            const field = document.getElementById(fieldId);
            field.style.borderColor = 'var(--red-600)';
            field.focus();
            
            document.getElementById('error-text').textContent = message;
            document.getElementById('error-message').style.display = 'flex';
        }
        
        function isValidEmail(email) {
            return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
        }
        
        // Clear error on input
        document.querySelectorAll('input').forEach(input => {
            input.addEventListener('input', function() {
                this.style.borderColor = 'var(--gray-300)';
                document.getElementById('error-message').style.display = 'none';
            });
        });
    </script>
</body>
</html>