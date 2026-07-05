@forelse($stocks as $s)

    @php

        /*
        |--------------------------------------------------------------------------
        | ITEM FROM STOCK
        |--------------------------------------------------------------------------
        */

        $item = $s->item;

    @endphp

    <tr class="{{ ($s->quantity ?? 0) <= ($s->min_quantity ?? 0)
            ? 'table-danger'
            : 'table-success' }}">

        {{-- ITEM --}}
        <td>

            <strong>
                {{ $item->name ?? '-' }}
            </strong>

        </td>

        {{-- BRAND --}}
        <td>

            {{ optional($s->brand)->name ?? '-' }}

        </td>

        {{-- CONDITION --}}
        <td>

            {{ optional($s->condition)->name ?? '-' }}

        </td>

        {{-- LOCATION --}}
        <td>

            {{ optional($s->location)->name ?? '-' }}

        </td>

        {{-- BASE UNIT --}}
        <td>

            {{ optional($s->unit)->name ?? '-' }}

        </td>

        {{-- STOCK --}}
        <td class="text-center">

            {{-- BASE STOCK --}}
            <div class="mb-1">

                <span class="badge badge-primary">

                    {{ number_format($s->quantity, 2) }}

                    {{ optional($s->unit)->name ?? '' }}

                </span>

            </div>

            {{-- CONVERTED STOCK --}}
            @foreach($item->unitConversions ?? [] as $conversion)

                @php

                    /*
                    |--------------------------------------------------------------------------
                    | EXAMPLE
                    |--------------------------------------------------------------------------
                    |
                    | Base Stock = 2200 PCS
                    |
                    | Conversion:
                    | 1 BOX = 25 PCS
                    |
                    | BOX = 2200 / 25
                    |
                    */

                    $convertedQty =
                        $conversion->factor > 0
                        ? $s->quantity / $conversion->factor
                        : 0;

                @endphp

                <div class="mb-1">

                    <span class="badge badge-info">

                        {{ number_format($convertedQty, 2) }}

                        {{ optional($conversion->fromUnit)->name ?? '' }}

                    </span>

                </div>

            @endforeach

        </td>

        {{-- MIN STOCK --}}
        <td class="text-center">

            <span class="badge badge-secondary">

                {{ $s->min_quantity ?? 0 }}

            </span>

        </td>

        {{-- STATUS --}}
        <td class="text-center">

            @if(($s->quantity ?? 0) <= ($s->min_quantity ?? 0))

                <span class="badge badge-danger">

                    <i class="fa fa-exclamation-triangle"></i>

                    LOW

                </span>

            @else

                <span class="badge badge-success">

                    <i class="fa fa-check-circle"></i>

                    OK

                </span>

            @endif

        </td>

    </tr>

@empty

    <tr>

        <td colspan="8" class="text-center text-muted">

            😢 No stock found

        </td>

    </tr>

@endforelse