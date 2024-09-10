<?php

require_once './connectMyCar.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['entrar'])) {

    // Obtenha o veículo e outros dados do POST
    $veiculo = $_POST['veiculo'];
    $data = $_POST['data'];
    $km = intval($_POST['km']);
    $litros = floatval($_POST['litros']);

    // Prepare a consulta para obter os últimos 2 abastecimentos
    $sql = "SELECT km, litros FROM abastecimento WHERE veiculo = ? ORDER BY data DESC LIMIT 2";
    $stmt = $connMyCar->prepare($sql);
    $stmt->bind_param("s", $veiculo);
    $stmt->execute();
    $result = $stmt->get_result();

    // Verifique se há pelo menos dois registros
    if ($result->num_rows >= 2) {
        $rows = $result->fetch_all(MYSQLI_ASSOC);
        $kmAtual = $km;
        $kmAnterior = $rows[0]['km'];
        $qtdLitros = $rows[0]['litros'];
        $qtdKm = $kmAtual - $kmAnterior;
        $economia = ($qtdLitros > 0) ? $qtdKm / $qtdLitros : 0;
    } else {
        $economia = 0; // Não é possível calcular a economia sem 2 abastecimentos
    }

    // Prepare e execute o insert
    $sql2 = "INSERT INTO abastecimento (veiculo, data, km, litros, economia) VALUES (?, ?, ?, ?, ?)";
    $stmt = $connMyCar->prepare($sql2);
    $stmt->bind_param("ssdds", $veiculo, $data, $km, $litros, $economia);
    $stmt->execute();
    $stmt->close();
    $connMyCar->close();

    // Redirecione após o insert
    header("Location: ./calculadoraConsumo.php?p=" . urlencode($_GET['p']));
    exit();
}
?>
