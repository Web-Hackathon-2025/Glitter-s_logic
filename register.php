<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Karigar</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <style>
        body, html { height: 100%; margin: 0; }
        .register-container {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background: linear-gradient(135deg, #1e0533, #2a0a4a);
        }
        .register-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
            overflow: hidden;
            max-width: 900px;
            width: 100%;
        }
        .illustration-side {
            background: #12052a url('https://images.unsplash.com/photo-1522202176988-66273c2fd55f?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80') center/cover no-repeat;
            color: white;
            padding: 60px;
        }
        .illustration-side h2 { font-size: 2.5rem; font-weight: bold; }
        .form-side { padding: 60px; }
        .form-side .btn-primary {
            background: linear-gradient(to right, #6a11cb, #2575fc);
            border: none;
        }
        @media (max-width: 768px) {
            .illustration-side { display: none; }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-card row no-gutters">
            <div class="col-md-6 illustration-side">
                <h2>Join Karigar Today!</h2>
                <p>Register as a Customer to book services or as a Service Provider to offer your skills.</p>
            </div>
            
            <div class="col-md-6 form-side">
                <h3 class="text-center mb-4">Sign Up</h3>
                <form id="registerForm">
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Confirm Password</label>
                        <input type="password" name="confirm_password" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Phone Number</label>
                        <input type="tel" name="phone" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Register As</label>
                        <select name="role" class="form-control" required>
                            <option value="customer">Customer</option>
                            <option value="provider">Service Provider</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Sign Up</button>
                </form>
                <p class="text-center mt-3">Already have an account? <a href="login.html">Sign In</a></p>
                <p class="text-center"><a href="../index.html">Back to Home</a></p>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script>
        $('#registerForm').on('submit', function(e) {
            e.preventDefault();
            alert('Frontend only. Connect to your backend registration API.');
        });
    </script>
</body>
</html>
<script>
    $.ajax({
    url: '../backend/register_process.php'',  // Correct path: frontend se backend folder
    type: 'POST',
    data: $(this).serialize(),
    dataType: 'json',
    // ... baaki code same
});
</script>