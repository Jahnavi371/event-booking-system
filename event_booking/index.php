<?php
include 'config.php';
session_start();

$result = $conn->query("SELECT * FROM events");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Event Booking</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h2>Available Events</h2>
    <ul>
        <?php while ($row = $result->fetch_assoc()): ?>
            <li>
                <h3><?php echo $row['title']; ?></h3>
                <p><?php echo $row['description']; ?></p>
                <p>Date: <?php echo $row['date']; ?></p>
                <p>Venue: <?php echo $row['venue']; ?></p>
                <p>Seats Available: <?php echo $row['available_seats']; ?></p>
                <?php if ($row['available_seats'] > 0): ?>
                    <button class="book-btn" data-event="<?php echo $row['id']; ?>">Book Now</button>
                <?php else: ?>
                    <span>Sold Out</span>
                <?php endif; ?>
            </li>
        <?php endwhile; ?>
    </ul>

    <script src="js/main.js"></script>
</body>
</html>
