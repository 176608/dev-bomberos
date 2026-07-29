@extends('VisorSIGEM.layouts.visor')

@section('visor_title', 'Error')

@section('visor_content')
<div class="container-fluid py-5 text-center">
    <div class="alert alert-warning d-inline-block">
        <i class="bi bi-exclamation-triangle me-2"></i>
        {{ $message }}
    </div>
</div>
@endsection
