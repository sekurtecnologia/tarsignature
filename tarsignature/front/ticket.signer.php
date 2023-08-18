<?php

include('../../../inc/includes.php');

global $CFG_GLPI;

$base_api_url = $CFG_GLPI['url_base_api'];

if (!isset($_GET['session_token']) or !isset($_GET['app_token'])) {
  Session::redirectIfNotLoggedIn();
}

$session_token = $_GET['session_token'];
$app_token = $_GET['app_token'];


// ID da ordem de serviço que será buscada
$id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);

global $DB;
// Consulta SQL para buscar a ordem de serviço
$result = $DB->query("SELECT * FROM glpi_tickets WHERE id = {$id}");
$url_base = "{$_SERVER['REQUEST_SCHEME']}://{$_SERVER['SERVER_NAME']}:{$_SERVER['SERVER_PORT']}";

// Verifica se a consulta retornou algum resultado
if ($result->num_rows > 0) {
  // Converte o resultado em um array associativo
  $ordem_de_servico = $result->fetch_assoc();
}
?>

<html>
<link rel="stylesheet" href="<?php echo $url_base . '/css_compiled/css_palettes_auror.min.css' ?>">

<body>
  <?php if ($result->num_rows > 0): ?>
    <div class="mt-5" style="text-align:center">
      <p><strong>Chamado #</strong>
        <?php echo $ordem_de_servico['id'] ?>
      </p>
      <p><strong>Solicitação: </strong>
        <?php echo $ordem_de_servico['name'] ?>
      </p><br>

      <canvas class="border" id="signature-pad" width="400" height="200"></canvas>
      <br>
      <div class="mt-4">
        <button class="btn btn-outline-secondary" id="clear-button">Limpar</button>
        <button class="btn btn-secondary" id="signer-button">Enviar Assinatura</button>
      </div>
    </div>
    <hr>

    <script type="text/javascript" src="/js/glpi_dialog.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.2.61/jspdf.debug.js"></script>
    <script>
      var canvas = document.getElementById("signature-pad");
      var context = canvas.getContext("2d");
      var clearButton = document.getElementById("clear-button");
      var saveButton = document.getElementById("save-button");
      var exportButton = document.getElementById("export-button");
      var signerButton = document.getElementById("signer-button");

      // Inicialize as configurações do canvas
      context.lineWidth = 2;
      context.lineCap = "round";
      context.strokeStyle = "black";

      // Adicione um evento de touchstart ao canvas
      canvas.addEventListener("touchstart", function (event) {
        // Obtenha as coordenadas do toque
        var x = event.touches[0].clientX - canvas.offsetLeft;
        var y = event.touches[0].clientY - canvas.offsetTop;

        // Mude para o início da linha atual
        context.moveTo(x, y);
      });

      // Adicione um evento de touchmove ao canvas
      canvas.addEventListener("touchmove", function (event) {
        // Evite a propagação do evento
        event.preventDefault();

        // Obtenha as coordenadas do toque
        var x = event.touches[0].clientX - canvas.offsetLeft;
        var y = event.touches[0].clientY - canvas.offsetTop;

        // Desenhe uma linha até as coordenadas do toque
        context.lineTo(x, y);
        context.stroke();
      });

      // Adicione um evento de mousedown ao canvas
      canvas.addEventListener("mousedown", function (event) {
        // Mude para o início da linha atual
        context.moveTo(event.offsetX, event.offsetY);
      });

      // Adicione um evento de mousemove ao canvas
      canvas.addEventListener("mousemove", function (event) {
        // Desenhe uma linha até as coordenadas do mouse
        context.lineTo(event.offsetX, event.offsetY);
        context.stroke();
      });

      // Adicione um evento de click ao botão de limpar
      clearButton.addEventListener("click", function () {
        // Limpe o canvas
        context.clearRect(0, 0, canvas.width, canvas.height);
        window.location.reload();
      });

      // Adicione um evento de click ao botão de salvar
      signerButton.addEventListener("click", function () {
        // Salve a assinatura como uma imagem PNG

        const browser = navigator.userAgent;
        const device = navigator.platform;

        navigator.geolocation.getCurrentPosition(async function (position) {
          const latitude = position.coords.latitude;
          const longitude = position.coords.longitude;

          const params = new URLSearchParams(window.location.search);

          date = new Date()

          const id_ticket = '<?php echo $id; ?>';
          const ip = '<?php echo $_SERVER['REMOTE_ADDR']; ?>';

          const mapUrl = `https://www.google.com/maps?q=${latitude},${longitude}`;

          const dataURL = canvas.toDataURL();

          const content = `
                Coleta: ${date.toLocaleString('pt-BR')}<br>
                Latitude: ${latitude}<br>
                Longitude: ${longitude}<br>
                Local: <br><a href='${mapUrl}'>[Clique para visualizar no mapa]</a><br>
                Navegador: ${browser}<br>
                Equipamento : ${device}<br>
                IP : ${ip}<br>
                <img src='${dataURL}' style='max-height:200px'/>
              `;

          const base_api_url = '<?= $base_api_url ?>'

          const req = await fetch(
            `${base_api_url}/Ticket/${id_ticket}/TicketFollowup`,
            {
              method: 'POST',
              body: JSON.stringify({
                'input': {
                  'items_id': id_ticket,
                  'itemtype': "Ticket",
                  'is_private': "0",
                  'requesttypes_id': "6",
                  'content': content
                }
              }),
              headers: {
                'Content-Type': 'application/json',
                'Session-Token': params.get('session_token'),
                'App-Token': params.get('app_token')
              }
            })

          const res = await req.json()

          // Apagar token de sessão
          async function killSession() {
            const req = await fetch(
              `${base_api_url}/killSession`,
              {
                headers: {
                  'Content-Type': 'application/json',
                  'Session-Token': params.get('session_token'),
                  'App-Token': params.get('app_token')
                }
              })

            const res = await req.json()

            return res;
          }

          if (res?.id) {
            alert('Assinatura salva com sucesso');
            await killSession();

            window.location = window.location.origin;
          } else {
            alert('Erro ao tentar salvar assintura');
          }
        });

      });


    </script>
  <?php else: ?>
    <h1 class="mt-5" style="text-align:center">Não foi possível encontrar a ordem de serviço com o ID especificado</h1>
  <?php endif; ?>
</body>

</html>