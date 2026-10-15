<div class="col-12">
    <label class="form-label fw-semibold" for="{{ $name }}">{{ $label }}</label>
    <textarea
        class="form-control ckeditor"
        style="border-radius: 0"
        id="{{ $name }}"
        name="{{ $name }}"
        cols="30"
        rows="5"
    >{{ $value }}</textarea>
</div>
