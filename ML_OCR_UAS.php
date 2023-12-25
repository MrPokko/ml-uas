<?php

// Fungsi untuk melakukan OCR pada gambar
function doOCR($imagePath)
{
    // Lokasi Tesseract OCR di server (sesuaikan dengan lokasi di server Anda)
    $tesseractPath = 'C:\\Program Files\\Tesseract-OCR\\tesseract.exe';

    // Lokasi output teks dari OCR
    $outputPath = 'D:\Kuliah\RPL 1\Pembelajaran Mesin\Data OCR\output';

    // Perintah untuk menjalankan Tesseract OCR
    $command = "\"{$tesseractPath}\" \"{$imagePath}\" \"{$outputPath}\"";

    // Jalankan perintah shell dengan aman
    exec($command, $output, $returnCode);

    // Periksa apakah Tesseract OCR berhasil dijalankan
    if ($returnCode !== 0) {
        die('Error executing Tesseract OCR');
    }

    // Baca isi file output teks
    $text = file_get_contents($outputPath . '.txt');

    // Hapus file output teks setelah digunakan
    unlink($outputPath . '.txt');

    return $text;
}

// Ambil data gambar dari request POST
if (isset($_POST['imageData'])) {
    $imageData = $_POST['imageData'];

    // Decode data gambar dari format base64
    $imageData = str_replace('data:image/png;base64,', '', $imageData);
    $imageData = str_replace(' ', '+', $imageData);
    $imageData = base64_decode($imageData);

    // Simpan gambar ke file
    $imagePath = 'D:\Kuliah\RPL 1\Pembelajaran Mesin\Data OCR\captured_image.png';
    file_put_contents($imagePath, $imageData);

    // Lakukan OCR pada gambar
    $hasilOCR = doOCR($imagePath);

    // Tampilkan hasil OCR
    echo $hasilOCR;
}

// Tampilkan form untuk mengakses kamera
echo '
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Optical Character Recognition (OCR) </title>
    <!-- Tambahkan Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
    body {
        background-color: #add8e6;
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
    }

    .container {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100vh;
    }

    .logo {
        width: 100px; /* Ubah sesuai dengan ukuran logo */
        height: auto; /* Sesuaikan proporsi */
        margin-bottom: 20px; /* Jarak antara logo dan judul */
    }

    .title {
        font-size: 24px;
        font-weight: bold;
        color: #333; /* Warna judul */
        margin-bottom: 20px; /* Jarak antara judul dan tombol */
    }

    .btn-primary {
        background-color: #3498db;
        border-color: #3498db;
        color: white;
        padding: 10px 20px;
        border-radius: 5px;
        text-align: center;
        text-decoration: none;
        transition: background-color 0.3s ease, border-color 0.3s ease, transform 0.3s ease;
        box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.2);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
    }

    .btn-primary:hover {
        background-color: #2980b9;
        border-color: #2980b9;
        transform: scale(1.05);
        transform: translateY(-3px);
        box-shadow: 0 8px 12px rgba(0, 0, 0, 0.1);
    }
    
</style>

</head>
<body>
    <div class="container">
        <h2 class="mb-4">Optical Character Recognition (OCR)</h2>
        <img src="C:\Users\fahmi\OneDrive\Pictures\Screenshots\Screenshot 2023-12-25 140228.logo" alt="Logo" class="logo">
        <video id="video" class="mb-3" width="640" height="480" autoplay></video>
        <button class="btn btn-primary btn-lg" onclick="captureAndOCR()">Ambil Gambar</button>
    </div>

    <!-- Modal untuk menampilkan hasil OCR dan gambar yang diambil -->
    <div class="modal fade" id="hasilOCRModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Hasil OCR</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <img id="capturedImage" class="img-fluid mb-3" alt="Captured Image">
                    <div id="hasilOCR"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tambahkan Bootstrap JS dan Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Ambil gambar dari kamera dan kirim ke server PHP
        function captureAndOCR() {
            var video = document.getElementById("video");
            var canvas = document.createElement("canvas");
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            var context = canvas.getContext("2d");
            context.drawImage(video, 0, 0, canvas.width, canvas.height);

            // Tampilkan gambar yang diambil dalam modal
            var capturedImage = document.getElementById("capturedImage");
            capturedImage.src = canvas.toDataURL("image/png");

            var dataURL = canvas.toDataURL("image/png");

            // Kirim data gambar ke server PHP
            fetch("", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded",
                },
                body: "imageData=" + encodeURIComponent(dataURL),
            })
            .then(response => response.text())
            .then(result => {
                // Tampilkan hasil OCR dalam modal
                document.getElementById("hasilOCR").innerHTML = "<strong>" + result + "</strong>";
                var hasilOCRModal = new bootstrap.Modal(document.getElementById("hasilOCRModal"));
                hasilOCRModal.show();
            })
            .catch(error => console.error("Error:", error));
        }

        // Mengakses kamera menggunakan HTML5
        navigator.mediaDevices.getUserMedia({ video: true })
        .then(function (stream) {
            var video = document.getElementById("video");
            video.srcObject = stream;
        })
        .catch(function (error) {
            console.error("Error accessing webcam:", error);
        });
    </script>
</body>
</html>


';
?>