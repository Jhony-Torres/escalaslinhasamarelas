<?php
session_start();

function dia_semana($data) {
    $dias = [
        'Domingo', 'Segunda-feira', 'Terça-feira',
        'Quarta-feira', 'Quinta-feira', 'Sexta-feira', 'Sábado'
    ];
    $indice = date('w', strtotime($data));
    return $dias[$indice] ?? '';
}

/* =========================================
   CONFIGURAÇÕES DE LOGIN E BANCO (AJUSTE)
========================================= */

// Login do painel
const ADMIN_USER = 'Nilson';
const ADMIN_PASS = '1234LA';

// WhatsApp padrão (caso queira usar depois para número direto)
const WHATSAPP_PADRAO = '5563999999999';

// Dados do MySQL da Hostinger
const DB_HOST = 'localhost';
const DB_NAME = 'u326160813_escalas2';   // <-- TROCAR SE PRECISAR
const DB_USER = 'u326160813_escalas2';   // <-- TROCAR SE PRECISAR
const DB_PASS = 'Escalas@2';   // <-- TROCAR SE PRECISAR

// Rotas fixas em ordem exata enviada
$ROUTES = [
    'Palmas/TO 🔁 Araguaína/TO',
    'Palmas/TO 🔁 Filadélfia/TO',
    'Palmas/TO 🔁 Miracema/TO',
    'Palmas/TO 🔁 Aparecida/TO',
    'Palmas/TO 🔁 Novo Acordo/TO',
    'Palmas/TO 🔁 Porto Nacional/TO',
    'Palmas/TO 🔁 Tocantinía/TO',
    'Araguaína/TO 🔁 Marabá/PA',
    'Marabá/PA 🔁 Xinguara/PA',
    'Altamira/PA 🔁 Marabá/PA',
    'Tucuruí/PA 🔁 Marabá/PA',
];
/* =========================================
   CORES POR ROTA
========================================= */

function corDaRota(string $rota, array $rotasFixas): string {
    // Paleta elegante (pode ajustar depois)
    $cores = [
        '#3b82f6', // azul
        '#22c55e', // verde
        '#a855f7', // roxo
        '#ef4444', // vermelho
        '#14b8a6', // teal
        '#f97316', // laranja
        '#6366f1', // indigo
        '#06b6d4', // cyan
        '#84cc16', // lime
        '#ec4899', // pink
        '#8b5cf6', // violet
    ];

    // Se NÃO for rota fixa → AMARELO
    if (!in_array($rota, $rotasFixas, true)) {
        return '#facc15'; // amarelo padrão Linhas Amarelas
    }

    // Usa posição da rota para definir cor fixa
    $index = array_search($rota, $rotasFixas, true);
    return $cores[$index % count($cores)];
}


/* =========================================
   CONEXÃO PDO (MySQL)
========================================= */

function db() {
    static $pdo;
    if (!$pdo) {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }
    return $pdo;
}

/* =========================================
   LOGIN / LOGOUT
========================================= */

$action = $_POST['action'] ?? $_GET['action'] ?? null;
$view   = $_GET['view'] ?? 'app';

if ($action === 'login') {
    $user = trim($_POST['user'] ?? '');
    $pass = trim($_POST['pass'] ?? '');
    if ($user === ADMIN_USER && $pass === ADMIN_PASS) {
        $_SESSION['logged'] = true;
        header('Location: escalas.php');
        exit;
    } else {
        $login_error = 'Usuário ou senha inválidos.';
    }
}

if ($action === 'logout') {
    session_destroy();
    header('Location: escalas.php');
    exit;
}

$logged = !empty($_SESSION['logged']);

/* =========================================
   FUNÇÕES AUXILIARES
========================================= */

function encode_whats($text) {
    return rawurlencode($text);
}

