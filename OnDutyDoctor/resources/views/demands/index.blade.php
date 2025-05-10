<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>OnDutyDoctor - Demandas</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body {
            background-color: #0a0a0a;
            color: #f8f9fa;
        }
        pre {
            background-color: #212529;
            padding: 1rem;
            border-radius: 0.5rem;
            color: #0df;
        }
        .toast-container {
            position: fixed;
            top: 1rem;
            right: 1rem;
            z-index: 1055;
        }
        p {
            color: white;
        }
    </style>
</head>
<body>
    <div class="toast-container" id="toastContainer"></div>

    <div class="container py-5">
        <h1 class="text-center text-success mb-4">📋 Demandas Recebidas</h1>

        <div class="row g-4">
        </div>
    </div>

    <script>
        let isLoading = false;

        function mostrarToast(mensagem, tipo = 'success') {
            const toastEl = document.createElement('div');
            toastEl.className = `toast align-items-center text-bg-${tipo} border-0 show mb-2`;
            toastEl.setAttribute('role', 'alert');
            toastEl.setAttribute('aria-live', 'assertive');
            toastEl.setAttribute('aria-atomic', 'true');
            toastEl.innerHTML = `
                <div class="d-flex">
                    <div class="toast-body">${mensagem}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            `;
            document.getElementById('toastContainer').appendChild(toastEl);

            setTimeout(() => toastEl.remove(), 5000);
        }

        async function assumeDemand(id) {
            const response = await fetch(`/api/demands/${id}/assume`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                }
            });
            if (response.ok) {
                mostrarToast('✅ Demanda assumida com sucesso!');
                await reloadDemands(); 
            } else {
                mostrarToast('❌ Erro ao assumir demanda.', 'danger');
            }
        }

        async function reloadDemands() {
            if (isLoading) return;
            isLoading = true;

            try {
                const res = await fetch('/api/demands');
                const data = await res.json();

                const container = document.querySelector('.row.g-4');
                container.innerHTML = '';

                if (data.length === 0) {
                    container.innerHTML = `<p class="text-center text-muted">Nenhuma demanda registrada ainda.</p>`;
                    return;
                }

                data
                    .sort((a, b) => new Date(b.created_at) - new Date(a.created_at))
                    .forEach(d => {
                        const status = d.status || 'pending'; // default
                        let statusBadge = '';
                        let borderClass = 'border-secondary';

                        if (status === 'assumed') {
                        statusBadge = '<span class="badge bg-success">✅ Assumida</span>';
                        borderClass = 'border-success';
                        } else if (status === 'ignored') {
                        statusBadge = '<span class="badge bg-danger">⛔ Ignorada</span>';
                        borderClass = 'border-danger';
                        }

                        const disableButtons = status !== 'pending' ? 'disabled' : '';

                        const html = `
                        <div class="col-md-6 col-lg-4">
                            <div class="card bg-dark ${borderClass} h-100">
                            <div class="card-body d-flex flex-column justify-content-between">
                                <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <strong>${d.payload.specialty}</strong>
                                    ${statusBadge}
                                </div>
                                <p><strong>Hospital ID:</strong> ${d.payload.hospitalId}</p>
                                <p><strong>Urgência:</strong> ${d.payload.urgency}</p>
                                <p class="text-muted small">${new Date(d.created_at).toLocaleString()}</p>
                                </div>
                                <div class="d-flex gap-2">
                                <button onclick="assumeDemand(${d.id})" class="btn btn-outline-success w-100" ${disableButtons}>
                                    Assumir
                                </button>
                                <button onclick="ignoreDemand(${d.id})" class="btn btn-outline-danger w-100" ${disableButtons}>
                                    Ignorar
                                </button>
                                </div>
                            </div>
                            </div>
                        </div>
                        `;
                        container.insertAdjacentHTML('beforeend', html);
                    });


            } catch (err) {
                console.error("Erro ao carregar demandas:", err);
            } finally {
                isLoading = false;
            }
        }

        async function ignoreDemand(id) {
            const response = await fetch(`/api/demands/${id}/ignore`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json'
                }
            });
            if (response.ok) {
                mostrarToast('⚠️ Demanda ignorada.');
                await reloadDemands();
            } else {
                mostrarToast('❌ Erro ao ignorar demanda.', 'danger');
            }
        }


        setInterval(reloadDemands, 6000);
        reloadDemands();
    </script>

</body>
</html>
