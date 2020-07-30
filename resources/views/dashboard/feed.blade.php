@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-4">
            @auth
                <user-invitation-window
                    :user="{{ $user }}"
                />
            @endauth
        </div>
        <div class="col-md-8">
            @guest
                <div class="alert alert-info">
                    You're not logged in! Please log in to see Your feed!
                </div>
            @else
                <div class="">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    <div class="alert alert-warning"
                        v-if="{{ $posts->count() == 0 }}"
                    >
                        Join groups to see recent posts!
                    </div>

                    <group-feed
                        :posts="{{ $posts }}"
                        :user_id="{{ $user->id }}"
                        :display_group="true"
                        :is_member="true"
                        passed_next_page_url="{{ $next_page_url }}"
                    />
                </div>
            @endguest
        </div>
    </div>
</div>
@endsection