function limitar_periodo_10_dias(string $start, string $end): array {
    $startTs = strtotime($start) ?: time();
    $start   = date('Y-m-d', $startTs);

    $endTs = strtotime($end);
    if ($endTs === false || $endTs < $startTs) {
        $endTs = $startTs;
    }

    $maxEndTs = strtotime('+9 days', $startTs); // 10 dias no total
    if ($endTs > $maxEndTs) {
        $endTs = $maxEndTs;
    }

    $end = date('Y-m-d', $endTs);
    return [$start, $end];
}

/* =========================================
   CRUD DE ESCALAS (só logado)
========================================= */

if ($logged && $action === 'save_schedule') {
    // Captura rota do select + campo extra
    $routeSelect = $_POST['route_select'] ?? '';
    $routeInput  = trim($_POST['route'] ?? '');

    // PRIORIDADE:
    // 1) Se selecionou rota fixa, usa ela
    // 2) Se escolheu "Outra", usa o que foi digitado
    if ($routeSelect && $routeSelect !== 'other') {
        $route = $routeSelect;
    } else {
        $route = $routeInput;
    }

    $id        = $_POST['id'] ?? '';
    $date      = $_POST['date'] ?? '';
    $bus       = trim($_POST['bus'] ?? '');
    $driver    = trim($_POST['driver'] ?? '');
    $departure = $_POST['departure'] ?? '';
    $whatsapp  = preg_replace('/\D+/', '', $_POST['whatsapp'] ?? '');
    $notes     = trim($_POST['notes'] ?? '');

    if ($date && $route && $bus && $driver && $departure) {
        if ($id) {
            // Quando editando, verificar se não está criando duplicação com outra escala
            // Compara apenas os 5 primeiros caracteres da hora (HH:MM) para evitar problemas com segundos
            $checkSql = "SELECT COUNT(*) FROM schedules 
                        WHERE date = :date 
                        AND TRIM(route) = TRIM(:route) 
                        AND LEFT(departure, 5) = LEFT(:departure, 5) 
                        AND TRIM(driver) = TRIM(:driver) 
                        AND id != :id";
            $checkStmt = db()->prepare($checkSql);
            $checkStmt->execute([
                'date' => $date,
                'route' => $route,
                'departure' => $departure,
                'driver' => $driver,
                'id' => $id
            ]);
            
            if ($checkStmt->fetchColumn() > 0) {
                // Viagem duplicada detectada ao editar - redirecionar com mensagem de erro
                header('Location: escalas.php?start_date=' . urlencode($date) . '&end_date=' . urlencode($date) . '&error=duplicate');
                exit;
            }
            
            $sql = "UPDATE schedules
                    SET date = :date, route = :route, bus = :bus,
                        driver = :driver, departure = :departure,
                        whatsapp = :whatsapp, notes = :notes
                    WHERE id = :id";
            db()->prepare($sql)->execute(compact(
                'date','route','bus','driver','departure','whatsapp','notes','id'
            ));
        } else {
            // Verificar se já existe uma viagem com a mesma data, rota, horário e motorista
            // Compara apenas os 5 primeiros caracteres da hora (HH:MM)
            $checkSql = "SELECT COUNT(*) FROM schedules 
                        WHERE date = :date 
                        AND TRIM(route) = TRIM(:route) 
                        AND LEFT(departure, 5) = LEFT(:departure, 5) 
                        AND TRIM(driver) = TRIM(:driver)";
            $checkStmt = db()->prepare($checkSql);
            $checkStmt->execute([
                'date' => $date,
                'route' => $route,
                'departure' => $departure,
                'driver' => $driver
            ]);
            
            if ($checkStmt->fetchColumn() > 0) {
                // Viagem duplicada detectada - redirecionar com mensagem de erro
                header('Location: escalas.php?start_date=' . urlencode($date) . '&end_date=' . urlencode($date) . '&error=duplicate');
                exit;
            }
            
            $sql = "INSERT INTO schedules
                    (date, route, bus, driver, departure, whatsapp, notes)
                    VALUES (:date, :route, :bus, :driver, :departure, :whatsapp, :notes)";
            db()->prepare($sql)->execute(compact(
                'date','route','bus','driver','departure','whatsapp','notes'
            ));
        }
    }

    // Depois de salvar, volta pro período que contém essa data (1 dia)
    header('Location: escalas.php?start_date=' . urlencode($date) . '&end_date=' . urlencode($date));
    exit;
}

