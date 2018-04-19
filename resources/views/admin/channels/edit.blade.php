@extends ('admin.layout.app')

@section('administration-content')
    <form method="post" action="{{ route('admin.channels.update', $channel) }}">
        {{ method_field('patch') }}
        @include('admin.channels._form', ['buttonText' => 'Update Channel'])
    </form>
@endsection