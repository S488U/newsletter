<?php
session_start();
if (!isset($_SESSION['admin'])) {
  header("Location: admin_login.php");
  exit();
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';
include 'connection.php';

// Adjust maximum execution time (if needed)
ini_set('max_execution_time', 600); // 10 minutes

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $subject = $_POST['subject'];
  $htmlBody = $_POST['htmlBody'];

  // Initialize variables for logging
  $logFile = 'email_log.txt';
  $logData = '';

  // Initialize PHPMailer
  $mail = new PHPMailer(true);
  try {
    // SMTP Configuration
    $mail->isSMTP();
    $mail->Host = 'smtpout.secureserver.net';
    $mail->SMTPAuth = true;
    $mail->Username = 'info@fetlla.com';
    $mail->Password = 'Fet!@2024$Secure&Dev';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port = 465;

    // Sender information
    $mail->setFrom('info@fetlla.com', 'Fetlla');
    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body = $htmlBody;

    // Fetch recipients from database
    $sql = "SELECT DISTINCT email FROM subscribers";
    $result = $conn->query($sql);

    // Batch sending configuration
    $batchSize = 10; // Number of emails to send per batch
    $delaySeconds = 1; // Delay in seconds between batches

    // Loop through recipients and send emails in batches
    $count = 0; // Counter for batch
    while ($row = $result->fetch_assoc()) {
      $email = $row['email'];

      // Validate email address
      if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Log invalid email address
        logEmailStatus($logFile, $email, 'Invalid', 'Invalid email address format');
        continue; // Skip invalid email addresses
      }

      // Add recipient to PHPMailer
      $mail->addAddress($email);

      // Attempt to send email
      try {
        $mail->send();
        logEmailStatus($logFile, $email, 'Sent', 'Email sent successfully');
      } catch (Exception $e) {
        logEmailStatus($logFile, $email, 'Failed', "Error: {$mail->ErrorInfo}");
      }

      // Clear recipients and reset PHPMailer
      $mail->clearAddresses();
      $mail->clearAllRecipients();
      $mail->Subject = $subject;
      $mail->Body = $htmlBody;

      // Increment batch counter
      $count++;

      // Check if reached batch size or end of recipients
      if ($count % $batchSize == 0 || $count == $result->num_rows) {
        // Delay to avoid hitting rate limits
        sleep($delaySeconds);
      }
    }

    // Success message and redirection
    echo "Newsletter sent successfully!";
    header("Location: dashboard.php");
    exit();
  } catch (Exception $e) {
    // Error message
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
  }
}

// Function to log email status with formatted timestamp and email address
function logEmailStatus($file, $email, $status, $message) {
  $timestamp = date('Y-m-d h:i:s A'); // Format timestamp as YYYY-MM-DD hh:mm:ss AM/PM
  $emailFormatted = str_pad($email, 40); // Ensure email address takes 40 characters
  $logData = "[$timestamp] [$status] $emailFormatted: $message" . PHP_EOL;
  file_put_contents($file, $logData, FILE_APPEND);
}

// mongodb+srv://fetllaofficial:Fet11aMongoDb@cluster0.6dvy8oq.mongodb.net/
// mongodb+srv://fetllaofficial:Fet11aMongoDb@cluster0.6dvy8oq.mongodb.net/?retryWrites=true&w=majority&appName=Cluster0