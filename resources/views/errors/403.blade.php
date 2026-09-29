@extends('errors.layout')

@section('title', '403 - Akses Ditolak')
@section('code', '403')
@section('badge-class', 'badge-danger')
@section('badge-text', '403 • Akses Ditolak / Dibatasi')
@section('message')
    Akun Anda tidak memiliki izin untuk membuka fitur atau halaman ini. Jika Anda login sebagai Administrator, silakan akses Panel Admin. Jika Anda bertugas sebagai Tenaga Pendidik, silakan akses Portal Guru.
@endsection
