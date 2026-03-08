<?php
session_start();
if (isset($_SESSION['admin_logged_in'])) {
    header("Location: index.php");
    exit();
}

$error = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $admin_user = 'warungerik';
    $admin_pass = 'Advan.ku123'; 

    if ($_POST['username'] == $admin_user && $_POST['password'] == $admin_pass) {
        $_SESSION['admin_logged_in'] = true;
        header("Location: index.php");
        exit();
    } else {
        $error = "Username atau password salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin — AdminStore</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        :root {
            --bg:        #f7f5f2;
            --surface:   #ffffff;
            --surface2:  #faf9f7;
            --border:    #e8e4de;
            --border2:   #f0ede8;
            --text:      #1a1714;
            --muted:     #8c8279;
            --muted2:    #b5afa7;
            --accent:    #c17f3e;
            --accent-bg: rgba(193,127,62,0.08);
            --accent-border: rgba(193,127,62,0.25);
            --success:   #2d7a4f;
            --success-bg: rgba(45,122,79,0.08);
            --info:      #2563a8;
            --info-bg:   rgba(37,99,168,0.08);
            --warn:      #b45309;
            --warn-bg:   rgba(180,83,9,0.08);
            --danger:    #b91c1c;
            --danger-bg: rgba(185,28,28,0.08);
            --font: 'Plus Jakarta Sans', sans-serif;
            --mono: 'JetBrains Mono', monospace;
            --radius: 14px;
            --shadow: 0 1px 3px rgba(0,0,0,0.06), 0 4px 16px rgba(0,0,0,0.04);
            --shadow-lg: 0 2px 8px rgba(0,0,0,0.08), 0 12px 32px rgba(0,0,0,0.06);
        }

        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: var(--font);
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            overflow: hidden;
        }

        /* Background decoration */
        body::before {
            content: '';
            position: fixed;
            top: -50%;
            right: -20%;
            width: 800px;
            height: 800px;
            background: radial-gradient(circle, rgba(193,127,62,0.08) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
            animation: float 20s ease-in-out infinite;
        }

        body::after {
            content: '';
            position: fixed;
            bottom: -30%;
            left: -10%;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(193,127,62,0.05) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
            animation: float 15s ease-in-out infinite reverse;
        }

        @keyframes float {
            0%, 100% { transform: translate(0, 0) rotate(0deg); }
            50% { transform: translate(-30px, -30px) rotate(5deg); }
        }

        /* Login container */
        .login-wrapper {
            width: 100%;
            max-width: 440px;
            position: relative;
            z-index: 1;
            animation: fadeUp 0.6s ease both;
        }

        /* Logo & Branding */
        .login-brand {
            text-align: center;
            margin-bottom: 32px;
        }

        .logo-mark {
            width: 64px;
            height: 64px;
            background: linear-gradient(135deg, var(--accent), #e09b55);
            border-radius: 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 28px;
            margin-bottom: 16px;
            box-shadow: 0 8px 24px rgba(193,127,62,0.35);
            animation: logoFloat 3s ease-in-out infinite;
        }

        @keyframes logoFloat {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-8px); }
        }

        .brand-name {
            font-weight: 800;
            font-size: 24px;
            letter-spacing: -0.02em;
            margin-bottom: 4px;
        }

        .brand-sub {
            font-size: 13px;
            color: var(--muted);
            font-weight: 500;
        }

        /* Login form card */
        .login-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 40px;
            box-shadow: var(--shadow-lg);
            position: relative;
            overflow: hidden;
        }

        .login-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--accent), #e09b55);
        }

        .login-title {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 8px;
            text-align: center;
        }

        .login-subtitle {
            font-size: 13px;
            color: var(--muted);
            text-align: center;
            margin-bottom: 32px;
        }

        /* Error message */
        .error-message {
            background: var(--danger-bg);
            border: 1px solid rgba(185,28,28,0.2);
            color: var(--danger);
            padding: 12px 16px;
            border-radius: 10px;
            margin-bottom: 24px;
            font-size: 13px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: shake 0.5s ease;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-8px); }
            75% { transform: translateX(8px); }
        }

        .error-message i {
            font-size: 16px;
        }

        /* Form group */
        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 8px;
        }

        .input-wrapper {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--muted2);
            font-size: 14px;
            pointer-events: none;
            transition: color 0.15s;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 14px 14px 14px 42px;
            background: var(--bg);
            border: 1px solid var(--border);
            border-radius: 10px;
            font-family: var(--font);
            font-size: 14px;
            color: var(--text);
            transition: all 0.15s;
            outline: none;
        }

        input::placeholder {
            color: var(--muted2);
        }

        input:focus {
            background: var(--surface);
            border-color: var(--accent-border);
            box-shadow: 0 0 0 3px var(--accent-bg);
        }

        input:focus + .input-icon {
            color: var(--accent);
        }

        /* Login button */
        .btn-login {
            width: 100%;
            padding: 15px;
            background: var(--accent);
            color: white;
            border: none;
            border-radius: 10px;
            font-family: var(--font);
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
            box-shadow: 0 4px 14px rgba(193,127,62,0.3);
            margin-top: 8px;
        }

        .btn-login:hover {
            background: #a96d31;
            box-shadow: 0 6px 20px rgba(193,127,62,0.4);
            transform: translateY(-2px);
        }

        .btn-login:active {
            transform: translateY(0);
            box-shadow: 0 2px 8px rgba(193,127,62,0.3);
        }

        /* Footer text */
        .login-footer {
            text-align: center;
            margin-top: 24px;
            font-size: 12px;
            color: var(--muted);
        }

        .login-footer a {
            color: var(--accent);
            text-decoration: none;
            font-weight: 600;
        }

        .login-footer a:hover {
            text-decoration: underline;
        }

        /* Animations */
        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive */
        @media (max-width: 480px) {
            .login-card {
                padding: 28px 24px;
            }

            .logo-mark {
                width: 56px;
                height: 56px;
                font-size: 24px;
            }

            .brand-name {
                font-size: 20px;
            }

            .login-title {
                font-size: 18px;
            }
        }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <!-- Branding -->
        <div class="login-brand">
            <div class="logo-mark">
                <i class="fas fa-store"></i>
            </div>
            <div class="brand-name">DASHBOARD ADMIN</div>
            <div class="brand-sub">Panel Manajemen</div>
        </div>

        <!-- Login Card -->
        <div class="login-card">
            <h2 class="login-title">Selamat Datang Kembali</h2>
            <p class="login-subtitle">Masuk untuk mengakses dashboard admin</p>

            <?php if ($error): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i>
                    <span><?php echo htmlspecialchars($error); ?></span>
                </div>
            <?php endif; ?>

            <form method="post">
                <div class="form-group">
                    <label class="form-label" for="username">Username</label>
                    <div class="input-wrapper">
                        <input 
                            type="text" 
                            id="username" 
                            name="username" 
                            placeholder="Masukkan username" 
                            required 
                            autofocus
                        >
                        <i class="fas fa-user input-icon"></i>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <div class="input-wrapper">
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            placeholder="Masukkan password" 
                            required
                        >
                        <i class="fas fa-lock input-icon"></i>
                    </div>
                </div>

                <button type="submit" class="btn-login">
                    <i class="fas fa-sign-in-alt" style="margin-right: 8px;"></i>
                    Masuk ke Dashboard
                </button>
            </form>

            <div class="login-footer">
                Membutuhkan bantuan? <a href="https://wa.me/6285183129647">Hubungi Support</a>
            </div>
        </div>
    </div>
</body>
</html>