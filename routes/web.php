<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\SubjectController as AdminSubjectController;
use App\Http\Controllers\Admin\ChapterController as AdminChapterController;
use App\Http\Controllers\Admin\SectionController as AdminSectionController;
use App\Http\Controllers\Admin\LessonController as AdminLessonController;
use App\Http\Controllers\Admin\QuestionController as AdminQuestionController;
use App\Http\Controllers\Admin\ExamController as AdminExamController;

use App\Http\Controllers\Api\QuestionController as ApiQuestionController;
use App\Http\Controllers\Api\ExamQuestionController;

use App\Http\Controllers\SubjectController;
use App\Http\Controllers\ChapterController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\HomeController;

use App\Http\Controllers\Auth\LoginController;
use App\Models\Chapter;
use App\Models\Lesson;
use App\Models\Section;

Route::get('/', [HomeController::class, 'index']);

Route::prefix('admin')->middleware('auth')->group(function () {
    Route::get('/', [AdminSubjectController::class, 'index'])->name('admin.index');
    Route::resource('subjects', AdminSubjectController::class);
    Route::resource('chapters', AdminChapterController::class);
    Route::resource('lessons', AdminLessonController::class);
    Route::resource('sections', AdminSectionController::class);
    Route::resource('questions', AdminQuestionController::class);
    Route::resource('exams', AdminExamController::class);
});

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::prefix('api')->group(function () {
    Route::delete('/exams/{exam}/questions/{question}', [ExamQuestionController::class, 'destroy']);

    Route::get('/chapters-by-subject/{subject_id}', function ($subject_id) {
        return Chapter::where('subject_id', $subject_id)->get(['id', 'title']);
    });

    Route::get('/lessons-by-chapter/{chapter_id}', function ($chapter_id) {
        return Lesson::where('chapter_id', $chapter_id)->get(['id', 'title']);
    });

    Route::get('/section-by-lesson/{lesson_id}', function ($lesson_id) {
        return Section::where('lesson_id', $lesson_id)->get(['id', 'title']);
    });

    // routes/api.php
    Route::get('/section/{section}/random-question', [ApiQuestionController::class, 'getRandom']);
    Route::get('/lesson/{lesson}/random-question', [ApiQuestionController::class, 'getRandomByLesson']);
    Route::get('/chapter/{chapter}/random-question', [ApiQuestionController::class, 'randomFromChapter']);

    // routes/web.php
    Route::get('/subject/{id}/exams', [ApiQuestionController::class, 'getExamsBySubject']);
});

Route::post('/upload', [UploadController::class, 'uploadImage'])->name('ckeditor.upload');


Route::get('/bang-gia-thiet-ke-website', [HomeController::class, 'priceTableWeb']);
// Trang môn học
Route::get('/{subject_slug}', [SubjectController::class, 'showSubject'])->name('show.subject');

// Trang chương
Route::get('/{subject_slug}/{chapter_slug}', [ChapterController::class, 'show'])->name('show.chapter');

// Trang ôn tập chương
Route::get('{subject_slug}/{chapter_slug}/on-tap', [ChapterController::class, 'review'])->name('review.chapter');

// Trang section
Route::get('{subject_slug}/{chapter_slug}/{section_slug}', [SectionController::class, 'show'])->name('show.section');

// Trang lesson
Route::get('{subject_slug}/{chapter_slug}/lesson/{lesson_slug}', [LessonController::class, 'show'])->name('show.lesson');
