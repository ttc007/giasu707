@extends('layouts.app')

@section('title', $lesson->title . ' - ' . $chapter->title)

@section('content')
<div class="container section">
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
                               data.type === 'fill_blank' ? 'Điền khuyết' : 'Không xác định'}
                            - <em>Cấp độ: ${data.level}</em>
                        </div>
                        <hr>
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

        resultArea.innerHTML += `
            <button class="btn btn-primary mt-2" onclick="nextQuestion()">Câu tiếp theo ➡️</button>
            `;
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
</script>
@endsection
