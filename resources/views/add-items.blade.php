<x-app-layout-component>
    <h1>{{$project->title}}</h1>
    <a href="{{route('project',$project->id)}}">Goto counter page</a>
    <div class="my-5">
        <livewire:add-project-items :project="$project" />
    </div>

</x-app-layout-component>
