<div style="text-align: center">


    

    <button wire:click="increment">+</button>
    <button wire:click="decrement">-</button>

    <h1>{{ $count }}</h1>

    {{-- <h1 x-data="{}" x-init="setInterval(() => { $wire.increment() }, 1000);">asdf</h1> --}}

    @for ($i = 0; $i < $count; $i++)
        <div class="card">
            <div class="card-body">
                This is some text within a card body.
            </div>
        </div>
    @endfor
</div>
