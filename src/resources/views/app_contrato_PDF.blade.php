@extends('assinatura-digital::layouts.app-assignature')
@section('content')

    <style>
        .assinatura-contratante {
            margin-top: -20px;
        }

        .image-container {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .logo-contrato {
            width: 300px;
            height: 200px;
        }

        #pdf {
            padding: 20px;
        }
    </style>

                        <table class="table">
                            <!-- Contratante -->
                            <thead>
                                <tr>
                                    <th colspan="2" style="text-align: center;"><img class="logo-contrato" src="/images/logo.png" alt="" style="width:165px;height:113px"></th>
                                </tr>
                                <tr>
                                    <th colspan="2" style="text-align: center;"><b>CONTRATO DE PRESTAÇÃO DE SERVIÇOS DE RASTREAMENTO CURRICULAR</b></th>
                                </tr>
                                <tr>
                                    <th colspan="2" style="text-align: center;"></th>
                                </tr>
                                <tr>
                                    <th colspan="2" style="text-align: center;"></th>
                                </tr>
                                <tr>
                                    <th colspan="2"><h3>Contratante:</h3></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="2">
                                        <p>{{ $cliente->nome }}, {{ $cliente->nacionalidade }}, {{ $cliente->profissao }}, portador(a) da Cédula de Identidade RG nº {{ $cliente->rg }}, inscrito(a) no CPF/MF nº {{ $cliente->cpf }}, residente e domiciliado na rua {{ $cliente->rua }}, Bairro {{ $cliente->endereco }}, {{ $cliente->estado }} - {{ $cliente->cidade }}, CEP {{ $cliente->cep }}, e endereço eletrônico {{ $cliente->email }}.</p>
                                    </td>
                                </tr>
        
                                <!-- Representante Legal -->
                                <tr>
                                    <th colspan="2"><h3>Representante Legal:</h3></th>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <p>{{ $cliente->nome_fantasia }}, {{ $cliente->nacionalidade_socio }}, portador(a) da Cédula de Identidade CEP nº {{ $cliente->cep_empresa }}, inscrito(a) no CNPJ/MF nº {{ $cliente->cnpj }}, residente e domiciliado na rua {{ $cliente->empresa_rua }}, Bairro {{ $cliente->endereco }}, {{ $cliente->estado_endereco }} - {{ $cliente->cidade_endereco }}, CEP {{ $cliente->cep_empresa }}, e endereço eletrônico {{ $cliente->email_empresa }}.</p>
                                    </td>
                                </tr>
        
                                <!-- Contratado -->
                                <tr>
                                    <th colspan="2"><h3>Contratado:</h3></th>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <p>{{ $cliente->nome }}, {{ $cliente->nacionalidade }}, {{ $cliente->profissao }}, portador(a) da Cédula de Identidade RG nº {{ $cliente->rg }}, inscrito(a) no CPF/MF nº {{ $cliente->cpf }}, residente e domiciliado na rua {{ $cliente->rua }}, Bairro {{ $cliente->endereco }}, {{ $cliente->estado }} - {{ $cliente->cidade }}, CEP {{ $cliente->cep }}, e endereço eletrônico {{ $cliente->email }}.</p>
                                    </td>
                                </tr>
        
                                <!-- Conteúdo Contratado -->
                                @if ($conteudo_contratado)
                                    <tr>
                                        <td colspan="2">{!! $conteudo_contratado->valor !!}</td>
                                    </tr>
                                @endif
        
                                <!-- Serviços contratados e preço -->
                                <tr>
                                    <th colspan="2"><h3>Serviços contratados e o Preço:</h3></th>
                                </tr>
                                @if ($servicos_selecionados)
                                    <tr>
                                        <td colspan="2">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>ID</th>
                                                        <th>Nome do Serviço</th>
                                                        <th>Preço de Instalação</th>
                                                        <th>Preço de Adesão</th>
                                                        <th>Preço Total</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($servicos_selecionados as $item)
                                                        <tr>
                                                            <td>{{ $item->id }}</td>
                                                            <td>{{ $item->nome_servico }}</td>
                                                            <td>{{ $item->preco_instalacao }}</td>
                                                            <td>{{ $item->preco_adesao }}</td>
                                                            <td>{{ $item->preco_total }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                @endif
        
                                <!-- Veículos Rastreados -->
                                <tr>
                                    <th colspan="2"><h3>Veículos Rastreados:</h3></th>
                                </tr>
                                @if ($veiculos_rastreados)
                                    <tr>
                                        <td colspan="2">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>ID</th>
                                                        <th>Placa do Veículo</th>
                                                        <th>Tipo</th>
                                                        <th>Marca</th>
                                                        <th>Cor</th>
                                                        <th>Ano de Fabricação</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($veiculos_rastreados as $item)
                                                        <tr>
                                                            <td>{{ $item->id }}</td>
                                                            <td>{{ $item->nome }}</td>
                                                            <td>{{ $item->tipo }}</td>
                                                            <td>{{ $item->modelo }}</td>
                                                            <td>{{ $item->cor }}</td>
                                                            <td>{{ $item->ano_fabricacao }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                @endif
        
                                <!-- Total Mensal -->
                                <tr>
                                    <td colspan="2">
                                        <h4>Total Mensal: R$ {{ number_format($contrato->valor_total, 2, ',', '.') }}</h4>
                                    </td>
                                </tr>
        
                                <!-- Cláusula do Contrato -->
                                @if ($clausula_contrato)
                                    <tr>
                                        <td colspan="2">
                                            {!! $clausula_contrato->valor !!}
                                        </td>
                                    </tr>
                                @endif
        
                                <!-- Assinaturas -->
                                <tr>
                                    <td>
                                        <center>
                                            @if ($assinatura_contratado)
                                                <img class="assinatura-contratante" src="{{ $assinatura_contratado->valor }}" alt="">
                                            @endif
                                            <p style="text-align: center; margin-top: -30px;">
                                                ________________________________________________________________________
                                            </p>
                                            <p style="text-align: center;">Assinatura do Contratado</p>
                                        </center>
                                    </td>
                                    <td>
                                        <center>
                                            <img class="assinatura-contratante" src="{{ $contrato->assinatura_cliente }}" alt="">
                                            <p style="text-align: center; margin-top: -30px;">
                                                ________________________________________________________________________
                                            </p>
                                            <p style="text-align: center;">Assinatura do Contratante</p>
                                        </center>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

@endsection
