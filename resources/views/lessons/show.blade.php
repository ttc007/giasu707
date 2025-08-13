@extends('layouts.app')

@section('title', $lesson->title . ' - ' . $chapter->title)

@section('content')
<div class="container section">
    <p><a href="{{ route('show.chapter', [
        'subject_slug' => $subject->slug,
        'chapter_slug' => $chapter->slug,
    ]) }}" class=""><strong>{{ $lesson->chapter->title }} - {{ $lesson->chapter->subject->name }}</strong></a></p>
    <h2 class="my-3">{{ $lesson->title }}</h2>
    <div class="text-center" style="font-size:20px; display: flex; justify-content: center; gap: 15px; align-items: center;">
        <span id="view-count">👀 {{ $lesson->countView() }}</span>
        <span id="like-count">❤️{{ $lesson->countLikes() }}</span>
        <div id="like-container"></div>
    </div>

    <hr>
    @if ($lesson->sections->count())
        <h5 class="mt-4">📚 Các phần trong bài học</h5>
        <ul class="list-group mb-4">
            @foreach ($lesson->sections as $section)
                <li class="list-group-item">
                    <a href="{{ route('show.section', [
                        'subject_slug' => $subject->slug,
                        'chapter_slug' => $chapter->slug,
                        'section_slug' => $section->slug
                    ]) }}">
                        {{ $section->title ?? 'Phần ' . $loop->iteration }}
                    </a>
                </li>
            @endforeach
        </ul>
    @endif

    <hr>
    <div class="p-3 mb-4">
        <h5 class="text-muted">Nội dung tổng hợp</h5>
        {!! $lesson->summary !!}
    </div>

    <hr>
    <h4>Bài tập ôn tập ({{ $lesson->getQuestionsCountAttribute() }} tổng câu hỏi)</h4>
    <i class='text-danger'>*Lưu ý: làm hết câu này rồi đến câu khác. Xin đừng nôn nóng.</i>
        <div class="mb-3">
            <label for="mode" class="form-label">Chọn chế độ hiển thị câu hỏi:</label>
            <select id="mode" class="form-select w-auto d-inline-block" onchange="onModeChange()">
                <option value="ordered" selected>Theo thứ tự</option>
                <option value="random" >Ngẫu nhiên</option>
            </select>

            <div class="d-inline-block ms-3" id="question-number-wrapper" style="display: inline!important;">
                <label for="question-number">Số câu:</label>
                <input type="number" id="question-number" class="form-control d-inline-block" 
                    min="1" max="{{ $lesson->getQuestionsCountAttribute() }}" style="width: 70px;" value="1" 
                    onchange="loadQuestion()">
                / {{ $lesson->getQuestionsCountAttribute() }}
            </div>
        </div>

        <div id="exercise-area">
            <p><em>Đang tải câu hỏi...</em></p>
        </div>

        <hr class="mt-5">
        <div class="d-flex justify-content-between mt-4">
            @if ($lesson->prevLesson())
                <a href="{{ route('show.lesson', [
                    'subject_slug' => $subject->slug,
                    'chapter_slug' => $chapter->slug,
                    'lesson_slug' => $lesson->prevLesson()->slug
                ]) }}" class="btn btn-outline-success">
                    {{$lesson->prevLesson()->title}}
                </a>
            @else
                <button class="btn btn-outline-success" disabled>Bài trước</button>
            @endif

            @if ($lesson->nextLesson())
                <a href="{{ route('show.lesson', [
                    'subject_slug' => $subject->slug,
                    'chapter_slug' => $chapter->slug,
                    'lesson_slug' => $lesson->nextLesson()->slug
                ]) }}" class="btn btn-outline-success">
                    {{$lesson->nextLesson()->title}} 
                </a>
            @else
                <button class="btn btn-outline-success" disabled>Bài sau </button>
            @endif
        </div>
    </div>

