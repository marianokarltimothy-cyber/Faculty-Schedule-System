<?php
session_start();

// Redirect to dashboard if already logged in
if(isset($_SESSION['faculty_logged_in'])) {
    header("Location: index.php");
    exit();
}

$error = "";

// Security Credentials check
if(isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // 1. First Account
    $account1_email = "dccwebsitesa@gmail.com";
    $account1_pass  = "DCCF@culty2023!";

    // 2. Second Account (The one you just provided)
    $account2_email = "dccitservicesoffice@gmail.com";
    $account2_pass  = "Est.2023@";

    // Validating against multiple credentials
    if(($email === $account1_email && $password === $account1_pass) || 
       ($email === $account2_email && $password === $account2_pass)) {
        
        $_SESSION['faculty_logged_in'] = true;
        header("Location: index.php");
        exit();
    } else {
        $error = "Invalid email or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Login - SchedPro</title>
    <style>
        /* Base Page Layout */
        body { 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            height: 100vh; 
            margin: 0;
            padding: 0;
            background-image: url('background.jpg'); 
            background-size: cover; 
            background-position: center;
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            overflow: hidden;
        }

        /* Glassmorphism Card Design */
        .login-card { 
            background: rgba(255, 255, 255, 0.15); 
            backdrop-filter: blur(15px); 
            -webkit-backdrop-filter: blur(15px); 
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 45px 40px; 
            border-radius: 24px; 
            width: 360px; 
            box-shadow: 0 20px 40px rgba(0,0,0,0.3); 
            text-align: center;
            z-index: 10;
            animation: fadeInUp 0.8s ease-out; /* Entrance Animation */
        }

        .login-card h2 {
            color: #ffffff;
            font-size: 1.8rem;
            margin-bottom: 25px;
            text-shadow: 0 2px 10px rgba(0,0,0,0.3);
        }

        /* Input Styling with transitions */
        .login-input {
            width: 100%; 
            padding: 14px; 
            margin: 12px 0; 
            background: rgba(255, 255, 255, 0.9); 
            border: 1px solid transparent; 
            border-radius: 10px;
            box-sizing: border-box;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .login-input:focus {
            outline: none;
            background: #ffffff;
            transform: scale(1.02);
            box-shadow: 0 0 15px rgba(255, 255, 255, 0.4);
        }

        /* Animated Button */
        .login-btn {
            width: 100%; 
            margin-top: 20px; 
            padding: 14px; 
            cursor: pointer;
            background-color: #2563eb; 
            color: white;
            border: none;
            border-radius: 10px;
            font-weight: 700;
            font-size: 1rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(37, 99, 235, 0.3);
        }

        .login-btn:hover {
            background-color: #1d4ed8;
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.4);
        }

        .login-btn:active {
            transform: translateY(0);
        }

        /* Error Messaging */
        .error-box {
            background: rgba(239, 68, 68, 0.2);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #ffcccc;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 15px;
            font-size: 0.9rem;
            animation: shake 0.4s ease;
        }

        /* Animations */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(40px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }
    </style>
</head>
<body>

    <div class="login-card">
        <!-- Logo Image -->
        <img src="logo.jpg" alt="DCC Logo" style="width: 90px; height: 90px; margin-bottom: 15px; border-radius: 50%; border: 3px solid rgba(255,255,255,0.3); object-fit: cover;">
        
        <h2>DCC Faculty Login</h2>
        
        <?php if($error): ?>
            <div class="error-box"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST">
            <input type="email" name="email" class="login-input" placeholder="Email Address" required>
            <input type="password" name="password" class="login-input" placeholder="Password" required>
            <button type="submit" name="login" class="login-btn">Sign In</button>
        </form>
    </div>

</body>
</html>