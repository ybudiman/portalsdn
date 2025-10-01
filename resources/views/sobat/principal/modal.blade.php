<!-- Modal -->
<div class="modal fade" id="confirmationModal{{ $item->id }}" tabindex="-1" aria-labelledby="confirmationModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="confirmationModal">Delete Category ?</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true" class="text-white">&times;</span>
        </button>
      </div>
      <div class="modal-body text-left">
        <div class="row">
            <div class="col-6">
                Category Name
            </div>
            <div class="col-6">
                : {{ $item->category_name }}
            </div>

            <div class="col-6">
                Status
            </div>
            <div class="col-6">
                : {{ $item->status }}
            </div>

            <div class="col-12 mt-3 text-center">
                @if ($item->category_image)
                    <img src="{{ asset('storage/category-img/' . basename($item->category_image)) }}" 
                         alt="Category Image" 
                         width="200" height="200" 
                         style="object-fit:contain;border:1px solid #ddd;padding:4px;border-radius:6px;">
                @else
                    <span class="text-muted">No Image</span>
                @endif
            </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">
            <i class="fas fa-times mr-1"></i>
            No
        </button>
        <a href="{{ route('categoryDestroy', $item->id) }}" type="button" class="btn btn-danger btn-sm">
            <i class="fas fa-trash mr-1"></i>
            Yes
        </a>
      </div>
    </div>
  </div>
</div>
