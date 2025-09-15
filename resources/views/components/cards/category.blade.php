<div class="col-6 col-md-4 mb-4">
    <a href="/category/{{ $category->slug }}" wire:navigate class="text-decoration-none">
        <div class="card border-0 rounded shadow-sm">
            <div class="card-body d-flex align-items-center">
                <img src="{{ asset('/storage/' . $category->image) }}" class="img-fluid me-2" width="40">
                <label class="mb-0">{{ $category->name }}</label>
            </div>
        </div>
    </a>
</div>
