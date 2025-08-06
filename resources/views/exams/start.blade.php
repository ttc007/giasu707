@extends('layouts.app')

@section('title', 'Bắt đầu thi thử | Giasu707')

@section('content')
<style>
    #timer {
        position: fixed;
        top: 320px;
        right: 230px;
        background-color: white;
        padding: 10px 15px;
        border: 2px solid red;
        border-radius: 5px;
        z-index: 1000;
        font-size: 24px;
        font-weight: bold;
        color: red;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }

    .result {
        background-color: #f9f9f9; /* nền xám nhạt */
        border: 1px solid #ddd;    /* viền xám mờ */
        padding: 10px;
        margin-top: 10px;
        border-radius: 5px;
        font-size: 14px;
    }

    /* Mobile: màn hình nhỏ hơn hoặc bằng 768px */
    @media (max-width: 768px) {
        #timer {
            top: 10px;
            right: 10px;
            font-size: 18px;
            padding: 8px 12px;
        }
    }
</style>

<div class="container">
    <div class="text-center">
        <h2 class="mb-3">Thi thử</h2>
        <h4 class="mb-4">{{$exam->title}}  - Môn: {{$exam->subject->name}}</h4>
        <div id="timer" style="font-size: 24px; font-weight: bold; color: red;" class='mb-4'>
            50:00
        </div>
    </div>

        {{-- Phần trắc nghiệm --}}
        @if($multipleChoiceQuestions->count())
            <h4>Phần 1: Trắc nghiệm</h4>
            @foreach ($multipleChoiceQuestions as $index => $question)
                <div class="card mb-3 question-block" data-question-id="{{ $question->id }}" data-answer="{{ $question->answer }}" data-solution="{{ $question->solution }}" data-type="multiple_choice">
                    <div class="card-body">
                        <strong>Câu {{ $index + 1 }}:</strong>
                        {!! $question->content !!}

                        <input type="text" class="form-control mt-2 w-auto" name="answers[{{ $question->id }}]" placeholder="Nhập đáp án của bạn">

                        <div class="result" style="display:none; margin-top: 10px;"></div>

                    </div>
                </div>
            @endforeach
        @endif

        {{-- Phần đúng sai --}}
        @if($trueFalseQuestions->count())
            <h4>Phần 2: Đúng / Sai</h4>
            @foreach ($trueFalseQuestions as $index => $question)
                <div class="card mb-3 question-block" data-question-id="{{ $question->id }}" data-answer="{{ $question->answer }}" data-solution="{{ $question->solution }}" data-type="true_false">
                    <div class="card-body">
                        <strong>Câu {{ $index + 1}}:</strong>
                        {!! $question->content !!}

                        <input type="text" class="form-control mt-2 w-auto" name="answers[{{ $question->id }}]" placeholder="Nhập đáp án của bạn">

                        <div class="result" style="display:none; margin-top: 10px;"></div>

                    </div>
                </div>
            @endforeach
        @endif

        {{-- Phần điền đáp án --}}
        @if($fillBlankQuestions->count())
            <h4>Phần 3: Điền đáp án</h4>
            @foreach ($fillBlankQuestions as $index => $question)
                <div class="card mb-3 question-block" data-question-id="{{ $question->id }}" data-answer="{{ $question->answer }}" data-solution="{{ $question->solution }}" data-type="fill_blank">
                    <div class="card-body">
                        <strong>Câu {{ $index + 1}}:</strong>
                        {!! $question->content !!}

                        <input type="text" class="form-control mt-2 w-auto" name="answers[{{ $question->id }}]" placeholder="Nhập đáp án của bạn">

                        <div class="result" style="display:none; margin-top: 10px;"></div>

                    </div>
                </div>
            @endforeach
        @endif

        <!-- ... danh sách câu hỏi ở đây ... -->

        <!-- Thêm div kết quả tổng điểm ở cuối -->
        <div id="final-score" style="margin-top:20px; font-weight:bold; font-size:18px;"></div>

        <button onclick="submitExam()" class="btn btn-primary" id="submit-btn">Nộp bài</button>
