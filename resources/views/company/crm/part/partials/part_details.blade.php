<div class="mb-3">

    <h4>
        {{ $part->name }}
    </h4>

    <p class="text-muted">
        {{ $part->code }}
    </p>

</div>


<hr>

<h5>
    Part Items
</h5>

<table class="table table-bordered">

    <thead>

        <tr>

            <th>Item</th>

            <th>Qty</th>

            <th>Notes</th>

        </tr>

    </thead>

    <tbody>

        @foreach($part->items as $item)

            <tr>

                <td>
                    {{ $item->item->name ?? '-' }}
                </td>

                <td>
                    {{ $item->quantity }}
                </td>

                <td>
                    {{ $item->notes ?? '-' }}
                </td>

            </tr>

        @endforeach

    </tbody>

</table>


@if($part->notes)

    <div class="mb-3">

        <strong>
            Notes:
        </strong>

        <div>
            {!! $part->notes !!}
        </div>

    </div>

@endif