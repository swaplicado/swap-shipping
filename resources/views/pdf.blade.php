@extends('layouts.principal')

@section('headStyles')
    <link rel="stylesheet" href="{{ asset('css/DataTables/datatables.css') }}">
@endsection

@section('headJs')
    <script src="{{ asset('css/DataTables/datatables.js') }}"></script>
@endsection

@section('aside')
    @include('layouts.aside')
    @endsection

@section('nav-up')
    @include('layouts.nav-up')
@endsection

@section('content')
    <h1 class="normal-text">PDF</h1>

    <iframe src='data:application/pdf;base64,{{$pdf}}' width="100%" height="700" style="border:1px solid black;"></iframe>
@endsection

@section('scripts')
    <script>
        $(document).ready( function () {

        });
    </script>
@endsection