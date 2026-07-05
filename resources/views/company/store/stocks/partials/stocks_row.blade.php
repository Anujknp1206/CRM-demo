@forelse($stockIns as $index => $stock)
    <tr>
        <td>{{ $index + 1 }}</td>
        <td>
            {{ $stock->doc_no }}
            @if($stock->purchaseOrder)
                / <small class="badge badge-info mt-1">
                    PO: {{ $stock->purchaseOrder->po_code ?? 'PO-' . $stock->purchase_order_id }}
                </small>
            @endif
        </td>
        <td>{{ \Carbon\Carbon::parse($stock->doc_date)->format('d/m/Y') }}</td>
        <td>{{ optional($stock->supplier)->name ?? 'Self' }}</td>
        <td>
            @can('view stock')
                <button class="btn btn-sm view-stock" data-id="{{ $stock->id }}" title="View Stock">
                    <i class="fa fa-eye"></i>
                </button>
            @endcan
            @can('print stock')
                <a href="{{ route('stock-ins.print', [$company->id, $stock->id]) }}" class="btn btn-sm" title="Print Slip">
                    <i class="fa fa-print text-primary"></i>
                </a>
            @endcan
            @can('edit stock')
                @if($stock->purchase_order_id)
                    <a href="{{route('stock-ins.edit.po', [
                            'company' => $company->id,
                            'stockIn' => $stock->id
                        ])}}" class="btn btn-sm" title="Edit Stock With PO">
                        <i class="fa fa-edit text-primary"></i>
                    </a>
                @else
                    <a href="{{ route('stock-ins.edit', [$company->id, $stock->id]) }}" class="btn btn-sm" title="Edit Stock">
                        <i class="fa fa-edit text-success"></i>
                    </a>
                @endif
            @endcan
            @can('delete stock')
                <!-- <form class="delete-form" data-url="{{ route('stock-ins.destroy', [$company->id, $stock->id]) }}"
                                            style="display:inline">

                                            @csrf
                                            @method('DELETE')

                                            <button type="button" class="delete-confirm" data-name="{{ $stock->doc_no }}" title="Delete Stock"
                                                style="border:none;background:none">
                                                <i class="fa fa-trash text-danger"></i>
                                            </button>
                                        </form> -->
            @endcan
        </td>
    </tr>
@empty
    <tr class="no-record-row">
        <td colspan="8" class="text-center"> 😢 No records found</td>
    </tr>
@endforelse