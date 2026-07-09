@extends('invitations.layout')

@section('title', __('core.invitations.mismatch_title'))

@section('content')
    <h1>{{ __('core.invitations.mismatch_title') }}</h1>
    <p>{{ __('core.invitations.mismatch_text', [
        'invited' => $invitation->email,
        'current' => $current->email,
    ]) }}</p>
    <p class="muted">{{ __('core.invitations.mismatch_hint') }}</p>
@endsection
