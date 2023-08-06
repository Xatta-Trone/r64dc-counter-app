<x-app-layout-component>
    <h1>{{$project->title}}</h1>
    <x-alert-component/>
    <livewire:project-component :project="$project" />

</x-app-layout-component>
