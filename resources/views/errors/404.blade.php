@extends('errors::minimal')

@section('title', __('Not Found'))
@section('code', '404')
@section('message', __('Page Not Found'))
@section('hint', __("The page you're looking for doesn't exist or may have been moved."))
