<x-app-layout>
    <x-slot name="header">
        <h1 class="h4 mb-0">Escanear QR</h1>
    </x-slot>

    <div class="row justify-content-center">
        <div class="col-12 col-md-7 col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <p class="text-muted small">Apunta la cámara al QR del alumno. El resultado aparece grande y se reinicia solo para el siguiente.</p>

                    {{-- Resultado GRANDE del último escaneo --}}
                    <div id="resultado" class="d-none text-center rounded-3 p-4 mb-3" style="transition: opacity .15s;">
                        <div id="res-icon" style="font-size:3rem;line-height:1"></div>
                        <div id="res-alumno" class="fs-4 fw-bold mt-1"></div>
                        <div id="res-estatus" class="fs-5"></div>
                        <div id="res-meta" class="small text-muted"></div>
                    </div>

                    <div id="reader"></div>

                    <a href="{{ route('asistencias.index') }}" class="btn btn-outline-secondary btn-sm mt-3">Ver asistencias de hoy</a>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
        <script>
            (function () {
                const csrf = document.querySelector('meta[name="csrf-token"]').content;
                const endpoint = "{{ route('asistencias.registrar') }}";
                const box = document.getElementById('resultado');
                const elIcon = document.getElementById('res-icon');
                const elAlumno = document.getElementById('res-alumno');
                const elEstatus = document.getElementById('res-estatus');
                const elMeta = document.getElementById('res-meta');

                let busy = false;
                let ultimo = null;          // último código leído (anti-doble)
                let ocultarTimer = null;

                const ESTILOS = {
                    presente:  { bg: 'bg-success-subtle',  text: 'text-success-emphasis',  icon: '✓', label: 'Presente' },
                    retardo:   { bg: 'bg-warning-subtle',  text: 'text-warning-emphasis',  icon: '⏱', label: 'Retardo' },
                    duplicado: { bg: 'bg-info-subtle',     text: 'text-info-emphasis',     icon: 'ℹ', label: 'Ya registrada' },
                    error:     { bg: 'bg-danger-subtle',   text: 'text-danger-emphasis',   icon: '✕', label: 'Error' },
                };

                function pintar(tipo, alumno, estatusTexto, meta) {
                    const e = ESTILOS[tipo] || ESTILOS.error;
                    box.className = 'text-center rounded-3 p-4 mb-3 ' + e.bg + ' ' + e.text;
                    elIcon.textContent = e.icon;
                    elAlumno.textContent = alumno || '';
                    elEstatus.textContent = estatusTexto || e.label;
                    elMeta.textContent = meta || '';
                    box.style.opacity = '1';
                    if (navigator.vibrate) navigator.vibrate(tipo === 'error' ? [80, 60, 80] : 120);
                    clearTimeout(ocultarTimer);
                    ocultarTimer = setTimeout(() => { box.classList.add('d-none'); }, 2600);
                }

                async function registrar(contenido) {
                    if (busy || contenido === ultimo) return;   // evita doble registro del mismo QR
                    busy = true; ultimo = contenido;
                    try {
                        const resp = await fetch(endpoint, {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                            body: JSON.stringify({ contenido }),
                        });
                        const d = await resp.json();
                        if (!d.ok) {
                            pintar('error', null, d.mensaje || 'QR no válido', '');
                        } else if (d.duplicado) {
                            pintar('duplicado', d.alumno, 'Ya tenía asistencia hoy', d.grupo ?? '');
                        } else {
                            pintar(d.estatus === 'presente' ? 'presente' : 'retardo', d.alumno,
                                   (d.estatus === 'presente' ? 'Presente' : 'Retardo') + ' · ' + (d.hora ?? ''),
                                   d.grupo ?? '');
                        }
                    } catch (e) {
                        pintar('error', null, 'Error de red', '');
                    } finally {
                        // libera para el siguiente alumno rápidamente; el mismo QR se rebloquea un momento
                        setTimeout(() => { busy = false; }, 900);
                        setTimeout(() => { ultimo = null; }, 2600);
                    }
                }

                const scanner = new Html5QrcodeScanner('reader', { fps: 10, qrbox: 250 }, false);
                scanner.render(registrar, () => {});
            })();
        </script>
    @endpush
</x-app-layout>
