@extends ("layout")

@section ("body")

<table class="table table-condensed">
<thead>
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th></th>
        <th>Price</th>
    </tr>
</thead>
@foreach ($products as $product)
<thead class="thead-light">
    <tr>
        <th>{{ $product->id }}</th>
        <th>{{ $product->name }}</th>
        <th>{{ $product->weight }} кг.</th>
        <th></th>
    </tr>
</thead>
<tbody>
    <tr>
        <td></td>
        <td colspan="3"><i>{{ $product->usage }}</i></td>
    </tr>
    @foreach ($product->offers as $offer)
    <tr>     
        <td></td>
        <td>{{ $offer->city->name }}</td>
        <td>{{ $offer->quantity }} шт.</td>
        <td>{{ $offer->price }} Р</td>
    </tr>
    @endforeach
</tbody>
@endforeach
</table>

{{ $products->links() }}
@endsection
