<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

include 'config.php';

// Handle event creation
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_event'])) {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $date = $_POST['date'];
    $venue = trim($_POST['venue']);
    $available_seats = intval($_POST['available_seats']);

    if (!empty($title) && !empty($description) && !empty($venue) && $available_seats > 0) {
        $stmt = $conn->prepare("INSERT INTO events (title, description, date, venue, available_seats) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssi", $title, $description, $date, $venue, $available_seats);

        if ($stmt->execute()) {
            echo "<p class='success'>Event added successfully!</p>";
        } else {
            echo "<p class='error'>Error: " . $stmt->error . "</p>";
        }
    }
}

// Handle event deletion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_event'])) {
    $event_id = intval($_POST['event_id']);

    $stmt = $conn->prepare("DELETE FROM events WHERE id = ?");
    $stmt->bind_param("i", $event_id);

    if ($stmt->execute()) {
        echo "<p class='success'>Event deleted successfully!</p>";
    } else {
        echo "<p class='error'>Error: " . $stmt->error . "</p>";
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

        if ($stmt->execute()) {
            echo "<p class='success'>Event updated successfully!</p>";
        } else {
            echo "<p class='error'>Error: " . $stmt->error . "</p>";
        }
    }
}

// Fetch events
$stmt = $conn->prepare("SELECT * FROM events ORDER BY date ASC");
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="admin.css">
    <script>
        function confirmDelete(eventId) {
            if (confirm("Are you sure you want to delete this event?")) {
                document.getElementById('delete_form_' + eventId).submit();
            }
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
    <table>
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
                <td><?php echo htmlspecialchars($row['title']); ?></td>
                <td><?php echo htmlspecialchars($row['description']); ?></td>
                <td><?php echo htmlspecialchars($row['date']); ?></td>
                <td><?php echo htmlspecialchars($row['venue']); ?></td>
                <td><?php echo htmlspecialchars($row['available_seats']); ?></td>
                <td>
                    <!-- Delete Event -->
                    <form action="admin.php" method="post" id="delete_form_<?php echo $row['id']; ?>" style="display:inline;">
                        <input type="hidden" name="event_id" value="<?php echo $row['id']; ?>">
                        <button type="button" onclick="confirmDelete(<?php echo $row['id']; ?>)" style="background-color:red; color:white;">Delete</button>
                    </form>
                    
                    <!-- Edit Event -->
                    <form action="admin.php" method="post" style="display:inline;">
                        <input type="hidden" name="event_id" value="<?php echo $row['id']; ?>">
                        <input type="text" name="title" value="<?php echo htmlspecialchars($row['title']); ?>" required>
                        <input type="text" name="description" value="<?php echo htmlspecialchars($row['description']); ?>" required>
                        <input type="date" name="date" value="<?php echo htmlspecialchars($row['date']); ?>" required>
                        <input type="text" name="venue" value="<?php echo htmlspecialchars($row['venue']); ?>" required>
                        <input type="number" name="available_seats" value="<?php echo htmlspecialchars($row['available_seats']); ?>" required>
                        <button type="submit" name="edit_event" style="background-color:blue; color:white;">Update</button>
                    </form>
                </td>
            </tr>
        <?php } ?>
    </table>
</body>
</html>
