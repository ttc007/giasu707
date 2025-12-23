from flask import Flask, request, jsonify, render_template_string
from flask_sock import Sock

app = Flask(__name__)
sock = Sock(app)

# --- Trạng thái robot ---
robot_state = {
    "action": "stop",
    "speed": 180
}

# Danh sách ESP32 đang kết nối WS
ws_clients = []


# ============================
#   API CŨ GIỮ LẠI ĐỂ TEST
# ============================
@app.route('/get_command', methods=['GET'])
def get_command():
    return jsonify(robot_state)


@app.route('/update_command', methods=['POST'])
def update_command():
    data = request.get_json()
    action = data.get('action', '').lower()
    speed = int(data.get('speed', robot_state['speed']))

    if action not in ['forward','backward','left','right','stop','auto']:
        return jsonify({"status":"error","message":"Invalid action"}), 400

    robot_state['action'] = action
    robot_state['speed'] = speed

    # Gửi realtime cho ESP32
    push_to_esp()

    return jsonify({"status": "ok", "robot_state": robot_state})


# ============================
#     WEBSOCKET SERVER
# ============================
@sock.route('/ws')
def ws_endpoint(ws):
    print("ESP32 CONNECTED")
    ws_clients.append(ws)

    # Gửi trạng thái ban đầu
    ws.send(jsonify(robot_state).data.decode())

    while True:
        try:
            msg = ws.receive()  # ESP32 không gửi gì, nhưng cần listen
            if msg is None:
                break
        except:
            break

    print("ESP32 DISCONNECTED")
    ws_clients.remove(ws)


def push_to_esp():
    """Gửi JSON realtime đến ALL ESP32"""
    dead = []
    for ws in ws_clients:
        try:
            ws.send(jsonify(robot_state).data.decode())
        except:
            dead.append(ws)

    # Xóa kết nối chết
    for ws in dead:
        ws_clients.remove(ws)


# ============================
#          WEB GUI
# ============================
@app.route('/', methods=['GET'])
def gui():
    html = """
    <!DOCTYPE html>
    <html>
    <head>
    <title>Robot Control</title>
    <style>
      body {font-family: Arial; text-align: center;}
      .row {display: flex; justify-content: center; margin: 10px;}
      button {width:100px; height:50px; margin:5px; font-size:18px;}
      .speed-control {margin-top:20px;}
      .active-btn {background-color: green; color: white;}
    </style>
    </head>
    <body>
      <h2>Robot Control</h2>

      <div class="row">
        <button data-action="auto" onclick="clickButton('auto', this)">Auto</button>
        <button data-action="stop" onclick="clickButton('stop', this)" id="stopBtn">Stop</button>
      </div>

      <div class="row">
        <button data-action="forward"
            onmousedown="holdButton('forward', this)"
            ontouchstart="holdButton('forward', this)">Forward</button>
      </div>

      <div class="row">
        <button data-action="left"
                onmousedown="holdButton('left', this)"
                ontouchstart="holdButton('left', this)">Left</button>

        <button data-action="right"
                onmousedown="holdButton('right', this)"
                ontouchstart="holdButton('right', this)">Right</button>
      </div>

      <div class="row">
        <button data-action="backward"
                onmousedown="holdButton('backward', this)"
                ontouchstart="holdButton('backward', this)">Backward</button>
      </div>

      <div class="speed-control">
        Speed: <input type="range" id="speed" min="100" max="255" value="180" oninput="speedDisplay.value=this.value">
        <output id="speedDisplay">180</output>
      </div>

      <script>
          // --- Gửi lệnh chung ---
          function postCommand(action) {
            const speed = document.getElementById('speed').value;

            fetch('/update_command', {
              method: 'POST',
              headers: {'Content-Type': 'application/json'},
              body: JSON.stringify({action: action, speed: parseInt(speed)})
            });
          }

          // --- Cho 4 nút cần giữ ---
          function holdButton(action, btn) {
            postCommand(action);

            document.querySelectorAll('button').forEach(b => b.classList.remove('active-btn'));
            btn.classList.add("active-btn");

            const stopFunc = () => {
              postCommand("stop");
              btn.classList.remove("active-btn");

              document.removeEventListener("mouseup", stopFunc);
              document.removeEventListener("touchend", stopFunc);
              document.removeEventListener("mouseleave", stopFunc);

              document.getElementById('stopBtn').classList.add('active-btn');
            };

            document.addEventListener("mouseup", stopFunc);
            document.addEventListener("touchend", stopFunc);
            document.addEventListener("mouseleave", stopFunc);
          }

          // --- Stop & Auto ---
          function clickButton(action, buttonElement) {
            postCommand(action);
            document.querySelectorAll('button').forEach(b => b.classList.remove('active-btn'));
            buttonElement.classList.add('active-btn');
          }

          window.onload = () => {
            const action = "{{ robot_state['action'] }}";
            const btn = document.querySelector(`button[data-action="${action}"]`);
            btn?.classList.add("active-btn");
          };
      </script>
    </body>
    </html>
    """
    return render_template_string(html, robot_state=robot_state)


# ============================
#           RUN
# ============================
if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5000, debug=True)
