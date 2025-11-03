<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AttendanceHub</title>
    <style>
        body {
            font-family: 'Helvetica Neue', sans-serif;
            background-color: #e0f7fa;
            text-align: center;
            margin: 0;
            padding: 20px;
        }
        .container {
            background: #ffffff;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0px 8px 20px rgba(0, 0, 0, 0.2);
            max-width: 600px;
            margin: auto;
        }
        h2 {
            color: #00796b;
            font-size: 2em;
            margin-bottom: 20px;
        }
        .camera-feed {
            width: 100%;
            height: 350px;
            background: #b2dfdb;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 30px;
            box-shadow: inset 0px 4px 8px rgba(0, 0, 0, 0.1);
        }
        video {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 15px;
        }
        button {
            background: #00796b;
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 25px;
            font-size: 18px;
            cursor: pointer;
            transition: background 0.3s ease;
            margin: 10px;
        }
        button:hover {
            background: #004d40;
        }
        #message {
            font-size: 1.2em;
            color: #00796b;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>AttendanceHub</h2>
        <div class="camera-feed">
            <video id="video" autoplay playsinline></video>
        </div>
        <button onclick="startCamera()">Start Camera</button>
        <button onclick="captureFace()">Scan & Take Attendance</button>
        <p id="message"></p>
    </div>
    
    <script>
       let videoStream;

function startCamera() {
    const video = document.getElementById("video");

    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
        alert("Your browser does not support camera access.");
        return;
    }

    // Check if already signed out before starting the camera
    fetch("check_attendance.php")
        .then(response => response.json())
        .then(data => {
            if (data.status === "signed_out") {
                alert("Already signed out for today!");
            } else {
                // Request camera access
                navigator.mediaDevices.getUserMedia({ video: true })
                    .then((stream) => {
                        videoStream = stream;
                        video.srcObject = stream;
                    })
                    .catch((error) => {
                        console.error("Camera Error:", error);
                        alert("Camera access denied or unavailable: " + error.message);
                    });
            }
        })
        .catch(error => {
            console.error("Attendance Check Error:", error);
            alert("Error checking attendance. Please try again.");
        });
}


        function captureFace() {
            const video = document.getElementById("video");
            const canvas = document.createElement("canvas");
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            const ctx = canvas.getContext("2d");
            ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

            // Convert to base64
            const imageData = canvas.toDataURL("image/png");

            // Send to PHP for face recognition
            fetch("face_recognition.php", {
                method: "POST",
                body: JSON.stringify({ image: imageData }),
                headers: { "Content-Type": "application/json" }
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById("message").innerText = data.message;
                if (data.success) {
                    stopCamera();
                }
            })
            .catch(error => console.error("Error:", error));
        }

        function stopCamera() {
            if (videoStream) {
                videoStream.getTracks().forEach(track => track.stop());
            }
        }
    </script>
</body>
</html>
