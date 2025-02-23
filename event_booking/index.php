<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

include 'config.php';

// Handle event creation
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_event'])) {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $date = $_POST['date'];
    $venue = trim($_POST['venue']);
    $available_seats = intval($_POST['available_seats']);
    
    if (strtotime($date) < strtotime(date("Y-m-d"))) {
        echo "<p style='color:red;'>Error: Cannot create events in the past.</p>";
    } elseif (!empty($title) && !empty($description) && !empty($venue) && $available_seats > 0) {
        $stmt = $conn->prepare("INSERT INTO events (title, description, date, venue, available_seats) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssi", $title, $description, $date, $venue, $available_seats);
        $stmt->execute();
        echo "<p style='color:green;'>Event added successfully!</p>";
    }
}

// Handle event deletion (prevent deletion if bookings exist)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_event'])) {
    $event_id = intval($_POST['event_id']);
    
    $check = $conn->prepare("SELECT COUNT(*) FROM bookings WHERE event_id = ?");
    $check->bind_param("i", $event_id);
    $check->execute();
    $check->bind_result($booking_count);
    $check->fetch();
    $check->close();
    
    if ($booking_count > 0) {
        echo "<p style='color:red;'>Cannot delete: This event has active bookings.</p>";
    } else {
        $stmt = $conn->prepare("DELETE FROM events WHERE id = ?");
        $stmt->bind_param("i", $event_id);
        $stmt->execute();
        echo "<p style='color:green;'>Event deleted successfully!</p>";
    }
}

// Handle event update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_event'])) {
    $event_id = intval($_POST['event_id']);
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $date = $_POST['date'];
    $venue = trim($_POST['venue']);
    $available_seats = intval($_POST['available_seats']);
    
    if (!empty($title) && !empty($description) && !empty($venue) && $available_seats > 0) {
        $stmt = $conn->prepare("UPDATE events SET title=?, description=?, date=?, venue=?, available_seats=? WHERE id=?");
        $stmt->bind_param("ssssii", $title, $description, $date, $venue, $available_seats, $event_id);
        $stmt->execute();
        echo "<p style='color:green;'>Event updated successfully!</p>";
    }
}

// Fetch events
$result = $conn->query("SELECT * FROM events ORDER BY date ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="styles.css">
    <script>
        function confirmDelete() {
            return confirm("Are you sure you want to delete this event?");
        }
    </script>
</head>
<body>
    <h2>Admin Panel</h2>
    <a href="logout.php">Logout</a>

    <h3>Add New Event</h3>
    <form action="admin.php" method="post">
        <label>Title:</label>
        <input type="text" name="title" required><br><br>

        <label>Description:</label>
        <textarea name="description" required></textarea><br><br>

        <label>Date:</label>
        <input type="date" name="date" required><br><br>

        <label>Venue:</label>
        <input type="text" name="venue" required><br><br>

        <label>Available Seats:</label>
        <input type="number" name="available_seats" required><br><br>

        <button type="submit" name="add_event">Add Event</button>
    </form>

    <h3>All Events</h3>
    <table border="1">
        <tr>
            <th>Title</th>
            <th>Description</th>
            <th>Date</th>
            <th>Venue</th>
            <th>Seats</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $row['title']; ?></td>
                <td><?php echo $row['description']; ?></td>
                <td><?php echo $row['date']; ?></td>
                <td><?php echo $row['venue']; ?></td>
                <td><?php echo $row['available_seats']; ?></td>
                <td>
                    <form action="admin.php" method="post" style="display:inline;" onsubmit="return confirmDelete();">
                        <input type="hidden" name="event_id" value="<?php echo $row['id']; ?>">
                        <button type="submit" name="delete_event" style="background-color:red; color:white;">Delete</button>
                    </form>
                    <a href="edit_event.php?id=<?php echo $row['id']; ?>" style="background-color:blue; color:white; padding:5px; text-decoration:none;">Edit</a>
                </td>
            </tr>
        <?php } ?>
    </table>

    <h3>View Bookings</h3>
    <table border="1">
        <tr>
            <th>User</th>
            <th>Event</th>
            <th>Booking Date</th>
        </tr>
        <?php
        $bookings = $conn->query("SELECT users.name AS user_name, events.title AS event_title, bookings.booking_date 
                                  FROM bookings 
                                  JOIN users ON bookings.user_id = users.id 
                                  JOIN events ON bookings.event_id = events.id");
        while ($booking = $bookings->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $booking['user_name']; ?></td>
                <td><?php echo $booking['event_title']; ?></td>
                <td><?php echo $booking['booking_date']; ?></td>
            </tr>
        <?php } ?>
    </table>
</body>
</html>
