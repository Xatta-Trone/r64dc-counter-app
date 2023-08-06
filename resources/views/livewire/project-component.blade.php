<div>
    @if (session()->has('message'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @foreach ($projectData as $data)
        <div class="card my-2">
            <div class="card-header">
                <div class="row">
                    <div class="col-6">
                        <label for="inlineFormSelectPref">Start time</label>
                        <select class="form-select" id="inlineFormSelectPref"
                            wire:model="projectData.{{ $loop->index }}.start_time">
                            <option value="">Select Start time</option>
                            @foreach ($hours as $h)
                                <option value="{{ $h }}">{{ $h }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6">
                        <label for="inlineFormSelectPref2">End time</label>
                        <select class="form-select" id="inlineFormSelectPref2"
                            wire:model="projectData.{{ $loop->index }}.end_time">
                            <option value="">Select End time</option>
                            @foreach ($hours as $h)
                                <option value="{{ $h }}">{{ $h }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach ($data['data'] as $countItem)
                        <div class="col-3">
                            <h5>{{ $countItem['title'] }}</h5>
                            <div class="btn-group" role="group" aria-label="Basic outlined example">
                                <button type="button" class="btn btn-outline-success"
                                    wire:click="increment({{ $loop->parent->index }},{{ $loop->index }},'left')">Left
                                    {{ $countItem['left'] }}</button>
                                <button type="button" class="btn btn-outline-success"
                                    wire:click="increment({{ $loop->parent->index }},{{ $loop->index }},'through')">Through
                                    {{ $countItem['through'] }}</button>
                                <button type="button" class="btn btn-outline-success"
                                    wire:click="increment({{ $loop->parent->index }},{{ $loop->index }},'right')">Right
                                    {{ $countItem['right'] }}</button>
                            </div>
                            <div class="btn-group mt-2" role="group" aria-label="Basic outlined example">
                                <button type="button" class="btn btn-outline-danger"
                                    wire:click="decrement({{ $loop->parent->index }},{{ $loop->index }},'left')">Left
                                    {{ $countItem['left'] }}</button>
                                <button type="button" class="btn btn-outline-danger"
                                    wire:click="decrement({{ $loop->parent->index }},{{ $loop->index }},'through')">Through
                                    {{ $countItem['through'] }}</button>
                                <button type="button" class="btn btn-outline-danger"
                                    wire:click="decrement({{ $loop->parent->index }},{{ $loop->index }},'right')">Right
                                    {{ $countItem['right'] }}</button>
                            </div>
                        </div>
                    @endforeach
                </div>


            </div>
        </div>
    @endforeach

    @if ($projectData)
        <div>
            <button type="button" class="btn btn-primary" wire:click="update">Update Data</button>
        </div>
    @endif




    <div class="card mt-2">
        <div class="card-body">
            <div class="row">
                <div class="row">
                    <div class="col-6">
                        <label for="inlineFormSelectPref">Start time</label>
                        <select class="form-select" id="inlineFormSelectPref" wire:model="newCardData.start_time">
                            <option value="">Select Start time</option>
                            @foreach ($hours as $h)
                                <option value="{{ $h }}">{{ $h }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6">
                        <label for="inlineFormSelectPref2">End time</label>
                        <select class="form-select" id="inlineFormSelectPref2" wire:model="newCardData.end_time">
                            <option value="">Select End time</option>
                            @foreach ($hours as $h)
                                <option value="{{ $h }}">{{ $h }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-12">
                    <label>Items to count</label>
                    <p>
                        @foreach ($newCardData['data'] as $item)
                            <span class="badge badge-lg text-bg-primary">{{ $item['title'] }} </span>
                        @endforeach
                    </p>
                    <input type="text" class="form-control mt-1" wire:model="itemToCount" />
                    <a class="btn btn-danger mt-4" wire:click="addItem">Add Item</a>
                </div>


                <div class="col-12 mt-2">
                    <button wire:click="save" class="btn btn-primary">Save</button>

                </div>

            </div>
        </div>
    </div>
</div>
