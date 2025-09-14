<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\SubjectController as AdminSubjectController;
use App\Http\Controllers\Admin\ChapterController as AdminChapterController;
use App\Http\Controllers\Admin\SectionController as AdminSectionController;
use App\Http\Controllers\Admin\LessonController as AdminLessonController;
use App\Http\Controllers\Admin\QuestionController as AdminQuestionController;
use App\Http\Controllers\Admin\ExamController as AdminExamController;
use App\Http\Controllers\Admin\PostController as AdminPostController;
use App\Http\Controllers\Admin\CollectionController as AdminCollectionController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\StudentController as AdminStudentController;
use App\Http\Controllers\Admin\BookController;

use App\Http\Controllers\Api\QuestionController as ApiQuestionController;
use App\Http\Controllers\Api\ExamQuestionController;

use App\Http\Controllers\SubjectController;
use App\Http\Controllers\ChapterController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\HomePostController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\BauCuaController;


use App\Http\Controllers\Auth\LoginController;
use App\Models\Chapter;
use App\Models\Lesson;
use App\Models\Section;
use App\Models\Collection;

use App\Http\Middleware\StudentAuth;

Route::get('/', [HomeController::class, 'index']);

Route::prefix('admin')->middleware('auth')->group(function () {
    Route::get('/', [AdminSubjectController::class, 'admin'])->name('admin.index');
    Route::resource('subjects', AdminSubjectController::class);
    Route::resource('chapters', AdminChapterController::class);
    Route::resource('lessons', AdminLessonController::class);
    Route::resource('sections', AdminSectionController::class);

    Route::post('update-order-questions', [AdminQuestionController::class, 'updateOrder'])->name('questions.updateOrder');
    Route::resource('questions', AdminQuestionController::class);
    Route::resource('exams', AdminExamController::class);

    Route::resource('posts', AdminPostController::class);
    Route::resource('collections', AdminCollectionController::class);
    Route::resource('categories', AdminCategoryController::class);

    Route::get('students', [AdminStudentController::class, 'index'])->name('admin.students.index');
    Route::get('views', [AdminStudentController::class, 'view'])->name('admin.students.views');
    Route::get('comments', [AdminStudentController::class, 'comment'])->name('admin.students.comments');

    Route::get('books', [BookController::class, 'index'])->name('admin.books.index');

    Route::post('/books', [BookController::class, 'store']);
});

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::prefix('api')->group(function () {
    Route::get('/chapters-by-subject/{subject_id}', function ($subject_id) {
        return Chapter::where('subject_id', $subject_id)->get(['id', 'title']);
    });

    Route::get('/lessons-by-chapter/{chapter_id}', function ($chapter_id) {
        return Lesson::where('chapter_id', $chapter_id)->get(['id', 'title']);
    });

    Route::get('/section-by-lesson/{lesson_id}', function ($lesson_id) {
        return Section::where('lesson_id', $lesson_id)->get(['id', 'title']);
    });

    Route::get('collections-by-category/{category_id}', function ($category_id) {
        return Collection::where('category_id', $category_id)->get(['id', 'title']);
    });

    // routes/api.php
    Route::get('/{type}/{id}/random-question', [ApiQuestionController::class, 'getRandom']);
    // routes/api.php
    Route::get('/{type}/{id}/ordered-question/{number}', [ApiQuestionController::class, 'getOrderedQuestion']);
    // routes/web.php
    Route::get('/subject/{id}/exams', [ApiQuestionController::class, 'getExamsBySubject']);

    // Like 
    Route::post('/{model}/{id}/like', [RegistrationController::class, 'like']);
    // Unlike 
    Route::delete('/{model}/{model_id}/unlike', [RegistrationController::class, 'unlike']);

    Route::get('/books/opening/{opening_id}/{step}', [BookController::class, 'getOpeningFirstStep']);
    Route::get('/get-book-from-variation/{id}', [BookController::class, 'getBookFromVariation']);
    Route::post('/get-book-from-image', [BookController::class, 'getBookFromImage']);
});

Route::post('/upload', [UploadController::class, 'uploadImage'])->name('ckeditor.upload');

Route::get('/bang-gia-thiet-ke-website', [HomeController::class, 'priceTableWeb']);
Route::get('/thi-thu', [HomeController::class, 'thiThu'])->name('thi-thu');
Route::post('/thi-thu/bat-dau', [HomeController::class, 'startThiThu'])->name('thi-thu.start');


Route::get('/thu-vien', [HomePostController::class, 'index'])->name('home.posts');
Route::get('/danh-muc/{slug}', [HomePostController::class, 'category'])->name('home.category');
Route::get('/tuyen-tap/{slug}', [HomePostController::class, 'collection'])->name('home.collection');
Route::get('/tuyen-tap/{slug}/{post_slug}', [HomePostController::class, 'show'])->name('home.post.show');

Route::middleware(StudentAuth::class)->group(function () {
    Route::get('/trang-ca-nhan', [RegistrationController::class, 'index'])->name('registration.index');
    Route::get('/cap-nhat-trang-ca-nhan', [RegistrationController::class, 'create'])->name('registration.create');
    Route::post('/dang-ky', [RegistrationController::class, 'update'])->name('registration.store');

    Route::post('/comments', [CommentController::class, 'store'])->name('comments.store');
});

Route::get('/dang-nhap', [StudentController::class, 'showLoginForm'])->name('student.login');
Route::post('/dang-nhap', [StudentController::class, 'login'])->name('student.login.post');
// Hiển thị form đăng ký
Route::get('/dang-ki', [StudentController::class, 'showRegisterForm'])->name('student.register');
// Xử lý đăng ký
Route::post('/dang-ki', [StudentController::class, 'register'])->name('student.register.post');
// Kích hoạt tài khoản student
Route::get('/kich-hoat-tai-khoan/{key}', [StudentController::class, 'activate'])
     ->name('student.activate');
Route::get('/dang-xuat', [StudentController::class, 'logout'])
     ->name('student.logout');

// Trang chơi game
Route::get('/co-tuong-book', [BauCuaController::class, 'index'])->name('bau-cua.index');

// Trang môn học
Route::get('/{subject_slug}', [SubjectController::class, 'showSubject'])->name('show.subject');

// Trang chương
Route::get('/{subject_slug}/{chapter_slug}', [ChapterController::class, 'show'])->name('show.chapter');

// Trang ôn tập chương
Route::get('{subject_slug}/{chapter_slug}/on-tap', [ChapterController::class, 'review'])->name('review.chapter');

// Trang lesson
Route::get('{subject_slug}/{chapter_slug}/lesson/{lesson_slug}', [LessonController::class, 'show'])->name('show.lesson');
