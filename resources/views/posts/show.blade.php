@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <post-item
            :post="{{ $post }}"
            :owner="{{ $post->group->owner }}"
            :user_id="{{ $user_id }}"
            :display_group="true"
            :is_group_admin="{{ $editable?'true':'false' }}"
            :is_member="{{ $is_member?'true':'false' }}"
        />
    </div>
</div>
@endsection
