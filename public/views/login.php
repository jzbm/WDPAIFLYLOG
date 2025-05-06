<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/8fd9367667.js" crossorigin="anonymous"></script>
    <link href="/public/styles/main.css" rel="stylesheet">
    <link href="/public/styles/auth.css" rel="stylesheet">


    <title>LOGIN</title>
</head>
<body class="auth-page">
    <div class="auth-container">
        <h1>LOGIN</h1>
        <p>Don't have an account? <a href="/register">Create one</a></p>
        <form method="POST" action="/login">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">SIGN IN</button>
            <?php if (!empty($error)): ?>
                <p class="error"><?php echo $error; ?></p>
            <?php endif; ?>
        </form>
        <p><a href="/dashboard">Go to Main Page</a></p>
    </div>
</body>

</html>
