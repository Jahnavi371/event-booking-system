<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $event_id = $_POST['event_id'];

    // Check if the user has already booked this event
    $checkBooking = $conn->prepare("SELECT id FROM bookings WHERE user_id = ? AND event_id = ?");
    $checkBooking->bind_param("ii", $user_id, $event_id);
    $checkBooking->execute();
    $checkBooking->store_result();

    if ($checkBooking->num_rows > 0) {
        echo "<p style='color:red;'>You have already booked this event!</p>";
        echo "<a href='events.php'>Back to Events</a>";
        exit;
    }

    // Check available seats
    $checkSeats = $conn->prepare("SELECT available_seats FROM events WHERE id = ?");
    $checkSeats->bind_param("i", $event_id);
    $checkSeats->execute();
    $result = $checkSeats->get_result();
    $event = $result->fetch_assoc();

    if ($event['available_seats'] > 0) {
        // Reduce available seats
        $updateSeats = $conn->prepare("UPDATE events SET available_seats = available_seats - 1 WHERE id = ?");
        $updateSeats->bind_param("i", $event_id);
        $updateSeats->execute();

        // Insert booking
        $bookEvent = $conn->prepare("INSERT INTO bookings (user_id, event_id) VALUES (?, ?)");
        $bookEvent->bind_param("ii", $user_id, $event_id);

        if ($bookEvent->execute()) {
            echo "<p style='color:green;'>Booking successful!</p>";
        } else {
            echo "<p style='color:red;'>Error: " . $bookEvent->error . "</p>";
        }
    } else {
        echo "<p style='color:red;'>Sorry, no seats available!</p>";
    }
}

echo "<a href='events.php'>Back to Events</a>";
?>
