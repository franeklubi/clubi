@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">{{ __('Create group') }}</div>

                <div class="card-body">
                    <group
                        :create="true"
                        :group="{{ $group }}"
                    ></group>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
