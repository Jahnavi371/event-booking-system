<?php
session_start();
include 'config.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch events
$result = $conn->query("SELECT * FROM events WHERE available_seats > 0 ORDER BY date ASC");

// Handle event registration
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['book_event'])) {
    $event_id = intval($_POST['event_id']);

    // Check if the user has already booked the event
    $checkBooking = $conn->prepare("SELECT * FROM bookings WHERE user_id = ? AND event_id = ?");
    $checkBooking->bind_param("ii", $user_id, $event_id);
    $checkBooking->execute();
    $checkBookingResult = $checkBooking->get_result();

    if ($checkBookingResult->num_rows > 0) {
        $error = "❌ You have already booked this event.";
    } else {
        // Book the event (insert into bookings table)
        $stmt = $conn->prepare("INSERT INTO bookings (user_id, event_id, booking_date) VALUES (?, ?, NOW())");
        $stmt->bind_param("ii", $user_id, $event_id);
        if ($stmt->execute()) {
            // Reduce available seats in the events table
            $stmtUpdateSeats = $conn->prepare("UPDATE events SET available_seats = available_seats - 1 WHERE id = ?");
            $stmtUpdateSeats->bind_param("i", $event_id);
            $stmtUpdateSeats->execute();

            echo "<p style='color:green;'>Event booked successfully!</p>";
        } else {
            $error = "❌ Error booking the event.";
        }
    }
}

// Handle cancel booking
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['cancel_booking'])) {
    $event_id = intval($_POST['event_id']);

    // Remove the booking from the bookings table
    $cancelBooking = $conn->prepare("DELETE FROM bookings WHERE user_id = ? AND event_id = ?");
    $cancelBooking->bind_param("ii", $user_id, $event_id);
    
    if ($cancelBooking->execute()) {
        // Increase available seats in the events table
        $stmtUpdateSeats = $conn->prepare("UPDATE events SET available_seats = available_seats + 1 WHERE id = ?");
        $stmtUpdateSeats->bind_param("i", $event_id);
        $stmtUpdateSeats->execute();

        echo "<p style='color:green;'>Booking canceled successfully!</p>";
    } else {
        $error = "❌ Error canceling the booking.";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h2>Welcome, <?php echo $_SESSION['user_name']; ?>!</h2>
    <a href="logout.php">Logout</a>

    <h3>Available Events</h3>

    <?php if (isset($error)) { echo "<p style='color:red;'>$error</p>"; } ?>

    <table border="1">
        <tr>
            <th>Title</th>
            <th>Description</th>
            <th>Date</th>
            <th>Venue</th>
            <th>Available Seats</th>
            <th>Action</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $row['title']; ?></td>
                <td><?php echo $row['description']; ?></td>
                <td><?php echo $row['date']; ?></td>
                <td><?php echo $row['venue']; ?></td>
                <td><?php echo $row['available_seats']; ?></td>
                <td>
                    <?php
                    // Check if the user has already booked this event
                    $checkBooking = $conn->prepare("SELECT * FROM bookings WHERE user_id = ? AND event_id = ?");
                    $checkBooking->bind_param("ii", $user_id, $row['id']);
                    $checkBooking->execute();
                    $checkBookingResult = $checkBooking->get_result();

                    if ($checkBookingResult->num_rows > 0) { ?>
                        <!-- Display cancel button if the user has already booked this event -->
                        <form action="events.php" method="post" style="display:inline;">
                            <input type="hidden" name="event_id" value="<?php echo $row['id']; ?>">
                            <button type="submit" name="cancel_booking" style="background-color:red; color:white;">Cancel Booking</button>
                        </form>
                    <?php } elseif ($row['available_seats'] > 0) { ?>
                        <!-- Display book button if the user hasn't booked and seats are available -->
                        <form action="events.php" method="post" style="display:inline;">
                            <input type="hidden" name="event_id" value="<?php echo $row['id']; ?>">
                            <button type="submit" name="book_event">Book</button>
                        </form>
                    <?php } else { ?>
                        <span>Sold Out</span>
                    <?php } ?>
                </td>
            </tr>
        <?php } ?>
    </table>
</body>
</html>
