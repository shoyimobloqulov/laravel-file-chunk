<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chunked File Upload</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Large File Upload</h2>
        <input type="file" id="fileInput" class="form-control mb-3">
        <button onclick="uploadFile()" class="btn btn-primary">Upload</button>

        <!-- Progress bar -->
        <div class="progress mt-3">
            <div id="progressBar" class="progress-bar progress-bar-striped progress-bar-animated" 
                 role="progressbar" aria-valuemin="0" aria-valuemax="100">0%</div>
        </div>
    </div>

    <script>
        async function uploadFile() {
            const file = document.getElementById("fileInput").files[0];
            const chunkSize = 1024 * 1024 * 5; // 5 MB
            const totalChunks = Math.ceil(file.size / chunkSize);

            for (let chunkIndex = 0; chunkIndex < totalChunks; chunkIndex++) {
                const start = chunkIndex * chunkSize;
                const end = Math.min(start + chunkSize, file.size);
                const chunk = file.slice(start, end);

                const formData = new FormData();
                formData.append("file", chunk);
                formData.append("fileName", file.name);
                formData.append("chunkIndex", chunkIndex);
                formData.append("totalChunks", totalChunks);

                await fetch("/api/upload-chunk", {
                    method: "POST",
                    body: formData
                });

                // Update progress bar
                const progress = Math.floor(((chunkIndex + 1) / totalChunks) * 100);
                document.getElementById("progressBar").style.width = `${progress}%`;
                document.getElementById("progressBar").innerText = `${progress}%`;
            }

            alert("File upload completed!");
        }
    </script>
</body>
</html>
