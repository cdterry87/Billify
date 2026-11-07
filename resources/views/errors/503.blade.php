@extends('errors.layout')

@section('code', '503')
@section('title', 'Down for maintenance')
@section('message', config('app.name').' is being updated. Please check back in a few minutes.')
@section('action')
    <x-ui.button :href="url()->current()" block>Try again</x-ui.button>
@endsection
