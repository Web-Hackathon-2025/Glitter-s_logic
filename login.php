<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Karigar</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <style>
        body, html { height: 100%; margin: 0; }
        .login-container {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background: linear-gradient(135deg, #1e0533, #2a0a4a);
        }
        .login-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
            overflow: hidden;
            max-width: 900px;
            width: 100%;
        }
        .illustration-side {
            background: #12052a url('https://images.unsplash.com/photo-1556742111-72d5e3e4b1d6?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80') center/cover no-repeat;
            color: white;
            padding: 60px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .illustration-side h2 { font-size: 2.5rem; font-weight: bold; }
        .form-side { padding: 60px; }
        .form-side .btn-primary {
            background: linear-gradient(to right, #6a11cb, #2575fc);
            border: none;
        }
        @media (max-width: 768px) {
            .illustration-side { display: none; }
            .login-card { margin: 20px; }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card row no-gutters">
            <div class="col-md-6 illustration-side">
                <h2>Welcome Back to Karigar!</h2>
                <p>Connect with reliable local service providers — plumbers, electricians, tutors, and more.</p>
                <p>Login as Customer, Service Provider, or Admin.</p>
            </div>
            
            <div class="col-md-6 form-side">
                <h3 class="text-center mb-4">Sign In</h3>
                <form id="loginForm">
                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Login As</label>
                        <select name="role" class="form-control" required>
                            <option value="customer">Customer</option>
                            <option value="provider">Service Provider</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Sign In</button>
                </form>
                <p class="text-center mt-3">Don't have an account? <a href="register.html">Sign Up</a></p>
                <p class="text-center"><a href="../index.html">Back to Home</a></p>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script>
        $('#loginForm').on('submit', function(e) {
            e.preventDefault();
            alert('This is frontend only. Connect to your backend API here.');
            // Example: $.post('backend/auth.php', $(this).serialize(), function(res){...});
        });
    </script>
</body>
</html>
<script>
$.ajax({
    url: '../backend/login_process.php',  // Correct path: frontend se backend folder
    type: 'POST',
    data: $(this).serialize(),
    dataType: 'json',
    // ... baaki code same
});
</script>
