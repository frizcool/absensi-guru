@extends('errors.layout')

@section('title', '419 - Sesi Halaman Berakhir')
@section('code', '419')
@section('badge-class', 'badge-warning')
@section('badge-text', '419 • Sesi Keamanan Berakhir')
@section('message')
    Halaman ini telah terbuka terlalu lama tanpa aktivitas sehingga token keamanan formulir (CSRF) kedaluwarsa. Silakan tekan tombol "Muat Ulang" di bawah atau masuk kembali melalui Portal Login.
@endsection
