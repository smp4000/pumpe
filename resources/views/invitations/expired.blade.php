@extends('invitations.layout')

@section('title', __('core.invitations.expired_title'))

@section('content')
    <h1>{{ __('core.invitations.expired_title') }}</h1>
    <p>{{ __('core.invitations.expired_text') }}</p>
@endsection
