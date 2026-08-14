@extends('errors::minimal')

@section('title', __('Server Error'))
@section('code', '500')
@section('message', __('Server Error'))
@section('hint', __("Something went wrong on our end. If this keeps happening, please contact support."))
