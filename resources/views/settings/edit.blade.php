@extends('layouts.app')

@section('content')
    <div class="container">
        <form action="/settings" enctype="multipart/form-data" method="post">
            @csrf
            @method('PATCH')

            <div class="row">
                <div class="col-8 offset-2">
                    <div class="row">
                        <h1>Edit Profile</h1>
                    </div>

                    {{-- change username --}}
                    <div class="form-group row">
                        <label for="username" class="col-md-4 col-form-label">
                            username
                        </label>

                        <input id="username" type="text"
                            class="form-control
                                @error('username') is-invalid @enderror"
                            name="username"
                            value="{{ old('username') ?? $user->username }}"
                            autocomplete="username" autofocus
                        >

                        @error('username')
                            <span class="invalid-feedback" role="alert">
                                <strong>
                                    {{ $errors->first('username') }}
                                </strong>
                            </span>
                        @enderror
                    </div>

                    {{-- change description --}}
                    <div class="row">
                        <label for="description" class="col-md-4 col-form-label"
                        >
                            description
                        </label>
                        <input id="description" type="text"
                            class="form-control
                                @error('description') is-invalid @enderror"
                            name="description"
                            value="{{ old('description') ??
                            $user->profile->description }}"
                            autocomplete="description" autofocus
                        >

                        @error('description')
                            <strong>
                                {{ $errors->first('description') }}
                            </strong>
                        @enderror
                    </div>

                    {{-- change picture --}}
                    <div class="row pt-3">
                        <label for="picture" class="col-md-4 col-form-label"
                        >
                            profile picture
                        </label>
                        <input name="picture"
                            type="file" class="form-control-file" id="picture"
                        >

                        @error('picture')
                            <strong>{{ $errors->first('picture') }}</strong>
                        @enderror
                    </div>

                    <div class="row pt-4">
                        <button class="btn btn-primary">Save Profile</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
