@php
    $data = \App\Models\Trip::with(['vehicle', 'mine'])
        ->orderByDesc('departure_date')
        ->limit(50)
        ->get();
@endphp

<div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-900">
        <thead class="bg-gray-50 dark:bg-gray-800">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Vehículo</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Destino</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Salida</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Estado</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
            @foreach($data as $row)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">{{ $row->vehicle->plate }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $row->mine->name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $row->departure_date->format('d/m/Y H:i') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $row->status->getColor() === 'success' ? 'bg-green-100 text-green-800' : ($row->status->getColor() === 'warning' ? 'bg-yellow-100 text-warning-800' : 'bg-blue-100 text-blue-800') }}">
                            {{ $row->status->getLabel() }}
                        </span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
