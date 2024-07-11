<?php

include 'connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $email = $_POST['email'];

  // Prepared statement with parameter binding for email
  $stmt = $conn->prepare("DELETE FROM subscribers WHERE email = ?");
  $stmt->bind_param("s", $email);

  // Execute the statement and handle potential errors
  if ($stmt->execute()) {
    echo "Unsubscribed";
  } else {
    echo "Error unsubscribing: " . $conn->error;
  }

  $stmt->close(); // Close the prepared statement
  header("Location: unsubscribe.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
</head>
<body>
  <h1>Unsubscribe form</h1>
  <form action="" method="POST">
    <input type="email" name="email" id="" required placeholder="Enter your email">
    <input type="submit" value="Submit">
  </form>
</body>
</html>
