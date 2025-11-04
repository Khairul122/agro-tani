<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Informasi Toko Gani Agro Tani</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2c3e50;
            --accent-color: #e74c3c;
            --light-color: #f8f9fa;
            --dark-color: #212529;
        }
        
        body {
            background-color: white;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
            margin: 0;
            padding: 20px;
            color: var(--dark-color);
        }
        
        .login-container {
            width: 100%;
            max-width: 1000px;
            display: flex;
            min-height: 700px;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            overflow: hidden;
            background: white;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.05);
        }
        
        .login-left {
            flex: 1;
            background: var(--secondary-color);
            color: white;
            padding: 50px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: flex-start;
        }
        
        .login-left h2 {
            font-size: 1.8rem;
            margin-bottom: 15px;
            font-weight: 600;
            line-height: 1.3;
        }
        
        .login-left p {
            font-size: 1rem;
            line-height: 1.6;
            margin-bottom: 30px;
            opacity: 0.9;
            max-width: 350px;
        }
        
        .feature-list {
            width: 100%;
        }
        
        .feature-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 18px;
        }
        
        .feature-item i {
            margin-right: 12px;
            font-size: 0.9rem;
            margin-top: 4px;
            color: #3498db;
            width: 20px;
        }
        
        .login-right {
            flex: 1;
            padding: 50px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: white;
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 35px;
        }
        
        .logo {
            width: 70px;
            height: 70px;
            background: var(--primary-color);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            color: white;
            font-size: 1.8rem;
            font-weight: bold;
            box-shadow: 0 4px 12px rgba(52, 152, 219, 0.25);
        }
        
        .login-header h3 {
            color: var(--secondary-color);
            font-size: 1.4rem;
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .login-header p {
            color: #6c757d;
            font-size: 0.95rem;
            margin: 0;
        }
        
        .form-group {
            margin-bottom: 22px;
        }
        
        .form-label {
            font-weight: 500;
            color: var(--secondary-color);
            margin-bottom: 7px;
            font-size: 0.9rem;
        }
        
        .form-control {
            border: 1px solid #ced4da;
            border-radius: 6px;
            padding: 12px 15px;
            font-size: 1rem;
            transition: all 0.2s ease;
            width: 100%;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(52, 152, 219, 0.15);
        }
        
        .input-group {
            position: relative;
        }
        
        .btn-login {
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 6px;
            padding: 12px;
            font-size: 1rem;
            font-weight: 500;
            width: 100%;
            transition: all 0.2s ease;
            margin-top: 5px;
        }
        
        .btn-login:hover {
            background: #2980b9;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(52, 152, 219, 0.25);
        }
        
        .password-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #6c757d;
        }
        
        .role-info {
            background: var(--light-color);
            border-radius: 6px;
            padding: 18px;
            margin-top: 25px;
            border: 1px solid #e9ecef;
        }
        
        .role-info h5 {
            color: var(--secondary-color);
            margin-bottom: 12px;
            text-align: center;
            font-size: 1rem;
            font-weight: 600;
        }
        
        .role-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 8px;
            font-size: 0.85rem;
        }
        
        .role-item i {
            margin-right: 8px;
            color: var(--primary-color);
            font-size: 0.8rem;
            margin-top: 3px;
            width: 18px;
        }
        
        .footer-text {
            text-align: center;
            color: #6c757d;
            font-size: 0.8rem;
            margin-top: 25px;
            font-weight: 400;
        }
        
        .input-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
            z-index: 10;
        }
        
        .input-add-on {
            position: relative;
        }
        
        .input-add-on .form-control {
            padding-left: 40px;
        }
        
        @media (max-width: 992px) {
            .login-container {
                flex-direction: column;
                min-height: auto;
                max-width: 500px;
            }
            
            .login-left {
                padding: 30px;
                align-items: center;
                text-align: center;
            }
            
            .login-right {
                padding: 40px 30px;
            }
            
            .login-left p {
                max-width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-left">
            <h2>Sistem Informasi Toko Gani Agro Tani</h2>
            
            <div class="feature-list">
              
            </div>
        </div>
        
        <div class="login-right">
            <div class="login-header">
                <div class="logo">GA</div>
                <h3>Rekap Data Penjualan & Stok Barang</h3>
                <p>Sistem Informasi Toko Gani Agro Tani</p>
            </div>
            
            <?php if (isset($error)): ?>
                <script>
                    alert('<?php echo $error; ?>');
                </script>
            <?php endif; ?>
            
            <form method="POST" action="index.php?controller=auth&action=login">
                <div class="form-group">
                    <label for="username" class="form-label">Username</label>
                    <div class="input-add-on">
                        <span class="input-icon"><i class="fas fa-user"></i></span>
                        <input type="text" class="form-control" id="username" name="username" 
                               placeholder="Masukkan username Anda" required autofocus>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-add-on">
                        <span class="input-icon"><i class="fas fa-lock"></i></span>
                        <input type="password" class="form-control" id="password" name="password" 
                               placeholder="Masukkan password Anda" required>
                        <span class="password-toggle" onclick="togglePassword()">
                            <i class="fas fa-eye"></i>
                        </span>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-login">Login</button>
            </form>
            
            
            <div class="footer-text">
                <p>&copy; 2025 Toko Gani Agro Tani. Hak Cipta Dilindungi.</p>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Font Awesome JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
    
    <!-- Custom JS -->
    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const passwordToggle = document.querySelector('.password-toggle i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordToggle.classList.remove('fa-eye');
                passwordToggle.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                passwordToggle.classList.remove('fa-eye-slash');
                passwordToggle.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>