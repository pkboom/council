@extends ('admin.layout.app')

@section('administration-content')
    <form action="{{ route('admin.channels.update', $channel->slug) }}">
        {{ method_field('patch') }}
        @include('admin.channels._form')
    </form>
@endsection