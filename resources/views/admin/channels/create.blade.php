@extends('admin.layout.app')

@section('administration-content')
    <form action="{{ route('admin.channels.store') }}" method="post">
        @include('admin.channels._form')
    </form>
@endsection