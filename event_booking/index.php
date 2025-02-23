<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Event Booking</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            background: linear-gradient(135deg, #ff7eb3, #ff758c);
            color: white;
            margin: 0;
            padding: 0;
        }
        .container {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        h1 {
            font-size: 3rem;
            margin-bottom: 20px;
        }
        p {
            font-size: 1.2rem;
            max-width: 600px;
            margin-bottom: 30px;
        }
        .btn {
            padding: 12px 24px;
            font-size: 1.2rem;
            color: white;
            background-color: #ff4757;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            transition: 0.3s;
            margin: 10px;
        }
        .btn:hover {
            background-color: #e84118;
        }
        .btn-container {
            display: flex;
            gap: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Welcome to Event Booking</h1>
        <p>Your one-stop solution for booking and managing events effortlessly.</p>
        <div class="btn-container">
            <a href="login.php" class="btn">Login</a>
            <a href="register.php" class="btn">Register</a>
            <a href="events.php" class="btn">View Events</a>
        </div>
    </div>
</body>
</html>
