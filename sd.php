<?php
// Directory to save uploaded files
$uploadDir = 'uploads/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Initialize upload message
$uploadMessage = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
        $fileTmpPath = $_FILES['file']['tmp_name'];
        $fileName = $_FILES['file']['name'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        // Validate file type (only PDFs allowed)
        if ($fileExtension === 'pdf') {
            $destPath = $uploadDir . basename($fileName);
            if (move_uploaded_file($fileTmpPath, $destPath)) {
                $uploadMessage = "<p class='success'>File uploaded successfully: <a href='$destPath' target='_blank'>$fileName</a></p>";
            } else {
                $uploadMessage = "<p class='error'>There was an error moving the uploaded file.</p>";
            }
        } else {
            $uploadMessage = "<p class='error'>Only PDF files are allowed.</p>";
        }
    } else {
        $uploadMessage = "<p class='error'>Error uploading file: " . $_FILES['file']['error'] . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload PDF</title>
    <style>
        /* General Styling */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }
        body {
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background-color: white;
            width: 400px;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            text-align: center;
            position: relative;
        }
        .container:before {
            content: '';
            width: 100%;
            height: 5px;
            background-color: #f0ad4e;
            border-radius: 8px 8px 0 0;
            position: absolute;
            top: 0;
            left: 0;
        }
        h2 {
            margin-bottom: 20px;
            color: #444;
        }
        /* Drag-and-Drop Styling */
        .drag-drop-area {
            border: 2px dashed #ccc;
            padding: 20px;
            border-radius: 6px;
            background-color: #f9f9f9;
            transition: border-color 0.3s ease, background-color 0.3s ease;
        }
        .drag-drop-area.dragging {
            border-color: #f0ad4e;
            background-color: #fff3cd;
        }
        .drag-drop-area p {
            margin: 10px 0;
            color: #666;
        }
        /* Buttons */
        .custom-file-btn {
            background-color: #333;
            color: white;
            padding: 10px 20px;
            border-radius: 4px;
            margin-top: 10px;
            cursor: pointer;
            display: inline-block;
            font-size: 14px;
            font-weight: bold;
            border: none;
            transition: background-color 0.3s ease;
        }
        .custom-file-btn:hover {
            background-color: #444;
        }
        input[type="file"] {
            opacity: 0;
            position: absolute;
            z-index: -1;
        }
        /* File Status Note */
        .file-status {
            margin-top: 10px;
            font-size: 14px;
            color: #666;
            font-style: italic;
        }
        .file-status.ready {
            color: #28a745;
            font-weight: bold;
            font-style: normal;
        }
        .upload-btn {
            background-color: #f0ad4e;
            color: white;
            padding: 12px;
            width: 100%;
            font-size: 16px;
            font-weight: bold;
            border: none;
            border-radius: 6px;
            margin-top: 20px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }
        .upload-btn:hover {
            background-color: #e0a437;
            transform: scale(1.05);
        }
        /* Feedback Messages */
        .success {
            color: #28a745;
            margin-top: 15px;
        }
        .error {
            color: #dc3545;
            margin-top: 15px;
        }
        a {
            color: #f0ad4e;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Upload Document</h2>

        <!-- Feedback Message -->
        <?php if ($uploadMessage): ?>
            <div><?php echo $uploadMessage; ?></div>
        <?php endif; ?>

        <!-- Drag-and-Drop Area -->
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" enctype="multipart/form-data">
            <div id="drag-drop-area" class="drag-drop-area">
                <p>Drag and drop your PDF file here</p>
                <p>or</p>
                <label class="custom-file-btn">
                    Choose File
                    <input type="file" name="file" id="file-input" accept=".pdf" required>
                </label>
                <div id="file-status" class="file-status">No file selected</div>
            </div>
            <button type="submit" class="upload-btn">Upload PDF</button>
        </form>
    </div>

    <script>
        const dragDropArea = document.getElementById('drag-drop-area');
        const fileInput = document.getElementById('file-input');
        const fileStatus = document.getElementById('file-status');

        // Highlight the drag-and-drop area on drag over
        dragDropArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            dragDropArea.classList.add('dragging');
        });

        // Remove highlight on drag leave
        dragDropArea.addEventListener('dragleave', () => {
            dragDropArea.classList.remove('dragging');
        });

        // Handle dropped files
        dragDropArea.addEventListener('drop', (e) => {
            e.preventDefault();
            dragDropArea.classList.remove('dragging');
            const files = e.dataTransfer.files;
            if (files.length > 0 && files[0].type === 'application/pdf') {
                fileInput.files = files;
                fileStatus.textContent = `File ready to upload: ${files[0].name}`;
                fileStatus.classList.add('ready');
            } else {
                alert('Only PDF files are allowed!');
            }
        });

        // Update file status when selecting a file via the button
        fileInput.addEventListener('change', () => {
            if (fileInput.files[0]) {
                fileStatus.textContent = `File ready to upload: ${fileInput.files[0].name}`;
                fileStatus.classList.add('ready');
            } else {
                fileStatus.textContent = 'No file selected';
                fileStatus.classList.remove('ready');
            }
        });
    </script>
</body>
</html>