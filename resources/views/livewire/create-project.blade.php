<div class="card">
    <div class="card-body">
        <h3>Create a new project</h3>
        <form wire:submit.prevent="submit">
            <div class="mb-3">
                <label for="exampleInputEmail1" class="form-label">Project Name</label>
                <input type="text" class="form-control" id="exampleInputEmail1" wire:model="title">
                 @error('title') <span class="error">{{ $message }}</span> @enderror
            </div>
            <button type="submit" class="btn btn-primary">Create</button>
        </form>
    </div>
</div>
