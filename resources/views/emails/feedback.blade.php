<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback</title>
</head>
<body>
    <h2>Você recebeu um novo feedback</h2>
    <p><strong>Nome:</strong> {{ $nome }}</p>
    <p><strong>E-mail:</strong> {{ $email }}</p>
    <p><strong>Mensagem:</strong></p>
    <p>{!! nl2br(e($mensagem)) !!}</p> <!-- nl2br() para preservar quebras de linha -->
</body>
</html>