<?php
session_start();

/* =========================================
   CONFIGURAÇÕES
========================================= */

const ADMIN_USER = 'Nilson';
const ADMIN_PASS = '1234LA';

const DB_HOST = 'localhost';
const DB_NAME = 'u326160813_escalas2';
const DB_USER = 'u326160813_escalas2';
const DB_PASS = 'Escalas@2';

/* =========================================
   ROTAS FIXAS
========================================= */

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
   FUNÇÕES
========================================= */

function db() {
    static $pdo;
    if (!$pdo) {
        $pdo = new PDO(
            'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
            DB_USER,
            DB_PASS,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
    }
    return $pdo;
}

function dia_semana($data) {
    $dias = ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb'];
    return $dias[date('w', strtotime($data))];
}

function corDaRota($rota, $rotas) {
    $cores = ['#3b82f6','#22c55e','#a855f7','#ef4444','#14b8a6','#f97316'];
    if (!in_array($rota, $rotas, true)) return '#facc15';
    return $cores[array_search($rota, $rotas, true) % count($cores)];
}

function limitar_periodo_10_dias($start, $end) {
    $start = date('Y-m-d', strtotime($start));
    $end   = date('Y-m-d', strtotime($end));
    $max   = date('Y-m-d', strtotime('+9 days', strtotime($start)));
    if ($end > $max) $end = $max;
    return [$start, $end];
}
