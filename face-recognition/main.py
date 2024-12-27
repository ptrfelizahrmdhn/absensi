from flask import Flask, Response, render_template
import face_recognition
import cv2
import numpy as np
import os

app = Flask(__name__)

# Folder untuk data wajah
DATA_FOLDER = "static/images/"

# Fungsi untuk memuat wajah yang dikenal
def load_known_faces():
    known_faces = []
    known_names = []

    for file_name in os.listdir(DATA_FOLDER):
        path = os.path.join(DATA_FOLDER, file_name)
        name, _ = os.path.splitext(file_name)

        # Muat gambar dan encode wajah
        image = face_recognition.load_image_file(path)
        encodings = face_recognition.face_encodings(image)

        if encodings:
            known_faces.append(encodings[0])
            known_names.append(name)

    return known_faces, known_names

# Muat data wajah
known_faces, known_names = load_known_faces()

# Inisialisasi kamera
video_capture = cv2.VideoCapture(0)

def generate_frames():
    while True:
        success, frame = video_capture.read()
        if not success:
            break

        # Konversi frame ke RGB
        rgb_frame = cv2.cvtColor(frame, cv2.COLOR_BGR2RGB)

        # Deteksi lokasi wajah
        face_locations = face_recognition.face_locations(rgb_frame)
        face_encodings = face_recognition.face_encodings(rgb_frame, face_locations)

        for (top, right, bottom, left), face_encoding in zip(face_locations, face_encodings):
            matches = face_recognition.compare_faces(known_faces, face_encoding)
            face_distances = face_recognition.face_distance(known_faces, face_encoding)
            name = "Unknown"

            if True in matches:
                best_match_index = np.argmin(face_distances)
                name = known_names[best_match_index]

            # Gambar kotak di sekitar wajah
            cv2.rectangle(frame, (left, top), (right, bottom), (0, 255, 0), 2)

            # Tampilkan nama pengguna
            cv2.putText(frame, name, (left, top - 10), cv2.FONT_HERSHEY_SIMPLEX, 0.9, (255, 255, 255), 2)

        # Encode frame menjadi JPEG
        ret, buffer = cv2.imencode('.jpg', frame)
        frame = buffer.tobytes()

        # Streaming frame
        yield (b'--frame\r\n'
               b'Content-Type: image/jpeg\r\n\r\n' + frame + b'\r\n')

@app.route('/')
def index():
    return render_template('index.html')

@app.route('/video_feed')
def video_feed():
    return Response(generate_frames(), mimetype='multipart/x-mixed-replace; boundary=frame')

if __name__ == "__main__":
    app.run(debug=True, host='0.0.0.0')
