<?php

require_once './connectMyCar.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['entrar'])) {
    $modelo = strtoupper($_POST['modelo']);
    $ano = $_POST['ano'];
    $placa = strtoupper($_POST['placa']);
    $apelido = strtoupper($_POST['apelido']);
    $tipo = $_POST['tipo'];
    $combustivel = $_POST['combustivel'];
    $descricao = strtoupper($_POST['descricao']);

    // Prepare e execute o insert
    $sql = "INSERT INTO `veiculos` (`id`, `placa`, `modelo`, `ano`, `descricao`, `apelido`, `tipo`, `combustivel`) VALUES (NULL, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $connMyCar->prepare($sql);
    $stmt->bind_param("sssssss", $placa, $modelo, $ano, $descricao, $apelido, $tipo, $combustivel);
    $stmt->execute();
    $stmt->close();
    $connMyCar->close();

    // Redirecione após o insert
    header("Location: ./veiculos.php");
    exit();
}
?>