<script>
    const lesson = {{ $lesson->id }};
    let currentAnswer = '';
    let currentSolution = '';
    let currentQuestionId = '';
    let mode = 'ordered';

    function onModeChange() {
        mode = document.getElementById('mode').value;
        const wrapper = document.getElementById('question-number-wrapper');
        if (mode === 'ordered') {
            wrapper.style.display = 'inline-block';
        } else {
            wrapper.style.setProperty('display', 'none', 'important');
        }

        loadQuestion(); // Load lại câu theo chế độ mới
    }

    function loadQuestion() {

        let url = `/api/lesson/${lesson}/random-question`;
        if (currentQuestionId) url =  `/api/lesson/${lesson}/random-question?exclude_id=${currentQuestionId}`;

        if (mode === 'ordered') {
            const questionNumber = document.getElementById('question-number').value;
            if (questionNumber === '' || questionNumber < 1) {
                document.getElementById('exercise-area').innerHTML = `<p class="text-danger">Vui lòng nhập số câu hợp lệ.</p>`;
                return;
            }
            url = `/api/lesson/${lesson}/ordered-question/${questionNumber}`;
        }

        fetch(url)
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    document.getElementById('exercise-area').innerHTML = `<p class="text-danger">${data.error}</p>`;
                    return;
                }

                currentAnswer = data.answer;
                currentSolution = data.solution ?? '';
                currentQuestionId = data.id;

                document.getElementById('exercise-area').innerHTML = `
                    <div class="card p-3">
                        <div>
                            <strong>Câu hỏi:</strong> 
                            ${data.type === 'multiple_choice' ? 'Trắc nghiệm' : 
                               data.type === 'true_false' ? 'Đúng/Sai' : 
                               data.type === 'fill_blank' ? 'Điền kết quả' : 'Không xác định'}
                            - <em>Cấp độ: ${data.level}</em>
                        </div>
                        <hr>
                        <div class="mb-2">${data.content}</div>
                        <hr>
                        <input type="text" id="user-answer" class="form-control" placeholder="Nhập câu trả lời...">
                        <button class="btn btn-primary mt-2" onclick="checkAnswer()">Chấm điểm</button>
                        <div id="result-area" class="mt-3"></div>
                    </div>
                `;

                if (window.MathJax) {
                  MathJax.typeset();
                }
            });
    }

    function checkAnswer() {
        const userAnswerInput = document.getElementById('user-answer');
        const userAnswer = userAnswerInput.value.trim();
        const resultArea = document.getElementById('result-area');
        const checkButton = event.target;

        if (userAnswer === '') {
            resultArea.innerHTML = `<span class="text-danger">Vui lòng nhập câu trả lời.</span>`;
            return;
        }

        userAnswerInput.disabled = true;
        checkButton.disabled = true;

        if (userAnswer.toLowerCase() === currentAnswer.toLowerCase()) {
            resultArea.innerHTML = `<span class="text-success">✅ Đúng rồi! Giỏi lắm!</span><br>${currentSolution}`;
        } else {
            resultArea.innerHTML = `
                <span class="text-danger">❌ Sai rồi!</span><br>
                <strong>Đáp án đúng:</strong> ${currentAnswer}<br>
                ${currentSolution}
            `;
        }

        resultArea.innerHTML += `
            <button class="btn btn-primary mt-2" onclick="nextQuestion()">Câu tiếp theo ➡️</button>
            `;
        if (window.MathJax) {
          MathJax.typeset();
        }
    }

    function nextQuestion() {
        if (mode === 'ordered') {
            const input = document.getElementById('question-number');
            let questionNumber = parseInt(input.value, 10) || 1;
            questionNumber++;
            input.value = questionNumber;
            loadQuestion(questionNumber);
        } else {
            loadQuestion(); // random mode
        }
    }
    // Auto load khi trang vừa mở
    document.addEventListener('DOMContentLoaded', loadQuestion);

    document.addEventListener("DOMContentLoaded", function() {
        let clientId = localStorage.getItem("client_id");
        fetch(`/api/lesson/view`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({
                client_id: clientId,
                model_id: {{ $lesson->id }}
            })
        });
    });

    document.addEventListener('DOMContentLoaded', function () {
        const collectionId = '{{ $lesson->id ?? '' }}';
        const clientId = localStorage.getItem('client_id');
        const container = document.getElementById('like-container');
        const likeCountSpan = document.getElementById('like-count');
        const type = 'lesson';

        if (collectionId && clientId) {
            fetch(`/api/${type}/${collectionId}/is-favorite?client_id=${clientId}`)
                .then(response => response.json())
                .then(data => {
                    updateLikeButton(data.liked);
                });

            function updateLikeButton(isLiked) {
                if (isLiked) {
                    container.innerHTML = `<button class="btn btn-secondary" id="unlike-btn">💔 Bỏ thích</button>`;
                } else {
                    container.innerHTML = `<button class="btn btn-outline-danger" id="like-btn">❤️ Thích</button>`;
                }

                // Gán lại sự kiện sau khi render
                setTimeout(() => {
                    document.getElementById('like-btn')?.addEventListener('click', function () {
                        fetch(`/api/${type}/${collectionId}/like`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            },
                            body: JSON.stringify({ client_id: clientId })
                        }).then(() => {updateLikeButton(true); updateLikeCount(1)});
                    });

                    document.getElementById('unlike-btn')?.addEventListener('click', function () {
                        fetch(`/api/${type}/${collectionId}/unlike`, {
                            method: 'DELETE',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            },
                            body: JSON.stringify({ client_id: clientId })
                        }).then(() => {updateLikeButton(false); updateLikeCount(-1)});
                    });
                }, 10);
            }

            function updateLikeCount(change) {
                const text = likeCountSpan.textContent.trim(); // ❤️123
                const number = parseInt(text.replace('❤️', '').trim());
                likeCountSpan.textContent = `❤️${number + change}`;
            }
        }
    });
</script>
@endsection
