<?php 

namespace Seomidia\AssinaturaDigital;

use App\Models\Contratos;
use App\Models\Clientes;
use TCPDF;
use App\Models\Configuracoes;
use App\Models\PropostaVenda;
use App\Models\TiposServicos;
use App\Models\Veiculos;
use Illuminate\Support\Facades\Auth;
use App\Models\Vendedores;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use setasign\Fpdi\Tcpdf\Fpdi;


class Signature extends FPDI
{
    public $Contrato;
    public $Cliente;
    private $nome;
    private $cpf;


    public function __construct($Contrato,$Cliente)
    {
        parent::__construct();
        $this->Contrato   = $Contrato;
        $this->Cliente = $Cliente;

        $this->CreatePDF();
    }

    public function CreatePDF()
    {
        $user = Auth::user();
        $contrato = $this->Contrato;
        $proposta = PropostaVenda::where('id', $contrato->proposta_venda_id)->first();
        $cliente = Clientes::find($proposta->cliente_id);
        $servicos_contratados = TiposServicos::all();
        $veiculo = Veiculos::where('cliente_id',$cliente->id)->latest()->first();
        $assinatura_contratado = Configuracoes::where('chave', 'assinatura_contratado')->first();
        $conteudo_contratado = Configuracoes::where('chave', 'conteudo_contrato')->first();
        $clausula_contrato = Configuracoes::where('chave', 'clausula_contrato')->first();
        $veiculos_rastreados = Veiculos::where('cliente_id',$cliente->id)
                                ->where('proposta_venda_id', $proposta->id)
                                ->get();

        $vendedor = Vendedores::find($cliente->vendedor_id);



        $servicos_selecionados = [];

        $valores_servicos = [];

        if ($proposta->tipos_servico) {
            $propostaTiposServico = json_decode($proposta->tipos_servico);
            foreach ($propostaTiposServico as $servicoJson) {
                $servicoObj = json_decode($servicoJson);
                $servicos_selecionados[] = $servicoObj;
            }
        }

        if ($valores_servicos) {
            foreach ($valores_servicos as $option) {
                $servico = TiposServicos::find($option);
                $servicos_selecionados[] = $servico;
            }
        }

        // Diretório onde o PDF será salvo
        $directory = 'contratos/';
        $filename  =  Str::slug($this->Cliente->nome) . '.pdf';
        $filepath  = storage_path('app/' . $directory . $filename);

        // Verifica se o diretório existe, caso não, cria o diretório
        if (!Storage::exists($directory)) {
            Storage::makeDirectory($directory);
        }
        $type = !is_null($this->Cliente->cnpj) ? 'Juridica' : 'Fisica';
        $typedoc = !is_null($this->Cliente->cnpj) ? 'cnpj' : 'cpf';
        $email_validation = $contrato->email_validation == 'CONFIRMED' ? 'Confirmado' : 'Nao confirmado';
        $info = $this->Cliente->nome . " " .strtoupper($typedoc) . " ". $this->Cliente->$typedoc . "\n E-mail ".$email_validation." via codigo vificador \n IP da rede ". $contrato->code_confirmation_ip .  " da assinatura \n Data: " . date('d/m/Y H:i');
        // criando e configurando o PDF
        $pdf = new TCPDF('L', 'mm', array(350, 210), true, 'UTF-8', false);
         // criando o cabeçalho do PDF com as informações do certificado
         $pdf->SetHeaderData('', 2, 'Assinado de forma eletrônica por', $info, array(0, 0, 0), array(255, 255, 255));
        // definindo a fonte do cabeçalho do PDF
        $pdf->setHeaderFont(['helvetica', '', 9]);
        // definindo as margens do cabeçalho do PDF
        $pdf->SetMargins(15, 21, 15);
        $pdf->SetHeaderMargin(5);
        // removendo o rodapé do PDF
        $pdf->setPrintFooter(false);
        // definindo a fonte e o título da página do PDF
        $pdf->SetFont('helvetica', '', 12);
        $pdf->SetTitle('Assinado de forma digital');
        $pdf->AddPage();

         // view contendo a página PDF que será imprimida
         $text = view('conteudos/contratos/app_contrato_PDF',compact('cliente','user',
         'proposta','veiculo','servicos_contratados','veiculos_rastreados','conteudo_contratado'
         ,'servicos_selecionados','vendedor','contrato','assinatura_contratado','clausula_contrato'));
         // adicionando o conteúdo do certificado e do PDF para impressão
        $pdf->writeHTML($text, true, 0, true, 0);

        $pdf->Output($filepath, 'F');
        $this->asign($filepath);
    } 

