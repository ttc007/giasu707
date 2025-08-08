@csrf

<div class="mb-3">
    <label>Tiêu đề</label>
    <input type="text" name="title" class="form-control" value="{{ old('title', $post->title ?? '') }}" required>
</div>

<!-- <div class="mb-3">
    <label for="image" class="form-label">Ảnh đại diện</label>
    @if(isset($post) && $post->image)
        <img src="{{ asset($post->image) }}" alt="Ảnh bài viết" style="max-width: 200px;">
    @endif
    <input type="file" name="image" class="form-control" accept="image/*">
</div> -->

<div class="mb-3">
    <label>Danh mục</label>
    <select name="category_id" id="category_id" class="form-select" required>
        <option value="">-- Select --</option>
        @foreach($categories as $cat)
            <option value="{{ $cat->id }}" @selected(old('category_id', $post->category_id ?? '') == $cat->id)>
                {{ $cat->name }}
            </option>
        @endforeach
    </select>
</div>

<div class="mb-3">
    <label>Tuyển tập</label>
    <select name="collection_id" id="collection_id" class="form-select" required>
        <option value="">-- Select --</option>
        @if(isset($collections))
            @foreach($collections as $col)
                <option value="{{ $col->id }}" @selected(old('collection_id', $post->collection_id ?? '') == $col->id)>
                    {{ $col->title }}
                </option>
            @endforeach
        @endif
    </select>
</div>

<div class="mb-3">
    <label>Nội dung</label>
    <textarea name="content" class="form-control"  id="editor"  rows="5">{{ old('content', $post->content ?? '') }}</textarea>
</div>

<button class="btn btn-success">Lưu</button>

<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
<script>
ClassicEditor
    .create(document.querySelector('#editor'), {
        ckfinder: {
            uploadUrl: '/upload?_token={{ csrf_token() }}'
        }
    })
    .catch(error => {
        console.error(error);
    });

const categorySelect = document.getElementById('category_id');
const collectionSelect = document.getElementById('collection_id');

categorySelect.addEventListener('change', function () {
    const categoryId = this.value;
    collectionSelect.innerHTML = `<option value="">Loading...</option>`;

    fetch(`/api/collections-by-category/${categoryId}`)
        .then(response => response.json())
        .then(data => {
            collectionSelect.innerHTML = `<option value="">-- Select --</option>`;
            data.forEach(collection => {
                const option = document.createElement('option');
                option.value = collection.id;
                option.textContent = collection.title;
                collectionSelect.appendChild(option);
            });
        })
        .catch(error => {
            console.error('Error fetching chapters:', error);
            collectionSelect.innerHTML = `<option value="">-- Select --</option>`;
        });
});
</script>