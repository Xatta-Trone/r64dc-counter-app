<x-app-layout-component>
    <h1>{{$project->title}}</h1>
    <x-alert-component/>
    <div class="my-5">
        <livewire:project-component :project="$project" />
    </div>

</x-app-layout-component>
