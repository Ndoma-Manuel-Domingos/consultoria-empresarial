@php
    $statusLabels = [
        'waiting' => 'Aguardando',
        'triage' => 'Triagem',
        'awaiting_payment' => 'Pagamento',
        'paid' => 'Pago',
        'in_progress' => 'Em atendimento',
        'completed' => 'Concluído',
        'cancelled' => 'Cancelado',
    ];

    $statusClasses = [
        'waiting' => 'status-warning',
        'triage' => 'status-warning',
        'awaiting_payment' => 'status-danger',
        'paid' => 'status-success',
        'in_progress' => 'status-info',
        'completed' => 'status-success',
        'cancelled' => 'status-danger',
    ];

    $priorityLabels = [
        'low' => 'Baixa',
        'normal' => 'Normal',
        'high' => 'Alta',
        'urgent' => 'Urgente',
    ];
@endphp


@if($appointments->count())

<table class="table table-hover align-middle mb-0">

    <thead>

        <tr>

            <th>
                Atendimento
            </th>

            <th>
                Cliente
            </th>

            <th>
                Data
            </th>

            <th>
                Serviço
            </th>

            <th>
                Prioridade
            </th>

            <th>
                Estado
            </th>

            <th class="text-right">
                Ações
            </th>

        </tr>

    </thead>


    <tbody>

        @foreach($appointments as $appointment)

            <tr>

                {{-- CÓDIGO --}}
                <td>

                    <a href="{{ route('tenant.reception.show', $appointment) }}"
                       style="font-weight:700;color:#2563eb;">

                        {{ $appointment->code }}

                    </a>

                    <div class="text-muted"
                         style="font-size:10px;">

                        #{{ $appointment->id }}

                    </div>

                </td>


                {{-- CLIENTE --}}
                <td>

                    <div style="font-weight:600;">

                        {{ $appointment->client->name ?? 'Cliente removido' }}

                    </div>

                    @if($appointment->client?->nif)

                        <div class="text-muted"
                             style="font-size:10px;">

                            NIF: {{ $appointment->client->nif }}

                        </div>

                    @endif

                </td>


                {{-- DATA --}}
                <td>

                    <div>

                        {{ optional($appointment->appointment_date)->format('d/m/Y') }}

                    </div>

                    @if($appointment->appointment_time)

                        <small class="text-muted">

                            {{ substr($appointment->appointment_time, 0, 5) }}

                        </small>

                    @endif

                </td>


                {{-- SERVIÇO --}}
                <td>

                    {{ $appointment->service_type ?: '—' }}

                </td>


                {{-- PRIORIDADE --}}
                <td>

                    @php
                        $priorityClass = match($appointment->priority) {
                            'urgent' => 'text-danger',
                            'high' => 'text-warning',
                            'low' => 'text-muted',
                            default => 'text-primary',
                        };
                    @endphp

                    <span class="{{ $priorityClass }}"
                          style="font-weight:600;">

                        {{ $priorityLabels[$appointment->priority] ?? ucfirst($appointment->priority) }}

                    </span>

                </td>


                {{-- STATUS --}}
                <td>

                    <span class="status {{ $statusClasses[$appointment->status] ?? 'status-info' }}">

                        {{ $statusLabels[$appointment->status] ?? ucfirst($appointment->status) }}

                    </span>

                </td>


                {{-- AÇÕES --}}
                <td class="text-right">

                    <div class="btn-group">

                        <a href="{{ route('tenant.reception.show', $appointment) }}"
                           class="btn btn-sm btn-outline-primary"
                           title="Ver atendimento">

                            <i class="fas fa-eye"></i>

                        </a>


                        @if(in_array($appointment->status, ['waiting', 'triage']))

                            <a href="{{ route('tenant.reception.triage', $appointment) }}"
                               class="btn btn-sm btn-outline-secondary"
                               title="Triagem">

                                <i class="fas fa-stethoscope"></i>

                            </a>

                        @endif


                        @if($appointment->status === 'awaiting_payment')

                            <a href="{{ route('tenant.reception.payment', $appointment) }}"
                               class="btn btn-sm btn-outline-success"
                               title="Pagamento">

                                <i class="fas fa-credit-card"></i>

                            </a>

                        @endif


                        <a href="{{ route('tenant.reception.ficha', $appointment) }}"
                           class="btn btn-sm btn-outline-dark"
                           title="Ficha">

                            <i class="fas fa-file-medical"></i>

                        </a>

                    </div>

                </td>

            </tr>

        @endforeach

    </tbody>

</table>


@else

<div class="text-center py-5">

    <div style="
        width:55px;
        height:55px;
        border-radius:14px;
        background:#eff6ff;
        color:#2563eb;
        display:flex;
        align-items:center;
        justify-content:center;
        margin:0 auto 12px;
        font-size:20px;
    ">

        <i class="fas fa-inbox"></i>

    </div>


    <div style="
        font-weight:700;
        color:#334155;
    ">

        Nenhum atendimento encontrado

    </div>


    <div class="text-muted mt-1"
         style="font-size:12px;">

        Não existem atendimentos correspondentes aos filtros.

    </div>

</div>

@endif