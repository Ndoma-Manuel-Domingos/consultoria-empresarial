<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>
        {{ $sale->number }}
    </title>
    <style>
        * {
            box-sizing: border-box;
        }
        body {
            margin: 0;
            padding: 30px;
            background: #f1f5f9;
            font-family:
                Arial,
                Helvetica,
                sans-serif;

            color: #1e293b;
        }
        .invoice {
            width: 900px;
            max-width: 100%;
            margin: auto;
            background: white;
            padding: 40px;
        }
        .top {
            display: flex;
            justify-content:
                space-between;

            margin-bottom: 35px;

        }

        .company {

            font-size: 20px;

            font-weight: 700;

        }

        .muted {

            color: #64748b;

            font-size: 13px;

        }

        .document {

            text-align: right;

        }

        .document h1 {

            margin: 0;

            color: #2563eb;

            font-size: 26px;

        }

        .box {

            border: 1px solid #e2e8f0;

            border-radius: 8px;

            padding: 15px;

            margin-bottom: 25px;

        }

        table {

            width: 100%;

            border-collapse:
                collapse;

        }

        th {

            background: #f8fafc;

            font-size: 12px;

            text-align: left;

            padding: 10px;

            border-bottom:
                1px solid #e2e8f0;

        }

        td {

            padding: 10px;

            border-bottom:
                1px solid #f1f5f9;

            font-size: 13px;

        }

        .right {

            text-align: right;

        }

        .totals {

            width: 320px;

            margin-left: auto;

            margin-top: 25px;

        }

        .total {

            font-size: 20px;

            font-weight: 700;

            color: #2563eb;

        }

        .footer {

            margin-top: 50px;

            padding-top: 20px;

            border-top:
                1px solid #e2e8f0;

            font-size: 11px;

            color: #64748b;

            text-align: center;

        }

        @media print {

            body {

                background: white;

                padding: 0;

            }

            .invoice {

                width: 100%;

                padding: 20px;

            }

            .no-print {

                display: none !important;

            }

        }

    </style>
</head>
<body>
    <div class="invoice">
        <div class="top">
            <div>
                <div class="company">
                    {{ config( 'app.name', 'Sistema de Consultoria') }}
                </div>
                <div class="muted">
                    Sistema de Facturação
                </div>
            </div>
            <div class="document">
                <h1>
                    @if($sale->document_type === 'invoice')
                    FACTURA
                    @elseif($sale->document_type === 'receipt')
                    RECIBO
                    @elseif($sale->document_type === 'proforma')
                    FACTURA PROFORMA
                    @else
                    ORÇAMENTO
                    @endif
                </h1>
                <div class="muted">
                    Nº {{ $sale->number }}
                </div>
                <div class="muted">
                    {{ $sale->sale_date->format('d/m/Y H:i') }}
                </div>
            </div>
        </div>
        {{-- CLIENTE --}}
        <div class="box">
            <strong>
                Cliente
            </strong>
            <div style="margin-top:8px;">
                {{ $sale->client?->name ?? 'Consumidor final' }}
            </div>
            @if($sale->client?->nif)
            <div class="muted">
                NIF:
                {{ $sale->client->nif }}
            </div>
            @endif
        </div>
        {{-- ITEMS --}}
        <table>
            <thead>
                <tr>
                    <th>Descrição</th>
                    <th class="right">Qtd.</th>
                    <th class="right">Preço</th>
                    <th class="right">IVA</th>
                    <th class="right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sale->items as $item)
                <tr>
                    <td>
                        {{ $item->product?->name
                        ?? 'Item livre' }}
                    </td>
                    <td class="right">
                        {{ number_format(
                        $item->quantity,
                        3,
                        ',',
                        '.'
                    ) }}
                    </td>
                    <td class="right">
                        {{ number_format(
                        $item->unit_price,
                        2,
                        ',',
                        '.'
                    ) }}
                        Kz
                    </td>
                    <td class="right">
                        {{ number_format(
                        $item->tax_rate,
                        2,
                        ',',
                        '.'
                    ) }}%

                    </td>
                    <td class="right">
                        {{ number_format(
                        $item->total,
                        2,
                        ',',
                        '.'
                    ) }}
                        Kz
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        {{-- TOTAIS --}}
        <div class="totals">
            <table>
                <tr>
                    <td>
                        Subtotal
                    </td>
                    <td class="right">
                        {{ number_format($sale->subtotal,2,',','.') }}
                        Kz
                    </td>
                </tr>
                <tr>
                    <td>
                        Desconto
                    </td>
                    <td class="right">
                        {{ number_format(
                        $sale->discount,
                        2,
                        ',',
                        '.'
                    ) }}
                        Kz
                    </td>
                </tr>
                <tr>
                    <td>
                        IVA
                    </td>
                    <td class="right">
                        {{ number_format(
                        $sale->tax_amount,
                        2,
                        ',',
                        '.'
                    ) }}
                        Kz
                    </td>
                </tr>
                <tr>
                    <td class="total">
                        Total
                    </td>
                    <td class="right total">
                        {{ number_format(
                        $sale->total,
                        2,
                        ',',
                        '.'
                    ) }}
                        Kz
                    </td>
                </tr>
                <tr>
                    <td>
                        Pago
                    </td>
                    <td class="right">
                        {{ number_format($sale->paid_amount,2,',','.' ) }}
                        Kz
                    </td>
                </tr>
                @if($sale->change_amount > 0)
                <tr>
                    <td>
                        Troco
                    </td>
                    <td class="right">
                        {{ number_format($sale->change_amount,2,',','.') }}
                        Kz
                    </td>
                </tr>
                @endif
                @if($sale->balance_due > 0)
                <tr>
                    <td>
                        Saldo em aberto
                    </td>
                    <td class="right" style="color:#dc2626;">
                        {{ number_format($sale->balance_due,2,',','.') }}
                        Kz
                    </td>
                </tr>
                @endif
            </table>
        </div>
        {{-- PAGAMENTOS --}}
        <div class="box" style="margin-top:30px;">
            <strong>
                Pagamentos
            </strong>
            <table style="margin-top:10px;">
                @foreach($sale->payments as $payment)
                <tr>
                    <td>
                        {{ ucfirst($payment->method ) }}
                        @if($payment->reference)
                        <div class="muted">
                            Ref:
                            {{ $payment->reference }}
                        </div>
                        @endif
                    </td>
                    <td class="right">
                        {{ number_format(
                            $payment->amount,
                            2,
                            ',',
                            '.'
                        ) }}

                        Kz

                    </td>

                </tr>

                @endforeach

            </table>

        </div>


        @if($sale->notes)
        <div class="box">
            <strong>
                Observações
            </strong>
            <div style="margin-top:8px;">
                {{ $sale->notes }}
            </div>
        </div>
        @endif
        <div class="footer">
            Documento emitido pelo sistema de facturação.
            <br>
            {{ config( 'app.name', 'Sistema de Consultoria') }}
        </div>
    </div>
    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>
