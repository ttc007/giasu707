// chạy với: python -m http.server 80

window.addEventListener('load', () => {
    const checkVM = setInterval(() => {
        if (window.vm && window.vm.runtime) {
            clearInterval(checkVM);
            const stage = window.vm.runtime.getTargetForStage();
            const allVars = stage.variables;

            // 1. Tìm đúng đối tượng biến Name và Score
            const nameVar = Object.values(allVars).find(v => v.name === 'Tên');
            const scoreVar = Object.values(allVars).find(v => v.name === 'Điểm');
            const isSetName = Object.values(allVars).find(v => v.name === 'is_set_name');
            const gameStatus = Object.values(allVars).find(v => v.name === 'GameStatus');
            const score1 = Object.values(allVars).find(v => v.name === 'score1');
            const score2 = Object.values(allVars).find(v => v.name === 'score2');
            const score3 = Object.values(allVars).find(v => v.name === 'score3');
            const score4 = Object.values(allVars).find(v => v.name === 'score4');
            const score5 = Object.values(allVars).find(v => v.name === 'score5');
            const name1 = Object.values(allVars).find(v => v.name === 'name1');
            const name2 = Object.values(allVars).find(v => v.name === 'name2');
            const name3 = Object.values(allVars).find(v => v.name === 'name3');
            const name4 = Object.values(allVars).find(v => v.name === 'name4');
            const name5 = Object.values(allVars).find(v => v.name === 'name5');

            let lastScore = -1;
            let lastName = nameVar.value;

            setInterval(() => {
                const currentScore = scoreVar.value;

                const is_set_name = isSetName.value;
                const savedName = localStorage.getItem('game_user_name');

                if (is_set_name == 0) {
                    // 2. KIỂM TRA LOCALSTORAGE KHI VỪA VÀO GAME
                    if (savedName) {
                        // Nếu đã có tên lưu sẵn, ghi đè lên giá trị -1 của Scratch
                        nameVar.value = savedName;
                        console.log("Đã khôi phục tên từ máy:", savedName);
                    }
                    isSetName.value = 1;
                }

                let currentName = nameVar.value; 
                if (currentName.length > 10) {
                    currentName = currentName.substring(0, 10);
                }

                console.log(currentName);
                console.log(lastName);
                console.log(savedName);
                if (currentName !== lastName && savedName == null &&
                    currentName != "-1" && currentName !== "") {
                    localStorage.setItem('game_user_name', currentName);
                    // Tạo ID ngẫu nhiên nếu bạn muốn quản lý sâu hơn
                    const userId = 'ID_' + Math.random().toString(36).substr(2, 9);
                    localStorage.setItem('game_user_id', userId);
                    lastName = currentName;
                    nameVar.value = currentName;
                }

                const currentGameStatus = gameStatus.value;
                if (currentGameStatus == 1 && scoreVar.value > 0) {
                    // 1. Gọi lên server Python để lấy bảng điểm mới nhất
                    fetch('http://localhost:5000/save_score', { 
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({
                            id: localStorage.getItem('game_user_id'),
                            name: localStorage.getItem('game_user_name'),
                            score: scoreVar.value
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === "success") {
                            const leaderboard = data.leaderboard; // Mảng chứa {name, score}

                            // 2. Chuyển đổi dữ liệu từ Server thành mảng đơn thuần cho Scratch
                            const names = leaderboard.map(item => item.name);
                            const scores = leaderboard.map(item => item.score);
                            for (let i = 0; i < 5; i++) {
                                if (i < scores.length) {
                                    if (i == 0) {
                                        score1.value = scores[i];
                                        name1.value = names[i];
                                    } if (i == 1) {
                                        score2.value = scores[i];
                                        name2.value = names[i];
                                    } if (i == 2) {
                                        score3.value = scores[i];
                                        name4.value = names[i];
                                    } if (i == 3) {
                                        score4.value = scores[i];
                                        name4.value = names[i];
                                    } if (i == 4) {
                                        score5.value = scores[i];
                                        name5.value = names[i];
                                    }
                                }
                            }
                            
                        }
                        gameStatus.value = 2;
                    })
                    .catch(err => {
                        console.error("Lỗi kết nối Server:", err);
                        //gameStatus.value = 2; // Vẫn chuyển trạng thái để tránh treo game
                    });
                }
            }, 500);
        }
    }, 500);
});