@extends('errors.layouts.master')

@section('title', 'Access Denied')

@section('icon', '⛔')
@section('code', '403')

@section('message', 'Access Restricted')

@section('description')
    You don’t have permission to access this page.<br><br>

    🔐 <strong>Possible reasons:</strong><br>
    • You are not logged in<br>
    • Your account lacks required permissions<br>
    • This resource is restricted<br><br>

    👉 Try logging in with the correct account or contact your administrator.
@endsection