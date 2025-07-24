@extends('layouts.app')

@section('title', 'Ôn tập chương- ' . $chapter->title . ' | ' . $subject->name)

@section('content')
<div class="container">
    <div class="text-center">
        <p><a href="{{ route('show.chapter', [
            'subject_slug' => $subject->slug,
            'chapter_slug' => $chapter->slug,
            ]) }}" class=""><strong>{{ $chapter->title }} - {{ $chapter->subject->name }}</strong></a></p>
        <h2>Ôn tập chương</h2>    
    </div>
    
    <hr>
    <div class="card p-3 mb-4">
        <h5 class="text-muted">Nội dung tổng hợp</h5>
        {!! $chapter->summary !!}
    </div>

    {{-- Bài tập --}}
    <hr>
    <h4>Bài tập ôn tập</h4>
    <i class='text-danger'>*Lưu ý: làm hết câu này rồi đến câu khác. Xin đừng nôn nóng.</i>
    <div id="exercise-area">
        <p><em>Đang tải câu hỏi...</em></p>
    </div>

    <hr class="mt-5">
    <a href="{{ route('show.chapter', [
        'subject_slug' => $subject->slug,
        'chapter_slug' => $chapter->slug
    ]) }}" class="btn btn-outline-primary">
        ⬅️ Quay lại chương
    </a>
</div>

{{-- JavaScript --}}
<script>
    const chapterId = {{ $chapter->id }};
    let currentAnswer = '';
    let currentSolution = '';

    function loadQuestion() {
        fetch(`/api/chapter/${chapterId}/random-question`)
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    document.getElementById('exercise-area').innerHTML = `<p>${data.error}</p>`;
                    return;
                }

                currentAnswer = data.answer;
                currentSolution = data.solution ?? '';

                document.getElementById('exercise-area').innerHTML = `
                    <div class="card p-3 shadow-sm">
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

        resultArea.innerHTML += `<button class="btn btn-outline-secondary mt-3" onclick="loadQuestion()">Câu tiếp theo ➡️</button>`;
    }

    loadQuestion();
</script>
@endsection
