@extends('errors.layout')

@section('title', '500 - Kesalahan Server')
@section('code', '500')
@section('badge-class', 'badge-danger')
@section('badge-text', '500 • Kendala Server Internal')
@section('message')
    Terjadi kendala teknis saat server memproses permintaan Anda. Sistem telah mencatat rincian kejadian ini untuk penanganan lebih lanjut. Silakan coba muat ulang halaman atau kembali beberapa saat lagi.
@endsection
