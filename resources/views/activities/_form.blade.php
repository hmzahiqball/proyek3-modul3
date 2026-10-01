<div>
    <label for="code">Kode Kegiatan</label>
    <input id="code" name="code" value="{{ old('code', $activity->code ?? '') }}">
    @error('code')
        <p class="error" style="color: red;">{{ $message }}</p>
    @enderror
</div>

<div>
    <label for="title">Judul</label>
    <input id="title" name="title" value="{{ old('title', $activity->title ?? '') }}">
    @error('title')
        <p class="error" style="color: red;">{{ $message }}</p>
    @enderror
</div>

<div>
    <label for="description">Deskripsi</label>
    <textarea id="description" name="description">{{ old('description', $activity->description ?? '') }}</textarea>
    @error('description')
        <p class="error" style="color: red;">{{ $message }}</p>
    @enderror
</div>

<div>
    <label for="category_id">Kategori</label>
    <select id="category_id" name="category_id">
        <option value="">-- Pilih Kategori --</option>
        @foreach($categories as $category)
            <option value="{{ $category->id }}" {{ old('category_id', $activity->category_id ?? '') == $category->id ? 'selected' : '' }}>
                {{ $category->name }}
            </option>
        @endforeach
    </select>
    @error('category_id')
        <p class="error" style="color: red;">{{ $message }}</p>
    @enderror
</div>

<div>
    <label for="start_at">Tanggal Mulai</label>
    <input type="date" id="start_at" name="start_at" value="{{ old('start_at', isset($activity->start_at) ? $activity->start_at->format('Y-m-d') : '') }}">
    @error('start_at')
        <p class="error" style="color: red;">{{ $message }}</p>
    @enderror
</div>

<div>
    <label for="end_at">Tanggal Selesai</label>
    <input type="date" id="end_at" name="end_at" value="{{ old('end_at', isset($activity->end_at) ? $activity->end_at->format('Y-m-d') : '') }}">
    @error('end_at')
        <p class="error" style="color: red;">{{ $message }}</p>
    @enderror
</div>

<div>
    <label for="capacity">Kapasitas</label>
    <input type="number" id="capacity" name="capacity" min="1" max="500" value="{{ old('capacity', $activity->capacity ?? '1') }}">
    @error('capacity')
        <p class="error" style="color: red;">{{ $message }}</p>
    @enderror
</div>

<div>
    <label for="status">Status</label>
    <select id="status" name="status">
        <option value="draft" {{ old('status', $activity->status ?? '') == 'draft' ? 'selected' : '' }}>Draft</option>
        <option value="published" {{ old('status', $activity->status ?? '') == 'published' ? 'selected' : '' }}>Published</option>
        <option value="completed" {{ old('status', $activity->status ?? '') == 'completed' ? 'selected' : '' }}>Completed</option>
    </select>
    @error('status')
        <p class="error" style="color: red;">{{ $message }}</p>
    @enderror
</div>
