<div class="row mt-3">
    <div class="col-md-12">
        @foreach (['info', 'success', 'danger', 'warning'] as $msg)
            @if (Session::has('system.message.' . $msg))
                <span class="alert alert-{{$msg}} alert-dismissible fade show d-block text-left">
                    {{ Session::get('system.message.' . $msg) }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="閉じる">
                        <span aria-hidden"true">&times;</span>
                    </button>
                </span>
            @endif
        @endforeach
    </div>
</div>