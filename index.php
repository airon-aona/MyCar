<?php

require_once './connectMyCar.php'; 
$today = date("Y-m-d");

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/style.css">
    <link rel="shortcut icon" href="../../img/Icon/AzulClaro/IconClaro.png" type="image/x-icon">
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <title>MyCar</title>
</head>
<body>
    
    <div class="formularios">
    
        <form action="./addAbastecimento.php?p=<?php echo $_GET['p']?>" method="post" id="formAbast" onsubmit="return validarForm()">
            <i class="bi bi-x-lg" onClick="openForm('formAbast')"></i>
            <select id="veiculo" name="veiculo">
                <option value="0" disabled selected>Escolha seu Veículo</option>
                <?php
                $sql = "SELECT * FROM veiculos";
                $result = $connMyCar->query($sql);
    
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<option value=\"{$row['placa']}\">{$row['apelido']}</option>";
                    }
                } else {
                    echo "<option value=\"0\" disabled selected>Sem Veículos Aqui!</option>";
                }
                ?>
            </select>
            <input type="date" value="<?php echo $today ?>" id="data" name="data">
            <input type="number" placeholder="Quilometragem" id="km" name="km" min="0">
            <input type="number" placeholder="Quantidade Abastecida (Litros)" id="litros" name="litros" min="0" step="0.0010">
            <input type="submit" value="Enviar" name="entrar">
        </form>
        
        <form action="./addVeiculo.php" method="post" id="formVeic" onsubmit="return validarForm1()">
            <i class="bi bi-x-lg" onClick="openForm('formVeic')"></i>
            <input type="text" placeholder="Modelo" name="modelo" id="modelo">
            <input type="number" min="1900" max="2999" step="1" placeholder="Ano" name="ano" id="ano" />
            <input type="text" placeholder="Placa" name="placa" id="placa" />
            <input type="text" placeholder="Apelido" name="apelido" id="apelido" />
            <select name="tipo" id="tipo">
                <option value="0" disabled selected>Escolha um tipo de veículo</option>
                <option value="CARRO">Carro</option>
                <option value="MOTO">Moto</option>
                <option value="Caminhão">Caminhão</option>
                <option value="OUTRO">Outro</option>
            </select>
            <select name="combustivel" id="combustivel">
                <option value="0" disabled selected>Escolha um tipo de combustivel</option>
                <option value="GASOLINA">Gasolina</option>
                <option value="ETANOL">Etanol</option>
                <option value="FLEX">Flex</option>
                <option value="ELETRICO">Elétrico</option>
                <option value="DIESEL">Diesel</option>
                <option value="OUTRO">Outro</option>
            </select>
            <textarea name="descricao" placeholder="Descricao"></textarea>
            <input type="submit" value="Adicionar" name="entrar">
        </form>
    
    </div>

    <main id="main">
        <img src="https://airontechzone.com/img/Imgs/AdobeStock_474947920.jpeg">
        <div class="atalhos">
            <div onClick="window.location.href = './calculadoraConsumo.php' " class="card">Consumo <i class="bi bi-graph-up"></i></div>
            <div onClick="window.location.href = './veiculos.php' " class="card">Veículos <i class="bi bi-car-front-fill"></i></div>
            <div onClick="openForm('formAbast')" class="card">Abastecer <i class="bi bi-fuel-pump-fill"></i></div>
            <div onClick="openForm('formVeic')" class="card">Adicionar Veículo <i class="bi bi-plus-circle-fill"></i></div>
        </div>
    </main>
    
    <?php require "./footerMyCar.php"; ?>
    
    <script src="../../md5Js/js/md5.js"></script>
    <script>
        function validarForm1() {
            var modelo = document.getElementById('modelo').value;
            var ano = document.getElementById('ano').value;
            var placa = document.getElementById('placa').value;
            var apelido = document.getElementById('apelido').value;
            var tipo = document.getElementById('tipo').value;
            var combustivel = document.getElementById('combustivel').value;
            
            if (modelo == '' || ano == 0 || placa == '' || apelido == '' || tipo == 0 || combustivel == 0) {
                alert('Complete Todas as Informações');
                return false;
            }
            return true;
        }
        
        function validarForm() {
            var veiculo = document.getElementById('veiculo').value;
            var data = document.getElementById('data').value;
            var km = document.getElementById('km').value;
            var litros = document.getElementById('litros').value;
            
            if (veiculo == 0 || data == '' || km == '' || litros == '') {
                alert('Complete Todas as Informações');
                return false;
            }
            return true;
        }
        
        function escolha(){
            window.location='?p=' + md5(veiculoSelect.value)
        }
        
        function openForm(id){
            form = document.getElementById(id);
            form.classList.toggle('openForm');
        }
    </script>
    
</body>
</html>
