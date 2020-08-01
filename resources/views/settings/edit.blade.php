@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-8 offset-2">
                <edit-profile
                    :user="{{ $user }}"
                />
            </div>
        </div>
    </div>
@endsection
