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

        <?php
        if (isset($_GET['p'])) {
            $veiculo = $_GET['p'];

            $sql = "SELECT * FROM abastecimento WHERE md5(veiculo) = ? ORDER BY data DESC LIMIT 15";
            $stmt = $connMyCar->prepare($sql);
            $stmt->bind_param("s", $veiculo);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $rows = $result->fetch_all(MYSQLI_ASSOC);

            if (count($rows) >= 2) {
                $kmAtual = $rows[0]['km'];
                $kmAnterior = $rows[1]['km'];
                $qtdLitros = $rows[0]['litros'];
                $qtdKm = $kmAtual - $kmAnterior;
                $economia = ($qtdLitros > 0) ? $qtdKm / $qtdLitros : 0;

                echo "<p>Km Atual: $kmAtual </p><p>Km Anterior: $kmAnterior </p><p>Distância Percorrida: $qtdKm km </p><p>Quantidade Abastecida: $qtdLitros litros </p><p>Economia: " . number_format($economia, 2) . " KM/L</p>";
            } else {
                echo "<p>Não há registros suficientes para calcular economia.</p>";
            }
            
            ?>
            
            <div class="grafico">
                <div class="economiaChart">
                    <canvas id="economiaChart" width="600" height="400"></canvas>
                </div>
            </div>
            
            <?php
            
            $sql3 = "SELECT (MAX(km)-MIN(km))/count(*) as mediaPercorrida, ((MAX(km)-MIN(km))/count(*))/(SUM(litros)/count(*)) as mediaEconomia, count(*) as qtdRegs FROM abastecimento WHERE md5(veiculo) = ?;";
            $stmt = $connMyCar->prepare($sql3);
            $stmt->bind_param("s", $veiculo);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $row3 = $result->fetch_all(MYSQLI_ASSOC);
            
            $mediaKm = number_format($row3[0]['mediaPercorrida'], 2, ',');
            $mediaEco = number_format($row3[0]['mediaEconomia'], 2, ',');
            
            echo "<p><b>Média Percorida entre os abastecimentos: {$mediaKm} km</b></p><p><b>Média de economia: {$mediaEco} km/l</b></p><hr>";
            
            $sql2 = "SELECT *, DATE_FORMAT(data,'%d/%m/%Y') as dataForm FROM abastecimento, veiculos WHERE md5(veiculo) = ? AND veiculo = placa ORDER BY data DESC LIMIT 15";
            $stmt2 = $connMyCar->prepare($sql2);
            $stmt2->bind_param("s", $veiculo);
            $stmt2->execute();
            $result2 = $stmt2->get_result();
            
            // Exibir registros de abastecimento
            echo "<h3>Registros - {$row3[0]['qtdRegs']}</h3>";
            if ($result2->num_rows > 0) {
                while($row2 = $result2->fetch_assoc()) {
                    echo "<hr><b>Data: {$row2['dataForm']}</b>
                    <p>{$row2['apelido']} - Quilometragem: {$row2['km']} km.</p>
                    <p>Abastecimento: {$row2['litros']} litros - Media: {$row2['economia']} km/l.</p>";
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
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('economiaChart').getContext('2d');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: <?php echo json_encode(array_column($rows, 'data')); ?>,
                    datasets: [
                        {
                            label: 'Média do Consumo',
                            data: <?php echo json_encode(array_column($rows, 'economia')); ?>,
                            borderWidth: 1,
                            borderColor: '#02f7ef',
                            yAxisID: 'y-axis-1', // Associar este dataset ao primeiro eixo y
                            fill: false
                        },
                        {
                            label: 'Quilometragem',
                            data: <?php echo json_encode(array_column($rows, 'km')); ?>,
                            borderWidth: 1,
                            borderColor: '#ff5733',
                            yAxisID: 'y-axis-2', // Associar este dataset ao segundo eixo y
                            fill: false
                        }
                    ]
                },
                options: {
                    scales: {
                        y: {
                            // Configura o primeiro eixo y
                            'y-axis-1': {
                                type: 'linear',
                                position: 'left',
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Economia (km/L)'
                                }
                            },
                            // Configura o segundo eixo y
                            'y-axis-2': {
                                type: 'linear',
                                position: 'right',
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Quilometragem (km)'
                                },
                                grid: {
                                    drawOnChartArea: false // Desenha a grade apenas no eixo y principal
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
</body>
</html>
