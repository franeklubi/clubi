@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <group-big
            :editable="{{ $editable?'true':'false' }}"
            :group="{{ $group }}"
        ></group-big>
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>
            </div>
        </div>
    </div>
</div>
@endsection
