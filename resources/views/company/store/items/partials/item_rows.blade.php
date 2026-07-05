@forelse($items as $index => $item)

    <tr id="item-row-{{ $item->id }}">

        <td>{{ $index + 1 }}</td>

        <td>{{ $item->code }}</td>

        <td>{{ $item->name }}</td>

        <td>{{ $item->hi_name ?? '---' }}</td>

        <td>{{ optional($item->category)->name ?? '-' }}</td>

        <td>{{ optional($item->subcategory)->name ?? '-' }}</td>

        <td>{{ optional($item->unit)->name ?? '-' }}</td>

        <td>

            @can('edit item')
                <a href="{{ route('items.edit', ['company' => $company->id, 'item' => $item->id]) }}">
                    <i class="fa fa-edit text-green"></i>
                </a>
            @endcan

        </td>

    </tr>

@empty

    <tr>
        <td colspan="8" class="text-center">
            😢 No items found.
        </td>
    </tr>

@endforelse