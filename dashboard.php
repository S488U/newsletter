<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
</head>

<body>
    <div class="container">
        <h2>Compose Newsletter</h2>
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#htmlModal">
            Insert HTML Source
        </button>
        <form action="send_newsletter.php" method="post" id="newsletterForm">
            <div class="form-group">
                <label for="subject">Subject:</label>
                <input type="text" class="form-control" id="subject" name="subject" required>
            </div>
            <div class="form-group">
                <label for="body">Body:</label>
                <!-- Hidden textarea to store the HTML content -->
                <input type="hidden" class="form-control d-none" id="htmlBody" name="htmlBody"/>
                <!-- Div for editable content -->
                <div class="form-control" id="body" contenteditable="true" style="min-height: 100px; max-height: auto;" required></div>
            </div>
            <button type="button" class="btn btn-primary" onclick="prepareAndSubmit()">Send</button>
            <button type="button" onclick="clearBody()" class="btn btn-primary">Clear</button>
        </form>

        <h3>Upload Email List</h3>
        <form action="upload_email_list.php" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="file">Upload File:</label>
                <input type="file" class="form-control" id="file" name="file" required>
            </div>
            <button type="submit" class="btn btn-primary">Upload</button>
        </form>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="htmlModal" tabindex="-1" role="dialog" aria-labelledby="htmlModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="htmlModalLabel">Insert HTML Source Code</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="htmlSource">HTML Source Code:</label>
                        <textarea class="form-control" id="htmlSource" rows="10"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="insertHtmlSource()">Insert</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>

    <script>
        function insertHtmlSource() {
            var htmlSource = document.getElementById('htmlSource').value;
            document.getElementById('body').innerHTML = htmlSource;
            document.getElementById('htmlBody').value = htmlSource; // Set hidden textarea value
            $('#htmlModal').modal('hide');
        }

        function clearBody() {
            document.getElementById('body').innerHTML = '';
            document.getElementById('htmlBody').value = ''; // Clear hidden textarea value
        }

        function prepareAndSubmit() {
            var htmlSource = document.getElementById('body').innerHTML;
            document.getElementById('htmlBody').value = htmlSource;
            document.getElementById('newsletterForm').submit();
        }
    </script>
</body>

</html>
