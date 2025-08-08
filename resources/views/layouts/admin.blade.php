<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Admin Panel')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <!-- Bootstrap CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/icon.png') }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

</head>
<body>
    <div class="container my-4">
        <h1 class="mb-4">Trang Quản Trị - Gia Sư 707</h1>
        <nav class="mb-4">
            <a href="{{ route('subjects.index') }}">Môn học</a> |
            <a href="{{ route('chapters.index') }}">Chương</a> |
            <a href="{{ route('lessons.index') }}">Bài</a> |
            <a href="{{ route('sections.index') }}">Phần</a> |
            <a href="{{ route('questions.index') }}">Câu hỏi</a> |
            <a href="{{ route('exams.index') }}">Đề thi thử</a> |
            <a href="{{ route('categories.index') }}">Danh mục bài viết</a> |
            <a href="{{ route('collections.index') }}">Tuyển tập bài viết</a> |
            <a href="{{ route('posts.index') }}">Bài viết</a> |
            <a href="{{ route('admin.students.index') }}">Danh sách đăng kí học</a>
        </nav>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @yield('content')
    </div>
    

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const subjectSelect = document.getElementById('subject_id');
            const chapterSelect = document.getElementById('chapter_id');

            subjectSelect.addEventListener('change', function () {
                const subjectId = this.value;
                chapterSelect.innerHTML = `<option value="">Loading...</option>`;

                fetch(`/api/chapters-by-subject/${subjectId}`)
                    .then(response => response.json())
                    .then(data => {
                        chapterSelect.innerHTML = `<option value="">-- Select Chapter --</option>`;
                        data.forEach(chapter => {
                            const option = document.createElement('option');
                            option.value = chapter.id;
                            option.textContent = chapter.title;
                            chapterSelect.appendChild(option);
                        });
                    })
                    .catch(error => {
                        console.error('Error fetching chapters:', error);
                        chapterSelect.innerHTML = `<option value="">-- Select Chapter --</option>`;
                    });
            });

            // Chọn bài học theo chương
            document.getElementById('chapter_id').addEventListener('change', function () {
                const chapterId = this.value;
                fetch(`/api/lessons-by-chapter/${chapterId}`)
                    .then(res => res.json())
                    .then(data => {
                        const lessonSelect = document.getElementById('lesson_id');
                        lessonSelect.innerHTML = '<option value="">-- Select Lesson --</option>';
                        data.forEach(les => {
                            lessonSelect.innerHTML += `<option value="${les.id}">${les.title}</option>`;
                        });
                    });
            });

            // Chọn bài học theo chương
            document.getElementById('lesson_id').addEventListener('change', function () {
                const lessonId = this.value;
                fetch(`/api/section-by-lesson/${lessonId}`)
                    .then(res => res.json())
                    .then(data => {
                        const lessonSelect = document.getElementById('section_id');
                        lessonSelect.innerHTML = '<option value="">-- Select section --</option>';
                        data.forEach(les => {
                            lessonSelect.innerHTML += `<option value="${les.id}">${les.title}</option>`;
                        });
                    });
            });
        });
    </script>
    <script>
      window.MathJax = {
        tex: {
          inlineMath: [['$', '$'], ['\\(', '\\)']]
        },
        startup: {
          typeset: true
        }
      };
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-chtml.js"></script>
</body>
</html>
