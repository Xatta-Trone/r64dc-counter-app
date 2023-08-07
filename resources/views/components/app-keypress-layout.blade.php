<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Route64DC Count Data Sheet</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
    @livewireStyles
</head>

<body x-data="initLayout()" @keypress="handleClick($event)">
    <div class="container">
        <h1 class="text-center">Route64DC Count Data Sheet</h1>
        <p><a href="{{ route('home') }}">Home</a></p>
        {{ $slot }}
    </div>

    @livewireScripts
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm" crossorigin="anonymous">
    </script>

    @stack('scripts')

    <script>
        function initLayout() {
            return {
                handleClick(e) {
                    console.log(e)
                    Livewire.emit('clickKey', e.key)
                }
            }
        }
    </script>





</body>

</html>