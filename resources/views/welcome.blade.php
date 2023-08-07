<x-app-layout-component>
    <div>
        <livewire:create-project />
        {{-- <livewire:counter /> --}}
    </div>

    <h2>Projects</h2>

    <ul class="list-group">
        @foreach ($projects as $project)
            <li class="list-group-item">
                <a class="mr-2 inline-block" href="{{route('project',['id' => $project->id])}}">{{ $project->title }}</a>

                <a href="{{route('project.addItems',$project->id)}}" class="btn btn-sm btn-primary">Add time slot/count items</a>
                <a href="{{route('project.view',$project->id)}}" class="btn btn-sm btn-primary">view data</a>
                <a href="{{route('project.export',$project->id)}}" class="btn btn-sm btn-secondary">Export to excel</a>
                <a href="{{route('project.delete',$project->id)}}" class="btn btn-sm btn-danger">Delete</a>
            </li>
        @endforeach
    </ul>


    {{ $projects->links() }}
</x-app-layout-component>
