@extends('layouts.app')

@section('content')
<div class="container">
    <group-container
        :group="{{ $group }}"
        :posts="{{ $group->posts }}"
        :is_member="{{ $is_member?'true':'false' }}"
        :is_group_admin="{{ $editable?'true':'false' }}"
    />
</div>
@endsection