</div>

<script>
    let totalSeconds = 50 * 60; // 50 phút

    function updateTimerDisplay() {
        let minutes = Math.floor(totalSeconds / 60);
        let seconds = totalSeconds % 60;

        // Thêm số 0 nếu nhỏ hơn 10
        minutes = minutes < 10 ? '0' + minutes : minutes;
        seconds = seconds < 10 ? '0' + seconds : seconds;

        document.getElementById('timer').innerText = `${minutes}:${seconds}`;
    }

    function startCountdown() {
        updateTimerDisplay(); // cập nhật ngay từ đầu

        const countdown = setInterval(() => {
            totalSeconds--;

            if (totalSeconds < 0) {
                clearInterval(countdown);
                alert("Hết giờ làm bài!");
                // TODO: submit form tự động ở đây nếu muốn
                // document.getElementById("examForm").submit();
            }

            updateTimerDisplay();
        }, 1000);
    }

    startCountdown();

function submitExam() {
    let total = 0;

    document.querySelectorAll('.question-block').forEach(block => {
        const qid = block.dataset.questionId;
        const correctAnswer = block.dataset.answer.trim();
        const solution = block.dataset.solution;
        const type = block.dataset.type;
        const inputs = block.querySelectorAll('input');
        let userAnswer = '';

        inputs.forEach(input => {
            if ((input.type === 'radio' && input.checked) || input.type === 'text') {
                userAnswer = input.value.trim();
            }

        });

        const resultDiv = block.querySelector('.result');
        resultDiv.style.display = 'block';


        if (type === 'multiple_choice' || type === 'fill_blank') {
            if (userAnswer.toLowerCase() === correctAnswer.toLowerCase()) {
                total += 0.25;
                resultDiv.innerHTML = `<span style="color:green">✅ Đúng +0.25 điểm</span><br>
                    <strong>Lời giải:</strong> ${solution}`;
            } else {
                resultDiv.innerHTML = `
                    <span style="color:red">❌ Sai</span><br>
                    <strong>Đáp án đúng:</strong> ${correctAnswer}<br>
                    <strong>Lời giải:</strong> ${solution}`;
            }
        } else if (type === 'true_false') {
            let correctAnswers = correctAnswer.split('-');
            let userAnswers = userAnswer.split('-');
            let count = 0;
            var point = 0;

            for (let i = 0; i < 4; i++) {
                if (correctAnswers[i]?.trim().toUpperCase() === userAnswers[i]?.trim().toUpperCase()) {
                    count++;
                }
            }

            if (count === 4) point = 1;
            else if (count === 3) point = 0.5;
            else if (count === 2) point += 0.25;
            else if (count === 1) point += 0.1;

            total += point;

            resultDiv.innerHTML = `
                <span style="color:blue">✅ Bạn đúng ${count}/4 ý +${point} điểm</span><br>
                <strong>Đáp án đúng:</strong> ${correctAnswer}<br>
                <strong>Lời giải:</strong> ${solution}`;
        }

        // Disable tất cả các input trong mỗi câu
        block.querySelectorAll('input').forEach(input => input.disabled = true);
    });

    if (window.MathJax) {
      MathJax.typeset();
    }

    // 1. Xoá đồng hồ
    const countdown = document.getElementById('timer');
    if (countdown) {
        countdown.style.display = 'none';
    }

    // 2. Disable nút nộp bài
    const submitBtn = document.getElementById('submit-btn');
        if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerText = 'Đã nộp bài';
        submitBtn.classList.add('btn-disabled'); // tuỳ bạn định nghĩa class CSS cho hiệu ứng
    }

    alert(`Bạn làm được: ${total} điểm`);
    document.getElementById('final-score').innerHTML = `🎯 Tổng điểm của bạn: <strong>${total.toFixed(2)}</strong>`;
}
</script>

@endsection
