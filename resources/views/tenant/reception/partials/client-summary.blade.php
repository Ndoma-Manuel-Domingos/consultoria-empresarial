@php
$clientName = $client->name ?? 'Cliente';

$words = preg_split('/\s+/', trim($clientName));

if (count($words) >= 2) {

$initials =
mb_substr($words[0], 0, 1) .
mb_substr($words[count($words) - 1], 0, 1);

} else {

$initials = mb_substr($clientName, 0, 2);

}

$initials = strtoupper($initials);
@endphp

<div class="dashboard-card chart-card mb-4">

    <div class="card-header-modern">

        <div>

            <h3 class="card-title-modern">
                Cliente
            </h3>

            <div class="card-subtitle-modern">
                Identificação da empresa atendida.
            </div>

        </div>


        @if(isset($client->id))

        <a href="{{ route('tenant.clients.show', $client) }}" class="btn btn-sm btn-outline-primary">

            <i class="fas fa-external-link-alt mr-1"></i>
            Cliente

        </a>

        @endif

    </div>


    <div class="row align-items-center">

        {{-- AVATAR --}}
        <div class="col-md-3 text-center">

            <div class="avatar avatar-lg mx-auto mb-2" style="
                    width:72px;
                    height:72px;
                    font-size:20px;
                    display:flex;
                    align-items:center;
                    justify-content:center;
                 ">

                @if(($client->type ?? 'individual') === 'company')

                <i class="fas fa-building"></i>

                @else

                {{ $initials }}

                @endif

            </div>


            <div style="
                font-size:13px;
                font-weight:700;
                color:#0f172a;
            ">

                {{ $clientName }}

            </div>


            <div class="text-muted" style="font-size:10px;">

                {{ ($client->type ?? '') === 'company'
                    ? 'Empresa'
                    : 'Pessoa singular' }}

            </div>

        </div>


        {{-- DADOS --}}
        <div class="col-md-9">

            <div class="row">

                <div class="col-md-6 mb-3">

                    <small class="text-muted">
                        NIF
                    </small>

                    <div style="font-weight:600;">
                        {{ $client->nif ?: 'Não informado' }}
                    </div>

                </div>


                <div class="col-md-6 mb-3">

                    <small class="text-muted">
                        Telefone
                    </small>

                    <div style="font-weight:600;">
                        {{ $client->phone ?: 'Não informado' }}
                    </div>

                </div>


                <div class="col-md-6 mb-3">

                    <small class="text-muted">
                        Email
                    </small>

                    <div style="font-weight:600;">
                        {{ $client->email ?: 'Não informado' }}
                    </div>

                </div>


                <div class="col-md-6 mb-3">

                    <small class="text-muted">
                        Localização
                    </small>

                    <div style="font-weight:600;">

                        {{ $client->city
                            ?: $client->municipality
                            ?: $client->province
                            ?: 'Não informado' }}

                    </div>

                </div>


                @if($client->company_name)

                <div class="col-12 mb-3">

                    <small class="text-muted">
                        Razão social
                    </small>

                    <div style="font-weight:600;">
                        {{ $client->company_name }}
                    </div>

                </div>

                @endif


                @if($client->contact_person)

                <div class="col-md-6">

                    <small class="text-muted">
                        Pessoa de contacto
                    </small>

                    <div style="font-weight:600;">
                        {{ $client->contact_person }}
                    </div>

                </div>

                @endif


                @if($client->contact_phone)

                <div class="col-md-6">

                    <small class="text-muted">
                        Telefone de contacto
                    </small>

                    <div style="font-weight:600;">
                        {{ $client->contact_phone }}
                    </div>

                </div>

                @endif

            </div>

        </div>

    </div>

</div>
