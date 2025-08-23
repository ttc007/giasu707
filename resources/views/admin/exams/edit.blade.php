@extends('layouts.admin')

@section('content')
<h2>Cập nhật đề thi</h2>

<form method="POST" action="{{ route('exams.update', $exam) }}">
    @csrf @method('PUT')
    <button class="btn btn-success">Lưu thay đổi</button>

    <div class="mb-3">
        <label>Tiêu đề</label>
        <input type="text" name="title" value="{{ $exam->title }}" class="form-control" required>
    </div>

    <div class="mb-3">
        <label>Mô tả</label>
        <textarea name="description" class="form-control" rows="3">{{ $exam->description }}</textarea>
    </div>

    <div class="mb-3">
        <label class="form-label">Thuộc môn học</label>
        <select name="subject_id" class="form-select" required>
            <option value="">-- Chọn môn học --</option>
            @foreach ($subjects as $subject)
                <option value="{{ $subject->id }}" {{ $exam->subject_id == $subject->id ? 'selected' : '' }}>
                    {{ $subject->name }}
                </option>
            @endforeach
        </select>
    </div>

    <hr>
    <h3 class="pt-4">Cấu trúc đề thi: {{ $exam->title }}</h3>

    <!-- Trắc nghiệm -->
    <div class="mb-4">
        <h4>Trắc nghiệm (18 câu)</h4>
        @php $mcqs = $exam->questions->where('type', 'multiple_choice')->values(); @endphp
        @for ($i = 0; $i < 18; $i++)
            <div class="card p-3 mb-3">
                <label><b>Câu {{ $i+1 }}:</b></label>
                <textarea id="editor_mcq_{{ $i }}" name="mcq[{{ $i }}][content]" class="form-control">@isset($mcqs[$i]){{ $mcqs[$i]->content }}@endisset</textarea>

                <label class="mt-2">Câu trả lời:</label>
                <input type="text" name="mcq[{{ $i }}][answer]" class="form-control"
                    value="@isset($mcqs[$i]){{ $mcqs[$i]->answer }}@endisset">

                <label class="mt-2">Lời giải:</label>
                <textarea id="solution_mcq_{{ $i }}" name="mcq[{{ $i }}][solution]" class="form-control">@isset($mcqs[$i]){{ $mcqs[$i]->solution }}@endisset</textarea>

                <label class="mt-2">Thuộc Section:</label>
                <select name="mcq[{{ $i }}][section_id]" class="form-select">
                    @foreach($sections as $section)
                        <option value="{{ $section->id }}"
                            @if(isset($mcqs[$i]) && $mcqs[$i]->section_id == $section->id) selected @endif>
                            {{ $section->lesson->chapter->title }} - {{ $section->lesson->title }} - {{ $section->title }}
                        </option>
                    @endforeach
                </select>

                <label class="mt-2">Cấp độ</label>
                <select name="mcq[{{ $i }}][level]" class="form-control">
                    <option value="Nhận biết" @if(isset($mcqs[$i]) && $mcqs[$i]->level == 'Nhận biết') selected @endif>Nhận biết</option>
                    <option value="Thông hiểu" @if(isset($mcqs[$i]) && $mcqs[$i]->level == 'Thông hiểu') selected @endif>Thông hiểu</option>
                    <option value="Vận dụng" @if(isset($mcqs[$i]) && $mcqs[$i]->level == 'Vận dụng') selected @endif>Vận dụng</option>
                    <option value="Vận dụng cao" @if(isset($mcqs[$i]) && $mcqs[$i]->level == 'Vận dụng cao') selected @endif>Vận dụng cao</option>
                </select>

                @isset($mcqs[$i])
                    <input type="hidden" name="mcq[{{ $i }}][id]" value="{{ $mcqs[$i]->id }}">
                @endisset
            </div>
        @endfor
    </div>

    <!-- Đúng / Sai -->
    <div class="mb-4">
        <h4>Đúng / Sai (4 câu)</h4>
        @php $tfs = $exam->questions->where('type', 'true_false')->values(); @endphp
        @for ($i = 0; $i < 4; $i++)
            <div class="card p-3 mb-3">
                <label><b>Câu {{ $i+1 }}:</b></label>
                <textarea id="editor_tf_{{ $i }}" name="truefalse[{{ $i }}][content]" class="form-control">@isset($tfs[$i]){{ $tfs[$i]->content }}@endisset</textarea>

                <label class="mt-2">Câu trả lời:</label>
                <input type="text" name="truefalse[{{ $i }}][answer]" class="form-control"
                    value="@isset($tfs[$i]){{ $tfs[$i]->answer }}@endisset">

                <label class="mt-2">Lời giải:</label>
                <textarea id="solution_tf_{{ $i }}" name="truefalse[{{ $i }}][solution]" class="form-control">@isset($tfs[$i]){{ $tfs[$i]->solution }}@endisset</textarea>

                <label class="mt-2">Thuộc Section:</label>
                <select name="truefalse[{{ $i }}][section_id]" class="form-select">
                    @foreach($sections as $section)
                        <option value="{{ $section->id }}"
                            @if(isset($tfs[$i]) && $tfs[$i]->section_id == $section->id) selected @endif>
                            {{ $section->lesson->chapter->title }} - {{ $section->lesson->title }} - {{ $section->title }}
                        </option>
                    @endforeach
                </select>

                <label class="mt-2">Cấp độ</label>
                <select name="truefalse[{{ $i }}][level]" class="form-control">
                    <option value="Nhận biết" @if(isset($tfs[$i]) && $tfs[$i]->level == 'Nhận biết') selected @endif>Nhận biết</option>
                    <option value="Thông hiểu" @if(isset($tfs[$i]) && $tfs[$i]->level == 'Thông hiểu') selected @endif>Thông hiểu</option>
                    <option value="Vận dụng" @if(isset($tfs[$i]) && $tfs[$i]->level == 'Vận dụng') selected @endif>Vận dụng</option>
                    <option value="Vận dụng cao" @if(isset($tfs[$i]) && $tfs[$i]->level == 'Vận dụng cao') selected @endif>Vận dụng cao</option>
                </select>

                @isset($tfs[$i])
                    <input type="hidden" name="truefalse[{{ $i }}][id]" value="{{ $tfs[$i]->id }}">
                @endisset
            </div>
        @endfor
    </div>

    <!-- Điền kết quả -->
    <div class="mb-4">
        <h4>Điền kết quả (6 câu)</h4>
        @php $fbs = $exam->questions->where('type', 'fill_blank')->values(); @endphp
        @for ($i = 0; $i < 6; $i++)
            <div class="card p-3 mb-3">
                <label><b>Câu {{ $i+1 }}:</b></label>
                <textarea id="editor_fb_{{ $i }}" name="fillblank[{{ $i }}][content]" class="form-control">@isset($fbs[$i]){{ $fbs[$i]->content }}@endisset</textarea>

                <label class="mt-2">Câu trả lời:</label>
                <input type="text" name="fillblank[{{ $i }}][answer]" class="form-control"
                    value="@isset($fbs[$i]){{ $fbs[$i]->answer }}@endisset">

                <label class="mt-2">Lời giải:</label>
                <textarea id="solution_fb_{{ $i }}" name="fillblank[{{ $i }}][solution]" class="form-control">@isset($fbs[$i]){{ $fbs[$i]->solution }}@endisset</textarea>

                <label class="mt-2">Thuộc Section:</label>
                <select name="fillblank[{{ $i }}][section_id]" class="form-select">
                    @foreach($sections as $section)
                        <option value="{{ $section->id }}"
                            @if(isset($fbs[$i]) && $fbs[$i]->section_id == $section->id) selected @endif>
                            {{ $section->lesson->chapter->title }} - {{ $section->lesson->title }} - {{ $section->title }}
                        </option>
                    @endforeach
                </select>

                <label class="mt-2">Cấp độ</label>
                <select name="fillblank[{{ $i }}][level]" class="form-control">
                    <option value="Nhận biết" @if(isset($fbs[$i]) && $fbs[$i]->level == 'Nhận biết') selected @endif>Nhận biết</option>
                    <option value="Thông hiểu" @if(isset($fbs[$i]) && $fbs[$i]->level == 'Thông hiểu') selected @endif>Thông hiểu</option>
                    <option value="Vận dụng" @if(isset($fbs[$i]) && $fbs[$i]->level == 'Vận dụng') selected @endif>Vận dụng</option>
                    <option value="Vận dụng cao" @if(isset($fbs[$i]) && $fbs[$i]->level == 'Vận dụng cao') selected @endif>Vận dụng cao</option>
                </select>

                @isset($fbs[$i])
                    <input type="hidden" name="fillblank[{{ $i }}][id]" value="{{ $fbs[$i]->id }}">
                @endisset
            </div>
        @endfor
    </div>

    <button class="btn btn-success">Lưu thay đổi</button>
    <a href="{{ route('exams.index') }}" class="btn btn-secondary">Quay lại</a>
</form>


<!-- CKEditor -->
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        document.querySelectorAll("textarea[id^='editor_'], textarea[id^='solution_']").forEach((el) => {
            ClassicEditor.create(el, {
                ckfinder: {
                    uploadUrl: '/upload?_token={{ csrf_token() }}'
                }
            }).catch(error => console.error(error));
        });
    });
</script>

@endsection
