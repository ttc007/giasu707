@extends('layouts.app')

@section('title', $section->title . ' - ' . $subject->name . ' | Giasu707')

@section('content')
<div class="container section">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb p-2">
            <li class="breadcrumb-item">
                <a href="{{ route('show.chapter', [
                    'subject_slug' => $subject->slug,
                    'chapter_slug' => $chapter->slug
                ]) }}">
                    {{ $section->lesson->chapter->subject->name }}
                </a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('show.chapter', [
                    'subject_slug' => $subject->slug,
                    'chapter_slug' => $chapter->slug
                ]) }}">
                    {{ $section->lesson->chapter->title }}
                </a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('show.lesson', [
                    'subject_slug' => $subject->slug,
                    'chapter_slug' => $chapter->slug,
                    'lesson_slug' => $section->lesson->slug
                ]) }}">
                    {{ $section->lesson->title }}
                </a>
            </li>
        </ol>
    </nav>

    <h3 class="text-center mt-4">{{$section->title}}</h3>
    <div class="text-center" style="font-size:18px;gap: 15px; align-items: center;">
        <span id="view-count">👀 {{ $section->countView() }}</span>
        <span id="like-count">❤️{{ $section->countLikes() }}</span>
        <div id="like-container">
            @if($liked)
            <button class="btn btn-secondary" id="unlike-btn">💔 Bỏ thích</button>
            @else
            <button class="btn btn-outline-danger" id="like-btn">❤️ Thích</button>
            @endif
        </div>
        <p class="text-muted text-end">Cập nhật gần nhất: {{ $section->getUpdatedDate() }}</p>
    </div>
    <hr>
    <div>
        {!! $section->content !!}
    </div>

    <hr>
    <h4>Bài tập ôn tập({{ $section->questions_count }} tổng câu hỏi)</h4>
    <i class='text-danger'>*Lưu ý: làm hết câu này rồi đến câu khác. Xin đừng nôn nóng.</i>
    
    <div class="mb-3">
        <label for="mode" class="form-label">Chọn chế độ hiển thị câu hỏi:</label>
        <select id="mode" class="form-select w-auto d-inline-block" onchange="onModeChange()">
            <option value="ordered" >Theo thứ tự</option>
            <option value="random" selected>Ngẫu nhiên</option>
        </select>

        <div class="d-inline-block ms-3" id="question-number-wrapper" style="display: inline!important;">
            <label for="question-number">Số câu:</label>
            <input type="number" id="question-number" class="form-control d-inline-block" 
                min="1" max='{{ $section->questions_count }}' style="width: 70px;" value="1" 
                onchange="loadQuestion()">
            / {{ $section->questions_count }}
        </div>
    </div>

    <div id="exercise-area">
        <p><em>Đang tải câu hỏi...</em></p>
    </div>

    <hr class="mt-5">
    <div class="d-flex justify-content-between mt-4">
        @if ($section->prevSection())
            <a href="{{ route('show.section', [
                'subject_slug' => $subject->slug,
                'chapter_slug' => $chapter->slug,
                'section_slug' => $section->prevSection()->slug
            ]) }}" class="btn btn-outline-success">
                {{$section->prevSection()->title}}
            </a>
        @else
            <button class="btn btn-outline-success" disabled>Phần trước</button>
        @endif

        @if ($section->nextSection())
            <a href="{{ route('show.section', [
                'subject_slug' => $subject->slug,
                'chapter_slug' => $chapter->slug,
                'section_slug' => $section->nextSection()->slug
            ]) }}" class="btn btn-outline-success">
                {{$section->nextSection()->title}}
            </a>
        @else
            <button class="btn btn-outline-success" disabled>Phần sau</button>
        @endif
    </div>
</div>

<script>
    const sectionId = {{ $section->id }};
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

        let url = `/api/section/${sectionId}/random-question`;
        if (currentQuestionId) url =  `/api/section/${sectionId}/random-question?exclude_id=${currentQuestionId}`;

        if (mode === 'ordered') {
            const questionNumber = document.getElementById('question-number').value;
            if (questionNumber === '' || questionNumber < 1) {
                document.getElementById('exercise-area').innerHTML = `<p class="text-danger">Vui lòng nhập số câu hợp lệ.</p>`;
                return;
            }
            url = `/api/section/${sectionId}/ordered-question/${questionNumber}`;
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

    document.addEventListener('DOMContentLoaded', function () {
        const collectionId = '{{ $section->id ?? '' }}';
        const container = document.getElementById('like-container');
        const likeCountSpan = document.getElementById('like-count');
        const type = 'section';

        updateLikeButtonFunction();

        function updateLikeButton(isLiked) {
            if (isLiked) {
                container.innerHTML = `<button class="btn btn-secondary" id="unlike-btn">💔 Bỏ thích</button>`;
            } else {
                container.innerHTML = `<button class="btn btn-outline-danger" id="like-btn">❤️ Thích</button>`;
            }

            updateLikeButtonFunction();
        }

        function updateLikeCount(change) {
            const text = likeCountSpan.textContent.trim(); // ❤️123
            const number = parseInt(text.replace('❤️', '').trim());
            likeCountSpan.textContent = `❤️${number + change}`;
        }

        function updateLikeButtonFunction() {
            // Gán lại sự kiện sau khi render
            setTimeout(() => {
                document.getElementById('like-btn')?.addEventListener('click', function () {
                    fetch(`/api/${type}/${collectionId}/like`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        }
                    }).then(() => {updateLikeButton(true); updateLikeCount(1)});
                });

                document.getElementById('unlike-btn')?.addEventListener('click', function () {
                    fetch(`/api/${type}/${collectionId}/unlike`, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        }
                    }).then(() => {updateLikeButton(false); updateLikeCount(-1)});
                });
            }, 10);
        }
    });
</script>
@endsection
