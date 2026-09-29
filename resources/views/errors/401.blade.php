@extends('errors.layout')

@section('title', '401 - Autentikasi Diperlukan')
@section('code', '401')
@section('badge-class', 'badge-warning')
@section('badge-text', '401 • Sesi Masuk Diperlukan')
@section('message')
    Anda belum terautentikasi atau sesi login Anda telah berakhir. Silakan masuk terlebih dahulu melalui Portal Login menggunakan NIP atau email akun Anda.
@endsection
