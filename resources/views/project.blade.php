<x-app-keypress-layout>
    <h1>{{ $project->title }}</h1>
    <a href="{{ route('project.export', $project->id) }}" class="btn btn-primary">Download excel</a>



    <div class="my-5">
        <livewire:project-items-counter :project="$project" />
    </div>

</x-app-keypress-layout>
