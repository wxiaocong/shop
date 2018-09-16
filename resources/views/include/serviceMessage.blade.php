{{-- $errors for validator  Session::get('message') for customer defination --}}
<div class="hidden alert alert-warning alert-dismissible" role="alert">
	<button type="button" class="close" data-dismiss="alert" aria-label="Close">
		<span aria-hidden="true">&times;</span>
	</button>
    <ul>
        @if (isset($messages) && count($messages) > 0)
            @foreach ($messages as $message)
                <li>{{ $message }}</li>
            @endforeach
        @endif
    </ul>
</div>
