<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>

<style>
        table, th, td {
  border: 1px solid black;
  border-collapse: collapse;
}
</style>
</head>
<body>

    <table>
    <thead>
        <tr>
            <th>Time</th>
            <th>Movement</th>

            @if ($project->data)
                @foreach ($project->data[0]['data'] as $item)
                    <th>{{ $item['title'] }}</th>
                @endforeach

            @endif

        </tr>
    </thead>
    <tbody>
        @if ($project->data)
            @foreach ($project->data as $singleItem)
                <tr>
                    <td rowspan="3">{{ $singleItem['start_time'] }}-{{ $singleItem['end_time'] }}</td>
                    <td>L</td>
                    @foreach ($singleItem['data'] as $item)
                        <td>{{ $item['left'] }}</td>
                    @endforeach
                </tr>

                <tr>
                    <td>T</td>
                    @foreach ($singleItem['data'] as $item)
                        <td>{{ $item['through'] }}</td>
                    @endforeach
                </tr>

                <tr>
                    <td>R</td>
                    @foreach ($singleItem['data'] as $item)
                        <td>{{ $item['right'] }}</td>
                    @endforeach
                </tr>
            @endforeach

        @endif
    </tbody>
</table>



</body>
</html>
