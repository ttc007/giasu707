@extends('layouts.app')

@section('title', $lesson->title . ' - ' . $chapter->title)

@section('content')
<div class="container">
    <p><a href="{{ route('show.chapter', [
        'subject_slug' => $subject->slug,
        'chapter_slug' => $chapter->slug,
    ]) }}" class=""><strong>{{ $lesson->chapter->title }} - {{ $lesson->chapter->subject->name }}</strong></a></p>
    <h2 class="my-3">{{ $lesson->title }}</h2>

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
    <div class="card p-3 mb-4">
        <h5 class="text-muted">Nội dung tổng hợp</h5>
        {!! $lesson->summary !!}
    </div>

    <hr>
    <h4>Bài tập ôn tập</h4>
    <i class='text-danger'>*Lưu ý: làm hết câu này rồi đến câu khác. Xin đừng nôn nóng.</i>
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
        const lessonId = {{ $lesson->id }};
        let currentAnswer = '';
        let currentSolution = '';

        function loadQuestion() {
            fetch(`/api/lesson/${lessonId}/random-question`)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        document.getElementById('exercise-area').innerHTML = `<p>${data.error}</p>`;
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
            const checkButton = event.target; // chính là nút vừa được bấm

            if (userAnswer === '') {
                resultArea.innerHTML = `<span class="text-danger">Vui lòng nhập câu trả lời.</span>`;
                return;
            }

            // Disable input và nút sau khi đã chấm điểm
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

        loadQuestion();
    </script>
@endsection
