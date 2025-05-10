<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>OnDutyNow - Bem-vindo</title>

    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,600" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Instrument Sans', sans-serif;
            background-color: #0a0a0a;
            color: #f0f0f0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 2rem;
            min-height: 100vh;
            text-align: center;
        }

        .box {
            background-color: #1a1a1a;
            border: 1px solid #333;
            border-radius: 8px;
            padding: 2rem;
            max-width: 800px;
            box-shadow: 0 0 10px #000;
        }

        h1 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }

        p, li {
            font-size: 1.1rem;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
            color: #fff;
        }

        th, td {
            padding: 0.75rem;
            border: 1px solid #444;
        }

        th {
            background-color: #222;
        }

        a {
            color: #00d0ff;
            text-decoration: none;
        }

        code {
            background-color: #222;
            padding: 0.2rem 0.5rem;
            border-radius: 4px;
            color: #00ff9d;
        }
    </style>
</head>
<body>
    <div class="toast-container position-fixed top-0 end-0 p-3" id="toastContainer"></div>
    <div class="box">
        <h1>🚀 OnDutyNow - Ambiente Pronto!</h1>
        <p>Após rodar <code>docker-compose up -d</code>, acesse os seguintes serviços:</p>

        <table>
            <thead>
                <tr>
                    <th>Serviço</th>
                    <th>Porta</th>
                    <th>URL</th>
                    <th>Descrição</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>OnDutyNow API</td>
                    <td>8010</td>
                    <td><a href="http://localhost:8010" target="_blank">localhost:8010</a></td>
                    <td>API principal Laravel ou Utilizar botões abaixo <br>
                        <a href="https://murilo-4094558.postman.co/workspace/Murilo's-Workspace~31d836ce-170c-4a2f-8a5c-581892a4ebba/collection/43787109-3f8d0f00-7926-48b6-9e73-eeb63c38f8b7?action=share&creator=43787109">Postman</a>
                    </td>
                </tr>
                <tr>
                    <td>OnDuty Doctors</td>
                    <td>8011</td>
                    <td><a href="http://localhost:8011/demands" target="_blank">localhost:8011/demands</a></td>
                    <td>Aplicação consumidora em Laravel para processar demandas</td>
                </tr>
                <tr>
                    <td>Monitor Node</td>
                    <td>3000</td>
                    <td><a href="http://localhost:3000" target="_blank">localhost:3000</a></td>
                    <td>Aplicação consumidora em node para monitoramento</td>
                </tr>
                <tr>
                    <td>Kowl</td>
                    <td>8080</td>
                    <td><a href="http://localhost:8080" target="_blank">localhost:8080</a></td>
                    <td>Interface web para inspecionar tópicos Kafka</td>
                </tr>
                <tr>
                    <td>Kafka REST Proxy</td>
                    <td>8082</td>
                    <td><a href="http://localhost:8082" target="_blank">localhost:8082</a></td>
                    <td>Interface HTTP para comunicação com Kafka</td>
                </tr>
                <tr>
                    <td>Kafka Broker</td>
                    <td>9092</td>
                    <td>N/A</td>
                    <td>Porta de conexão Kafka nativa (clients/produtores)</td>
                </tr>
                <tr>
                    <td>Zookeeper</td>
                    <td>2181</td>
                    <td>N/A</td>
                    <td>Gerencia o estado do cluster Kafka</td>
                </tr>
            </tbody>
        </table>

        <hr class="my-5">

        <h2 class="mb-4">Testes rápidos</h2>
        <p class="mb-4">Clique em uma das opções abaixo para simular uma demanda para o tópico <code>demands.created</code>.</p>

        <div class="row g-4">
            <!-- Bloco Hospital 1 -->
            <div class="col-md-6">
                <div class="card bg-dark border-light h-100">
                    <div class="card-header text-white">
                        🏥 Hospital 1
                    </div>
                    <div class="card-body d-flex flex-column gap-3">
                        <button onclick="sendDemand(1, 'Cardiologia')" class="btn btn-primary">
                            Enviar Demanda - Cardiologia
                        </button>
                        <button onclick="sendDemand(1, 'Ortopedia')" class="btn btn-secondary">
                            Enviar Demanda - Ortopedia
                        </button>
                    </div>
                </div>
            </div>

            <!-- Bloco Hospital 2 -->
            <div class="col-md-6">
                <div class="card bg-dark border-light h-100">
                    <div class="card-header text-white">
                        🏥 Hospital 2
                    </div>
                    <div class="card-body d-flex flex-column gap-3">
                        <button onclick="sendDemand(2, 'Neurologia')" class="btn btn-danger">
                            Enviar Demanda - Neurologia
                        </button>
                        <button onclick="sendDemand(2, 'Pediatria')" class="btn btn-success">
                            Enviar Demanda - Pediatria
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <script>
            function mostrarToast(mensagem, tipo = 'success') {
                const toastEl = document.createElement('div');
                toastEl.className = `toast align-items-center text-bg-${tipo} border-0 show mb-2`;
                toastEl.setAttribute('role', 'alert');
                toastEl.setAttribute('aria-live', 'assertive');
                toastEl.setAttribute('aria-atomic', 'true');
                toastEl.innerHTML = `
                    <div class="d-flex">
                        <div class="toast-body">${mensagem}</div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Fechar"></button>
                    </div>
                `;
                document.getElementById('toastContainer').appendChild(toastEl);

                setTimeout(() => toastEl.remove(), 5000);
            }

            async function sendDemand(hospitalId, specialty) {
                const payload = {
                    hospitalId: hospitalId,
                    specialty: specialty,
                    urgency: "alta"
                };

                const res = await fetch('/api/demands', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(payload)
                });

                if (res.ok) {
                    const data = await res.json();
                    mostrarToast(`✅ Demanda enviada para Hospital ${hospitalId}: ${specialty}`);
                } else {
                    mostrarToast('❌ Erro ao enviar demanda.', 'danger');
                }
            }
        </script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    </div>
</body>
</html>