    public function asign($documentoParaAssinar)
    {
        $diretorio = base_path() . '/';
        $nomeCertPFX = $diretorio . env('CERT_PFX');
        $nomeCertCRT = $diretorio . env('CERT_CRT');
        $password = env('CERT_PASSWORD');
        if (!file_exists('tcpdf.crt')){
            shell_exec("openssl pkcs12 -in $nomeCertPFX -clcerts -nokeys -out $nomeCertCRT -nodes -passin pass:'".$password."'");
        }

        $p = file_get_contents($nomeCertCRT);
        $pkcs12 = file_get_contents($nomeCertPFX);

        $cert = openssl_x509_read( $p );
        $cert_parsed = openssl_x509_parse( $cert ,true);
        
        $nome_cpf = explode(":",$cert_parsed['subject']['CN']);

        $res = [];
        $openSSL = openssl_pkcs12_read($pkcs12, $res, $password);
        if(!$openSSL) {
            throw new \ClientException("Error: ".openssl_error_string());
        }

        if (openssl_pkcs12_read($pkcs12, $cert_info, $password)) {
            // echo "Certificate read\n";
        } else {
            echo "Error: Unable to read the cert store.\n";
            exit;
        }

        $info = array(
            'Name' => $this->Cliente->nome,
            'Location' => 'Brasil, '.$this->Cliente->cidade.'-' . $this->Cliente->estado,
            'Reason' => $this->Cliente->razao_social,
            'ContactInfo' => $this->Cliente->email,
         );
    // Carrega o documento PDF de origem
    $numPages = $this->setSourceFile($documentoParaAssinar);

    // Define dimensões personalizadas para as páginas do PDF (largura e altura em mm)
    $pageWidth = 350;  // Largura personalizada
    $pageHeight = 210; // Altura personalizada

    // Loop através de cada página do documento
    for ($i = 0; $i < $numPages; $i++) {
        // Adiciona uma nova página com dimensões personalizadas
        $this->AddPage('L', array($pageWidth, $pageHeight)); // 'L' para orientação paisagem

        // Importa a página atual
        $tplId = $this->importPage($i + 1);

        // Define a assinatura na página atual
        $this->setSignature($cert_info['cert'], $cert_info['pkey'], '', '', 2, $info);

        // Usa a página importada como modelo
        $this->useTemplate($tplId, 0, 0, $pageWidth, $pageHeight); // Importa com dimensões personalizadas

        // Define a aparência da assinatura
        $this->setSignatureAppearance(10, 10, 30, 10, 1);
    }

    // Envia o PDF assinado para download
    $this->Output($documentoParaAssinar, 'F');    
   }

    function Header(){
    }

    function Footer(){
        // Positionnement à 1,5 cm du bas
        $this->SetY(-10);
        // Police Arial italique 8
        $this->SetFont('Helvetica','',6);
        // Numéro de page
        $textoFooter = "DOCUMENTO ASSINADO DIGITALMENTE POR {$this->Cliente->nome} CPF {$this->Cliente->cpf} VERIFIQUE O DOCUMENTO EM https://verificador.iti.br/";
        $this->Cell(0,10,$textoFooter,'T',0,'C');
    }
}