@php
    $data = \App\Models\MaintenanceOrder::where('status', 'completed')
        ->select('vehicle_id', DB::raw('count(*) as count'), DB::raw('sum(total_cost) as total'))
        ->groupBy('vehicle_id')
        ->with('vehicle')
        ->orderByDesc('total')
        ->get();
@endphp

<div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-900">
        <thead class="bg-gray-50 dark:bg-gray-800">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Vehículo</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Órdenes</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Costo Total</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
            @foreach($data as $row)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">{{ $row->vehicle->plate }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-500 dark:text-gray-400">{{ $row->count }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-bold text-gray-900 dark:text-white">S/. {{ number_format($row->total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
