@extends('errors::minimal')

@section('title', __('Page Expired'))
@section('code', '419')
@section('message', __('Session Expired'))
@section('hint', __('Your session timed out for security. Please sign in again.'))
