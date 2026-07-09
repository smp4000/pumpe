@extends('invitations.layout')

@section('title', __('core.invitations.accept_title'))

@section('content')
    <h1>{{ __('core.invitations.accept_title') }}</h1>
    <p>{{ __('core.invitations.accept_intro', [
        'organization' => $invitation->organization->name,
        'email' => $invitation->email,
    ]) }}</p>

    <form method="POST" action="{{ route('invitations.store', ['token' => $invitation->token]) }}">
        @csrf

        <label for="name">{{ __('core.fields.full_name') }}</label>
        <input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus>
        @error('name') <div class="error">{{ $message }}</div> @enderror

        <label for="password">{{ __('core.fields.password') }}</label>
        <input id="password" name="password" type="password" required>
        @error('password') <div class="error">{{ $message }}</div> @enderror

        <label for="password_confirmation">{{ __('core.fields.password_confirmation') }}</label>
        <input id="password_confirmation" name="password_confirmation" type="password" required>

        <button type="submit">{{ __('core.invitations.accept_button') }}</button>
    </form>

    <p class="muted">{{ __('core.invitations.accept_footer') }}</p>
@endsection
