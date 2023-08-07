<x-app-layout-component>
    <h1>{{$project->title}}</h1>
    <div class="my-5">
        <livewire:add-project-items :project="$project" />
    </div>

</x-app-layout-component>
