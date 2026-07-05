@extends('errors.layouts.master')

@section('title', 'Page Not Found')

@section('icon', '🔍')
@section('code', '404')

@section('message', 'Oops! Page not found')

@section('description')
    The page you’re looking for doesn’t exist or may have been moved.<br><br>

    🔎 <strong>What you can do:</strong><br>
    • Check the URL for any mistakes<br>
    • Go back to the previous page<br>
    • Visit the dashboard or homepage<br><br>

    If you believe this is an error, please contact support.
@endsection