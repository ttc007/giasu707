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

    <h3>{{ $section->title }}</h3>

    <hr>
    <div>
        {!! $section->content !!}
    </div>

    <hr>
    <h4>Bài tập ôn tập</h4>
    <i class='text-danger'>*Lưu ý: làm hết câu này rồi đến câu khác. Xin đừng nôn nóng.</i>
    
    <div id="exercise-area">
        <p><em>Đang tải câu hỏi...</em></p>
    </div>

    <hr class="mt-5">
    <div class="d-flex justify-content-between mt-4">
        @if ($section->prev_id)
            <a href="{{ route('show.section', [
                'subject_slug' => $subject->slug,
                'chapter_slug' => $chapter->slug,
                'section_slug' => $section->slug
            ]) }}" class="btn btn-outline-primary">
                ⬅️ Quay lại phần trước
            </a>
        @else
            <button class="btn btn-outline-secondary" disabled>⬅️ Quay lại phần trước</button>
        @endif

        @if ($section->next_id)
            <a href="{{ route('show.section', [
                'subject_slug' => $subject->slug,
                'chapter_slug' => $chapter->slug,
                'section_slug' => $section->slug
            ]) }}" class="btn btn-outline-primary">
                Học bài sau ➡️
            </a>
        @else
            <button class="btn btn-outline-secondary" disabled>Học bài sau ➡️</button>
        @endif
    </div>
</div>

<script>
    const sectionId = {{ $section->id }};
    let currentAnswer = '';
    let currentSolution = '';

    function loadQuestion() {
        fetch(`/api/section/${sectionId}/random-question`)
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
        const userAnswer = document.getElementById('user-answer').value.trim();
        const resultArea = document.getElementById('result-area');

        if (userAnswer === '') {
            resultArea.innerHTML = `<span class="text-danger">Vui lòng nhập câu trả lời.</span>`;
            return;
        }

        if (userAnswer.toLowerCase() === currentAnswer.toLowerCase()) {
            resultArea.innerHTML = `<span class="text-success">✅ Đúng rồi! Giỏi lắm!</span><br>${currentSolution}`;
        } else {
            resultArea.innerHTML = `
                <span class="text-danger">❌ Sai rồi!</span><br>
                <strong>Đáp án đúng:</strong> ${currentAnswer}<br>
                ${currentSolution}
            `;
        }

        resultArea.innerHTML += `<button class="btn btn-outline-secondary mt-2" onclick="loadQuestion()">Câu tiếp theo ➡️</button>`;
    }

    loadQuestion(); // Load câu đầu tiên khi trang vừa mở
</script>

@endsection
