@extends('errors::minimal')

@section('title', __('Server Error'))
@section('code', 'Session Expired Login to Continue')
@section('message')
<div class="ml-4 text-lg text-gray-500 uppercase tracking-wider"><a href="{{ route('home') }}">Back Home</a></div>

@endsection
