@forelse($boms as $bom)
    <tr>
        <td>{{ $loop->iteration }}</td>
        <td class="text-center">

            @php

                $progress = $bom->production_progress ?? 0;

                /*
                -----------------------------------------
                COLORS
                -----------------------------------------
                */

                if ($progress <= 0) {

                    $color = '#dc3545';

                } elseif ($progress < 100) {

                    $color = '#ffc107';

                } else {

                    $color = '#28a745';
                }

            @endphp

            <a href="{{ route(
            'orders.production.detail',
            [
                'company' => $company->id,
                'order' => $bom->order->id
            ]
        ) }}" class="text-decoration-none d-inline-block" title="Open Production Tracker">

                <div class="progress-wrapper">

                    <div class="progress-ring" data-progress="{{ $progress }}" style="
                        --progress: {{ $progress }};
                        --color: {{ $color }};
                     ">

                        <span>{{ $progress }}%</span>

                    </div>

                    <div class="progress-tooltip">

                        {!! $bom->production_details !!}

                    </div>

                </div>

            </a>

        </td>
        <td><b>{{ $bom->bom_number }}</b></td>
        <td>
            {{ $bom->order->order_number ?? '-' }}
        </td>
        <td>
            {{ $bom->items->count() }} Items
        </td>
        <td class="text-center">

            @php
                $progress = $bom->progress_percent;
                $issued = $bom->issued_items_count;
                $total = $bom->total_items;

                // 🔥 NEW COLOR LOGIC
                if (!$bom->hasIssues()) {
                    $color = '#dc3545'; // 🔴 RED → no issue created
                } elseif (!$bom->hasIssuedItems()) {
                    $color = '#ffc107'; // 🟡 YELLOW → issue created but nothing issued
                } else {
                    $color = '#28a745'; // 🟢 GREEN → items issued
                }
            @endphp
            <!-- 🔥 CLICKABLE CIRCLE -->
            <div class="progress-ring show-bom-issues" data-id="{{ $bom->id }}" data-progress="{{ $progress }}" style="
                                                --progress: {{ $progress }};
                                                --color: {{ $color }};
                                             " title="View Issue Item Details">

                <span>{{ $issued }}/{{ $total }}</span>
            </div>

        </td>
        <td>
            @php
                $statusClass = match ($bom->status) {
                    'completed' => 'success',
                    'in_progress' => 'warning',
                    default => 'secondary'
                };
            @endphp

            <span class="badge badge-{{ $statusClass }}">
                {{ ucfirst(str_replace('_', ' ', $bom->status)) }}
            </span>
        </td>
        <td>
            {{ $bom->created_at->format('d/m/Y') }}
        </td>
        <td>
            {{ $bom->delivery_date
            ? \Carbon\Carbon::parse($bom->delivery_date)->format('d/m/Y')
            : '-' }}
        </td>
        <td>

            {{-- 👁 SHOW --}}
            <button class="btn btn-sm show-bom" data-id="{{ $bom->id }}" title="View BOM Details">
                <i class="fa fa-eye"></i>
            </button>

            {{-- ✏️ EDIT --}}
            @can('edit bom')
                <a href="{{ route('boms.edit', [$company->id, $bom->id]) }}" class="btn btn-sm" title="Edit BOM"
                    onclick="return handleBomClick(event, this)" data-company-id="{{ $company->id }}">
                    <i class="fa fa-edit text-success"></i>
                </a>
            @endcan
            @can('print bom')

                {{-- 🖨 PRINT --}}
                <a href="{{ route('boms.print', [$company->id, $bom->id]) }}" target="_blank" class="btn btn-sm"
                    title="Print BOM">
                    <i class="fa fa-print text-primary"></i>
                </a>
            @endcan
            {{-- 🗑 DELETE --}}
            @can('delete bom')
                <form action="{{ route('boms.destroy', [$company->id, $bom->id]) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm text-danger delete-confirm" data-name="BOM {{ $bom->bom_number }}"
                        title="Delete BOM">
                        <i class="fa fa-trash"></i>
                    </button>
                </form>
            @endcan
            @can('issues')<a href="{{ route('issues.create', $company) }}" class="btn btn-sm" title="Issue Items"
                    onclick="return handleBomClick(event, this)" data-company-id="{{ $company->id }}">
                    <i class="fas  fa-truck-moving text-success"></i>
                </a>
            @endcan
        </td>
    </tr>
@empty
    <tr>
        <td colspan="9" class="text-center">
            😢 No BOM Found
        </td>
    </tr>
@endforelse