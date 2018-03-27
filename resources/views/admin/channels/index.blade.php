@extends('admin.layout.app')

@section('administration-content')
    <p><a href="{{ route('admin.channels.create') }}" class="btn btn-sm btn-default">New channel <span class="glyphicon-plus"></span></a></p>

    <table class="table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Slug</th>
                <th>Description</th>
                <th>Threads</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($channels as $channel)
                <tr>
                    <td>{{ $channel->name }}</td>
                    <td>{{ $channel->slug }}</td>
                    <td>{{ $channel->description }}</td>
                    <td>{{ $channel->threads }}</td>
                </tr>
            @empty
                <tr>
                    <td>Nothing here.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection