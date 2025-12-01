<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register - ShopSphere</title>
  <style>
    body {
      font-family: 'Helvetica Neue', Arial, sans-serif;
      background-color: #fafafa;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: flex-start;
      min-height: 100vh;
      margin: 0;
    }

    /* Header */
    .header {
      background-color: #ffffff;
      color: #2e5d34;
      width: 100%;
      text-align: center;
      padding: 25px 0;
      border-bottom: 1px solid #e0e0e0;
      position: sticky;
      top: 0;
      box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }

    .header h1 {
      font-family: 'Georgia', serif;
      margin: 0;
      font-size: 2em;
    }

    .header p {
      font-size: 1em;
      margin-top: 5px;
      color: #2e5d34;
    }

    /* Register form */
    .register-form {
      background: white;
      padding: 35px;
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.08);
      width: 100%;
      max-width: 420px;
      margin-top: 60px;
    }

    .register-form h2 {
      text-align: center;
      color: #2e5d34;
      font-family: 'Georgia', serif;
      margin-bottom: 20px;
    }

    .form-group {
      margin-bottom: 18px;
    }

    label {
      display: block;
      margin-bottom: 6px;
      font-weight: bold;
      color: #2e5d34;
    }

    input[type="text"],
    input[type="email"],
    input[type="password"] {
      width: 100%;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 4px;
      box-sizing: border-box;
      font-size: 15px;
    }

    .btn {
      background-color: #2e5d34;
      color: white;
      padding: 12px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      width: 100%;
      font-size: 16px;
      font-weight: bold;
      transition: background-color 0.3s;
    }

    .btn:hover {
      background-color: #244928;
    }

    .error {
      color: #a94442;
      background-color: #f8d7da;
      border: 1px solid #f5c6cb;
      border-radius: 4px;
      padding: 10px;
      margin-bottom: 15px;
      text-align: center;
    }

    p.link {
      text-align: center;
      margin-top: 15px;
    }

    p.link a {
      color: #2e5d34;
      font-weight: bold;
      text-decoration: none;
    }

    p.link a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>

  <div class="header">
    <h1>ShopSphere</h1>
    <p>Create Your Account</p>
  </div>

  <div class="register-form">
    <h2>Register</h2>

    <!-- Display error message if present -->
    <?php if (isset($_GET['error'])): ?>
      <div class="error">
        <?php echo htmlspecialchars($_GET['error']); ?>
      </div>
    <?php endif; ?>

    <!-- Registration Form -->
    <form method="post" action="process_register.php">
      <div class="form-group">
        <label>Full Name *</label>
        <input type="text" name="name" placeholder="Enter your full name" required>
      </div>

      <div class="form-group">
        <label>Email *</label>
        <input type="email" name="email" placeholder="Enter your email" required>
      </div>

      <div class="form-group">
        <label>Password *</label>
        <input type="password" name="password" placeholder="Enter your password" required>
      </div>

      <div class="form-group">
        <input type="submit" class="btn" value="Register">
      </div>
    </form>

    <p class="link"><a href="index.php">← Back to Home</a></p>
  </div>

</body>
</html>