if ($logged && $action === 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    db()->prepare("DELETE FROM schedules WHERE id = :id")->execute(['id' => $id]);
    
    // Captura datas para manter o filtro
    $sd = $_GET['start_date'] ?? date('Y-m-d');
    $ed = $_GET['end_date'] ?? $sd;
    
    header('Location: escalas.php?start_date=' . urlencode($sd) . '&end_date=' . urlencode($ed));
    exit;
}

/* =========================================
   VIEW PDF – PERÍODO (ATÉ 10 DIAS)
========================================= */

if ($view === 'pdf') {
    if (!$logged) {
        header('Location: escalas.php');
        exit;
    }

    $start = $_GET['start'] ?? date('Y-m-d');
    $end   = $_GET['end']   ?? $start;
    [$start, $end] = limitar_periodo_10_dias($start, $end);

    $sql = "SELECT * FROM schedules
            WHERE date BETWEEN :start AND :end
            ORDER BY date, departure";
    $stmt = db()->prepare($sql);
    $stmt->execute(['start' => $start, 'end' => $end]);
    $rows = $stmt->fetchAll();

    $porData = [];
    foreach ($rows as $r) {
        $porData[$r['date']][] = $r;
    }
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <!-- Favicon padrão -->
<link rel="icon" type="image/png" sizes="32x32" href="/img.png">

<!-- Ícone para Android e iOS (atalho) -->
<link rel="apple-touch-icon" sizes="180x180" href="/img.png">

<!-- Manifest (ATALHO BONITO) -->
<link rel="manifest" href="/manifest.json">

<meta name="theme-color" content="#F4B000">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta name="apple-mobile-web-app-title" content="Escalas Motoristas">

<meta charset="UTF-8">
<title>Escalas – Linhas Amarelas</title>

<style>

    body{
        font-family: Arial, sans-serif;
        background:#f2f2f2;
        margin:0;
        padding:0;
    }
    .page{
        width:210mm;
        min-height:297mm;
        background:#fff;
        margin:20px auto;
        padding:25mm;
        box-sizing:border-box;
    }
    .top-info{
        display:flex;
        justify-content:space-between;
        font-size:12px;
        color:#555;
        margin-bottom:10px;
    }
    .title{
        text-align:center;
        font-size:22px;
        font-weight:bold;
        margin:10px 0 20px;
    }
    .title span{
        background:#fbbf24;
        padding:4px 10px;
        border-radius:6px;
    }
    .period{
        text-align:center;
        font-size:13px;
        margin-bottom:20px;
    }
    .date-box{
        background:#fbbf24;
        color:#000;
        padding:8px 12px;
        font-weight:bold;
        border-radius:6px 6px 0 0;
        font-size:14px;
    }
    table{
        width:100%;
        border-collapse:collapse;
        font-size:13px;
        margin-bottom:20px;
    }
    th{
        background:#000;
        color:#fff;
        padding:6px;
        text-align:left;
    }
    td{
        border:1px solid #ddd;
        padding:6px;
    }
    tr:nth-child(even){
        background:#f9f9f9;
    }
    .footer{
        position:fixed;
        bottom:20mm;
        left:25mm;
        right:25mm;
        text-align:center;
        font-size:11px;
        color:#666;
    }
</style>

<script>
window.onload = function(){
    window.print();
}
</script>

</head>
<body>

<div class="page">

    <div class="top-info">
        <div><?= date('d/m/Y H:i') ?></div>
        
    </div>

    <div class="title">
        <span>Linhas Amarelas</span> – Escalas
    </div>


    <?php foreach ($porData as $data => $lista): ?>
        <div class="date-box">
            Escala: <?= date('d/m/Y', strtotime($data)) ?>
            — <span style="font-weight:normal;">
                <?= htmlspecialchars($lista[0]['route']) ?>
              </span>
        </div>

        <table>
            <tr>
                <th>Horário</th>
                <th>Motorista</th>
                <th>Carro</th>
                <th>Rota</th>
                
            </tr>
            <?php foreach ($lista as $item): ?>
                <tr>
                    <td><?= substr($item['departure'],0,5) ?></td>
<td><?= htmlspecialchars($item['driver']) ?></td>
<td><?= htmlspecialchars($item['bus']) ?></td>
<td>
    <span style="
        background: <?= corDaRota($item['route'], $ROUTES) ?>;
        padding:3px 6px;
        border-radius:4px;
        font-size:11px;
        font-weight:bold;
        color:#000;
    ">
        <?= htmlspecialchars($item['route']) ?>
    </span>
</td>

                </tr>
            <?php endforeach; ?>
        </table>
    <?php endforeach; ?>

    <div class="footer">
        Linhas Amarelas – Transporte de Passageiros e Encomendas •
        Contato: (63) 99258-9452 / (63) 98521-0041 / (94) 98414-8869
    </div>

</div>



</body>
</html>
<?php
exit;
}



