<x-app-layout-component>
    <div>
        <livewire:create-project />
        {{-- <livewire:counter /> --}}
    </div>

    <h2>Projects</h2>

    <ul class="list-group">
        @foreach ($projects as $project)
            <li class="list-group-item">
                <a href="{{route('project',['id' => $project->id])}}">{{ $project->title }}</a>
            </li>
        @endforeach
    </ul>


    {{ $projects->links() }}
</x-app-layout-component>
