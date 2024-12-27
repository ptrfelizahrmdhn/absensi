from flask_socketio import SocketIO, emit
from flask import Flask, Response, render_template, request, jsonify
import cv2
import os
import numpy as np

app = Flask(__name__)
socketio = SocketIO(app, cors_allowed_origins="*")  # Enable CORS for WebSocket
DATA_DIR = "face_data"

# Fungsi untuk mencocokkan wajah
def match_face(input_face, known_faces):
    for name, known_face in known_faces.items():
        result = cv2.matchTemplate(input_face, known_face, cv2.TM_CCOEFF_NORMED)
        if np.max(result) > 0.6:  # Threshold untuk pencocokan
            return name
    return None

def load_known_faces():
    known_faces = {}
    for file_name in os.listdir(DATA_DIR):
        path = os.path.join(DATA_DIR, file_name)
        name, _ = os.path.splitext(file_name)
        image = cv2.imread(path, cv2.IMREAD_GRAYSCALE)
        known_faces[name] = image
    return known_faces

cap = cv2.VideoCapture(0)

def generate_frames():
    known_faces = load_known_faces()
    face_cascade = cv2.CascadeClassifier(cv2.data.haarcascades + "haarcascade_frontalface_default.xml")

    while True:
        success, frame = cap.read()
        if not success:
            break
        else:
            gray_frame = cv2.cvtColor(frame, cv2.COLOR_BGR2GRAY)
            faces = face_cascade.detectMultiScale(gray_frame, scaleFactor=1.1, minNeighbors=5, minSize=(50, 50))

            for (x, y, w, h) in faces:
                face = gray_frame[y:y + h, x:x + w]
                recognized_name = match_face(face, known_faces)

                if recognized_name:
                    # Kirim pesan ke frontend Laravel melalui WebSocket
                    socketio.emit('face_detected', {'message': f'{recognized_name}'})

                    # Tampilkan pesan pada layar
                    cv2.putText(frame, f"{recognized_name}", (x, y - 10), cv2.FONT_HERSHEY_SIMPLEX, 0.9, (0, 255, 0), 2)
                    cv2.rectangle(frame, (x, y), (x + w, y + h), (0, 255, 0), 2)

                else:
                    cv2.rectangle(frame, (x, y), (x + w, y + h), (0, 0, 255), 2)

            ret, buffer = cv2.imencode('.jpg', frame)
            frame = buffer.tobytes()
            
            yield (b'--frame\r\n'
                   b'Content-Type: image/jpeg\r\n\r\n' + frame + b'\r\n')

@app.route('/video_feed')
def video_feed():
    return Response(generate_frames(), mimetype='multipart/x-mixed-replace; boundary=frame')

@app.route('/upload', methods=['POST'])
def upload_file():
    if 'file' not in request.files:
        return jsonify({'error': 'No file in the request'}), 400
    
    file = request.files['file']
    
    if file.filename == '':
        return jsonify({'error': 'No selected file'}), 400
    
    if file:
        original_extension = os.path.splitext(file.filename)[1]
        filename = request.form['filename'] + original_extension
        filepath = os.path.join(DATA_DIR, filename)
        file.save(filepath)
        return jsonify({'message': f'File successfully uploaded to {filepath}'}), 200
    
    return jsonify({'message': 'Failed to upload file'}), 500

if __name__ == "__main__":
    socketio.run(app, debug=True, host='0.0.0.0', port=5000)
