<table class="table table-bordered tblPdct">
    <thead>
        <tr>
            <th>SL No.</th>
            <th>{{ $offer->isLensDiscount() ? 'Lens' : 'Product' }}</th>
            @if($offer->isLensDiscount())
                <th>Linked Frames</th>
            @endif
            <th>Remove</th>
        </tr>
    </thead>
    <tbody>
        @forelse($existing as $key => $item)
            <tr>
                <td>{{ $key + 1 }}</td>
                <td>{{ $item->product->name }}</td>
                @if($offer->isLensDiscount())
                    <td>{{ $item->linkedFrames->pluck('frame.name')->filter()->join(', ') ?: 'Any frame / lens only' }}</td>
                @endif
                <td class="text-center">
                    <a href="javascript:void(0)" class="dltOfferPdct" data-pid="{{ $item->id }}">
                        <i class="fa fa-trash text-danger fa-lg"></i>
                    </a>
                </td>
            </tr>
        @empty
            <tr><td colspan="{{ $offer->isLensDiscount() ? 4 : 3 }}" class="text-center">No products added</td></tr>
        @endforelse
    </tbody>
</table>
