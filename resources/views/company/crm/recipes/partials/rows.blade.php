@forelse($recipes as $i => $recipe)

    @php
        // total items across all parts
        $totalItems = $recipe->parts->sum(function ($part) {
            return $part->items->count();
        });

        $totalParts = $recipe->parts->count();
    @endphp

    <tr>
        <td>{{ $i + 1 }}</td>

        <td>{{ $recipe->name }}</td>

        <td>{{ class_basename($recipe->recipeable_type) }}</td>

        <td>{{ $recipe->recipeable?->name }}</td>

        <!-- ✅ PART COUNT -->
        <td>
            <span class="badge badge-info">
                {{ $totalParts }} Parts
            </span>
        </td>

        <!-- ✅ ITEM COUNT -->
        <td>
            <span class="badge badge-primary">
                {{ $totalItems }} Items
            </span>
        </td>

        <!-- DEFAULT -->
        <!-- <td>
            @if($recipe->is_default)
                <span class="badge badge-success">Default</span>
            @else
                -
            @endif
        </td> -->

        <!-- ACTIONS -->
        <td>
            @can('edit recipe')
                <a href="{{ route('recipes.edit', [$company, $recipe]) }}" class="btn btn-sm text-info" title="Edit">
                    <i class="fa fa-edit"></i>
                </a>
            @endcan

            @can('delete recipe')
                <button type="button" data-id="{{ $recipe->id }}" class="btn btn-sm text-danger deleteRecipe" title="Delete">
                    <i class="fa fa-trash"></i>
                </button>
            @endcan
        </td>
    </tr>

@empty

    <tr>
        <td colspan="8" class="text-center">
            😢 No Recipes Found
        </td>
    </tr>

@endforelse