<x-app-layout>
    <x-slot name="header">
        <h1 class="h4 mb-0">{{ __('Dashboard') }}</h1>
    </x-slot>

    @php
        $tarjetas = [
            ['Alumnos', $kpis['alumnos'], 'primary'],
            ['Docentes', $kpis['docentes'], 'secondary'],
            ['Grupos', $kpis['grupos'], 'secondary'],
            ['Asistencias hoy', $kpis['asistencias_hoy'], 'success'],
            ['Retardos hoy', $kpis['retardos_hoy'], 'warning'],
            ['Faltas hoy', $kpis['faltas_hoy'], 'danger'],
            ['Reportes (mes)', $kpis['reportes_mes'], 'dark'],
            ['Felicitaciones (mes)', $kpis['felicitaciones_mes'], 'info'],
            ['Avisos (mes)', $kpis['avisos_mes'], 'dark'],
        ];
    @endphp

    <div class="row g-3 mb-4">
        @foreach ($tarjetas as [$label, $valor, $color])
            <div class="col-6 col-md-4 col-xl-2">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body py-3">
                        <div class="text-muted small">{{ $label }}</div>
                        <div class="fs-3 fw-bold text-{{ $color }}">{{ $valor }}</div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h2 class="h6 text-muted mb-3">Asistencia semanal</h2>
                    <canvas id="chartSemanal" height="120"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h2 class="h6 text-muted mb-3">Conducta (mes)</h2>
                    <canvas id="chartConducta" height="160"></canvas>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h2 class="h6 text-muted mb-3">Rendimiento por grupo (promedio)</h2>
                    <canvas id="chartRendimiento" height="80"></canvas>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            const semanal = @json($asistenciaSemanal);
            const rendimiento = @json($rendimientoPorGrupo);
            const conducta = @json($conductaPorTipo);

            document.addEventListener('DOMContentLoaded', () => {
                if (!window.Chart) return;

                new Chart(document.getElementById('chartSemanal'), {
                    type: 'bar',
                    data: {
                        labels: semanal.labels,
                        datasets: [
                            { label: 'Presente', data: semanal.presente, backgroundColor: '#198754' },
                            { label: 'Retardo', data: semanal.retardo, backgroundColor: '#ffc107' },
                            { label: 'Falta', data: semanal.falta, backgroundColor: '#dc3545' },
                        ],
                    },
                    options: { responsive: true, scales: { x: { stacked: true }, y: { stacked: true, beginAtZero: true } } },
                });

                new Chart(document.getElementById('chartConducta'), {
                    type: 'doughnut',
                    data: {
                        labels: conducta.labels,
                        datasets: [{ data: conducta.valores, backgroundColor: ['#dc3545', '#fd7e14', '#6f42c1', '#0dcaf0', '#198754', '#6c757d'] }],
                    },
                    options: { responsive: true },
                });

                new Chart(document.getElementById('chartRendimiento'), {
                    type: 'bar',
                    data: {
                        labels: rendimiento.labels,
                        datasets: [{ label: 'Promedio', data: rendimiento.promedios, backgroundColor: '#2563eb' }],
                    },
                    options: { responsive: true, scales: { y: { beginAtZero: true, max: 10 } } },
                });
            });
        </script>
    @endpush
</x-app-layout>
