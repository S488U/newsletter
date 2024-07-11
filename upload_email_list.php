<?php
// Database connection details (replace with your actual credentials)
include("./connection.php");

// Check if file is uploaded
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $fileName = $_FILES['file']['name'];
    $fileType = $_FILES['file']['type'];
    $fileTmpName = $_FILES['file']['tmp_name'];
    $fileError = $_FILES['file']['error'];

    // Validate file type (CSV or JSON)
    $allowedTypes = array('text/csv', 'application/json');
    if (!in_array($fileType, $allowedTypes)) {
        echo "Invalid file type. Only CSV and JSON files are allowed.";
        exit;
    }

    // Process data based on file type
    if ($fileType === 'text/csv') {
        $data = processCSV($fileTmpName);
    } else {
        $data = processJSON($fileTmpName);
    }

    // Insert data into the database
    if (!empty($data) && is_array($data)) {
        foreach ($data as $subscriber) {
            $name = $subscriber['name'];
            $email = $subscriber['email'];

            // Check if email already exists in the database
            $existingEmailQuery = "SELECT * FROM subscribers WHERE email='$email'";
            $existingEmailResult = $conn->query($existingEmailQuery);
            if ($existingEmailResult->num_rows === 0) {
                // Email does not exist, insert into the database
                $sql = "INSERT INTO subscribers (name, email) VALUES ('$name', '$email')";
                if ($conn->query($sql) === TRUE) {
                    echo "Record inserted successfully for $name ($email).<br>";
                } else {
                    echo "Error inserting data for $name ($email): " . $conn->error . "<br>";
                }
            } else {
                echo "Skipping duplicate email: $email.<br>";
            }
        }
    } else {
        echo "Error processing file. Please check the file format.";
    }
}

// Function to process CSV data
function processCSV($filePath) {
    $subscribers = array();
    if (($handle = fopen($filePath, "r")) !== FALSE) {
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            // Assuming first row contains column names (modify as needed)
            if (isset($data[0]) && $data[0] === "name" && isset($data[1]) && $data[1] === "email") {
                continue; // Skip header row
            }
            if (isset($data[0]) && isset($data[1])) {
                $subscribers[] = array('name' => $data[0], 'email' => $data[1]);
            }
        }
        fclose($handle);
    }
    return $subscribers;
}

// Function to process JSON data
function processJSON($filePath) {
    $jsonData = file_get_contents($filePath);
    $data = json_decode($jsonData, true);
    $subscribers = array();
    if (is_array($data)) {
        foreach ($data as $subscriber) {
            if (isset($subscriber['name']) && isset($subscriber['email'])) {
                $subscribers[] = $subscriber;
            }
        }
    }
    return $subscribers;
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Newsletter Signup</title>
    <!-- Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.16/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto py-8">
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
            <div class="mb-4">
                <label for="file" class="block text-gray-700 text-sm font-bold mb-2">Upload subscriber list (CSV or JSON):</label>
                <input type="file" name="file" id="file" accept=".csv, .json" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
            </div>
            <div class="flex items-center justify-between">
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Upload</button>
            </div>
        </form>
        <a href="./dashboard.php" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline inline-block">Dashboard</a>
    </div>
</body>
</html>
