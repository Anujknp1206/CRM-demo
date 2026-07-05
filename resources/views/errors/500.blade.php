@extends('errors.layouts.master')

@section('title', 'Server Error')

@section('icon', '💥')
@section('code', '500')

@section('message', 'Oops! Something broke')

@section('description')
    We’re experiencing a technical issue on our end.<br><br>

    ⚙️ <strong>What you can do:</strong><br>
    • Refresh the page after a few seconds<br>
    • Try again later<br>
    • Contact support if the problem persists<br><br>

    Our team has been notified and is working on a fix.
@endsection