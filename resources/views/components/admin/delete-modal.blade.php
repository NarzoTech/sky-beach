@props(['id' => 'deleteModal'])

<div tabindex="-1" role="dialog" id="{{ $id }}" {{ $attributes->merge(['class' => 'modal fade']) }}
    aria-hidden="true">

    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Item Delete Confirmation') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-0">
                <p>{{ __('Are You sure want to delete this item ?') }}</p>
            </div>
            <div class="modal-footer bg-whitesmoke br">
                <form id="deleteForm" action="" method="POST">
                    @csrf
                    @method('DELETE')

                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">{{ __('Close') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Yes, Delete') }}</button>

                </form>
            </div>
        </div>
    </div>
</div>
