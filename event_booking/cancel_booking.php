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

    // Check if the user has booked this event
    $checkBooking = $conn->prepare("SELECT id FROM bookings WHERE user_id = ? AND event_id = ?");
    $checkBooking->bind_param("ii", $user_id, $event_id);
    $checkBooking->execute();
    $checkBooking->store_result();

    if ($checkBooking->num_rows == 0) {
        header("Location: events.php?error=You have not booked this event");
        exit;
    }

    // Delete the booking
    $deleteBooking = $conn->prepare("DELETE FROM bookings WHERE user_id = ? AND event_id = ?");
    $deleteBooking->bind_param("ii", $user_id, $event_id);

    if ($deleteBooking->execute()) {
        // Increase available seats
        $updateSeats = $conn->prepare("UPDATE events SET available_seats = available_seats + 1 WHERE id = ?");
        $updateSeats->bind_param("i", $event_id);
        $updateSeats->execute();

        // Redirect with success message
        header("Location: events.php?message=Booking canceled successfully");
        exit;
    } else {
        header("Location: events.php?error=Error canceling booking");
        exit;
    }
}
?>
