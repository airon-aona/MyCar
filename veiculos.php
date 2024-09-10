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
    <title>MyCar</title>
</head>
<body>
    
    <?php require "./headerMyCar.php" ?>
    
    <main>
        <form action="./addVeiculo.php" method="post" id="formVeiculo" onsubmit="return validarForm()">
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

        <?php
            
            $sql = "SELECT modelo, apelido, placa, (SELECT max(km) FROM abastecimento WHERE placa = veiculo) as km, (SELECT DATE_FORMAT(max(data),'%d/%m/%Y') FROM abastecimento WHERE placa = veiculo ) as data, tipo, combustivel FROM veiculos, abastecimento GROUP BY placa;";
            $stmt = $connMyCar->prepare($sql);
            $stmt->execute();
            $result = $stmt->get_result();
            
            // Exibir registros de abastecimento
            echo "<b>Registros</b>";
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<hr><p>{$row['modelo']} ({$row['apelido']}) - {$row['placa']}</p>";
                    if($row['km']){
                        echo "<p>Quilometragem: {$row['km']} km.</p>";
                    }
                    if($row['data']){
                        echo "<p>Ultm. Abastecimento: {$row['data']}.</p>";
                    }
                    echo "<p>{$row['tipo']} / {$row['combustivel']}</p>";
                }
            } else {
                echo "<p>Sem Registro<p>";
            }
            
            $stmt->close();
            $connMyCar->close();

        ?>
    </main>
    
    <?php require "./footerMyCar.php"; ?>

    <script src="../../md5Js/js/md5.js"></script>
    <script>
        function validarForm() {
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
        
        function escolha(){
            window.location='?p=' + md5(veiculoSelect.value)
        }
    </script>
</body>
</html>
