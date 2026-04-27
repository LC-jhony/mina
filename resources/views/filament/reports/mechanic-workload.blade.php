@php
    $data = \App\Models\MaintenanceOrder::where('status', 'completed')
        ->select('mechanic_id', DB::raw('count(*) as count'), DB::raw('sum(total_cost) as managed_cost'))
        ->groupBy('mechanic_id')
        ->with('mechanic')
        ->orderByDesc('count')
        ->get();
@endphp

<div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-900">
        <thead class="bg-gray-50 dark:bg-gray-800">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Mecánico</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">OM Completadas</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Costo Gestionado</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
            @foreach($data as $row)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">{{ $row->mechanic->full_name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-500 dark:text-gray-400">{{ $row->count }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-bold text-gray-900 dark:text-white">S/. {{ number_format($row->managed_cost, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
