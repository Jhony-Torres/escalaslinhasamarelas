<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="theme-color" content="#0d0d0d">

<title>Escalas dos Motoristas</title>

<link rel="icon" type="image/png" sizes="512x512" href="/favicon-512.png">
<link rel="apple-touch-icon" sizes="512x512" href="/favicon-512.png">
<link rel="manifest" href="manifest.json">

<script src="https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2"></script>

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
.filtro{
    display:flex;
    gap:8px;
    flex-wrap:wrap;
}
@media (max-width: 600px) {
    .filtro{
        flex-direction:column;
    }
    input,button{
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

<div class="filtro" style="margin-bottom:12px">
<input type="date" id="inicio">
<input type="date" id="fim">
<input type="text" id="motorista" placeholder="Nome do motorista">
<button onclick="carregarEscalas()">Filtrar</button>
</div>

<div style="overflow-x:auto;">
<table>
<thead>
<tr>
<th>Data</th>
<th>Horário</th>
<th>Rota</th>
<th>Motorista</th>
<th>Ônibus</th>
</tr>
</thead>
<tbody id="tabela">
<tr>
<td colspan="5" style="text-align:center;padding:20px;">Carregando...</td>
</tr>
</tbody>
</table>
</div>

</div>
</div>

<script>
/* 🔐 SUPABASE */
const SUPABASE_URL = 'https://SEU-PROJETO.supabase.co'
const SUPABASE_ANON_KEY = 'SUA_PUBLIC_ANON_KEY'

const supabase = supabase.createClient(SUPABASE_URL, SUPABASE_ANON_KEY)

/* 📅 Datas padrão */
const hoje = new Date().toISOString().split('T')[0]
document.getElementById('inicio').value = hoje
document.getElementById('fim').value = hoje

/* 🎨 Cor da rota (exemplo simples) */
function corDaRota(rota){
    const cores = {
        'Palmas / Araguaína':'#facc15',
        'Araguaína / Palmas':'#38bdf8'
    }
    return cores[rota] || '#a7f3d0'
}

/* 🔄 Carregar escalas */
async function carregarEscalas(){
    const inicio = document.getElementById('inicio').value
    const fim = document.getElementById('fim').value
    const motorista = document.getElementById('motorista').value

    let query = supabase
        .from('schedules')
        .select('*')
        .gte('date', inicio)
        .lte('date', fim)
        .order('date')
        .order('departure')

    if(motorista){
        query = query.ilike('driver', `%${motorista}%`)
    }

    const { data, error } = await query

    const tbody = document.getElementById('tabela')
    tbody.innerHTML = ''

    if(error || !data || data.length === 0){
        tbody.innerHTML = `
        <tr>
            <td colspan="5" style="text-align:center;padding:20px;">
                Nenhuma escala encontrada
            </td>
        </tr>`
        return
    }

    data.forEach(e=>{
        tbody.innerHTML += `
        <tr>
            <td>${new Date(e.date).toLocaleDateString()}</td>
            <td>${e.departure.slice(0,5)}</td>
            <td>
                <span style="
                    background:${corDaRota(e.route)};
                    padding:3px 6px;
                    border-radius:6px;
                    color:#000;
                    white-space:nowrap;
                    display:inline-block;
                ">
                ${e.route}
                </span>
            </td>
            <td>${e.driver}</td>
            <td>${e.bus}</td>
        </tr>`
    })
}

carregarEscalas()

/* 📲 PWA */
let deferredPrompt
window.addEventListener('beforeinstallprompt', (e)=>{
    e.preventDefault()
    deferredPrompt = e
    const btn = document.getElementById('btnInstalar')
    btn.style.display = 'block'
    btn.onclick = async ()=>{
        btn.style.display = 'none'
        deferredPrompt.prompt()
        deferredPrompt = null
    }
})
</script>

</body>
</html>
