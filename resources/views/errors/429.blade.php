@extends('errors.layout')

@section('title', '429 - Terlalu Banyak Permintaan')
@section('code', '429')
@section('badge-class', 'badge-warning')
@section('badge-text', '429 • Batas Permintaan Tercapai')
@section('message')
    Sistem mendeteksi terlalu banyak percobaan dalam waktu singkat. Hal ini diterapkan demi menjaga kestabilan dan melindungi akun Anda (fitur Anti Brute-force). Mohon tunggu sejenak sekitar 1 menit sebelum mencoba lagi.
@endsection
