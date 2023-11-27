@extends("backend.pdf.base")
@section("pdfcontent")
<div class="row">
    <div class="col text-center">
        <h3>{{ title() }}</h3>
        {{ branch()->name }}, {{ branch()->address }}, {{ branch()->phone }}
    </div>
</div>
<div class="row">
    <div class="col">
        <h4 class="text-center">Frame Product List</h4>
    </div>
    <div class="col">
        <table width="100%" class="mt-10" cellspacing="0" cellpadding="0">
            <thead>
                <tr>
                    <th>SL No</th>
                    <th>Product Name</th>
                    <th>Code</th>
                    <th>Type</th>
                    <th>Shape</th>
                    <th>Manufacturer</th>
                    <th>Price</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $key => $item)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->code }}</td>
                    <td>{{ $item->type->name }}</td>
                    <td>{{ $item->shape->name }}</td>
                    <td>{{ $item->manufacturer->name }}</td>
                    <td class="text-end">{{ $item->selling_price }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center">
                        <h3 class="text-danger">No records found!</h3>
                    </td>
                    @endforelse
            </tbody>
        </table>
    </div>
    <footer>
        Printed On: {{ Carbon\Carbon::now()->format('d, M Y h:i a') }}
    </footer>
</div>
@endsection