from flask import Flask, request, jsonify

app = Flask(__name__)

command = "stop"

@app.route("/")
def control_page():
    return """
    <html>
    <body style="text-align:center; font-family:Arial;">
        <h1>Robot Control</h1>

        <form action="/set" method="get">
            <button name="cmd" value="auto" style="width:200px;height:50px;font-size:22px;background:#ffcc00;">AUTO</button>
            <br><br>

            <button name="cmd" value="forward" style="width:150px;height:50px;font-size:20px;">Forward</button>
            <br><br>

            <button name="cmd" value="left" style="width:150px;height:50px;font-size:20px;">Left</button>
            <button name="cmd" value="stop" style="width:150px;height:50px;font-size:20px;">STOP</button>
            <button name="cmd" value="right" style="width:150px;height:50px;font-size:20px;">Right</button>
            <br><br>

            <button name="cmd" value="backward" style="width:150px;height:50px;font-size:20px;">Backward</button>
        </form>
    </body>
    </html>
    """

@app.route("/set")
def set_command():
    global command
    command = request.args.get("cmd", "stop")
    print("Command set to:", command)
    return jsonify({"status": "ok", "command": command})

@app.route("/get_command")
def get_command():
    return jsonify({"command": command})

app.run(host="0.0.0.0", port=5000)
