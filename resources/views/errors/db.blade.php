@extends('errors.layouts.master')

@section('title', 'Database Error')

@section('icon', '🗄️')
@section('code', 'DB')

@section('message', 'Database Error Occurred')

@section('description')
    Something went wrong while processing your request.<br><br>

    ⚠️ <strong>Possible reasons:</strong><br>
    • Missing required data (like date, email, etc.)<br>
    • System is temporarily unavailable<br>
    • Invalid input provided<br><br>

    👉 Please check your input and try again.<br>
    If the issue persists, contact support.
@endsection