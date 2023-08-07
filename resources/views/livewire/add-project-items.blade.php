<div>

    @foreach ($projectData as $data)
        <div class="card my-2">
            <div class="card-header">
                <div class="row">
                    <div class="col-6">
                        <label for="projectData.{{ $loop->index }}.start_time">Start time</label>
                        <select class="form-select" id="projectData.{{ $loop->index }}.start_time"
                            wire:model="projectData.{{ $loop->index }}.start_time">
                            <option value="">Select Start time</option>
                            @foreach ($hours as $h)
                                <option value="{{ $h }}">{{ $h }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6">
                        <label for="projectData.{{ $loop->index }}.end_time">End time</label>
                        <select class="form-select" id="projectData.{{ $loop->index }}.end_time"
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
                    <ul class="list-group">
                        @foreach ($data['data'] as $countItem)
                            <li class="list-group-item"><span class="badge text-bg-primary">Item Name</span>
                                {{ $countItem['title'] }} <span class="badge text-bg-primary">Keyboard key</span>
                                <kbd>{{ $countItem['key'] }}</kbd> </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endforeach

    @if ($projectData)
        <div class="row mb-6">
            <div class="col-6">
                @if (session()->has('message'))
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        {{ session('message') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                <button type="button" class="btn btn-primary" wire:click="update">Update Data</button>
            </div>
            <div class="col-6">
                <button type="button" class="btn btn-danger" wire:click="duplicate">Duplicate</button>
            </div>
        </div>
    @else
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
                        <ul class="list-group">
                            @foreach ($newCardData['data'] as $item)
                                <li class="list-group-item">
                                    <span class="badge text-bg-primary">Item</span>
                                    :: <strong>{{ $item['title'] }}</strong>
                                    ||
                                    <span class="badge text-bg-primary">Input Key</span>
                                    ::
                                    <input id={{ $item['id'] }} type="text"
                                        wire:model="newCardData.data.{{ $loop->index }}.key"
                                        onkeypress="return myKeyPress(event)" onfocus="onFocus({{ $item['id'] }})"
                                        placeholder="Please enter the input key..." readonly />
                                </li>
                            @endforeach
                        </ul>


                        <input type="text" class="form-control mt-1" wire:model="itemToCount" />
                        <a class="btn btn-danger mt-4" wire:click="addItem">Add Item</a>
                    </div>


                    <div class="col-12 mt-2">
                        <button wire:click="save" class="btn btn-primary">Save</button>
                    </div>

                    @if (env('APP_ENV') == 'local')
                        {{ json_encode($newCardData) }}
                        {{ $mapKeyData }}
                    @endif





                </div>
            </div>
        </div>

    @endif
</div>

@pushOnce('scripts')
    <script>
        function myKeyPress(e) {
            let keynum;
            // console.log(e)

            if (window.event) { // IE
                keynum = e.key;
            }

            if (keynum == null || keynum == undefined || keynum == '') {
                return alert('There was a problem setting this key.');
            }

            console.log(keynum)

            Livewire.emit('mapKey', keynum)

            // alert(String.fromCharCode(keynum));
        }

        function onFocus(data) {
            // console.log(data.id)
            Livewire.emit('focusId', data.id);
        }

        window.livewire.on('focus', function(id) {
            // console.log(id)
            document.getElementById(`${id}`).focus();
        });
    </script>
@endPushOnce
