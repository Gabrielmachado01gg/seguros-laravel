<!DOCTYPE html>
<html lang="pt-BR">
<body style="font-family: Arial, sans-serif; color: #1f2937;">
    <p>Olá, {{ $apolice->cliente->nome }}!</p>
    <p>
        Sua apólice <strong>{{ $apolice->numero }}</strong> ({{ $apolice->ramo }} - {{ $apolice->seguradora }})
        vence em <strong>{{ $apolice->vigencia_fim->format('d/m/Y') }}</strong>.
    </p>
    <p>Entre em contato para renovar com as melhores condições e manter sua proteção em dia.</p>
</body>
</html>
