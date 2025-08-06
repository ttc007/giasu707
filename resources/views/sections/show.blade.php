@extends('layouts.app')

@section('title', $section->title . ' - ' . $subject->name . ' | Giasu707')

@section('content')
<div class="container">
    <p><a href="{{ route('show.chapter', [
        'subject_slug' => $subject->slug,
        'chapter_slug' => $chapter->slug,
    ]) }}" class=""><strong>{{ $section->lesson->chapter->title }} - {{ $section->lesson->chapter->subject->name }}</strong></a></p>
    <h5><a href="{{ route('show.lesson', [
        'subject_slug' => $subject->slug,
        'chapter_slug' => $chapter->slug,
        'lesson_slug' => $section->lesson->slug
    ]) }}">{{ $section->lesson->title }}</a></h5>

    <h1 class="text-center pt-4">{{ $section->title }}</h1>

    <hr>
    <div>
        {!! $section->content !!}
    </div>

    <hr>
    <h4>Bài tập ôn tập</h4>
    <i class='text-danger'>*Lưu ý: làm hết câu này rồi đến câu khác. Xin đừng nôn nóng.</i>
    
    <div class="mb-3">
        <label for="mode" class="form-label">Chọn chế độ hiển thị câu hỏi:</label>
        <select id="mode" class="form-select w-auto d-inline-block" onchange="onModeChange()">
            <option value="random" selected>Ngẫu nhiên</option>
            <option value="ordered">Theo thứ tự</option>
        </select>

        <input type="number" id="question-number" class="form-control d-inline-block w-auto ms-3"
               placeholder="Số câu muốn làm" min="1" style="display: none;" onkeydown="if(event.key === 'Enter') loadQuestion()">
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
                Phần trước:{{$section->prevSection()->title}}
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
                Phần sau:{{$section->nextSection()->title}}
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
    let mode = 'random';

    function onModeChange() {
        mode = document.getElementById('mode').value;
        const inputBox = document.getElementById('question-number');

        if (mode === 'ordered') {
            inputBox.style.display = 'inline-block';
        } else {
            inputBox.style.display = 'none';
        }

        loadQuestion(); // Load lại theo chế độ mới
    }

    function loadQuestion() {
        let url = `/api/section/${sectionId}/random-question`;

        if (mode === 'ordered') {
            const questionNumber = document.getElementById('question-number').value;
            if (questionNumber === '' || questionNumber < 1) {
                document.getElementById('exercise-area').innerHTML = `<p class="text-danger">Vui lòng nhập số câu muốn làm.</p>`;
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

                document.getElementById('exercise-area').innerHTML = `
                    <div class="card p-3">
                        <div><strong>Câu hỏi:</strong></div>
                        <div class="mb-2">${data.content}</div>
                        <input type="text" id="user-answer" class="form-control" placeholder="Nhập câu trả lời...">
                        <button class="btn btn-primary mt-2" onclick="checkAnswer()">Chấm điểm</button>
                        <div id="result-area" class="mt-3"></div>
                    </div>
                `;
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

        resultArea.innerHTML += `<button class="btn btn-primary mt-2" onclick="loadQuestion()">Câu tiếp theo ➡️</button>`;
    }

    // Load câu đầu tiên khi trang vừa mở
    loadQuestion();
</script>

@endsection
