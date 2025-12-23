from flask import Flask, request, jsonify
from flask_socketio import SocketIO, emit

app = Flask(__name__)
app.config["SECRET_KEY"] = "secret"

socketio = SocketIO(app, cors_allowed_origins="*")

latest_distance = 0

# -------------------- TEST BẰNG GET --------------------
from flask import Flask, request, jsonify
from flask_socketio import SocketIO

app = Flask(__name__)
app.config["SECRET_KEY"] = "secret"
socketio = SocketIO(app, cors_allowed_origins="*")

latest_distance = 0.0

@app.route("/distance", methods=["GET", "POST"])
def set_distance():
    global latest_distance

    # Nếu GET, lấy từ query string
    if request.method == "GET":
        d = request.args.get("d", None)
    # Nếu POST, lấy từ JSON body
    else:  # POST
        data = request.get_json(silent=True) or {}
        d = data.get("d") or data.get("distance")

    if d is None:
        return jsonify({"error": "Missing distance value"}), 400

    try:
        latest_distance = float(d)
    except (ValueError, TypeError):
        return jsonify({"error": "Invalid number"}), 400

    # Emit WebSocket event
    socketio.emit("distance_update", {"distance": latest_distance})
    print("Updated distance:", latest_distance)

    return jsonify({"status": "ok", "distance": latest_distance})

# -------------------- TRANG WEB REALTIME --------------------
@app.route("/")
def index():
    return """
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="utf-8">
        <script src="https://cdn.socket.io/4.5.4/socket.io.min.js"></script>
        <style>
            body { font-family: Arial; text-align: center; padding-top: 40px; }
            #d { font-size: 60px; font-weight: bold; color: blue; }
        </style>
    </head>

    <body>
        <h1>Realtime Distance (WebSocket)</h1>
        <div id="d">-- cm</div>

        <script>
            var socket = io();

            socket.on("distance_update", function(data) {
                document.getElementById("d").innerHTML =
                    data.distance.toFixed(2) + " cm";
            });
        </script>
    </body>
    </html>
    """

if __name__ == "__main__":
    socketio.run(app, host="0.0.0.0", port=5000, debug=True)
