@foreach($stocks as $stock)
    <tr>

        <td class="text-center align-middle">
            <input type="checkbox" name="items[{{ $loop->index }}][selected]" class="modal-item-checkbox">
        </td>

        <td class="align-middle">{{ $stock->item->name }}</td>

        <td class="align-middle">{{ $stock->brand->name }}</td>
        <td class="align-middle">{{ $stock->condition->name }}</td>
        <td class="align-middle">{{ $stock->location->name }}</td>
        @php

            $conversion = $stock->item
                ->unitConversions
                ->firstWhere('to_unit_id', $stock->unit_id);

        @endphp

        <td class="align-middle">

            {{ optional($conversion?->fromUnit)->name ?? $stock->unit->name }}

        </td>

        {{-- RATE --}}
        <td>
            <input type="number" step="0.01" class="form-control form-control-sm text-center"
                name="items[{{ $loop->index }}][rate]" value="{{ optional($stock->stockInItems->last())->rate ?? 0 }}"
                oninput="if(this.value < 0) this.value = 1;">
        </td>
        {{-- REQ QTY --}}
        <td>
            <input type="number" name="items[{{ $loop->index }}][requested_quantity]"
                class="form-control form-control-sm text-center req-qty" min="1" value="{{ $stock->requested_quantity }}"
                oninput="if(this.value < 0) this.value = 1;">
        </td>

        {{-- ACTION --}}
        <td class="text-center">

        </td>

        {{-- HIDDEN --}}
        <input type="hidden" name="items[{{ $loop->index }}][item_id]" value="{{ $stock->item_id }}">
        <input type="hidden" name="items[{{ $loop->index }}][brand_id]" value="{{ $stock->brand_id }}">
        <input type="hidden" name="items[{{ $loop->index }}][condition_id]" value="{{ $stock->condition_id }}">
        <input type="hidden" name="items[{{ $loop->index }}][location_id]" value="{{ $stock->location_id }}">
        <input type="hidden" name="items[{{ $loop->index }}][unit_id]"value="{{ $conversion?->from_unit_id ?? $stock->unit_id }}">
    </tr>
@endforeach