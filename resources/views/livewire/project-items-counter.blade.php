<div>
    <div class="row">

    </div>
    <div class="card mb-2">
        <div class="card-body">
            <div class="row">
                <div class="col">
                    <label for="" class="mb-1">Select Time Slot</label>
                    <select class="form-select" wire:model="currentTimeIndex">
                        <option value="">Select time slot</option>
                        @foreach ($projectData as $data)
                            <option value="{{ $loop->index }}">
                                {{ $projectData[$loop->index]['start_time'] }}-{{ $projectData[$loop->index]['end_time'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col">
                    <label for="" class="mb-1">Select Current Side <span class="badge text-bg-primary">
                            {{ $currentSide }}</span></label> <br>
                    <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
                        <input type="radio" class="btn-check" id="btn-check" name="currentSide" value="left"
                            autocomplete="off" wire:model="currentSide">
                        <label class="btn btn-outline-primary" for="btn-check">Left</label>

                        <input type="radio" class="btn-check" id="btn-check-2" name="currentSide" value="through"
                            autocomplete="off" wire:model="currentSide">
                        <label class="btn btn-outline-primary" for="btn-check-2">Through</label>

                        <input type="radio" class="btn-check" id="btn-check-3" name="currentSide" value="right"
                            autocomplete="off" wire:model="currentSide">
                        <label class="btn btn-outline-primary" for="btn-check-3">Right</label>
                    </div>
                </div>
                <div class="col">
                    <label for="" class="mb-1">Auto Save data every 2 min</label> <br>
                    <div x-data="{ countdown: 120 }" x-init="window.setInterval(() => { if (countdown > 0) { countdown = countdown - 1; } else { countdown = 120;
                            Livewire.emit('saveData') } }, 1000)">
                        <div>
                            <template x-if="countdown > 0">
                                <div>
                                    <div>Saving data in <span x-text="countdown"></span>s</div>
                                </div>
                            </template>
                            <template x-if="countdown == 0">
                                <div>
                                    <div>Saving data....</div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    @if ($this->currentTimeIndex !== null)
        <div class="card my-2">
            <div class="card-header">
                Time Slot :
                {{ $projectData[$currentTimeIndex]['start_time'] }}-{{ $projectData[$currentTimeIndex]['end_time'] }}
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach ($data['data'] as $countItem)
                        <div class="col-3">
                            <h5>{{ $countItem['title'] }} || Key <kbd>{{ $countItem['key'] }}</kbd> </h5>
                            <div class="btn-group" role="group" aria-label="Basic outlined example">
                                <button type="button" class="btn btn-outline-success"
                                    wire:click="increment({{ $currentTimeIndex }},{{ $loop->index }},'left')">Left
                                    {{ $projectData[$currentTimeIndex]['data'][$loop->index]['left'] }}</button>
                                <button type="button" class="btn btn-outline-success"
                                    wire:click="increment({{ $currentTimeIndex }},{{ $loop->index }},'through')">Through
                                    {{ $projectData[$currentTimeIndex]['data'][$loop->index]['through'] }}</button>
                                <button type="button" class="btn btn-outline-success"
                                    wire:click="increment({{ $currentTimeIndex }},{{ $loop->index }},'right')">Right
                                    {{ $projectData[$currentTimeIndex]['data'][$loop->index]['right'] }}</button>
                            </div>
                            <div class="btn-group mt-2" role="group" aria-label="Basic outlined example">
                                <button type="button" class="btn btn-outline-danger"
                                    wire:click="decrement({{ $currentTimeIndex }},{{ $loop->index }},'left')">Left
                                    {{ $projectData[$currentTimeIndex]['data'][$loop->index]['left'] }}</button>
                                <button type="button" class="btn btn-outline-danger"
                                    wire:click="decrement({{ $currentTimeIndex }},{{ $loop->index }},'through')">Through
                                    {{ $projectData[$currentTimeIndex]['data'][$loop->index]['through'] }}</button>
                                <button type="button" class="btn btn-outline-danger"
                                    wire:click="decrement({{ $currentTimeIndex }},{{ $loop->index }},'right')">Right
                                    {{ $projectData[$currentTimeIndex]['data'][$loop->index]['right'] }}</button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="row mb-6">
            <div class="col">
                @if (session()->has('message'))
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        {{ session('message') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                <button type="button" class="btn btn-primary" wire:click="update">Update Data</button>
            </div>
        </div>
    @else
        <div class="alert alert-danger" role="alert">
            No time slot found. Please create a new time slot with items.
        </div>
    @endif


</div>


@pushOnce('scripts')
@endPushOnce
