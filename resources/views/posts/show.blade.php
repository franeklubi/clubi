@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
	    <group-post
	        :post="{{ $post }}"
	    />
    </div>
</div>
@endsection
