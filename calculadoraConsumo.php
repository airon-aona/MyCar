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
    
    <?php require "./headerMyCar.php"; ?>
    
    <main>
        <form action="./addAbastecimento.php?p=<?php echo $_GET['p']?>" method="post" id="formVeiculo" onsubmit="return validarForm()">
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
        
        <div class="select">
            <select id="veiculoSelect" name="veiculoSelect" onchange="escolha()">
                <option value="0" disabled selected>Escolha um Veículo</option>
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
        

        <?php

        if (isset($_GET['p'])) {
            $veiculo = $_GET['p'];
            
            echo "<button onclick=\"window.location='relatorio.php?p=$veiculo'\">Gerar Relatório</button></div>";
            
            $sql = "SELECT * FROM abastecimento WHERE md5(veiculo) = ? ORDER BY data DESC";
            $stmt = $connMyCar->prepare($sql);
            $stmt->bind_param("s", $veiculo);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $rows = $result->fetch_all(MYSQLI_ASSOC);

            if (count($rows) >= 2) {
                $kmAtual = $rows[0]['km'];
                $kmAnterior = $rows[1]['km'];
                $qtdLitros = $rows[1]['litros'];
                $qtdKm = $kmAtual - $kmAnterior;
                $economia = ($qtdLitros > 0) ? $qtdKm / $qtdLitros : 0;

                echo "<p>Km Atual: $kmAtual </p><p>Km Anterior: $kmAnterior </p><p>Distância Percorrida: $qtdKm km </p><p>Quantidade Abastecida: $qtdLitros litros </p><p>Economia: " . number_format($economia, 2) . " KM/L</p>";
            } else {
                echo "<p>Não há registros suficientes para calcular economia.</p>";
            }
            
            $sql2 = "SELECT *, DATE_FORMAT(data,'%d/%m/%Y') as dataForm FROM abastecimento, veiculos WHERE md5(veiculo) = ? AND veiculo = placa ORDER BY data DESC LIMIT 5";
            $stmt2 = $connMyCar->prepare($sql2);
            $stmt2->bind_param("s", $veiculo);
            $stmt2->execute();
            $result2 = $stmt2->get_result();
            
            // Exibir registros de abastecimento
            echo "<b>Registros</b>";
            if ($result2->num_rows > 0) {
                while($row2 = $result2->fetch_assoc()) {
                    echo "<hr><b>Data: {$row2['dataForm']}</b><p>{$row2['apelido']} - Quilometragem: {$row2['km']} km.</p>";
                }
            } else {
                echo "<p>Sem Registro<p>";
            }
            
            $stmt->close();
            $stmt2->close();
            $connMyCar->close();
            
        }

        ?>
    </main>
    
    <?php require "./footerMyCar.php"; ?>

    <script src="../../md5Js/js/md5.js"></script>
    <script>
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
    </script>
</body>
</html>
