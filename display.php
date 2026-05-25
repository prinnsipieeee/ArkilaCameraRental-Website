<?php
// Include the database connection file
include 'db_connect.php'; // Ensure this file has the correct DB connection logic

// Query to fetch images and payment proofs with associated user information
$query = "
    SELECT id AS user_id, id_image AS image FROM user_appointments WHERE id_image IS NOT NULL
    UNION ALL
    SELECT appointment_id AS user_id, payment_proof AS image FROM sales WHERE payment_proof IS NOT NULL
";
$result = $conn->query($query);

$images_by_user = [];

if ($result && $result->num_rows > 0) {
    // Group images by user_id
    while ($row = $result->fetch_assoc()) {
        $images_by_user[$row['user_id']][] = $row['image'];
    }
} else {
    // No images found in the database
    $images_by_user = [];
}

// Close the database connection
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Folders with Uploaded Photos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .body {
            background-color: #f8f9fa;
        }
        .container  {
            font-family:"Times New Roman", Times, serif;
        }
        .folder-container {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            justify-content: center;
            margin-top: 30px;
        }
        .folder-item {
            width: 200px;
            height: auto;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            background-color: dimgray;
            border: 1px solid dimgray;
            border-radius: 10px;
            box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.6);
            text-align: center;
            overflow: hidden;
        }
        .folder-title {
            font-weight: bold;
            margin-bottom: 15px;
        }
        .image-container {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: center;
        }
        .image-item {
            width: 100%;
            height: 150px;
            overflow: hidden;
            position: relative;
            border-radius: 5px;
            margin-bottom: 10px;
        }
        .image-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 5px;
        }
        .action-buttons {
            display: flex;
            justify-content: center;
            gap: 10px;
        }
        .action-buttons a {
            text-decoration: none;
            padding: 5px 10px;
            border-radius: 5px;
            color: #fff;
        }
        .view-btn {
            background-color: #007bff;
        }
        .download-btn {
            background-color: #28a745;
        }
    </style>
</head>
<body style="background-color: dimgray;">
    <div class="container">
        <!-- Back Button (Top Left) -->
        <div class="position-absolute" style="top: 20px; left: 20px;">
            <button class="btn btn-secondary" onclick="window.location.href='dashboard.php';">
                ← Back to Dashboard
            </button>
        </div>

        <h1 class="text-center mt-5">Customers Uploaded Photos and Payment Proofs</h1>
        <div class="folder-container">
            <?php foreach ($images_by_user as $user_id => $images): ?>
            <div class="folder-item">
                <div class="folder-title">User <?php echo htmlspecialchars($user_id); ?></div>
                <div class="image-container">
                    <?php foreach ($images as $image): ?>
                    <div class="image-item">
                        <img src="<?php echo htmlspecialchars($image); ?>" alt="User Image">
                    </div>
                    <div class="action-buttons">
                        <!-- View button triggers a modal -->
                        <button 
                            class="view-btn btn btn-primary" 
                            data-bs-toggle="modal" 
                            data-bs-target="#imageModal" 
                            data-bs-image="<?php echo htmlspecialchars($image); ?>">
                            View
                        </button>
                        <a href="<?php echo htmlspecialchars($image); ?>" download class="download-btn">Download</a>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endforeach; ?>

            <?php if (empty($images_by_user)): ?>
            <p class="no-photos">No photos uploaded yet.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal for Image Viewer -->
    <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="imageModalLabel">Image Viewer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img src="" alt="Full View" id="modalImage" class="img-fluid">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Dynamically update the modal image source
        const imageModal = document.getElementById('imageModal');
        const modalImage = document.getElementById('modalImage');

        imageModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget; // Button that triggered the modal
            const imageSrc = button.getAttribute('data-bs-image'); // Extract image URL
            modalImage.src = imageSrc; // Set modal image
        });

        // Reset the modal image when closed
        imageModal.addEventListener('hide.bs.modal', function () {
            modalImage.src = '';
        });
    </script>
</body>
</html>
