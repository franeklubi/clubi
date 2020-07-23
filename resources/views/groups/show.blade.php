@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <group-header
            :editable="{{ $editable?'true':'false' }}"
            :group="{{ $group }}"
        ></group-header>

        <group-feed
            :posts="{{ $group->posts }}"
            :is_group_admin="{{ $editable?'true':'false' }}"
        ></group-feed>
    </div>
</div>
@endsection