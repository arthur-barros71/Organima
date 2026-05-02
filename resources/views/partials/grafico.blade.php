@php
    use App\Models\Tb_Cena_Projeto;

    $totalCenas = Tb_Cena_Projeto::where('id_projeto', $id_projeto)->count();
    $cenasFinalizadas = Tb_Cena_Projeto::where('id_projeto', $id_projeto)->where('ic_conclusao', 1)->count();

    $percentual = 0;
    if ($totalCenas > 0) {
        $percentual = ($cenasFinalizadas / $totalCenas) * 100;
    }

    $percentual = min(round($percentual, 2), 100);
    $circunferencia = 100;
    $valorDash = ($percentual / 100) * $circunferencia;
    $strokeDasharray = $valorDash . ', ' . $circunferencia;

    // Agora define a cor com base no percentual
    $corProgresso = '#FF0000'; // Vermelho
    
    if ($percentual >= 100) {
        $corProgresso = '#3ED582'; // Verde
    } elseif ($percentual >= 85) {
        $corProgresso = '#3E52D5'; // Azul
    } elseif ($percentual >= 45) {
        $corProgresso = '#D5B73E'; // Amarelo
    }
@endphp

<div class="progressCircle">
    <svg width="100%" height="100%" viewBox="-2 -2 40 40">
        <!-- Círculo de fundo -->
        <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#ccc" stroke-width="5" />

        <!-- Círculo de progresso -->
        <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" id="progressPath" stroke="{{ $corProgresso }}" stroke-width="5" stroke-dasharray="{{ $strokeDasharray }}"/>

        <!-- Texto de percentual -->
        <text x="18" y="20.5"
            id="progressText"
            text-anchor="middle"
            font-size="6"
            font-weight="bold"
            fill="{{ $corProgresso }}">
            {{ number_format($percentual, 1) }}%
        </text>
    </svg>  
</div>