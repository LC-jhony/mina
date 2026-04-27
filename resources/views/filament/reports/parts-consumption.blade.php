@php
    $data = \App\Models\MaintenanceOrderPart::select('spare_part_id', DB::raw('sum(quantity) as total_qty'), DB::raw('sum(subtotal) as total_cost'))
        ->groupBy('spare_part_id')
        ->with('sparePart')
        ->orderByDesc('total_qty')
        ->get();
@endphp

<div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-900">
        <thead class="bg-gray-50 dark:bg-gray-800">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Repuesto</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Cantidad Usada</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Costo Acumulado</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
            @foreach($data as $row)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">{{ $row->sparePart->name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-500 dark:text-gray-400">{{ $row->total_qty }} {{ $row->sparePart->unit }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-bold text-gray-900 dark:text-white">S/. {{ number_format($row->total_cost, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
