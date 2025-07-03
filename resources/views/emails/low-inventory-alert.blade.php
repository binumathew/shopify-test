<h2>🟡 Low Inventory Products (Below {{$Inventorythreshold}})</h2>
<ul>
    @forelse($lowInventoryProducts as $item)
        <li>
            <strong>{{ $item['product_title'] }}</strong> - {{ $item['variant_title'] }}: 
            <span style="color: red">{{ $item['quantity'] }}</span> left
        </li>
    @empty
        <li>All products have sufficient stock in this category.</li>
    @endforelse
</ul>

<hr>

<h2>🟢 High Inventory Products ({{$Inventorythreshold}} or More)</h2>
<ul>
    @forelse($normalInventoryProducts as $item)
        <li>
            <strong>{{ $item['product_title'] }}</strong> - {{ $item['variant_title'] }}: 
            {{ $item['quantity'] }} in stock
        </li>
    @empty
        <li>No products have 10 or more stock.</li>
    @endforelse
</ul>
