@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-start">
        @auth
            <div class="col-lg-4 mb-3">
                <user-invitation-window
                    :user="{{ $user }}"
                />
            </div>
        @endauth
        <div class="col-lg-8">
            @guest
                <div class="alert alert-info">
                    You're not logged in! Please log in to see Your feed!
                </div>
            @else
                @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                @endif

                @if ( $posts->count() == 0 )
                    <div class="container alert alert-warning">
                        Join groups to see recent posts!
                    </div>
                @endif

                <group-feed
                    :posts="{{ $posts }}"
                    :user_id="{{ $user->id }}"
                    :display_group="true"
                    :is_member="true"
                    passed_next_page_url="{{ $next_page_url }}"
                />
            @endguest
        </div>
    </div>
</div>
@endsection
