<x-app-layout>
    <x-slot name="header">
        <h1 class="h4 mb-0">Escanear QR</h1>
    </x-slot>

    <div class="row justify-content-center">
        <div class="col-12 col-md-7 col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <p class="text-muted small">Apunta la cámara al QR del alumno para registrar su asistencia.</p>

                    <div id="reader" class="mb-3"></div>

                    <div id="resultado" class="d-none alert" role="alert"></div>

                    <a href="{{ route('asistencias.index') }}" class="btn btn-outline-secondary btn-sm">Ver asistencias de hoy</a>
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
                let busy = false, ultimo = null;

                function mostrar(tipo, html) {
                    box.className = 'alert alert-' + tipo;
                    box.innerHTML = html;
                    box.classList.remove('d-none');
                }

                async function registrar(contenido) {
                    if (busy || contenido === ultimo) return;
                    busy = true; ultimo = contenido;
                    try {
                        const resp = await fetch(endpoint, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrf,
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({ contenido }),
                        });
                        const d = await resp.json();
                        if (!d.ok) {
                            mostrar('danger', d.mensaje || 'No se pudo registrar.');
                        } else {
                            const color = d.duplicado ? 'warning' : (d.estatus === 'presente' ? 'success' : 'warning');
                            mostrar(color, `<strong>${d.alumno}</strong> · ${d.grupo ?? ''}<br>${d.mensaje} (${d.hora})`);
                        }
                    } catch (e) {
                        mostrar('danger', 'Error de red al registrar.');
                    } finally {
                        setTimeout(() => { busy = false; ultimo = null; }, 2500);
                    }
                }

                const scanner = new Html5QrcodeScanner('reader', { fps: 10, qrbox: 250 }, false);
                scanner.render(registrar, () => { /* ignorar errores de lectura por frame */ });
            })();
        </script>
    @endpush
</x-app-layout>
