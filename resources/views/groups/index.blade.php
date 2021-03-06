@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="">
                @if ( $groups->count() == 0 )
                    <div class="alert alert-warning">
                        {{ $zero_warning }}
                    </div>
                @endif

                @foreach ($groups as $group)
                    <group-header
                        :editable="{{
                            $user &&
                            ($group->owner_id == $user->id || $user->is_admin)
                                ?'true':'false'
                        }}"
                        :group="{{ $group }}"
                        class="mb-3"
                    ></group-header>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
