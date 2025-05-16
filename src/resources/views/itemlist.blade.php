@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/itemlist.css') }}">
@endsection

@section('content')
<div class="itemlist__content">
    <livewire:item-list-component :initial-tab="$initial_tab ?? 'recommended'"
    />
</div>
@endsection