/* =========================================
   VIEW APLICATIVO (PAINEL + LISTAGEM PERÍODO)
========================================= */

// Período da tela principal (até 10 dias)
$startDate = $_GET['start_date'] ?? date('Y-m-d');
$endDate   = $_GET['end_date']   ?? $startDate;
[$startDate, $endDate] = limitar_periodo_10_dias($startDate, $endDate);

// Escalas do período
$sql = "SELECT * FROM schedules
        WHERE date BETWEEN :start AND :end
        ORDER BY date, departure";
$stmt = db()->prepare($sql);
$stmt->execute(['start' => $startDate, 'end' => $endDate]);
$schedules = $stmt->fetchAll();

// Escala para edição
$editSchedule = null;
if ($logged && isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $s = db()->prepare("SELECT * FROM schedules WHERE id = :id");
    $s->execute(['id' => $id]);
    $editSchedule = $s->fetch();
}

// Texto geral do WhatsApp (período até 10 dias)
$textoWhats = "";

if ($schedules) {
    foreach ($schedules as $sc) {
        $textoWhats .= "Escala " . date('d/m/Y', strtotime($sc['date'])) .
                       " – " . dia_semana($sc['date']) . "\n";
        $textoWhats .= "Rota: {$sc['route']}\n";
        $textoWhats .= "Motorista: {$sc['driver']}\n";
        $textoWhats .= "Ônibus: {$sc['bus']}\n";
        $textoWhats .= "Saída: " . substr($sc['departure'],0,5) . "\n";
        if (!empty($sc['notes'])) {
            $textoWhats .= "Obs: {$sc['notes']}\n";
        }
        $textoWhats .= "\n";
    }
} else {
    $textoWhats .= "Sem escalas cadastradas.\n\n";
}

$textoWhats .= "Linhas Amarelas - Sigamos juntos, com responsabilidade e orgulho do que fazemos! 💛";

// Link de WhatsApp para enviar em grupo/contato
$linkWhatsDia = "https://api.whatsapp.com/send?text=" . encode_whats($textoWhats);

// PDF usa o mesmo período (já limitado a 10 dias)
$inicioPdf = $startDate;
$fimPdf    = $endDate;
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Linhas Amarelas</title>

    <link rel="icon" type="image/png" sizes="32x32" href="/img.png">
    <link rel="apple-touch-icon" href="/img.png">
