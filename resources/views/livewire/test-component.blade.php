<div x-data @keyup="console.log($event)">
    <input type="text" id="test" onkeypress="return myKeyPress(event)" />
    <button wire:click="changeFocus">change focus</button>
    <button wire:click="add">Add</button>
    {{ $postCount }}

    <ul>
        @foreach ($components as $key => $value)
            <li id="{{ $key }}">{{ $key }}:: {{ $value['key'] }} <input type="text"
                    wire:model="components.{{ $key }}.key" id="input{{ $key }}"
                    onkeypress="return myKeyPress(event)" /></li>
        @endforeach
    </ul>

    <input type="text" >

    {{ json_encode($components) }}


</div>


@pushOnce('scripts')
    <script>
        function myKeyPress(e) {
            var keynum;
            console.log(e)

            if (window.event) { // IE
                keynum = e.key;
            } else if (e.which) { // Netscape/Firefox/Opera
                keynum = e.which;
            }

            console.log(keynum)

            Livewire.emit('postAdded', keynum)

            // alert(String.fromCharCode(keynum));
        }
    </script>

    <script>
        window.livewire.on('change-focus-other-field', function() {
            console.log('asdf')
            document.getElementById('test').focus();
        });

        window.livewire.on('focus', function(id) {
            console.log(id)
            setTimeout(() => {
                document.getElementById(`input${id}`).focus();
            }, 50);
        });
    </script>
@endPushOnce
