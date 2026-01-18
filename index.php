<?php
require_once 'config.php';

// Filtros
$inicio = $_GET['inicio'] ?? date('Y-m-d');
$fim    = $_GET['fim'] ?? $inicio;
$motoristaFiltro = $_GET['motorista'] ?? '';

// Limita período (até 10 dias)
[$inicio, $fim] = limitar_periodo_10_dias($inicio, $fim);

// SQL base
$sqlQuery = "
    SELECT date, departure, route, driver, bus
    FROM schedules
    WHERE date BETWEEN :i AND :f
";

// Filtro por motorista
if (!empty($motoristaFiltro)) {
    $sqlQuery .= " AND driver LIKE :motorista";
}

$sqlQuery .= " ORDER BY date, departure";

// Prepare
$stmt = db()->prepare($sqlQuery);

// Bind datas
$stmt->bindValue(':i', $inicio);
$stmt->bindValue(':f', $fim);

// Bind motorista (se existir)
if (!empty($motoristaFiltro)) {
    $stmt->bindValue(':motorista', '%' . $motoristaFiltro . '%');
}

// Executa
$stmt->execute();
$escalas = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#0d0d0d">

    <title>Escalas dos Motoristas</title>

    <!-- FAVICON (CORRETO E ÚNICO) -->
    <link rel="icon" type="image/png" sizes="512x512" href="/favicon-512.png">
    <link rel="apple-touch-icon" sizes="512x512" href="/favicon-512.png">

    <!-- Manifest (se existir) -->
    <link rel="manifest" href="manifest.json">
    

    <style>
        body{
            background:#0f172a;
            color:#e5e7eb;
            font-family:Arial;
            margin:0;
        }
        .container{
            max-width:1000px;
            margin:20px auto;
            padding:16px;
        }
        .card{
            background:#020617;
            border:1px solid #1f2937;
            border-radius:12px;
            padding:16px;
        }
        table{
            width:100%;
            border-collapse:collapse;
            font-size:13px;
        }
        th,td{
            padding:6px;
            border-bottom:1px solid #1f2937;
        }
        th{
            background:#020617;
            text-align:left;
        }
        input,button{
            padding:8px;
            border-radius:6px;
            border:1px solid #1f2937;
            background:#020617;
            color:#e5e7eb;
        }
        button{
            cursor:pointer;
            background:#1f2937;
        }
        @media (max-width: 600px) {
            .filtro {
                display:flex;
                flex-direction:column;
                gap:8px;
            }
            input, button {
                width:100%;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="card">

        <h2>Escalas</h2>
        <button id="btnInstalar" style="
    display:none;
    margin-bottom:12px;
    background:#2563eb;
    border:none;
    padding:10px;
    border-radius:8px;
    color:#fff;
    font-size:14px;
    cursor:pointer;
">
📲 Instalar App
</button>


        <form method="GET" class="filtro" style="margin-bottom:12px">
            <input type="date" name="inicio" value="<?= htmlspecialchars($_GET['inicio'] ?? $inicio) ?>">
            <input type="date" name="fim" value="<?= htmlspecialchars($_GET['fim'] ?? $fim) ?>">
            <input
                type="text"
                name="motorista"
                placeholder="Nome do motorista"
                value="<?= htmlspecialchars($motoristaFiltro) ?>"
            >
            <button type="submit">Filtrar</button>
        </form>

        <div style="overflow-x:auto;">
            <table>
                <tr>
                    <th>Data</th>
                    <th>Horário</th>
                    <th>Rota</th>
                    <th>Motorista</th>
                    <th>Ônibus</th>
                </tr>

                <?php if (!$escalas): ?>
                <tr>
                    <td colspan="5" style="text-align:center; padding:20px;">
                        Nenhuma escala encontrada
                    </td>
                </tr>
                <?php endif; ?>

                <?php foreach ($escalas as $e): ?>
                <tr>
                    <td><?= date('d/m/Y', strtotime($e['date'])) ?></td>
                    <td><?= substr($e['departure'], 0, 5) ?></td>
                    <td>
                        <span style="background:<?= corDaRota($e['route'], $ROUTES ?? []) ?>;padding:3px 6px;border-radius:6px;color:#000;display:inline-block;white-space:nowrap;">
                            <?= htmlspecialchars($e['route']) ?>
                        </span>
                    </td>
                    <td><?= htmlspecialchars($e['driver']) ?></td>
                    <td><?= htmlspecialchars($e['bus']) ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>

    </div>
</div>

</body>
<script>
let deferredPrompt;

window.addEventListener('beforeinstallprompt', (e) => {
    e.preventDefault();
    deferredPrompt = e;

    const btn = document.getElementById('btnInstalar');
    btn.style.display = 'block';

    btn.addEventListener('click', async () => {
        btn.style.display = 'none';
        deferredPrompt.prompt();

        const { outcome } = await deferredPrompt.userChoice;
        deferredPrompt = null;
    });
});
</script>

</html>
