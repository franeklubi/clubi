@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
	    <post-item
	        :post="{{ $post }}"
	    />
    </div>
</div>
@endsection