</head>

    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body{
            font-family: Arial, Helvetica, sans-serif;
            margin: 0;
            background:#0f172a;
            color:#e5e7eb;
        }
        header{
            background:#020617;
            padding:10px 20px;
            border-bottom:1px solid #1f2937;
            display:flex;
            align-items:center;
            justify-content:space-between;
        }
        .logo{
            display:flex;
            align-items:center;
            gap:8px;
        }
        .logo-box{
            width:32px;
            height:32px;
            border-radius:8px;
            background:#facc15;
            display:flex;
            align-items:center;
            justify-content:center;
            color:#020617;
            font-weight:bold;
        }
        .container{
            max-width:1100px;
            margin:0 auto;
            padding:16px;
        }
        h1,h2{
            margin:0 0 8px 0;
        }
        .card{
            background:#020617;
            border:1px solid #1f2937;
            border-radius:12px;
            padding:16px;
            margin-bottom:16px;
        }
        label{
            font-size:12px;
            color:#9ca3af;
            display:block;
            margin-bottom:4px;
        }
        input, textarea, select{
            width:100%;
            box-sizing:border-box;
            border-radius:8px;
            border:1px solid #4b5563;
            background:#020617;
            color:#e5e7eb;
            padding:6px 8px;
            font-size:13px;
        }
        input:focus, textarea:focus, select:focus{
            outline:1px solid #facc15;
        }
        .grid{
            display:grid;
            grid-template-columns:repeat(auto-fit,minmax(200px,1fr));
            gap:10px;
        }
        button, .btn{
            border:none;
            border-radius:8px;
            padding:6px 10px;
            font-size:13px;
            cursor:pointer;
            text-decoration:none;
            display:inline-block;
        }
        .btn-yellow{ background:#facc15;color:#020617;font-weight:bold; }
        .btn-secondary{ background:#111827;color:#e5e7eb;border:1px solid #374151; }
        .btn-red{ background:#ef4444;color:#fff; }
        .btn-green{ background:#22c55e;color:#022c22; }
        table{
            width:100%;
            border-collapse:collapse;
            font-size:13px;
        }
        th,td{
            padding:6px 8px;
            border-bottom:1px solid #1f2937;
        }
        th{
            text-align:left;
            background:#020617;
        }
        tr:nth-child(even) td{
            background:#020617;
        }
        .top-row{
            display:flex;
            flex-wrap:wrap;
            gap:12px;
            align-items:flex-end;
            justify-content:space-between;
        }
        .top-row > div{
            min-width:220px;
        }
        @media (max-width:600px){
            header{flex-direction:column;align-items:flex-start;gap:8px;}
        }
    /* Modal de Erro */
    .modal-overlay {
        position: fixed;
        top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0,0,0,0.5);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 9999;
    }
    .modal-box {
        background: #fff;
        padding: 24px;
        border-radius: 8px;
        max-width: 400px;
        width: 90%;
        text-align: center;
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    }
    .modal-box h3 { color: #dc2626; margin-top: 0; }
    .modal-box p { color: #333; font-size: 15px; margin: 16px 0; }
    .btn-modal {
        background: #000; color: #fff; padding: 10px 20px;
        border: none; border-radius: 4px; cursor: pointer;
        font-weight: bold;
        text-transform: uppercase;
        font-size: 13px;
    }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var select = document.getElementById('route_select');
            var input  = document.getElementById('route_input');

            function syncRoute() {
                if (!select || !input) return;
                if (select.value === 'other' || select.value === '') {
                    input.style.display = 'block';
                    if (select.value !== 'other' && select.value !== '') {
                        input.value = '';
                    }
                } else {
                    input.style.display = 'none';
                    input.value = select.value;
                }
            }
            if (select) {
                select.addEventListener('change', syncRoute);
                syncRoute();
            }
        });
    </script>
</head>
<body>

<header>
    <div class="logo">
        <div class="logo-box">LA</div>
        <div>
            <div style="font-weight:bold;font-size:14px;">Linhas Amarelas</div>
            <div style="font-size:11px;color:#9ca3af;">Sistema de Escalas de Motoristas</div>
        </div>
    </div>
    <div>
        <?php if ($logged): ?>
            <form method="post" style="display:inline;">
                <input type="hidden" name="action" value="logout">
                <button class="btn btn-secondary">Sair</button>
            </form>
        <?php endif; ?>
    </div>
</header>

<div class="container">

    <?php if (!$logged): ?>
        <!-- LOGIN -->
        <div class="card" style="max-width:360px;margin:40px auto;">
            <h2>Login do painel</h2>
            <?php if (!empty($login_error)): ?>
                <div style="color:#fecaca;font-size:12px;margin-bottom:8px;"><?= htmlspecialchars($login_error) ?></div>
            <?php endif; ?>
            <form method="post">
                <input type="hidden" name="action" value="login">
                <div style="margin-bottom:8px;">
                    <label>Usuário</label>
                    <input type="text" name="user">
                </div>
                <div style="margin-bottom:12px;">
                    <label>Senha</label>
                    <input type="password" name="pass">
                </div>
                <button class="btn btn-yellow" style="width:100%;">Entrar</button>
            </form>
        </div>

    <?php else: ?>

        <!-- TOPO: PERÍODO + WHATSAPP + PDF -->
        <div class="card">
            <div class="top-row">
                <div>
                    <label>Período da escala (até 10 dias)</label>
                    <form method="get" style="display:flex;flex-wrap:wrap;gap:4px;">
                        <div style="flex:1 1 120px;">
                            <input type="date" name="start_date" value="<?= htmlspecialchars($startDate) ?>">
                        </div>
                        <div style="flex:1 1 120px;">
                            <input type="date" name="end_date" value="<?= htmlspecialchars($endDate) ?>">
                        </div>
                        <button class="btn btn-secondary" style="margin-top:4px;">Filtrar período</button>
                    </form>
                </div>
                <div>
                    <label>Escalas do período via WhatsApp</label>
                    <a class="btn btn-green" href="<?= htmlspecialchars($linkWhatsDia) ?>" target="_blank">
                        enviar escala pelo whatsapp
                    </a>
                </div>
                <div>
                    <label>Gerar PDF organizado (igual modelo)</label>
                    <form method="get" style="display:flex;flex-wrap:wrap;gap:4px;align-items:center;">
                        <input type="hidden" name="view" value="pdf">
                        <div style="flex:1 1 100px;">
                            <input type="date" name="start" value="<?= htmlspecialchars($inicioPdf) ?>">
                        </div>
                        <div style="flex:1 1 100px;">
                            <input type="date" name="end" value="<?= htmlspecialchars($fimPdf) ?>">
                        </div>
                        <button class="btn btn-yellow" type="submit">Abrir modo PDF</button>
                    </form>
                    <div style="font-size:11px;color:#9ca3af;margin-top:4px;">
                        Dica: no modo PDF aperte <strong>Ctrl+P</strong> e escolha “Salvar como PDF”.
                    </div>
                </div>
            </div>
        </div>

        <!-- FORMULÁRIO ESCALA -->
        <div class="card">
            <h2><?= $editSchedule ? 'Editar escala' : 'Cadastrar nova escala' ?></h2>
            <form method="post" id="form-escala" onsubmit="return handleFormSubmit(this);" autocomplete="off">
                <input type="hidden" name="action" value="save_schedule">
                <input type="hidden" name="id" value="<?= htmlspecialchars($editSchedule['id'] ?? '') ?>">

                <div class="grid">
                    <div>
                        <label>Data</label>
                        <input type="date" name="date"
                               value="<?= htmlspecialchars($editSchedule['date'] ?? $startDate) ?>" required>
                    </div>
                    <div>
                        <label>Rota</label>
                        <?php
                        $rotaPadrao = 'Palmas/TO 🔁 Araguaína/TO';
                        $rotaAtual = $editSchedule['route']
                        ?? ($_POST['route_select'] ?? $rotaPadrao);
                        $rotaEstaNasFixas = in_array($rotaAtual, $ROUTES, true);

                        ?>
                        <select name="route_select" id="route_select">
                            
                            <?php foreach ($ROUTES as $r): ?>
                                <option value="<?= htmlspecialchars($r) ?>" <?= $rotaEstaNasFixas && $rotaAtual === $r ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($r) ?>
                                </option>
                            <?php endforeach; ?>
                            <option value="other" <?= (!$rotaEstaNasFixas && $rotaAtual !== '') ? 'selected' : '' ?>>
                                Outra rota (digitar)
                            </option>
                        </select>
                        <input
                            type="text"
                            name="route"
                            id="route_input"
                            placeholder="Digite nova rota no mesmo formato"
                            value="<?= htmlspecialchars($rotaEstaNasFixas ? '' : $rotaAtual) ?>"
                            style="margin-top:6px;"
                        >
                    </div>
                    <div>
                        <label>Ônibus / Micro</label>
                        <input type="text" name="bus" value="<?= htmlspecialchars($editSchedule['bus'] ?? '') ?>" required>
                    </div>
                    <div>
                        <label>Motorista</label>
                        <input type="text" name="driver" value="<?= htmlspecialchars($editSchedule['driver'] ?? '') ?>" required>
                    </div>
                    <div>
                        <label>Horário de saída</label>
                        <input type="time" name="departure" value="<?= htmlspecialchars($editSchedule['departure'] ?? '') ?>" required>
                    </div>
                    <div>
                    </div>
                </div>

                <div style="margin-top:8px;">
                    <label>Observações</label>
                    <textarea name="notes" rows="2"><?= htmlspecialchars($editSchedule['notes'] ?? '') ?></textarea>
                </div>

                <div style="margin-top:10px;display:flex;gap:8px;">
                    <button class="btn btn-yellow" type="submit">
                        <?= $editSchedule ? 'Salvar alterações' : 'Adicionar escala' ?>
                    </button>
                    <?php if ($editSchedule): ?>
                        <a href="escalas.php?start_date=<?= urlencode($startDate) ?>&end_date=<?= urlencode($endDate) ?>"
                           class="btn btn-secondary">Cancelar</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <!-- LISTA DO PERÍODO -->
        <div class="card">
            <h2>
                Escalas do período
                <?= date('d/m/Y', strtotime($startDate)) ?>
                até
                <?= date('d/m/Y', strtotime($endDate)) ?>
            </h2>
            <?php if (!$schedules): ?>
                <p style="font-size:13px;color:#9ca3af;">Nenhuma escala cadastrada neste período.</p>
            <?php else: ?>
                <table>
                    <tr>
                        <th>Data</th>
                        <th>Horário</th>
                        <th>Rota</th>
                        <th>Motorista</th>
                        <th>Ônibus</th>
                        <th>Obs.</th>
                        <th style="text-align:right;">Ações</th>
                    </tr>
                    <?php foreach ($schedules as $sc): ?>
                        <tr>
                            <td><?= date('d/m/Y', strtotime($sc['date'])) ?></td>
                            <td><?= htmlspecialchars(substr($sc['departure'],0,5)) ?></td>
                            <td>     <span style="         background: <?= corDaRota($sc['route'], $ROUTES) ?>;         color:#020617;         padding:3px 8px;         border-radius:6px;         font-size:12px;         font-weight:600;         display:inline-block;     ">         <?= htmlspecialchars($sc['route']) ?>     </span> </td>
                            <td><?= htmlspecialchars($sc['driver']) ?></td>
                            <td><?= htmlspecialchars($sc['bus']) ?></td>
                            <td><?= nl2br(htmlspecialchars($sc['notes'])) ?></td>
                            <td style="text-align:right;">
                                <?php
                                $txt = "Escala " . date('d/m/Y', strtotime($sc['date'])) .
                                       " – " . dia_semana($sc['date']) . "\n"
                                    . "Rota: {$sc['route']}\n"
                                    . "Motorista: {$sc['driver']}\n"
                                    . "Ônibus: {$sc['bus']}\n"
                                    . "Saída: " . substr($sc['departure'],0,5) . "\n";
                                if (!empty($sc['notes'])) {
                                    $txt .= "Obs: {$sc['notes']}\n";
                                }
                                $linkLinha = "https://api.whatsapp.com/send?text=" . encode_whats($txt);
                                ?>
                                <a class="btn btn-secondary"
                                   href="escalas.php?edit=<?= $sc['id'] ?>&start_date=<?= urlencode($startDate) ?>&end_date=<?= urlencode($endDate) ?>">
                                    Editar
                                </a>
                                <a class="btn btn-red"
                                   href="#"
                                   onclick="openDeleteModal(<?= $sc['id'] ?>, '<?= urlencode($startDate) ?>', '<?= urlencode($endDate) ?>'); return false;">
                                    Excluir
                                </a>
                                <a class="btn btn-green" href="<?= htmlspecialchars($linkLinha) ?>" target="_blank">
                                    WhatsApp
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            <?php endif; ?>
        </div>

    <?php endif; ?>

</div>

<!-- MODAL DUPLICAÇÃO -->
<div class="modal-overlay" id="modalDuplicate">
    <div class="modal-box">
        <h3>⚠️ Escala Duplicada!</h3>
        <p>Já existe uma viagem cadastrada com essa data, rota, horário e motorista.</p>
        <p>Verifique a lista abaixo antes de cadastrar novamente.</p>
        <button class="btn-modal" onclick="closeModal()">Entendido</button>
    </div>
</div>

<!-- MODAL EXCLUSÃO -->
<div class="modal-overlay" id="modalDelete">
    <div class="modal-box">
        <h3>🗑️ Excluir Escala?</h3>
        <p>Tem certeza que deseja remover esta viagem?</p>
        <div style="display:flex;gap:10px;justify-content:center;margin-top:20px;">
            <button class="btn-modal" style="background:#374151;" onclick="closeDeleteModal()">Cancelar</button>
            <a id="btnConfirmDelete" href="#" class="btn-modal" style="background:#ef4444;text-decoration:none;">Sim, Excluir</a>
        </div>
    </div>
</div>

<script>
function handleFormSubmit(form) {
    const btn = form.querySelector('button[type="submit"]');
    if (btn.disabled) {
        return false;
    }
    btn.disabled = true;
    btn.innerText = 'Processando...';
    return true;
}

// Verifica erro na URL
const urlParams = new URLSearchParams(window.location.search);
if (urlParams.get('error') === 'duplicate') {
    document.getElementById('modalDuplicate').style.display = 'flex';
    
    // Remove o parâmetro de erro da URL sem recarregar
    const newUrl = window.location.href.replace('&error=duplicate', '').replace('?error=duplicate', '?');
    window.history.replaceState({}, document.title, newUrl);
}

function closeModal() {
    document.getElementById('modalDuplicate').style.display = 'none';
    
    // O formulário já vem limpo do PHP no redirecionamento, 
    // mas se houver algum autofill ou valor residual, limpamos aqui.
    const form = document.getElementById('form-escala');
    if(form) {
        // Preserva a data se estiver preenchida (útil para o usuário)
        const dateInput = form.querySelector('input[name="date"]');
        const dateVal = dateInput ? dateInput.value : '';
        
        form.reset();
        
        if(dateInput && dateVal) {
            dateInput.value = dateVal;
        }
    }
}

function openDeleteModal(id, start, end) {
    // Monta o link com ID e as datas para preservar o filtro
    const link = `escalas.php?action=delete&id=${id}&start_date=${start}&end_date=${end}`;
    document.getElementById('btnConfirmDelete').href = link;
    document.getElementById('modalDelete').style.display = 'flex';
}

function closeDeleteModal() {
    document.getElementById('modalDelete').style.display = 'none';
}
</script>

</body>
</html>
